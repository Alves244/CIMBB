<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Concelho;

class ConcelhoSeeder extends Seeder
{
    /**
     * Executa o preenchimento da tabela de concelhos.
     */
    public function run(): void
    {
        // 1. Vai buscar a lista de concelhos ao ficheiro de configuração (config/concelhos.php)
        // Se o ficheiro não existir ou estiver vazio, retorna um array vazio.
        $concelhos = config('concelhos', []);

        // 2. Itera sobre cada concelho da lista
        foreach ($concelhos as $concelho) {
            
            // 3. updateOrCreate: 
            // Procura um concelho pelo 'nome'. 
            // Se encontrar, atualiza o 'codigo'. 
            // Se não encontrar, cria um novo registo.
            Concelho::updateOrCreate(
                ['nome' => $concelho['nome']],
                ['codigo' => $concelho['codigo']]
            );
        }
    }
}