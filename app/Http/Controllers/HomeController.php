<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\RegionalDashboardService;

// Controlador responsável por gerir o acesso inicial e a visualização de dados no portal
class HomeController extends Controller
{
    // Injeção do serviço que processa a informação para a tomada de decisões estratégicas
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        // Proteção de rota para garantir que apenas utilizadores autorizados acedem aos dados
        $this->middleware('auth')->only('dashboard');
    }

    /**
     * Redireciona o utilizador para o dashboard principal ao aceder à raiz do portal.
     */
    public function home()
    {
        // Garante que o fluxo de instalação e monitorização começa na página de dados
        return redirect()->route('dashboard');
    }

    /**
     * Prepara a visão clara do estado da população estrangeira no território.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Constrói o conjunto de indicadores demográficos e qualitativos para o utilizador atual
        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user);
        
        // Configuração visual da interface para o perfil de acesso específico
        $dadosDashboard['mostrarDashboardRegional'] = false;

        // Renderiza o portal garantindo que os dados sejam consistentes e acessíveis
        return view('dashboard', $dadosDashboard);
    }
}