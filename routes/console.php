<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

// Pode manter o inspire se quiser, ou apagar.
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- COMANDO DE LIMPEZA DE DEMONSTRAÇÃO ---
Schedule::call(function () {
    // 1. Limpa arquivos físicos (Para economizar espaço no seu servidor de testes)
    Storage::disk('public')->deleteDirectory('products');
    Storage::disk('public')->makeDirectory('products');

    // 2. Limpa as imagens do banco primeiro (por causa da constraint)
    DB::table('product_images')->truncate();

    // 3. Limpa o SEO dos produtos
    DB::table('seo_metadata')->where('seoable_type', \App\Models\Product::class)->delete();

    // 4. Limpa os Produtos
    DB::table('products')->delete();

    // 5. Limpa Fornecedores EXCETO os 5 primeiros (assumindo que são do seu seeder)
    DB::table('suppliers')->where('id', '>', 5)->delete();

    \Log::info('Sistema resetado: Apenas dados do Seeder preservados.');
})->everyFiveMinutes();