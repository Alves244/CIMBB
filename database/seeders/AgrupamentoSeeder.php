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
    /**
     * Conjuntos de Agrupamentos / Escolas por concelho.
     */
    private array $agrupamentos = [
        ['nome' => 'Agrupamento de Escolas José Silvestre Ribeiro', 'codigo' => 'AE-JSR', 'concelho' => 'Idanha-a-Nova'],
        ['nome' => 'Agrupamento de Escolas Ribeiro Sanches', 'codigo' => 'AE-RS', 'concelho' => 'Penamacor'],
        ['nome' => 'Agrupamento de Escolas Afonso de Paiva', 'codigo' => 'AE-AP', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Agrupamento de Escolas Amato Lusitano', 'codigo' => 'AE-AL', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Agrupamento de Escolas José Sanches e São Vicente da Beira', 'codigo' => 'AE-JSSVB', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Agrupamento de Escolas Nuno Álvares', 'codigo' => 'AE-NA', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Agrupamento de Escolas de Vila Velha de Ródão', 'codigo' => 'AE-VVR', 'concelho' => 'Vila Velha de Ródão'],
        ['nome' => 'Agrupamento de Escolas de Proença-a-Nova', 'codigo' => 'AE-PN', 'concelho' => 'Proença-a-Nova'],
        ['nome' => 'Agrupamento de Escolas Padre António de Andrade', 'codigo' => 'AE-PAA', 'concelho' => 'Oleiros'],
        ['nome' => 'Agrupamento de Escolas da Sertã', 'codigo' => 'AE-SERTA', 'concelho' => 'Sertã'],
        ['nome' => 'Agrupamento de Escolas de Vila de Rei', 'codigo' => 'AE-VR', 'concelho' => 'Vila de Rei'],
        ['nome' => 'Instituto Vaz Serra', 'codigo' => 'INST-VS', 'concelho' => 'Sertã'],
        ['nome' => 'Escola Profissional da Raia', 'codigo' => 'EP-RAIA', 'concelho' => 'Idanha-a-Nova'],
        ['nome' => 'Escola Profissional Agostinho Roseta', 'codigo' => 'EP-AR', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Escola Profissional do Conservatório de Castelo Branco', 'codigo' => 'EP-CCB', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Escola Tecnológica e Profissional Albicastrense', 'codigo' => 'ETP-ALBI', 'concelho' => 'Castelo Branco'],
        ['nome' => 'Escola Tecnológica e Profissional da Sertã', 'codigo' => 'ETP-SERTA', 'concelho' => 'Sertã'],
        ['nome' => 'IPCB – ESA', 'codigo' => 'IPCB-ESA', 'concelho' => 'Castelo Branco'],
        ['nome' => 'IPCB – ESALD', 'codigo' => 'IPCB-ESALD', 'concelho' => 'Castelo Branco'],
        ['nome' => 'IPCB – ESART', 'codigo' => 'IPCB-ESART', 'concelho' => 'Castelo Branco'],
        ['nome' => 'IPCB – ESE', 'codigo' => 'IPCB-ESE', 'concelho' => 'Castelo Branco'],
        ['nome' => 'IPCB – ESGIN', 'codigo' => 'IPCB-ESGIN', 'concelho' => 'Castelo Branco'],
        ['nome' => 'IPCB – EST', 'codigo' => 'IPCB-EST', 'concelho' => 'Castelo Branco'],
    ];

    public function run(): void
    {
        $concelhos = Concelho::pluck('id', 'nome');

        // Remove o agrupamento de testes legado e respetivas dependências, caso ainda exista.
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

        foreach ($this->agrupamentos as $item) {
            $concelhoName = $item['concelho'];
            $concelhoId = $concelhos[$concelhoName] ?? null;

            if (! $concelhoId) {
                $this->command?->warn("Concelho '{$concelhoName}' não encontrado para '{$item['nome']}'.");
                continue;
            }

            $codigo = $item['codigo'] ?? strtoupper(Str::slug($item['nome'], '-'));

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
