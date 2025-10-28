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

        // Usar o Model para criar os registos (melhor prática)
        foreach ($concelhos as $concelho) {
            Conselho::create($concelho);
        }
        // Ou usar o DB facade (mais simples para inserts diretos)
        // DB::table('conselhos')->insert($concelhos);
    }
}