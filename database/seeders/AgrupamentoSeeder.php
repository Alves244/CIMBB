<?php

namespace Database\Seeders;

use App\Models\Agrupamento;
use App\Models\Concelho;
use App\Models\InqueritoAgrupamento;
use App\Models\InqueritoAgrupamentoRegisto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgrupamentoSeeder extends Seeder
{
    // 1. Lista mestra de instituições por concelho
    private array $agrupamentos = [
        ['nome' => 'Agrupamento de Escolas José Silvestre Ribeiro', 'codigo' => 'AE-JSR', 'concelho' => 'Idanha-a-Nova'],
        // ... (restantes instituições)
        ['nome' => 'IPCB – EST', 'codigo' => 'IPCB-EST', 'concelho' => 'Castelo Branco'],
    ];

    public function run(): void
    {
        // 2. Mapeamento de Concelhos (ID vs Nome) para evitar queries repetitivas no loop
        $concelhos = Concelho::pluck('id', 'nome');

        // 3. LIMPEZA DE DADOS DE TESTE
        // Se existir um agrupamento antigo de testes, remove-o e limpa as dependências
        $agrupamentoTeste = Agrupamento::where('codigo', 'AGR-TESTE')->first();
        if ($agrupamentoTeste) {
            $inqueritoIds = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoTeste->id)->pluck('id');
            if ($inqueritoIds->isNotEmpty()) {
                InqueritoAgrupamentoRegisto::whereIn('inquerito_id', $inqueritoIds)->delete();
                InqueritoAgrupamento::whereIn('id', $inqueritoIds)->delete();
            }
            User::where('agrupamento_id', $agrupamentoTeste->id)->update(['agrupamento_id' => null]);
            $agrupamentoTeste->delete();
        }

        // 4. SINCRONIZAÇÃO DA LISTA REAL
        foreach ($this->agrupamentos as $item) {
            $concelhoId = $concelhos[$item['concelho']] ?? null;

            if (! $concelhoId) {
                $this->command?->warn("Concelho '{$item['concelho']}' não encontrado.");
                continue;
            }

            // Garante um código único: usa o definido ou gera um slug a partir do nome
            $codigo = $item['codigo'] ?? strtoupper(Str::slug($item['nome'], '-'));

            // updateOrCreate: Se o nome já existir, atualiza o código e concelho; 
            // caso contrário, cria um registo novo.
            Agrupamento::updateOrCreate(
                ['nome' => $item['nome']],
                [
                    'codigo' => $codigo,
                    'concelho_id' => $concelhoId,
                ]
            );
        }

        $this->command?->info('Agrupamentos, escolas profissionais e IPCB sincronizados.');
    }
}