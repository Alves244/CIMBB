<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Services\RegionalDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para o dashboard do funcionário com visão territorial.
 */
class DashboardController extends Controller
{
    // Injeção de dependência do serviço de dashboard regional
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        // Aplica middleware de autenticação e verificação de funcionário
        $this->middleware(['auth', 'check_funcionario']);
    }

    // Exibição do dashboard com dados territoriais
    public function index(Request $request)
    {
        // Obtém o utilizador autenticado e o ano selecionado
        $user = Auth::user();
        $anoSelecionado = (int) $request->query('ano', date('Y'));
        // Gera os dados do dashboard para o utilizador e ano selecionado
        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user, $anoSelecionado);
        $dadosDashboard['title'] = 'Visão Territorial';
        $dadosDashboard['mostrarDashboardRegional'] = true;
        $dadosDashboard['anosDisponiveis'] = $dadosDashboard['anosDisponiveis'] ?? collect();
        $dadosDashboard['anoSelecionado'] = $dadosDashboard['inqueritoAnoAtual'];
        // Renderiza a vista do dashboard com os dados preparados
        return view('dashboard', $dadosDashboard);
    }
}
