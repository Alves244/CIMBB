<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conselho; // Importar o Model

class ConselhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $concelhos = config('concelhos', []);

        foreach ($concelhos as $concelho) {
            // Ensure every concelho carries its SIGO code pulled from config
            Conselho::updateOrCreate(
                ['nome' => $concelho['nome']],
                ['codigo' => $concelho['codigo']]
            );
        }
    }
}