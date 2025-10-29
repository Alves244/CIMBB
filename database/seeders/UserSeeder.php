<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Freguesia;

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

        // Criar funcionário CIMBB
        User::create([
            'nome' => 'Funcionario CIMBB',
            'email' => 'cimbb@cimbb.pt',
            'password' => Hash::make('12345678'),
            'perfil' => 'cimbb',
            'freguesia_id' => null,
            'telemovel' => null,
            'email_verified_at' => now(),
        ]);

        // Criar utilizador de Freguesia (exemplo com freguesia_id = 1)// --- Adicionar Utilizador Freguesia ---
        // Busca o ID da primeira freguesia encontrada na BD
        $primeiraFreguesiaId = Freguesia::first()?->id; // Usar '?->id' evita erro se não houver freguesias
        if ($primeiraFreguesiaId) { // Só cria se encontrar uma freguesia
            User::create([
                'nome' => 'Funcionário Teste Freguesia X', // Nome de exemplo
                'email' => 'freguesia_1@cimbb.pt',       // Email de exemplo
                'password' => Hash::make('12345678'),    // !! Lembra-te de mudar !!
                'perfil' => 'freguesia',
                'freguesia_id' => $primeiraFreguesiaId, // Associa o ID encontrado
                'telemovel' => null,
                'email_verified_at' => now(),
            ]);
        } else {
            // Avisa no terminal se não houver freguesias na base de dados
             $this->command->warn("Nenhuma freguesia encontrada. O utilizador de freguesia de teste não foi criado.");
        }
        // --- Fim da adição ---
    }
}