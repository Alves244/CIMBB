<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importar DB facade
use App\Models\Conselho; // Importar o Model

class ConselhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $concelhos = [
            ['nome' => 'Penamacor', 'codigo' => null],
            ['nome' => 'Oleiros', 'codigo' => null],
            ['nome' => 'Sertã', 'codigo' => null],
            ['nome' => 'Vila de Rei', 'codigo' => null],
            ['nome' => 'Proença-a-Nova', 'codigo' => null],
            ['nome' => 'Vila Velha de Ródão', 'codigo' => null],
            ['nome' => 'Castelo Branco', 'codigo' => null],
            ['nome' => 'Idanha-a-Nova', 'codigo' => null],
        ];

        // ***** ALTERAÇÃO AQUI *****
        // Usar firstOrCreate() em vez de create()
        foreach ($concelhos as $concelho) {
            Conselho::firstOrCreate(
                ['nome' => $concelho['nome']], // 1. Procura por um concelho com este nome
                [ // 2. Se não encontrar, cria-o com estes dados
                    'codigo' => $concelho['codigo']
                ]
            );
        }
    }
}