<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar o utilizador Administrador
        User::create([
            'nome' => 'Admin',
            'email' => 'admin@cimbb.pt',
            'password' => Hash::make('12345678'),
            'perfil' => 'admin',
            'freguesia_id' => null,
            'telemovel' => null,
            'email_verified_at' => now(),
        ]);

        // Criar funcionário CIMBB - SEM FACTORY
        User::create([
            'nome' => 'Funcionario CIMBB',
            'email' => 'cimbb@cimbb.pt',
            'password' => Hash::make('12345678'),
            'perfil' => 'cimbb',
            'freguesia_id' => null,
            'telemovel' => null,
            'email_verified_at' => now(),
        ]);
    }
}