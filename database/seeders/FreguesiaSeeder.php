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
        // 1. Obtém a estrutura completa definida em config/concelhos.php
        // Espera-se que cada concelho no array tenha uma chave ['freguesias']
        $concelhosConfig = config('concelhos', []);

        foreach ($concelhosConfig as $concelho) {
            
            // 2. Procura o ID do concelho na base de dados pelo código ou nome
            // É essencial que o ConcelhoSeeder tenha corrido primeiro
            $concelhoModel = Concelho::where('codigo', $concelho['codigo'])
                ->orWhere('nome', $concelho['nome'])
                ->first();

            // 3. Caso o concelho não exista na BD, lança um aviso e salta para o próximo
            if (!$concelhoModel) {
                $this->command?->warn('Concelho não encontrado no seeder: ' . $concelho['nome']);
                continue;
            }

            // 4. Itera sobre a lista de freguesias deste concelho específico
            foreach ($concelho['freguesias'] ?? [] as $freguesia) {
                
                // 5. updateOrCreate: 
                // Procura pelo 'codigo' único da freguesia (DICRE).
                // Se encontrar, atualiza o nome e o vínculo ao concelho.
                // Se não encontrar, cria uma nova.
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