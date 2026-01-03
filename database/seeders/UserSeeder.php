<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Freguesia;
use App\Models\Agrupamento;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Utilizador Admin ---
        User::firstOrCreate(
            ['email' => 'admin@cimbb.pt'], // 1. Procura por este email
            [ // 2. Se não encontrar, cria com estes dados
                'nome' => 'Admin',
                'password' => Hash::make('12345678'),
                'perfil' => 'admin',
                'freguesia_id' => null,
                'telemovel' => null,
                'email_verified_at' => now(),
            ]
        );

        // --- Utilizador Funcionário CIMBB ---
        User::firstOrCreate(
            ['email' => 'cimbb@cimbb.pt'], // 1. Procura por este email
            [ // 2. Se não encontrar, cria com estes dados
                'nome' => 'Funcionario CIMBB',
                'password' => Hash::make('12345678'),
                'perfil' => 'cimbb',
                'freguesia_id' => null,
                'telemovel' => null,
                'email_verified_at' => now(),
            ]
        );

        // --- Utilizador Freguesia ---
        $primeiraFreguesia = Freguesia::first(); 
        if ($primeiraFreguesia) { // Só cria se encontrar uma freguesia
            User::firstOrCreate(
                ['email' => 'freguesia_1@cimbb.pt'], // 1. Procura por este email
                [ // 2. Se não encontrar, cria com estes dados
                    'nome' => 'Funcionário Teste Freguesia X',
                    'password' => Hash::make('12345678'),
                    'perfil' => 'freguesia',
                    'freguesia_id' => $primeiraFreguesia->id,
                    'telemovel' => null,
                    'email_verified_at' => now(),
                ]
            );
        } else {
            $this->command?->warn('Nenhuma freguesia encontrada. O utilizador de freguesia de teste não foi criado.');
        }

        // --- Utilizador Agrupamento ---
        $agrupamentoPadrao = Agrupamento::where('codigo', 'AE-JSR')->first()
            ?? Agrupamento::first();
        if ($agrupamentoPadrao) { // Só cria se encontrar um agrupamento
            User::updateOrCreate(
                ['email' => 'escola@cimbb.pt'],// 1. Procura por este email
                [// 2. Se não encontrar, cria com estes dados
                    'nome' => 'Coordenador Agrupamento Teste',
                    'password' => Hash::make('12345678'),
                    'perfil' => 'agrupamento',
                    'agrupamento_id' => $agrupamentoPadrao->id,
                    'freguesia_id' => null,
                    'telemovel' => null,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}