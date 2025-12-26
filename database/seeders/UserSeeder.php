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
        // --- 1. Utilizador Administrador ---
        // Perfil total: gestão de utilizadores e configurações globais.
        User::firstOrCreate(
            ['email' => 'admin@cimbb.pt'],
            [
                'nome' => 'Admin',
                'password' => Hash::make('12345678'),
                'perfil' => 'admin',
                'freguesia_id' => null,
                'telemovel' => null,
                'email_verified_at' => now(),
            ]
        );

        // --- 2. Utilizador Funcionário CIMBB ---
        // Perfil de análise: visualização de dados e dashboards de todos os concelhos.
        User::firstOrCreate(
            ['email' => 'cimbb@cimbb.pt'],
            [
                'nome' => 'Funcionario CIMBB',
                'password' => Hash::make('12345678'),
                'perfil' => 'cimbb',
                'freguesia_id' => null,
                'telemovel' => null,
                'email_verified_at' => now(),
            ]
        );

        // --- 3. Utilizador Freguesia ---
        // Perfil operacional: regista famílias e preenche inquéritos locais.
        $primeiraFreguesia = Freguesia::first(); 

        if ($primeiraFreguesia) {
            User::firstOrCreate(
                ['email' => 'freguesia_1@cimbb.pt'],
                [
                    'nome' => 'Funcionário Teste Freguesia X',
                    'password' => Hash::make('12345678'),
                    'perfil' => 'freguesia',
                    'freguesia_id' => $primeiraFreguesia->id, // Vincula à entidade local
                    'telemovel' => null,
                    'email_verified_at' => now(),
                ]
            );
        } else {
            // Aviso caso os seeders geográficos não tenham corrido antes deste.
            $this->command?->warn('Nenhuma freguesia encontrada. O utilizador de freguesia de teste não foi criado.');
        }

        // --- 4. Utilizador Agrupamento (Escolas) ---
        // Perfil escolar: preenche o número de alunos imigrantes e nacionalidades.
        $agrupamentoPadrao = Agrupamento::where('codigo', 'AE-JSR')->first() 
            ?? Agrupamento::first();

        if ($agrupamentoPadrao) {
            User::updateOrCreate(
                ['email' => 'escola@cimbb.pt'],
                [
                    'nome' => 'Coordenador Agrupamento Teste',
                    'password' => Hash::make('12345678'),
                    'perfil' => 'agrupamento',
                    'agrupamento_id' => $agrupamentoPadrao->id, // Vincula à entidade escolar
                    'freguesia_id' => null,
                    'telemovel' => null,
                    'email_verified_at' => now(),
                ]
            );
        } else {
            $this->command?->warn('Nenhum agrupamento encontrado. O utilizador de agrupamento não foi criado.');
        }
    }
}