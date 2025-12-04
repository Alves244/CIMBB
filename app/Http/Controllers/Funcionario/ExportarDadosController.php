<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Conselho;
use App\Models\Freguesia;
use App\Models\InqueritoFreguesia;
use App\Services\EstatisticasService;
use App\Services\RegionalDashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExportarDadosController extends Controller
{
    public function __construct(
        private RegionalDashboardService $dashboardService,
        private EstatisticasService $estatisticasService
    ) {
        $this->middleware(['auth', 'check_funcionario']);
    }

    public function index()
    {
        $anos = collect(range(date('Y'), date('Y') - 5));

        return view('funcionario.exportar.index', [
            'title' => 'Exportar Dados',
            'anosDisponiveis' => $anos,
            'concelhos' => Conselho::orderBy('nome')->get(['id', 'nome']),
            'freguesias' => Freguesia::orderBy('nome')->get(['id', 'nome', 'conselho_id']),
        ]);
    }

    public function exportEstatisticasConcelhoPdf(Request $request)
    {
        $dados = $request->validate([
            'ano' => 'required|integer|min:2000|max:' . date('Y'),
            'concelho_id' => 'required|exists:concelhos,id',
        ]);

        return $this->estatisticasService->exportarPdf($dados);
    }

    public function exportEstatisticasFreguesiaPdf(Request $request)
    {
        $dados = $request->validate([
            'ano' => 'required|integer|min:2000|max:' . date('Y'),
            'concelho_id' => 'required|exists:concelhos,id',
            'freguesia_id' => ['required', Rule::exists('freguesias', 'id')->where('conselho_id', $request->input('concelho_id'))],
        ]);

        return $this->estatisticasService->exportarPdf($dados);
    }

    public function exportInqueritosPdf(Request $request)
    {
        $ano = (int) $request->input('ano', date('Y'));
        $inqueritos = InqueritoFreguesia::with('freguesia.conselho')
            ->where('ano', $ano)
            ->orderBy('freguesia_id')
            ->get();

        $pdf = Pdf::loadView('funcionario.exportar.pdf_inqueritos', [
            'ano' => $ano,
            'inqueritos' => $inqueritos,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(sprintf('inqueritos_%d.pdf', $ano));
    }

    public function exportEstatisticasPdf(Request $request)
    {
        $ano = (int) $request->input('ano', date('Y'));
        $overview = $this->dashboardService->getRegionalOverview($ano);

        $pdf = Pdf::loadView('funcionario.exportar.pdf_estatisticas', [
            'ano' => $ano,
            'concelhosResumo' => $overview['concelhosResumo'],
            'dashboardProgress' => $overview['dashboardProgress'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download(sprintf('estatisticas_%d.pdf', $ano));
    }
}
