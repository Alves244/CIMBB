<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Agrupamento;
use App\Models\Concelho;
use App\Models\Familia;
use App\Models\Freguesia;
use App\Models\InqueritoAgrupamento;
use App\Models\InqueritoAgrupamentoRegisto;
use App\Models\SetorAtividade;
use App\Services\EstatisticasService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Controlador para geração de relatórios estatísticos para funcionários.
 */
class RelatorioController extends Controller
{
    // Injeção de dependência do serviço de estatísticas
    public function __construct(private EstatisticasService $estatisticasService)
    {
        // Aplica middleware de autenticação e verificação de funcionário
        $this->middleware(['auth', 'check_funcionario']);
    }

    // Exibição da página de relatórios com filtros e dados estatísticos
    public function index(Request $request)
    {
        // Obtém o ano atual e valida o escopo dos dados
        $anoAtual = (int) date('Y');
        $escopo = $request->get('escopo', 'freguesias');
        if (! in_array($escopo, ['freguesias', 'escolas'], true)) {
            $escopo = 'freguesias';
        }

        // Geração dos dados conforme o escopo selecionado
        if ($escopo === 'escolas') {
            $anosDisponiveis = InqueritoAgrupamento::query()
                ->select('ano_referencia')
                ->distinct()
                ->orderByDesc('ano_referencia')
                ->pluck('ano_referencia')
                ->map(fn ($ano) => (int) $ano)
                ->filter(fn ($ano) => $ano === $anoAtual)
                ->values();
            // Validação dos anos disponíveis
            if ($anosDisponiveis->isEmpty()) {
                $anosDisponiveis = collect([$anoAtual]);
            }
            // Seleção do ano
            $anoSelecionado = (int) ($request->get('ano') ?? $anosDisponiveis->first());
            if (! $anosDisponiveis->contains($anoSelecionado)) {
                $anoSelecionado = (int) $anosDisponiveis->first();
            }
            // Geração dos filtros e resultados para escolas
            $filtros = $request->all();
            $filtros['ano'] = $anoSelecionado;
            $perPage = max(1, (int) $request->get('inqueritos_por_pagina', 10));
            $resultado = $this->estatisticasService->gerarEscolas($filtros, $perPage);
            // Renderiza a vista de relatórios para escolas
            return view('funcionario.relatorios.escolas', [
                'title' => 'Estatísticas das Escolas',
                'escopoDados' => 'escolas',
                'anoSelecionado' => $anoSelecionado,
                'anosDisponiveis' => $anosDisponiveis,
                'filters' => $resultado['filtros'],
                'totaisEscolas' => $resultado['totais'],
                'distribuicoesEscolas' => $resultado['distribuicoes'],
                'listaInqueritos' => $resultado['lista'],
                'concelhos' => Concelho::orderBy('nome')->get(),
                'agrupamentos' => Agrupamento::with('concelho:id,nome')->orderBy('nome')->get(),
                'niveisEnsino' => InqueritoAgrupamentoRegisto::select('nivel_ensino')->distinct()->orderBy('nivel_ensino')->pluck('nivel_ensino'),
                'nacionalidadesEscolas' => InqueritoAgrupamentoRegisto::select('nacionalidade')->distinct()->orderBy('nacionalidade')->pluck('nacionalidade'),
            ]);
        }
        // Geração dos anos disponíveis para freguesias
        $anosDisponiveis = Familia::query()
            ->select('ano_instalacao')
            ->distinct()
            ->orderByDesc('ano_instalacao')
            ->pluck('ano_instalacao')
            ->map(fn ($ano) => (int) $ano)
            ->filter(fn ($ano) => $ano === $anoAtual)
            ->values();
        // Validação dos anos disponíveis
        if ($anosDisponiveis->isEmpty()) {
            $anosDisponiveis = collect([$anoAtual]);
        }
        // Seleção do ano
        $anoSelecionado = (int) ($request->get('ano') ?? $anosDisponiveis->first());
        if (!$anosDisponiveis->contains($anoSelecionado)) {
            $anoSelecionado = (int) $anosDisponiveis->first();
        }
        // Geração dos filtros e resultados para freguesias
        $filtros = $request->all();
        $filtros['ano'] = $anoSelecionado;
        $perPage = max(1, (int) $request->get('familias_por_pagina', 10));
        $resultado = $this->estatisticasService->gerar($filtros, $perPage);
        $filtrosNormalizados = $resultado['filtros'];
        $listaFamilias = $resultado['listaFamilias'];
        // Renderiza a vista de relatórios para freguesias
        return view('funcionario.relatorios.index', [
            'title' => 'Estatísticas das Freguesias',
            'escopoDados' => 'freguesias',
            'anoSelecionado' => $filtrosNormalizados['ano'],
            'anosDisponiveis' => $anosDisponiveis,
            'filters' => $filtrosNormalizados,
            'totais' => $resultado['totais'],
            'distribuicoes' => $resultado['distribuicoes'],
            'freguesiasResumo' => $resultado['freguesias'],
            'listaFamilias' => $listaFamilias,
            'familiasPerPage' => $perPage,
            'chartTimeline' => $resultado['timeline'],
            'concelhos' => Concelho::with('freguesias:id,nome,concelho_id')->orderBy('nome')->get(),
            'freguesias' => Freguesia::orderBy('nome')->get(['id', 'nome', 'concelho_id']),
            'setores' => SetorAtividade::orderBy('nome')->get(['id', 'nome']),
            'nacionalidades' => Familia::select('nacionalidade')->distinct()->orderBy('nacionalidade')->pluck('nacionalidade'),
        ]);
    }
    // Exportação de estatísticas em PDF conforme o escopo
    public function export(Request $request)
    {
        $escopo = $request->get('escopo', 'freguesias');
        
        if ($escopo === 'escolas') {
            return $this->estatisticasService->exportarPdfEscolas($request->all());
        }

        return $this->estatisticasService->exportarPdf($request->all());
    }
    // Geração de dados personalizados para gráficos
    public function customChart(Request $request)
    {
        $resultado = $this->estatisticasService->contarFamilias($request->all());

        return response()->json([
            'totalFamilias' => $resultado['totalFamilias'],
            'filters' => $resultado['filters'],
        ]);
    }
}
