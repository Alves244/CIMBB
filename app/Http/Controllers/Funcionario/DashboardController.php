<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Services\RegionalDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controlador do Dashboard para funcionários com visão estratégica sobre o território (Objetivo 17)
class DashboardController extends Controller
{
    // Injeção do serviço de métricas regionais para suporte à tomada de decisão (Objetivo 11)
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        // Garante que apenas utilizadores autorizados acedem aos dados consolidados (Objetivo 14)
        $this->middleware(['auth', 'check_funcionario']);
    }

    // Gera a visualização clara do estado da população ao longo do tempo (Objetivo 21)
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Permite a análise de dados histórica através da seleção do ano de referência (Objetivo 21)
        $anoSelecionado = (int) $request->query('ano', date('Y'));

        // Obtém o payload completo com dados demográficos e socioeconómicos da região (Objetivo 22)
        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user, $anoSelecionado);
        
        // Define metadados para a interface do portal web (Objetivo 14)
        $dadosDashboard['title'] = 'Visão Territorial';
        $dadosDashboard['mostrarDashboardRegional'] = true;
        $dadosDashboard['anosDisponiveis'] = $dadosDashboard['anosDisponiveis'] ?? collect();
        $dadosDashboard['anoSelecionado'] = $dadosDashboard['inqueritoAnoAtual'];

        // Renderiza a vista principal de monitorização para os stakeholders (Objetivo 17)
        return view('dashboard', $dadosDashboard);
    }
}