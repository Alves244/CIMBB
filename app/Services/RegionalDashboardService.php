<?php

namespace App\Services;

use App\Models\AgregadoFamiliar;
use App\Models\Conselho;
use App\Models\Familia;
use App\Models\InqueritoFreguesia;
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

        $tituloDashboard = 'Dashboard Regional (Todo o Território)';
        $nomeLocalidade = 'Beira Baixa (Todos os Concelhos)';

        $anoAtual = (int) date('Y');
        $anoInquerito = ($ano && $ano >= 2000 && $ano <= $anoAtual) ? (int) $ano : $anoAtual;
        $ticketsRespondidos = 0;
        $jaPreencheuInquerito = false;
        $inqueritoDisponivel = false;
        $inqueritoPrazo = Carbon::create($anoInquerito, 12, 31, 23, 59, 59);

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

            $inqueritoDisponivel = !$jaPreencheuInquerito && now()->lessThanOrEqualTo($inqueritoPrazo);
        } else {
            $overview = $this->getRegionalOverview($anoInquerito);
            $concelhosResumo = $overview['concelhosResumo'];
            $dashboardProgress = $overview['dashboardProgress'];
            $regionalHighlights = $overview['regionalHighlights'] ?? $regionalHighlights;
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
        ];
    }

    public function getRegionalOverview(int $anoInquerito): array
    {
        $concelhos = Conselho::with(['freguesias:id,conselho_id,nome,codigo'])->withCount('freguesias')->get();

        $familiasPorConcelho = Familia::select('freguesias.conselho_id', DB::raw('count(familias.id) as total'))
            ->join('freguesias', 'familias.freguesia_id', '=', 'freguesias.id')
            ->groupBy('freguesias.conselho_id')
            ->pluck('total', 'freguesias.conselho_id');

        $membrosPorConcelho = AgregadoFamiliar::select('freguesias.conselho_id', DB::raw('sum(agregado_familiars.total_membros) as total'))
            ->join('familias', 'agregado_familiars.familia_id', '=', 'familias.id')
            ->join('freguesias', 'familias.freguesia_id', '=', 'freguesias.id')
            ->groupBy('freguesias.conselho_id')
            ->pluck('total', 'freguesias.conselho_id');

        $ticketsPendentesPorConcelho = TicketSuporte::select('freguesias.conselho_id', DB::raw('count(ticket_suportes.id) as total'))
            ->join('users', 'ticket_suportes.utilizador_id', '=', 'users.id')
            ->join('freguesias', 'users.freguesia_id', '=', 'freguesias.id')
            ->where('ticket_suportes.estado', 'em_processamento')
            ->groupBy('freguesias.conselho_id')
            ->pluck('total', 'freguesias.conselho_id');

        $freguesiasComInquerito = InqueritoFreguesia::select('inquerito_freguesias.freguesia_id', 'freguesias.conselho_id')
            ->join('freguesias', 'inquerito_freguesias.freguesia_id', '=', 'freguesias.id')
            ->where('inquerito_freguesias.ano', $anoInquerito)
            ->get()
            ->groupBy('conselho_id')
            ->map(fn ($items) => $items->pluck('freguesia_id')->unique());

        $ultimaSubmissaoPorConcelho = InqueritoFreguesia::select('freguesias.conselho_id', DB::raw('max(inquerito_freguesias.updated_at) as ultima'))
            ->join('freguesias', 'inquerito_freguesias.freguesia_id', '=', 'freguesias.id')
            ->where('inquerito_freguesias.ano', $anoInquerito)
            ->groupBy('freguesias.conselho_id')
            ->pluck('ultima', 'freguesias.conselho_id');

        $concelhosResumo = $concelhos->map(function ($conselho) use (
            $familiasPorConcelho,
            $membrosPorConcelho,
            $ticketsPendentesPorConcelho,
            $freguesiasComInquerito,
            $ultimaSubmissaoPorConcelho
        ) {
            $totalFreguesias = (int) ($conselho->freguesias_count ?? 0);
            $freguesiasComInqueritoIds = $freguesiasComInquerito[$conselho->id] ?? collect();
            $totalFreguesiasComInquerito = $freguesiasComInqueritoIds instanceof Collection
                ? $freguesiasComInqueritoIds->count()
                : count($freguesiasComInqueritoIds);
            $percentual = $totalFreguesias > 0
                ? round(($totalFreguesiasComInquerito / $totalFreguesias) * 100)
                : 0;

            $ultimaSubmissao = $ultimaSubmissaoPorConcelho[$conselho->id] ?? null;

            $todasFreguesias = collect($conselho->freguesias ?? []);

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
                'id' => $conselho->id,
                'nome' => $conselho->nome,
                'codigo' => $conselho->codigo,
                'total_freguesias' => $totalFreguesias,
                'freguesias_com_inquerito' => $totalFreguesiasComInquerito,
                'percentual_inquerito' => $percentual,
                'total_familias' => (int) ($familiasPorConcelho[$conselho->id] ?? 0),
                'total_membros' => (int) ($membrosPorConcelho[$conselho->id] ?? 0),
                'tickets_pendentes' => (int) ($ticketsPendentesPorConcelho[$conselho->id] ?? 0),
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
