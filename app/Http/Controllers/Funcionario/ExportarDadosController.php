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

// Controlador responsável pela exportação de relatórios estatísticos para suporte à decisão (Objetivo 11)
class ExportarDadosController extends Controller
{
    // Injeção de serviços para processamento de métricas regionais e geração de documentos
    public function __construct(
        private RegionalDashboardService $dashboardService,
        private EstatisticasService $estatisticasService
    ) {
        // Restringe o acesso apenas a funcionários autorizados da CIMBB (Objetivo 14)
        $this->middleware(['auth', 'check_funcionario']);
    }

    // Prepara a interface de exportação com filtros territoriais e temporais
    public function index()
    {
        $anoAtual = (int) date('Y');
        // Obtém anos de instalação únicos para permitir a análise de tendências demográficas
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

        // Carrega entidades geográficas da Beira Baixa para segmentação dos relatórios (Objetivo 14)
        return view('funcionario.exportar.index', [
            'title' => 'Exportar Dados',
            'anosDisponiveis' => $anos,
            'concelhos' => Concelho::orderBy('nome')->get(['id', 'nome']),
            'freguesias' => Freguesia::orderBy('nome')->get(['id', 'nome', 'concelho_id']),
            'agrupamentos' => Agrupamento::with('concelho:id,nome')->orderBy('nome')->get(['id', 'nome', 'concelho_id']),
        ]);
    }

    // Exporta métricas específicas de um concelho para análise de impacto local (Objetivo 22)
    public function exportEstatisticasConcelhoPdf(Request $request)
    {
        $dados = $this->sanitizeExportFilters($request->all());
        return $this->estatisticasService->exportarPdf($dados);
    }

    // Gera relatórios sobre a dinâmica escolar e integração de alunos estrangeiros
    public function exportEstatisticasEscolasPdf(Request $request)
    {
        $dados = $this->sanitizeExportEscolasFilters($request->all());

        return $this->estatisticasService->exportarPdfEscolas($dados);
    }

    // Exporta dados granulares ao nível da freguesia para monitorização detalhada
    public function exportEstatisticasFreguesiaPdf(Request $request)
    {
        $dados = $this->sanitizeExportFilters($request->all());

        if (!$dados['concelho_id'] || !$dados['freguesia_id']) {
            abort(422, 'Seleciona um concelho e uma freguesia válidos.');
        }

        return $this->estatisticasService->exportarPdf($dados);
    }

    // Gera um documento PDF consolidando os inquéritos anuais submetidos (Objetivo 15)
    public function exportInqueritosPdf(Request $request)
    {
        $anoAtual = (int) date('Y');
        $ano = (int) $request->input('ano', $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }
        // Agrupa inquéritos por freguesia e concelho para análise qualitativa e quantitativa
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

    // Exporta visão geral regional com indicadores socioeconómicos e demográficos (Objetivo 11)
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

    // Normaliza filtros de exportação para garantir a consistência do relatório
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

    // Ajusta filtros específicos para o contexto escolar/agrupamentos
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