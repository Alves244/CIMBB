<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Agrupamento;
use App\Models\Concelho;
use App\Models\Familia;
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
        $anoAtual = (int) date('Y');
        $anos = Familia::query()
            ->select('ano_instalacao')
            ->distinct()
            ->orderByDesc('ano_instalacao')
            ->pluck('ano_instalacao')
            ->map(fn ($ano) => (int) $ano)
            ->filter(fn ($ano) => $ano === $anoAtual)
            ->values();

        if ($anos->isEmpty()) {
            $anos = collect([$anoAtual]);
        }

        return view('funcionario.exportar.index', [
            'title' => 'Exportar Dados',
            'anosDisponiveis' => $anos,
            'concelhos' => Concelho::orderBy('nome')->get(['id', 'nome']),
            'freguesias' => Freguesia::orderBy('nome')->get(['id', 'nome', 'concelho_id']),
            'agrupamentos' => Agrupamento::with('concelho:id,nome')->orderBy('nome')->get(['id', 'nome', 'concelho_id']),
        ]);
    }

    public function exportEstatisticasConcelhoPdf(Request $request)
    {
        $dados = $this->sanitizeExportFilters($request->all());
        return $this->estatisticasService->exportarPdf($dados);
    }

    public function exportEstatisticasEscolasPdf(Request $request)
    {
        $dados = $this->sanitizeExportEscolasFilters($request->all());

        return $this->estatisticasService->exportarPdfEscolas($dados);
    }

    public function exportEstatisticasFreguesiaPdf(Request $request)
    {
        $dados = $this->sanitizeExportFilters($request->all());

        if (!$dados['concelho_id'] || !$dados['freguesia_id']) {
            abort(422, 'Seleciona um concelho e uma freguesia válidos.');
        }

        return $this->estatisticasService->exportarPdf($dados);
    }

    public function exportInqueritosPdf(Request $request)
    {
        $anoAtual = (int) date('Y');
        $ano = (int) $request->input('ano', $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }
        $inqueritos = InqueritoFreguesia::with('freguesia.concelho')
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
        $anoAtual = (int) date('Y');
        $ano = (int) $request->input('ano', $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }
        $overview = $this->dashboardService->getRegionalOverview($ano);

        $pdf = Pdf::loadView('funcionario.exportar.pdf_estatisticas', [
            'ano' => $ano,
            'concelhosResumo' => $overview['concelhosResumo'],
            'dashboardProgress' => $overview['dashboardProgress'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download(sprintf('estatisticas_%d.pdf', $ano));
    }

    private function sanitizeExportFilters(array $inputs): array
    {
        $anoAtual = (int) date('Y');
        $ano = (int) ($inputs['ano'] ?? $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }

        return [
            'ano' => $ano,
            'concelho_id' => $inputs['concelho_id'] ?? null,
            'freguesia_id' => $inputs['freguesia_id'] ?? null,
        ];
    }

    private function sanitizeExportEscolasFilters(array $inputs): array
    {
        $anoAtual = (int) date('Y');
        $ano = (int) ($inputs['ano'] ?? $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }

        return [
            'ano' => $ano,
            'concelho_id' => $inputs['concelho_id'] ?? null,
            'agrupamento_id' => $inputs['agrupamento_id'] ?? null,
        ];
    }
}
