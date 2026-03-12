<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $blueprint) {
            $blueprint->id();
            // onDelete('cascade') garante que se o produto sumir, as fotos sumam do banco
            $blueprint->foreignId('product_id')->constrained()->onDelete('cascade');
            $blueprint->string('path'); // Caminho do arquivo no Storage
            $blueprint->integer('order')->default(0); // Para você ordenar as fotos se quiser
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};