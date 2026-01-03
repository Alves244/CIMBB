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

/**
 * Controlador para exportação de dados estatísticos e inquéritos.
 */
class ExportarDadosController extends Controller
{
    // Injeção de dependências dos serviços necessários
    public function __construct(
        // Aplica middleware de autenticação e verificação de funcionário
        private RegionalDashboardService $dashboardService,
        private EstatisticasService $estatisticasService
    ) {
        // Aplica middleware de autenticação e verificação de funcionário
        $this->middleware(['auth', 'check_funcionario']);
    }

    // Exibição da página de exportação de dados
    public function index()
    {
        // Obtém os anos disponíveis com base nas famílias instaladas
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

        // Renderiza a vista de exportação com os dados necessários
        return view('funcionario.exportar.index', [
            'title' => 'Exportar Dados',
            'anosDisponiveis' => $anos,
            'concelhos' => Concelho::orderBy('nome')->get(['id', 'nome']),
            'freguesias' => Freguesia::orderBy('nome')->get(['id', 'nome', 'concelho_id']),
            'agrupamentos' => Agrupamento::with('concelho:id,nome')->orderBy('nome')->get(['id', 'nome', 'concelho_id']),
        ]);
    }
    // Exportação de estatísticas do concelho em PDF
    public function exportEstatisticasConcelhoPdf(Request $request)
    {
        // Sanitiza e valida os filtros de exportação
        $dados = $this->sanitizeExportFilters($request->all());
        return $this->estatisticasService->exportarPdf($dados);
    }
    // Exportação de estatísticas das escolas em PDF
    public function exportEstatisticasEscolasPdf(Request $request)
    {
        // Sanitiza e valida os filtros de exportação
        $dados = $this->sanitizeExportEscolasFilters($request->all());
        return $this->estatisticasService->exportarPdfEscolas($dados);
    }
    // Exportação de estatísticas da freguesia em PDF
    public function exportEstatisticasFreguesiaPdf(Request $request)
    {
        // Sanitiza e valida os filtros de exportação
        $dados = $this->sanitizeExportFilters($request->all());
        // Validação adicional para garantir que concelho e freguesia são fornecidos
        if (!$dados['concelho_id'] || !$dados['freguesia_id']) {
            abort(422, 'Seleciona um concelho e uma freguesia válidos.');
        }
        return $this->estatisticasService->exportarPdf($dados);
    }

    // Exportação dos inquéritos da freguesia em PDF
    public function exportInqueritosPdf(Request $request)
    {
        // Obtém o ano atual e valida o ano solicitado
        $anoAtual = (int) date('Y');
        $ano = (int) $request->input('ano', $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }
        // Carrega os inquéritos da freguesia para o ano especificado
        $inqueritos = InqueritoFreguesia::with('freguesia.concelho')
            ->where('ano', $ano)
            ->orderBy('freguesia_id')
            ->get();
        // Gera o PDF com os inquéritos carregados
        $pdf = Pdf::loadView('funcionario.exportar.pdf_inqueritos', [
            'ano' => $ano,
            'inqueritos' => $inqueritos,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(sprintf('inqueritos_%d.pdf', $ano));
    }

    // Exportação geral de estatísticas em PDF
    public function exportEstatisticasPdf(Request $request)
    {
        // Obtém o ano atual e valida o ano solicitado
        $anoAtual = (int) date('Y');
        $ano = (int) $request->input('ano', $anoAtual);
        if ($ano !== $anoAtual) {
            $ano = $anoAtual;
        }
        $overview = $this->dashboardService->getRegionalOverview($ano);
        // Gera o PDF com as estatísticas gerais
        $pdf = Pdf::loadView('funcionario.exportar.pdf_estatisticas', [
            'ano' => $ano,
            'concelhosResumo' => $overview['concelhosResumo'],
            'dashboardProgress' => $overview['dashboardProgress'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download(sprintf('estatisticas_%d.pdf', $ano));
    }

    // Sanitização e validação dos filtros de exportação
    private function sanitizeExportFilters(array $inputs): array
    {
        // Obtém o ano atual e valida o ano solicitado
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

    // Sanitização e validação dos filtros de exportação para escolas
    private function sanitizeExportEscolasFilters(array $inputs): array
    {
        // Obtém o ano atual e valida o ano solicitado
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
