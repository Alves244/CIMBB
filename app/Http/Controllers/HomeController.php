<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\RegionalDashboardService;

class HomeController extends Controller
{
    public function __construct(private RegionalDashboardService $dashboardService)
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
        $user = Auth::user();

        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user);
        $dadosDashboard['mostrarDashboardRegional'] = false;

        return view('dashboard', $dadosDashboard);
    }
}