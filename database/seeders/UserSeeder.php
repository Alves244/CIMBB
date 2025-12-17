<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Freguesia;
use App\Models\Agrupamento;
use App\Models\Concelho;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Utilizador Admin ---
        // Procura pelo email; se não existir, cria.
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
        // Busca o ID da primeira freguesia encontrada na BD
        $primeiraFreguesia = Freguesia::first(); 

        if ($primeiraFreguesia) { // Só cria se encontrar uma freguesia
            User::firstOrCreate(
                ['email' => 'freguesia_1@cimbb.pt'], // 1. Procura por este email
                [ // 2. Se não encontrar, cria com estes dados
                    'nome' => 'Funcionário Teste Freguesia X',
                    'password' => Hash::make('12345678'),
                    'perfil' => 'freguesia',
                    'freguesia_id' => $primeiraFreguesia->id, // Associa o ID encontrado
                    'telemovel' => null,
                    'email_verified_at' => now(),
                ]
            );
        } else {
            $this->command?->warn('Nenhuma freguesia encontrada. O utilizador de freguesia de teste não foi criado.');
        }

        // --- Utilizador Agrupamento ---
        $primeiroConcelho = Concelho::first();

        if ($primeiroConcelho) {
            $agrupamentoPadrao = Agrupamento::firstOrCreate(
                ['codigo' => 'AGR-TESTE'],
                [
                    'nome' => 'Agrupamento Escola Teste',
                    'concelho_id' => $primeiroConcelho->id,
                ]
            );

            User::firstOrCreate(
                ['email' => 'escola@cimbb.pt'],
                [
                    'nome' => 'Coordenador Agrupamento Teste',
                    'password' => Hash::make('12345678'),
                    'perfil' => 'agrupamento',
                    'agrupamento_id' => $agrupamentoPadrao->id,
                    'freguesia_id' => null,
                    'telemovel' => null,
                    'email_verified_at' => now(),
                ]
            );
        } else {
            $this->command?->warn('Nenhum concelho encontrado. O utilizador de agrupamento não foi criado.');
        }
    }
}