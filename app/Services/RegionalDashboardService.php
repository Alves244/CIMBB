<?php

namespace App\Services;

use App\Models\AgregadoFamiliar;
use App\Models\Agrupamento;
use App\Models\Concelho;
use App\Models\Familia;
use App\Models\InqueritoAgrupamento;
use App\Models\InqueritoFreguesia;
use App\Models\InqueritoPeriodo;
use App\Models\TicketSuporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegionalDashboardService
{
    public function buildDashboardPayload(User $user, ?int $ano = null): array
    {
        $familiaQuery = Familia::query();
        $agregadoQuery = AgregadoFamiliar::query();

        $tituloDashboard = 'Visão Territorial (Todo o Território)';
        $nomeLocalidade = 'Beira Baixa (Todos os Concelhos)';

        $tipoPeriodo = $user->isAgrupamento()
            ? InqueritoPeriodo::TIPO_AGRUPAMENTO
            : InqueritoPeriodo::TIPO_FREGUESIA;

        [$anoInquerito, $periodoSelecionado, $anosDisponiveis] = $this->resolverPeriodo($ano, $tipoPeriodo);

        $ticketsRespondidos = 0;
        $jaPreencheuInquerito = false;
        $inqueritoDisponivel = false;
        $inqueritoPrazo = $periodoSelecionado?->fecha_em ?? Carbon::create($anoInquerito, 12, 31, 23, 59, 59);

        $concelhosResumo = collect();
        $dashboardProgress = [
            'totalConcelhos' => 0,
            'concelhosComInquerito' => 0,
            'percentual' => 0,
        ];
        $regionalHighlights = [
            'totalPendentes' => 0,
            'concelhosComPendencias' => 0,
            'concelhosConcluidos' => 0,
            'familiasMonitorizadas' => 0,
            'ticketsPendentes' => 0,
        ];
        $agrupamentoResumo = [
            'totalSubmissoes' => 0,
            'ultimoAno' => null,
            'ultimoTotalAlunos' => 0,
        ];
        $escolasResumo = [
            'anoReferencia' => null,
            'totalInqueritos' => 0,
            'agrupamentosComDados' => 0,
            'totalAlunos' => 0,
        ];
        $escolasPendentesLista = collect();
        $escolasHighlights = [
            'totalAgrupamentos' => 0,
            'agrupamentosComDados' => 0,
            'agrupamentosPendentes' => 0,
            'totalAlunos' => 0,
            'mediaAlunos' => 0,
            'totalInqueritos' => 0,
            'anoReferencia' => null,
        ];

        if ($user->isFreguesia()) {
            $freguesiaId = $user->freguesia_id;

            $familiaQuery->where('freguesia_id', $freguesiaId);
            $agregadoQuery->whereHas('familia', function ($q) use ($freguesiaId) {
                $q->where('freguesia_id', $freguesiaId);
            });

            $tituloDashboard = 'Dashboard da Freguesia';
            $nomeLocalidade = $user->freguesia->nome ?? 'N/A';

            $ticketsRespondidos = TicketSuporte::where('utilizador_id', $user->id)
                ->where('estado', 'respondido')
                ->count();

            $jaPreencheuInquerito = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                ->where('ano', $anoInquerito)
                ->exists();

            $inqueritoDisponivel = !$jaPreencheuInquerito && ($periodoSelecionado?->estaAberto() ?? false);
        } elseif ($user->isAgrupamento()) {
            $agrupamentoId = $user->agrupamento_id;

            $familiaQuery->whereRaw('1 = 0');
            $agregadoQuery->whereRaw('1 = 0');

            $tituloDashboard = 'Dashboard do Agrupamento';
            $nomeLocalidade = $user->agrupamento->nome ?? 'N/A';

            $ticketsRespondidos = TicketSuporte::where('utilizador_id', $user->id)
                ->where('estado', 'respondido')
                ->count();

            if ($agrupamentoId) {
                $inqueritosAgrupamento = InqueritoAgrupamento::where('agrupamento_id', $agrupamentoId);

                $agrupamentoResumo['totalSubmissoes'] = (clone $inqueritosAgrupamento)->count();
                $ultimoInquerito = (clone $inqueritosAgrupamento)->orderByDesc('ano_referencia')->first();

                if ($ultimoInquerito) {
                    $agrupamentoResumo['ultimoAno'] = $ultimoInquerito->ano_referencia;
                    $agrupamentoResumo['ultimoTotalAlunos'] = $ultimoInquerito->total_alunos;
                }

                $jaPreencheuInquerito = (clone $inqueritosAgrupamento)
                    ->where('ano_referencia', $anoInquerito)
                    ->exists();

                $inqueritoDisponivel = !$jaPreencheuInquerito && ($periodoSelecionado?->estaAberto() ?? false);
            } else {
                $jaPreencheuInquerito = false;
                $inqueritoDisponivel = false;
            }
        } else {
            $overview = $this->getRegionalOverview($anoInquerito);
            $concelhosResumo = $overview['concelhosResumo'];
            $dashboardProgress = $overview['dashboardProgress'];
            $regionalHighlights = $overview['regionalHighlights'] ?? $regionalHighlights;

            $todosAgrupamentos = Agrupamento::with('concelho:id,nome')
                ->orderBy('nome')
                ->get(['id', 'nome', 'concelho_id']);
            $agrupamentosComInquerito = InqueritoAgrupamento::where('ano_referencia', $anoInquerito)
                ->pluck('agrupamento_id')
                ->unique();

            $escolasPendentesLista = $todosAgrupamentos
                ->reject(fn ($agrupamento) => $agrupamentosComInquerito->contains($agrupamento->id))
                ->map(function ($agrupamento) {
                    return [
                        'id' => $agrupamento->id,
                        'nome' => $agrupamento->nome,
                        'concelho' => optional($agrupamento->concelho)->nome ?? '—',
                    ];
                })
                ->values();
        }

        $nacionalidadesData = (clone $familiaQuery)
            ->select('nacionalidade', DB::raw('count(*) as total'))
            ->groupBy('nacionalidade')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $totalFamilias = $familiaQuery->count();
        $totalMembros = $agregadoQuery->sum('total_membros');
        $totalAdultos = $agregadoQuery->sum('adultos_laboral') + $agregadoQuery->sum('adultos_65_mais');
        $totalCriancas = $agregadoQuery->sum('criancas');

        $ticketsPendentes = TicketSuporte::where('estado', 'em_processamento')->count();
        $regionalHighlights['ticketsPendentes'] = $ticketsPendentes;

        $anoReferenciaEscolas = InqueritoAgrupamento::max('ano_referencia');
        if ($anoReferenciaEscolas) {
            $anoReferenciaEscolas = (int) $anoReferenciaEscolas;
            $baseInqueritos = InqueritoAgrupamento::where('ano_referencia', $anoReferenciaEscolas);
            $escolasResumo['anoReferencia'] = $anoReferenciaEscolas;
            $escolasResumo['totalInqueritos'] = (clone $baseInqueritos)->count();
            $escolasResumo['agrupamentosComDados'] = (clone $baseInqueritos)
                ->distinct('agrupamento_id')
                ->count('agrupamento_id');
            $escolasResumo['totalAlunos'] = (int) (clone $baseInqueritos)->sum('total_alunos');
        }

        if (!$user->isFreguesia() && !$user->isAgrupamento()) {
            $todosAgrupamentos = Agrupamento::with('concelho:id,nome')
                ->orderBy('nome')
                ->get(['id', 'nome', 'concelho_id']);

            $agrupamentosComInquerito = InqueritoAgrupamento::where('ano_referencia', $anoInquerito)
                ->pluck('agrupamento_id')
                ->unique();

            $escolasPendentesLista = $todosAgrupamentos
                ->reject(fn ($agrupamento) => $agrupamentosComInquerito->contains($agrupamento->id))
                ->map(fn ($agrupamento) => [
                    'id' => $agrupamento->id,
                    'nome' => $agrupamento->nome,
                    'concelho' => optional($agrupamento->concelho)->nome ?? '—',
                ])->values();

            $totalAgrupamentos = $todosAgrupamentos->count();
            $agrupamentosComDados = $agrupamentosComInquerito->count();
            $agrupamentosPendentes = max($totalAgrupamentos - $agrupamentosComDados, 0);
            $mediaAlunos = ($escolasResumo['totalInqueritos'] ?? 0) > 0
                ? (int) round(($escolasResumo['totalAlunos'] ?? 0) / max(1, $escolasResumo['totalInqueritos']))
                : 0;

            $escolasHighlights = [
                'totalAgrupamentos' => $totalAgrupamentos,
                'agrupamentosComDados' => $agrupamentosComDados,
                'agrupamentosPendentes' => $agrupamentosPendentes,
                'totalAlunos' => $escolasResumo['totalAlunos'] ?? 0,
                'mediaAlunos' => $mediaAlunos,
                'totalInqueritos' => $escolasResumo['totalInqueritos'] ?? 0,
                'anoReferencia' => $anoInquerito,
            ];
        }

        return [
            'title' => 'Página Inicial',
            'nomeLocalidade' => $nomeLocalidade,
            'tituloDashboard' => $tituloDashboard,
            'totalFamilias' => $totalFamilias,
            'totalMembros' => $totalMembros,
            'totalAdultos' => $totalAdultos,
            'totalCriancas' => $totalCriancas,
            'ticketsPendentes' => $ticketsPendentes,
            'ticketsRespondidos' => $ticketsRespondidos,
            'jaPreencheuInquerito' => $jaPreencheuInquerito,
            'inqueritoDisponivel' => $inqueritoDisponivel,
            'inqueritoAnoAtual' => $anoInquerito,
            'inqueritoPrazo' => $inqueritoPrazo,
            'chartLabels' => $nacionalidadesData->pluck('nacionalidade'),
            'chartValues' => $nacionalidadesData->pluck('total'),
            'concelhosResumo' => $concelhosResumo,
            'dashboardProgress' => $dashboardProgress,
            'regionalHighlights' => $regionalHighlights,
            'agrupamentoResumo' => $agrupamentoResumo,
            'anosDisponiveis' => $anosDisponiveis,
            'escolasResumo' => $escolasResumo,
            'escolasPendentes' => $escolasPendentesLista,
            'escolasHighlights' => $escolasHighlights,
        ];
    }

    private function resolverPeriodo(?int $anoDesejado, string $tipo): array
    {
        $anosDisponiveis = InqueritoPeriodo::anosDisponiveis($tipo);

        if ($anosDisponiveis->isEmpty()) {
            $anoBase = $anoDesejado ?? (int) date('Y');

            return [$anoBase, null, $anosDisponiveis];
        }

        $anoSelecionado = $anoDesejado && $anosDisponiveis->contains($anoDesejado)
            ? (int) $anoDesejado
            : (int) $anosDisponiveis->first();

        $periodo = InqueritoPeriodo::periodoParaAno($tipo, $anoSelecionado);

        return [$anoSelecionado, $periodo, $anosDisponiveis];
    }

    public function getRegionalOverview(int $anoInquerito): array
    {
        $concelhos = Concelho::with(['freguesias:id,concelho_id,nome,codigo'])->withCount('freguesias')->get();

        $familiasPorConcelho = Familia::select('freguesias.concelho_id', DB::raw('count(familias.id) as total'))
            ->join('freguesias', 'familias.freguesia_id', '=', 'freguesias.id')
            ->groupBy('freguesias.concelho_id')
            ->pluck('total', 'freguesias.concelho_id');

        $membrosPorConcelho = AgregadoFamiliar::select('freguesias.concelho_id', DB::raw('sum(agregado_familiars.total_membros) as total'))
            ->join('familias', 'agregado_familiars.familia_id', '=', 'familias.id')
            ->join('freguesias', 'familias.freguesia_id', '=', 'freguesias.id')
            ->groupBy('freguesias.concelho_id')
            ->pluck('total', 'freguesias.concelho_id');

        $ticketsPendentesPorConcelho = TicketSuporte::select('freguesias.concelho_id', DB::raw('count(ticket_suportes.id) as total'))
            ->join('users', 'ticket_suportes.utilizador_id', '=', 'users.id')
            ->join('freguesias', 'users.freguesia_id', '=', 'freguesias.id')
            ->where('ticket_suportes.estado', 'em_processamento')
            ->groupBy('freguesias.concelho_id')
            ->pluck('total', 'freguesias.concelho_id');

        $freguesiasComInquerito = InqueritoFreguesia::select('inquerito_freguesias.freguesia_id', 'freguesias.concelho_id')
            ->join('freguesias', 'inquerito_freguesias.freguesia_id', '=', 'freguesias.id')
            ->where('inquerito_freguesias.ano', $anoInquerito)
            ->get()
            ->groupBy('concelho_id')
            ->map(fn ($items) => $items->pluck('freguesia_id')->unique());

        $ultimaSubmissaoPorConcelho = InqueritoFreguesia::select('freguesias.concelho_id', DB::raw('max(inquerito_freguesias.updated_at) as ultima'))
            ->join('freguesias', 'inquerito_freguesias.freguesia_id', '=', 'freguesias.id')
            ->where('inquerito_freguesias.ano', $anoInquerito)
            ->groupBy('freguesias.concelho_id')
            ->pluck('ultima', 'freguesias.concelho_id');

        $concelhosResumo = $concelhos->map(function ($concelho) use (
            $familiasPorConcelho,
            $membrosPorConcelho,
            $ticketsPendentesPorConcelho,
            $freguesiasComInquerito,
            $ultimaSubmissaoPorConcelho
        ) {
            $totalFreguesias = (int) ($concelho->freguesias_count ?? 0);
            $freguesiasComInqueritoIds = $freguesiasComInquerito[$concelho->id] ?? collect();
            $totalFreguesiasComInquerito = $freguesiasComInqueritoIds instanceof Collection
                ? $freguesiasComInqueritoIds->count()
                : count($freguesiasComInqueritoIds);
            $percentual = $totalFreguesias > 0
                ? round(($totalFreguesiasComInquerito / $totalFreguesias) * 100)
                : 0;

            $ultimaSubmissao = $ultimaSubmissaoPorConcelho[$concelho->id] ?? null;

            $todasFreguesias = collect($concelho->freguesias ?? []);

            $freguesiasPendentes = $todasFreguesias
                ->reject(fn ($freguesia) => $freguesiasComInqueritoIds->contains($freguesia->id))
                ->map(fn ($freguesia) => [
                    'id' => $freguesia->id,
                    'nome' => $freguesia->nome,
                    'codigo' => $freguesia->codigo,
                ])->values()->toArray();

            $freguesiasConcluidas = $todasFreguesias
                ->filter(fn ($freguesia) => $freguesiasComInqueritoIds->contains($freguesia->id))
                ->map(fn ($freguesia) => [
                    'id' => $freguesia->id,
                    'nome' => $freguesia->nome,
                    'codigo' => $freguesia->codigo,
                ])->values()->toArray();

            $totalPendentes = count($freguesiasPendentes);

            return [
                'id' => $concelho->id,
                'nome' => $concelho->nome,
                'codigo' => $concelho->codigo,
                'total_freguesias' => $totalFreguesias,
                'freguesias_com_inquerito' => $totalFreguesiasComInquerito,
                'percentual_inquerito' => $percentual,
                'total_familias' => (int) ($familiasPorConcelho[$concelho->id] ?? 0),
                'total_membros' => (int) ($membrosPorConcelho[$concelho->id] ?? 0),
                'tickets_pendentes' => (int) ($ticketsPendentesPorConcelho[$concelho->id] ?? 0),
                'freguesias_pendentes' => $freguesiasPendentes,
                'freguesias_concluidas' => $freguesiasConcluidas,
                'total_pendentes' => $totalPendentes,
                'ultima_submissao' => $ultimaSubmissao ? Carbon::parse($ultimaSubmissao) : null,
            ];
        })->sortBy('nome')->values();

        $dashboardProgress = [
            'totalConcelhos' => $concelhosResumo->count(),
            'concelhosComInquerito' => $concelhosResumo->filter(function ($item) {
                return $item['freguesias_com_inquerito'] >= $item['total_freguesias'] && $item['total_freguesias'] > 0;
            })->count(),
            'percentual' => 0,
        ];

        $dashboardProgress['percentual'] = $dashboardProgress['totalConcelhos'] > 0
            ? round(($dashboardProgress['concelhosComInquerito'] / $dashboardProgress['totalConcelhos']) * 100)
            : 0;

        $regionalHighlights = [
            'totalPendentes' => $concelhosResumo->sum('total_pendentes'),
            'concelhosComPendencias' => $concelhosResumo->where('total_pendentes', '>', 0)->count(),
            'concelhosConcluidos' => $concelhosResumo->where('total_pendentes', '=', 0)->count(),
            'familiasMonitorizadas' => $concelhosResumo->sum('total_familias'),
            'ticketsPendentes' => $ticketsPendentesPorConcelho->sum() ?? 0,
        ];

        return [
            'concelhosResumo' => $concelhosResumo,
            'dashboardProgress' => $dashboardProgress,
            'regionalHighlights' => $regionalHighlights,
        ];
    }
}
