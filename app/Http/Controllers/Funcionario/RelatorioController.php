<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Services\RegionalDashboardService;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        $this->middleware(['auth', 'check_funcionario']);
    }

    public function index(Request $request)
    {
        $ano = (int) $request->input('ano', date('Y'));
        $overview = $this->dashboardService->getRegionalOverview($ano);

        return view('funcionario.relatorios.index', [
            'title' => 'Relatórios Regionais',
            'anoSelecionado' => $ano,
            'concelhosResumo' => $overview['concelhosResumo'],
            'dashboardProgress' => $overview['dashboardProgress'],
        ]);
    }

    public function export(Request $request)
    {
        $ano = (int) $request->input('ano', date('Y'));
        $overview = $this->dashboardService->getRegionalOverview($ano);
        $registos = $overview['concelhosResumo'];

        $filename = sprintf('relatorio_concelhos_%d.csv', $ano);

        $callback = function () use ($registos) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Concelho', 'Código', 'Famílias', 'Membros', 'Tickets pendentes', 'Freguesias com inquérito', 'Total de freguesias', '% Inquérito']);

            foreach ($registos as $linha) {
                fputcsv($handle, [
                    $linha['nome'],
                    $linha['codigo'],
                    $linha['total_familias'],
                    $linha['total_membros'],
                    $linha['tickets_pendentes'],
                    $linha['freguesias_com_inquerito'],
                    $linha['total_freguesias'],
                    $linha['percentual_inquerito'],
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
