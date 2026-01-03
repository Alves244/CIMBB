<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\RegionalDashboardService;

/**
 * Controlador para a página inicial e dashboard da aplicação.
 */
class HomeController extends Controller
{
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        $this->middleware('auth')->only('dashboard');
    }

    /**
     * Mostra a página inicial da aplicação.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        // Redireciona para o dashboard principal
        return redirect()->route('dashboard');
    }

    /**
     * Mostra o dashboard do utilizador autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        // Gera os dados do dashboard para o utilizador autenticado
        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user);
        $dadosDashboard['mostrarDashboardRegional'] = false;

        return view('dashboard', $dadosDashboard);
    }
}