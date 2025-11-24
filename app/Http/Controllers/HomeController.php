<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Familia;
use App\Models\AgregadoFamiliar; // 1. IMPORTAR AgregadoFamiliar
use App\Models\User;
use App\Models\TicketSuporte;
use App\Models\InqueritoFreguesia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        // Mantido por compatibilidade; rota '/' redireciona direto para login
        return redirect()->route('dashboard');
    }

    /**
     * Mostra o dashboard principal da aplicação com dados.
     * (ESTE MÉTODO FOI ATUALIZADO)
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // --- 1. Preparar as Consultas Base ---
        $familiaQuery = Familia::query();
        $agregadoQuery = AgregadoFamiliar::query();

        // --- 2. Aplicar Filtro de Freguesia (se necessário) ---
        $tituloDashboard = "Dashboard Regional (Todo o Território)";
        $nomeLocalidade = "Beira Baixa (Todos os Concelhos)";

        $anoInquerito = (int) date('Y');
        $ticketsRespondidos = 0;
        $jaPreencheuInquerito = false;
        $inqueritoDisponivel = false;
        $inqueritoPrazo = Carbon::create($anoInquerito, 12, 31, 23, 59, 59);

        if ($user->isFreguesia()) { //
            $freguesiaId = $user->freguesia_id;
            
            // Filtra a consulta de Famílias
            $familiaQuery->where('freguesia_id', $freguesiaId);
            
            // Filtra a consulta de Agregados (usando a relação)
            $agregadoQuery->whereHas('familia', function ($q) use ($freguesiaId) {
                $q->where('freguesia_id', $freguesiaId);
            });
            
            $tituloDashboard = "Dashboard da Freguesia";
            $nomeLocalidade = $user->freguesia->nome ?? 'N/A';

            $ticketsRespondidos = TicketSuporte::where('utilizador_id', $user->id)
                ->where('estado', 'respondido')
                ->count();

            $jaPreencheuInquerito = InqueritoFreguesia::where('freguesia_id', $freguesiaId)
                ->where('ano', $anoInquerito)
                ->exists();

            $inqueritoDisponivel = !$jaPreencheuInquerito && now()->lessThanOrEqualTo($inqueritoPrazo);
        }
        // Se for 'cimbb' ou 'admin', as consultas ($familiaQuery, $agregadoQuery) não levam filtro.

        // --- 3. Executar Consultas Agregadas ---
        
        // Contagem de Nacionalidades para o gráfico
        $nacionalidadesData = (clone $familiaQuery) // Clonamos para não afetar outras contagens
                                    ->select('nacionalidade', DB::raw('count(*) as total'))
                                    ->groupBy('nacionalidade')
                                    ->orderBy('total', 'desc')
                                    ->limit(10) 
                                    ->get();
        
        // Contagens principais
        $totalFamilias = $familiaQuery->count();
        $totalMembros = $agregadoQuery->sum('total_membros'); // Soma de todos os membros
        $totalAdultos = $agregadoQuery->sum('adultos_laboral') + $agregadoQuery->sum('adultos_65_mais');
        $totalCriancas = $agregadoQuery->sum('criancas');

        $ticketsPendentes = TicketSuporte::where('estado', 'em_processamento')->count();

        // --- 4. Preparar dados para o Chart.js ---
        $chartLabels = $nacionalidadesData->pluck('nacionalidade');
        $chartValues = $nacionalidadesData->pluck('total');

        // --- 5. Passar os dados para a view ---
        return view('dashboard', [
            'title' => 'Página Inicial',
            'nomeLocalidade' => $nomeLocalidade,
            'tituloDashboard' => $tituloDashboard,
            
            // Os 4 cartões de estatística
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
            
            // Os dados do gráfico
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }
}