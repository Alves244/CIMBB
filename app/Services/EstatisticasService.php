<?php

namespace App\Services;

use App\Models\AgregadoFamiliar;
use App\Models\AtividadeEconomica;
use App\Models\Familia;
use App\Models\Freguesia;
use App\Models\InqueritoFreguesia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EstatisticasService
{
    public function gerar(array $inputs, ?int $listLimit = 40): array
    {
        $filters = $this->sanitizeFilters($inputs);
        $inqueritoContext = $this->obterContextoInquerito($filters);
        $filters['freguesias_submetidas'] = $inqueritoContext['submetidas'];

        $familiaQuery = Familia::query()->with(['freguesia.conselho']);
        $this->aplicarFiltrosFamilia($familiaQuery, $filters);

        $totais = $this->resumoTotais($familiaQuery, $filters);
        $distribuicoes = $this->distribuicoes($familiaQuery, $filters);
        $freguesias = $this->resumoFreguesias($familiaQuery, $filters, $inqueritoContext);
        $listaFamilias = $this->listarFamilias($familiaQuery, $filters, $listLimit);

        return [
            'totais' => $totais,
            'distribuicoes' => $distribuicoes,
            'freguesias' => $freguesias,
            'listaFamilias' => $listaFamilias,
            'filtros' => $filters,
        ];
    }

    public function exportarPdf(array $inputs)
    {
        $resultado = $this->gerar($inputs, null);

        $pdf = Pdf::loadView('funcionario.relatorios.pdf', [
            'geradoEm' => now(),
            'ano' => $resultado['filtros']['ano'],
            'totais' => $resultado['totais'],
            'distribuicoes' => $resultado['distribuicoes'],
            'filtros' => $resultado['filtros'],
            'listaFamilias' => $resultado['listaFamilias'],
            'freguesiasResumo' => $resultado['freguesias'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download(sprintf('estatisticas_%s.pdf', now()->format('Ymd_His')));
    }

    private function sanitizeFilters(array $filters): array
    {
        $anoAtual = (int) date('Y');

        return [
            'ano' => isset($filters['ano']) ? (int) $filters['ano'] : $anoAtual,
            'concelho_id' => $this->toIntOrNull($filters['concelho_id'] ?? null),
            'freguesia_id' => $this->toIntOrNull($filters['freguesia_id'] ?? null),
            'setor_id' => $this->toIntOrNull($filters['setor_id'] ?? null),
            'nacionalidade' => $this->emptyToNull($filters['nacionalidade'] ?? null),
            'tipologia_habitacao' => $this->emptyToNull($filters['tipologia_habitacao'] ?? null),
            'tipologia_propriedade' => $this->emptyToNull($filters['tipologia_propriedade'] ?? null),
            'genero' => $this->emptyToNull($filters['genero'] ?? null),
            'faixa_etaria' => $this->emptyToNull($filters['faixa_etaria'] ?? null),
            'situacao_inquerito' => $this->emptyToNull($filters['situacao_inquerito'] ?? null),
            'periodo_inicio' => $this->toCarbonOrNull($filters['periodo_inicio'] ?? null, true),
            'periodo_fim' => $this->toCarbonOrNull($filters['periodo_fim'] ?? null, false),
        ];
    }

    private function toIntOrNull($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function emptyToNull($value)
    {
        return ($value === null || $value === '' || $value === 'all') ? null : $value;
    }

    private function toCarbonOrNull($value, bool $startOfDay): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            $date = Carbon::parse($value);
            return $startOfDay ? $date->startOfDay() : $date->endOfDay();
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function obterContextoInquerito(array $filters): array
    {
        $query = InqueritoFreguesia::query()->where('ano', $filters['ano']);

        if ($filters['periodo_inicio']) {
            $query->where('updated_at', '>=', $filters['periodo_inicio']);
        }

        if ($filters['periodo_fim']) {
            $query->where('updated_at', '<=', $filters['periodo_fim']);
        }

        $submetidas = $query->pluck('freguesia_id')->unique();

        return [
            'ano' => $filters['ano'],
            'submetidas' => $submetidas,
        ];
    }

    private function aplicarFiltrosFamilia(Builder $query, array $filters): void
    {
        if ($filters['freguesia_id']) {
            $query->where('freguesia_id', $filters['freguesia_id']);
        }

        if ($filters['concelho_id']) {
            $query->whereHas('freguesia', function (Builder $sub) use ($filters) {
                $sub->where('conselho_id', $filters['concelho_id']);
            });
        }

        if ($filters['nacionalidade']) {
            $query->where('nacionalidade', $filters['nacionalidade']);
        }

        if ($filters['tipologia_habitacao']) {
            $query->where('tipologia_habitacao', $filters['tipologia_habitacao']);
        }

        if ($filters['tipologia_propriedade']) {
            $query->where('tipologia_propriedade', $filters['tipologia_propriedade']);
        }

        if ($filters['setor_id']) {
            $query->whereHas('atividadesEconomicas', function (Builder $sub) use ($filters) {
                $sub->where('setor_id', $filters['setor_id']);
            });
        }

        if ($filters['genero']) {
            $this->aplicarFiltroGenero($query, $filters['genero']);
        }

        if ($filters['faixa_etaria']) {
            $this->aplicarFiltroFaixaEtaria($query, $filters['faixa_etaria']);
        }

        if ($filters['situacao_inquerito']) {
            $submetidas = $filters['freguesias_submetidas'] ?? collect();

            if ($filters['situacao_inquerito'] === 'submetido') {
                $query->whereIn('freguesia_id', $submetidas);
            } elseif ($filters['situacao_inquerito'] === 'pendente') {
                $query->whereNotIn('freguesia_id', $submetidas);
            }
        }

        if ($filters['periodo_inicio']) {
            $query->where('created_at', '>=', $filters['periodo_inicio']);
        }

        if ($filters['periodo_fim']) {
            $query->where('created_at', '<=', $filters['periodo_fim']);
        }
    }

    private function aplicarFiltroGenero(Builder $query, string $genero): void
    {
        $mapaColunas = [
            'masculino' => ['adultos_laboral_m', 'adultos_65_mais_m', 'criancas_m'],
            'feminino' => ['adultos_laboral_f', 'adultos_65_mais_f', 'criancas_f'],
            'nao_declarado' => ['adultos_laboral_n', 'adultos_65_mais_n', 'criancas_n'],
        ];

        if (!isset($mapaColunas[$genero])) {
            return;
        }

        $query->whereHas('agregadoFamiliar', function (Builder $agregado) use ($mapaColunas, $genero) {
            $agregado->where(function (Builder $sub) use ($mapaColunas, $genero) {
                foreach ($mapaColunas[$genero] as $coluna) {
                    $sub->orWhere($coluna, '>', 0);
                }
            });
        });
    }

    private function aplicarFiltroFaixaEtaria(Builder $query, string $faixa): void
    {
        $mapaColunas = [
            'criancas' => ['criancas_m', 'criancas_f', 'criancas_n'],
            'adultos_laboral' => ['adultos_laboral_m', 'adultos_laboral_f', 'adultos_laboral_n'],
            'adultos_65' => ['adultos_65_mais_m', 'adultos_65_mais_f', 'adultos_65_mais_n'],
        ];

        if (!isset($mapaColunas[$faixa])) {
            return;
        }

        $query->whereHas('agregadoFamiliar', function (Builder $agregado) use ($mapaColunas, $faixa) {
            $agregado->where(function (Builder $sub) use ($mapaColunas, $faixa) {
                foreach ($mapaColunas[$faixa] as $coluna) {
                    $sub->orWhere($coluna, '>', 0);
                }
            });
        });
    }

    private function resumoTotais(Builder $familiaQuery, array $filters): array
    {
        $totalFamilias = (clone $familiaQuery)->count();
        $totalNacionalidades = (clone $familiaQuery)->distinct('nacionalidade')->count('nacionalidade');

        $agregado = AgregadoFamiliar::query()
            ->selectRaw('
                coalesce(sum(adultos_laboral_m + adultos_laboral_f + adultos_laboral_n + adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n + criancas_m + criancas_f + criancas_n), 0) as total_membros,
                coalesce(sum(adultos_laboral_m + adultos_laboral_f + adultos_laboral_n), 0) as adultos_laboral,
                coalesce(sum(adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n), 0) as adultos_65,
                coalesce(sum(criancas_m + criancas_f + criancas_n), 0) as criancas,
                coalesce(sum(adultos_laboral_m + adultos_65_mais_m + criancas_m), 0) as total_m,
                coalesce(sum(adultos_laboral_f + adultos_65_mais_f + criancas_f), 0) as total_f,
                coalesce(sum(adultos_laboral_n + adultos_65_mais_n + criancas_n), 0) as total_n
            ')
            ->whereHas('familia', function (Builder $familia) use ($filters) {
                $this->aplicarFiltrosFamilia($familia, $filters);
            })
            ->first();

        return [
            'totalFamilias' => $totalFamilias,
            'totalMembros' => (int) ($agregado->total_membros ?? 0),
            'adultosLaboral' => (int) ($agregado->adultos_laboral ?? 0),
            'adultosSenior' => (int) ($agregado->adultos_65 ?? 0),
            'criancas' => (int) ($agregado->criancas ?? 0),
            'totalMasculino' => (int) ($agregado->total_m ?? 0),
            'totalFeminino' => (int) ($agregado->total_f ?? 0),
            'totalNaoDeclarado' => (int) ($agregado->total_n ?? 0),
            'totalNacionalidades' => $totalNacionalidades,
        ];
    }

    private function distribuicoes(Builder $familiaQuery, array $filters): array
    {
        $familiaBase = clone $familiaQuery;

        $porHabitacao = (clone $familiaBase)
            ->select('tipologia_habitacao', DB::raw('count(*) as total'))
            ->groupBy('tipologia_habitacao')
            ->pluck('total', 'tipologia_habitacao');

        $porPropriedade = (clone $familiaBase)
            ->select('tipologia_propriedade', DB::raw('count(*) as total'))
            ->groupBy('tipologia_propriedade')
            ->pluck('total', 'tipologia_propriedade');

        $porNacionalidade = (clone $familiaBase)
            ->select('nacionalidade', DB::raw('count(*) as total'))
            ->groupBy('nacionalidade')
            ->orderByDesc('total')
            ->get();

        $porSetor = AtividadeEconomica::query()
            ->select('setor_atividades.nome', DB::raw('count(atividade_economicas.id) as total'))
            ->join('setor_atividades', 'atividade_economicas.setor_id', '=', 'setor_atividades.id')
            ->whereHas('familia', function (Builder $familia) use ($filters) {
                $this->aplicarFiltrosFamilia($familia, $filters);
            })
            ->groupBy('setor_atividades.nome')
            ->orderByDesc('total')
            ->get();

        $agregado = AgregadoFamiliar::query()
            ->selectRaw('
                coalesce(sum(adultos_laboral_m + adultos_65_mais_m + criancas_m), 0) as masculino,
                coalesce(sum(adultos_laboral_f + adultos_65_mais_f + criancas_f), 0) as feminino,
                coalesce(sum(adultos_laboral_n + adultos_65_mais_n + criancas_n), 0) as nao_declarado,
                coalesce(sum(criancas_m + criancas_f + criancas_n), 0) as faixa_criancas,
                coalesce(sum(adultos_laboral_m + adultos_laboral_f + adultos_laboral_n), 0) as faixa_laboral,
                coalesce(sum(adultos_65_mais_m + adultos_65_mais_f + adultos_65_mais_n), 0) as faixa_65
            ')
            ->whereHas('familia', function (Builder $familia) use ($filters) {
                $this->aplicarFiltrosFamilia($familia, $filters);
            })
            ->first();

        return [
            'habitacao' => $porHabitacao,
            'propriedade' => $porPropriedade,
            'nacionalidades' => $porNacionalidade,
            'setores' => $porSetor,
            'genero' => [
                'masculino' => (int) ($agregado->masculino ?? 0),
                'feminino' => (int) ($agregado->feminino ?? 0),
                'nao_declarado' => (int) ($agregado->nao_declarado ?? 0),
            ],
            'faixa_etaria' => [
                'criancas' => (int) ($agregado->faixa_criancas ?? 0),
                'adultos_laboral' => (int) ($agregado->faixa_laboral ?? 0),
                'adultos_65' => (int) ($agregado->faixa_65 ?? 0),
            ],
        ];
    }

    private function resumoFreguesias(Builder $familiaQuery, array $filters, array $inqueritoContext): array
    {
        $freguesiaIds = (clone $familiaQuery)->pluck('freguesia_id')->unique();
        if ($filters['freguesia_id']) {
            $freguesiaIds = collect([$filters['freguesia_id']]);
        }

        $submetidas = $inqueritoContext['submetidas']->intersect($freguesiaIds);
        $pendentesIds = $freguesiaIds->diff($submetidas);

        $pendentesInfo = $pendentesIds->isEmpty()
            ? collect()
            : Freguesia::query()->whereIn('id', $pendentesIds)->orderBy('nome')->get(['id', 'nome', 'codigo']);

        return [
            'totalConsideradas' => $freguesiaIds->count(),
            'comInquerito' => $submetidas->count(),
            'pendentes' => $pendentesInfo,
            'ano' => $inqueritoContext['ano'],
        ];
    }

    private function listarFamilias(Builder $familiaQuery, array $filters, ?int $limit): array
    {
        $listaQuery = (clone $familiaQuery)
            ->with(['freguesia.conselho', 'agregadoFamiliar'])
            ->orderByDesc('created_at');

        if ($limit) {
            $listaQuery->limit($limit);
        }

        $submetidas = ($filters['freguesias_submetidas'] ?? collect())->toArray();

        return $listaQuery->get()->map(function (Familia $familia) use ($submetidas) {
            $agregado = $familia->agregadoFamiliar;
            $totalMembros = ($agregado->adultos_laboral_m ?? 0)
                + ($agregado->adultos_laboral_f ?? 0)
                + ($agregado->adultos_laboral_n ?? 0)
                + ($agregado->adultos_65_mais_m ?? 0)
                + ($agregado->adultos_65_mais_f ?? 0)
                + ($agregado->adultos_65_mais_n ?? 0)
                + ($agregado->criancas_m ?? 0)
                + ($agregado->criancas_f ?? 0)
                + ($agregado->criancas_n ?? 0);

            return [
                'codigo' => $familia->codigo,
                'concelho' => $familia->freguesia->conselho->nome ?? '—',
                'freguesia' => $familia->freguesia->nome ?? '—',
                'nacionalidade' => $familia->nacionalidade,
                'tipologia_habitacao' => $familia->tipologia_habitacao,
                'tipologia_propriedade' => $familia->tipologia_propriedade,
                'total_membros' => (int) $totalMembros,
                'situacao_inquerito' => in_array($familia->freguesia_id, $submetidas) ? 'Submetido' : 'Pendente',
            ];
        })->toArray();
    }
}
