<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SingleUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => '1@1.com'],
            [
                'name' => 'Admin Zenite',
                'password' => \Illuminate\Support\Facades\Hash::make('[SenhaNova2026]'),
                'access_level' => 1,
                'is_active' => true,
            ]
        );

        $this->command->info("Usuário {$user->email} verificado/criado!");
    }
}