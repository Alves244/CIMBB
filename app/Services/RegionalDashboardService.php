<?php

namespace App\Services;

use App\Models\AgregadoFamiliar;
use App\Models\Conselho;
use App\Models\Familia;
use App\Models\InqueritoFreguesia;
use App\Models\TicketSuporte;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegionalDashboardService
{
    public function buildDashboardPayload(User $user): array
    {
        $familiaQuery = Familia::query();
        $agregadoQuery = AgregadoFamiliar::query();

        $tituloDashboard = 'Dashboard Regional (Todo o Território)';
        $nomeLocalidade = 'Beira Baixa (Todos os Concelhos)';

        $anoInquerito = (int) date('Y');
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
        ];
    }

    public function getRegionalOverview(int $anoInquerito): array
    {
        $concelhos = Conselho::withCount('freguesias')->get();

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

        $inqueritosPorConcelho = InqueritoFreguesia::select('freguesias.conselho_id', DB::raw('count(distinct inquerito_freguesias.freguesia_id) as total'))
            ->join('freguesias', 'inquerito_freguesias.freguesia_id', '=', 'freguesias.id')
            ->where('inquerito_freguesias.ano', $anoInquerito)
            ->groupBy('freguesias.conselho_id')
            ->pluck('total', 'freguesias.conselho_id');

        $concelhosResumo = $concelhos->map(function ($conselho) use (
            $familiasPorConcelho,
            $membrosPorConcelho,
            $ticketsPendentesPorConcelho,
            $inqueritosPorConcelho
        ) {
            $totalFreguesias = (int) ($conselho->freguesias_count ?? 0);
            $freguesiasComInquerito = (int) ($inqueritosPorConcelho[$conselho->id] ?? 0);
            $percentual = $totalFreguesias > 0
                ? round(($freguesiasComInquerito / $totalFreguesias) * 100)
                : 0;

            return [
                'id' => $conselho->id,
                'nome' => $conselho->nome,
                'codigo' => $conselho->codigo,
                'total_freguesias' => $totalFreguesias,
                'freguesias_com_inquerito' => $freguesiasComInquerito,
                'percentual_inquerito' => $percentual,
                'total_familias' => (int) ($familiasPorConcelho[$conselho->id] ?? 0),
                'total_membros' => (int) ($membrosPorConcelho[$conselho->id] ?? 0),
                'tickets_pendentes' => (int) ($ticketsPendentesPorConcelho[$conselho->id] ?? 0),
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

        return [
            'concelhosResumo' => $concelhosResumo,
            'dashboardProgress' => $dashboardProgress,
        ];
    }
}
