<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/Index', [
            'products' => Product::with(['supplier:id,company_name', 'images'])
                ->latest()
                ->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create', [
            'suppliers' => Supplier::select('id', 'company_name')->orderBy('company_name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id'      => 'required|exists:suppliers,id',
            'description'      => 'required|string|max:255',            
            'brand'            => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100',
            'cost_price'       => 'required|numeric|min:0',
            'sale_price'       => 'required|numeric|min:0',
            'stock_quantity'   => 'required|integer|min:0',
            'is_active'        => 'boolean',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'images'           => 'required|array|min:1|max:6',
            'images.*'         => 'image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        return DB::transaction(function () use ($request, $data) {
            // Gerar slug do produto
            $data['slug'] = Str::slug($data['description']) . '-' . Str::lower(Str::random(5));
            $product = Product::create($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    if (!$this->isImageSafe($file)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'images' => 'Uma das imagens contém conteúdo impróprio.'
                        ]);
                    }
                    $path = $file->store('products', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }

            // CORREÇÃO: Enviando o slug para o SEO
            $seoTitle = $data['meta_title'] ?? $data['description'];
            $product->seo()->create([
                'meta_title'       => $seoTitle,
                'meta_description' => $data['meta_description'] ?? null,
                'slug'             => Str::slug($seoTitle) . '-' . time(), // Gerando slug único
            ]);

            return redirect()->route('products.index')->with('message', 'Produto cadastrado com sucesso!');
        });
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product->load(['images', 'seo']),
            'suppliers' => Supplier::select('id', 'company_name')->orderBy('company_name')->get()
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'supplier_id'      => 'required|exists:suppliers,id',
            'description'      => 'required|string|max:255',
            'cost_price'       => 'required|numeric|min:0',
            'sale_price'       => 'required|numeric|min:0',
            'stock_quantity'   => 'required|integer|min:0',
            'is_active'        => 'boolean',
            'is_featured'      => 'boolean',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'existing_images'  => 'nullable|array', 
            'new_images'       => 'nullable|array|max:6',
            'new_images.*'     => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $totalImages = count($request->existing_images ?? []) + count($request->file('new_images') ?? []);
        if ($totalImages < 1) {
            return back()->withErrors(['new_images' => 'O produto deve ter pelo menos uma imagem.']);
        }

        return DB::transaction(function () use ($request, $data, $product) {
            $existingIds = collect($request->existing_images)->pluck('id')->toArray();
            $imagesToDelete = $product->images()->whereNotIn('id', $existingIds)->get();

            foreach ($imagesToDelete as $oldImg) {
                Storage::disk('public')->delete($oldImg->path);
                $oldImg->delete();
            }

            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $file) {
                    if (!$this->isImageSafe($file)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'new_images' => 'Uma das novas imagens foi bloqueada.'
                        ]);
                    }
                    $path = $file->store('products', 'public');
                    $product->images()->create(['path' => $path]);
                }
            }

            if ($product->description !== $data['description']) {
                $data['slug'] = Str::slug($data['description']) . '-' . Str::lower(Str::random(5));
            }

            $product->update($data);

            // CORREÇÃO: Enviando o slug no UpdateOrCreate para o SEO
            $seoTitle = $data['meta_title'] ?? $data['description'];
            $product->seo()->updateOrCreate(
                ['seoable_id' => $product->id, 'seoable_type' => get_class($product)],
                [
                    'meta_title'       => $seoTitle,
                    'meta_description' => $data['meta_description'] ?? null,
                    'slug'             => Str::slug($seoTitle) . '-' . time(),
                ]
            );

            return redirect()->route('products.index')->with('message', 'Produto atualizado!');
        });
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();
        return redirect()->route('products.index')->with('message', 'Produto removido.');
    }

    private function isImageSafe($image)
    {
        $credentialPath = base_path('google-credentials.json');

        if (!class_exists('Google\Cloud\Vision\V1\ImageAnnotatorClient') || !file_exists($credentialPath)) {
            Log::info("Moderação de imagem ignorada: Biblioteca ou credenciais ausentes.");
            return true; 
        }

        try {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialPath);
            $imageAnnotator = new ImageAnnotatorClient();
            $content = file_get_contents($image->getRealPath());
            $response = $imageAnnotator->safeSearchDetection($content);
            $safe = $response->getSafeSearchAnnotation();
            $imageAnnotator->close();

            $unsafeLevels = [3, 4, 5];
            if (in_array($safe->getAdult(), $unsafeLevels) || in_array($safe->getViolence(), $unsafeLevels)) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Erro técnico na API Vision: " . $e->getMessage());
            return true;
        }
    }
}