<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Services\RegionalDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        $this->middleware(['auth', 'check_funcionario']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $anoSelecionado = (int) $request->query('ano', date('Y'));
        $anosDisponiveis = collect(range(date('Y'), date('Y') - 5));

        $dadosDashboard = $this->dashboardService->buildDashboardPayload($user, $anoSelecionado);
        $dadosDashboard['title'] = 'Dashboard Regional';
        $dadosDashboard['mostrarDashboardRegional'] = true;
        $dadosDashboard['anosDisponiveis'] = $anosDisponiveis;
        $dadosDashboard['anoSelecionado'] = $anoSelecionado;

        return view('dashboard', $dadosDashboard);
    }
}
