<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Concelho;
use App\Models\Freguesia;

class FreguesiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtém a lista de concelhos do arquivo de configuração
        $concelhos = config('concelhos', []);

        // Insere ou atualiza cada freguesia na base de dados
        foreach ($concelhos as $concelho) {
            $concelhoModel = Concelho::where('codigo', $concelho['codigo'])
                ->orWhere('nome', $concelho['nome'])
                ->first();

            if (!$concelhoModel) {
                $this->command?->warn('Concelho não encontrado no seeder: ' . $concelho['nome']);
                continue;
            }
            // Insere ou atualiza as freguesias associadas ao concelho
            foreach ($concelho['freguesias'] ?? [] as $freguesia) {
                Freguesia::updateOrCreate(
                    ['codigo' => $freguesia['codigo']],
                    [
                        'nome' => $freguesia['nome'],
                        'concelho_id' => $concelhoModel->id,
                    ]
                );
            }
        }
    }
}