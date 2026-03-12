<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 150); // fornecedor
            $table->string('email')->unique(); 
            $table->string('cnpj', 18)->unique();
            $table->string('state_registration', 20); // ie
            $table->string('address', 150);
            $table->string('neighborhood', 100);            
            $table->string('city', 100);
            $table->string('state', 2);
            $table->string('zip_code', 10);
            $table->string('contact_name_1', 100);
            $table->string('phone_1', 20);
            $table->string('contact_name_2', 100)->nullable();
            $table->string('phone_2', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('suppliers'); }
};