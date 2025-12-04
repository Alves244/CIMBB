<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Services\RegionalDashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        $this->middleware(['auth', 'check_funcionario']);
    }

    public function index()
    {
        $user = Auth::user();

        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user);
        $dadosDashboard['title'] = 'Dashboard Regional';
        $dadosDashboard['mostrarDashboardRegional'] = true;

        return view('dashboard', $dadosDashboard);
    }
}
