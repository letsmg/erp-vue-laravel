<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'company_name' => 'Nike Brasil Distribuidora',
                'email' => 'comercial@nike.com.br',
                'cnpj' => '00.000.000/0001-91',
                'state_registration' => 'ISENTO',
                'address' => 'Rua das Marcas, 1000', // Adicionado
                'neighborhood' => 'Distrito Industrial', // Adicionado
                'city' => 'São Paulo', // Adicionado
                'state' => 'SP', // Adicionado
                'zip_code' => '01001-000', // Adicionado
                'contact_name_1' => 'João Silva',
                'phone_1' => '(11) 99999-9999', // Adicionado
                'is_active' => true,
            ],
            [
                'company_name' => 'Adidas do Brasil Ltda',
                'email' => 'vendas@adidas.com.br',
                'cnpj' => '00.000.000/0001-00',
                'state_registration' => '123456789',
                'address' => 'Av. Esportiva, 500',
                'neighborhood' => 'Centro',
                'city' => 'Barueri',
                'state' => 'SP',
                'zip_code' => '06401-000',
                'contact_name_1' => 'João Silva',
                'phone_1' => '(11) 88888-8888',
                'is_active' => true,
            ],
        ];

        foreach ($data as $supplier) {
            \App\Models\Supplier::updateOrCreate(
                ['email' => $supplier['email']], 
                $supplier
            );
        }
    }
}