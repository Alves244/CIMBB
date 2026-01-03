<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Concelho;

class ConcelhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém a lista de concelhos do arquivo de configuração
        $concelhos = config('concelhos', []);

        // Insere ou atualiza cada concelho na base de dados
        foreach ($concelhos as $concelho) {
            Concelho::updateOrCreate(
                ['nome' => $concelho['nome']],
                ['codigo' => $concelho['codigo']]
            );
        }
    }
}
