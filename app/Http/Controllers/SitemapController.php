<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        // Cria o objeto do Sitemap
        $sitemap = Sitemap::create();

        // Adiciona a página inicial
        $sitemap->add(Url::create('/')->setPriority(1.0));

        // Adiciona todos os produtos ativos do seu banco
        $products = Product::where('is_active', true)->get();

        foreach ($products as $product) {
            $sitemap->add(
                Url::create("/products/{$product->slug}")
                    ->setLastModificationDate($product->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        }

        // Retorna o XML direto para o navegador
        return $sitemap->toResponse(request());
    }
}