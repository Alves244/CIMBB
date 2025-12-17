<?php

namespace Database\Seeders;

use App\Models\Agrupamento;
use App\Models\Concelho;
use App\Models\EstabelecimentoEnsino;
use App\Models\InqueritoAgrupamento;
use App\Models\InqueritoAgrupamentoRegisto;
use App\Models\User;
use Illuminate\Database\Seeder;

class InqueritoAgrupamentoSeeder extends Seeder
{
    public function run(): void
    {
        $anoReferencia = (int) date('Y');
        $anoLetivo = $anoReferencia . '/' . ($anoReferencia + 1);

        $agrupamento = Agrupamento::where('codigo', 'AGR-TESTE')->first();

        if (! $agrupamento) {
            $concelho = Concelho::first();

            if (! $concelho) {
                $this->command?->warn('Nenhum concelho disponível para criar o inquérito do agrupamento.');
                return;
            }

            $agrupamento = Agrupamento::create([
                'nome' => 'Agrupamento Escola Teste',
                'codigo' => 'AGR-TESTE',
                'concelho_id' => $concelho->id,
            ]);
        }

        $utilizadorAgrupamento = User::where('email', 'escola@cimbb.pt')->first();

        if (! $utilizadorAgrupamento) {
            $this->command?->warn('O utilizador escola@cimbb.pt não existe. Corre o UserSeeder primeiro.');
            return;
        }

        if ($utilizadorAgrupamento->agrupamento_id !== $agrupamento->id) {
            $utilizadorAgrupamento->agrupamento_id = $agrupamento->id;
            $utilizadorAgrupamento->save();
        }

        $estabelecimentosSeed = [
            ['codigo' => 'ESC-BASE-001', 'nome' => 'Escola Básica da Cidade'],
            ['codigo' => 'ESC-SEC-002', 'nome' => 'Escola Secundária do Vale'],
            ['codigo' => 'ESC-PRO-003', 'nome' => 'Centro Profissional do Interior'],
        ];

        foreach ($estabelecimentosSeed as $seed) {
            EstabelecimentoEnsino::updateOrCreate(
                ['codigo' => $seed['codigo']],
                [
                    'nome' => $seed['nome'],
                    'concelho_id' => $agrupamento->concelho_id,
                    'agrupamento_id' => $agrupamento->id,
                ]
            );
        }

        $estabelecimentos = EstabelecimentoEnsino::where('agrupamento_id', $agrupamento->id)
            ->get()
            ->keyBy('codigo');

        if ($estabelecimentos->isEmpty()) {
            $this->command?->warn('Nenhum estabelecimento encontrado para o agrupamento.');
            return;
        }

        $registosSeed = [
            [
                'estabelecimento_codigo' => 'ESC-BASE-001',
                'nacionalidade' => 'Portuguesa',
                'nivel_ensino' => '1.º ciclo',
                'numero_alunos' => 320,
            ],
            [
                'estabelecimento_codigo' => 'ESC-SEC-002',
                'nacionalidade' => 'Portuguesa',
                'nivel_ensino' => 'Secundário',
                'numero_alunos' => 410,
            ],
            [
                'estabelecimento_codigo' => 'ESC-PRO-003',
                'nacionalidade' => 'Lusófona/CPLP',
                'nivel_ensino' => 'Profissional',
                'numero_alunos' => 180,
            ],
        ];

        $totalAlunos = 0;
        $registosPreparados = [];

        foreach ($registosSeed as $seed) {
            $estabelecimento = $estabelecimentos->get($seed['estabelecimento_codigo']);

            if (! $estabelecimento) {
                $this->command?->warn('Estabelecimento não encontrado: '.$seed['estabelecimento_codigo']);
                continue;
            }

            $registosPreparados[] = [
                'nacionalidade' => $seed['nacionalidade'],
                'ano_letivo' => $anoLetivo,
                'estabelecimento_id' => $estabelecimento->id,
                'concelho_id' => $estabelecimento->concelho_id,
                'nivel_ensino' => $seed['nivel_ensino'],
                'numero_alunos' => $seed['numero_alunos'],
            ];

            $totalAlunos += $seed['numero_alunos'];
        }

        if (empty($registosPreparados)) {
            $this->command?->warn('Não há registos válidos para criar o inquérito.');
            return;
        }

        $inquerito = InqueritoAgrupamento::updateOrCreate(
            [
                'agrupamento_id' => $agrupamento->id,
                'ano_referencia' => $anoReferencia,
            ],
            [
                'utilizador_id' => $utilizadorAgrupamento->id,
                'total_registos' => count($registosPreparados),
                'total_alunos' => $totalAlunos,
                'submetido_em' => now(),
            ]
        );

        $inquerito->registos()->delete();

        foreach ($registosPreparados as $registo) {
            InqueritoAgrupamentoRegisto::create(array_merge($registo, [
                'inquerito_id' => $inquerito->id,
            ]));
        }

        $this->command?->info('Inquérito de escolas para o agrupamento criado/atualizado.');
    }
}
