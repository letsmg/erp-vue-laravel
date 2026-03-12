<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('description', 150);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('size', 20)->nullable();
            $table->string('collection', 100)->nullable();
            $table->string('gender', 30)->nullable();                        
            
            // Preços e Promoção
            $table->decimal('cost_price', 12, 2);
            $table->decimal('sale_price', 12, 2);
            $table->decimal('promo_price', 12, 2)->nullable();
            $table->dateTime('promo_start_at')->nullable();
            $table->dateTime('promo_end_at')->nullable();

            $table->string('barcode', 50)->nullable()->unique();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->string('slug')->unique();
            $table->json('images')->nullable(); 
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};