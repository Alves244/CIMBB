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

// Controlador responsável pela geração de relatórios qualitativos e quantitativos (Objetivo 2)
class RelatorioController extends Controller
{
    // Injeção do serviço especializado em métricas e cálculos estatísticos
    public function __construct(private EstatisticasService $estatisticasService)
    {
        // Restrição de acesso a funcionários autorizados para salvaguardar a segurança (Objetivo 4)
        $this->middleware(['auth', 'check_funcionario']);
    }

    // Método principal que gere os dois grandes âmbitos do projeto: Freguesias e Escolas
    public function index(Request $request)
    {
        $anoAtual = (int) date('Y');
        // Define o escopo da análise (população geral ou dinâmica escolar)
        $escopo = $request->get('escopo', 'freguesias');
        if (! in_array($escopo, ['freguesias', 'escolas'], true)) {
            $escopo = 'freguesias';
        }

        // Bloco dedicado à análise do fluxo de instalação em ambiente escolar (Objetivo 14)
        if ($escopo === 'escolas') {
            // Recupera anos com dados para permitir análise temporal (Objetivo 21)
            $anosDisponiveis = InqueritoAgrupamento::query()
                ->select('ano_referencia')
                ->distinct()
                ->orderByDesc('ano_referencia')
                ->pluck('ano_referencia')
                ->map(fn ($ano) => (int) $ano)
                ->filter(fn ($ano) => $ano === $anoAtual)
                ->values();

            if ($anosDisponiveis->isEmpty()) {
                $anosDisponiveis = collect([$anoAtual]);
            }

            $anoSelecionado = (int) ($request->get('ano') ?? $anosDisponiveis->first());
            if (! $anosDisponiveis->contains($anoSelecionado)) {
                $anoSelecionado = (int) $anosDisponiveis->first();
            }

            $filtros = $request->all();
            $filtros['ano'] = $anoSelecionado;
            $perPage = max(1, (int) $request->get('inqueritos_por_pagina', 10));
            
            // Invoca o serviço para processar totais e distribuições das escolas
            $resultado = $this->estatisticasService->gerarEscolas($filtros, $perPage);

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

        // Bloco dedicado à análise socioeconómica e demográfica das Freguesias (Objetivo 22)
        $anosDisponiveis = Familia::query()
            ->select('ano_instalacao')
            ->distinct()
            ->orderByDesc('ano_instalacao')
            ->pluck('ano_instalacao')
            ->map(fn ($ano) => (int) $ano)
            ->filter(fn ($ano) => $ano === $anoAtual)
            ->values();

        if ($anosDisponiveis->isEmpty()) {
            $anosDisponiveis = collect([$anoAtual]);
        }

        $anoSelecionado = (int) ($request->get('ano') ?? $anosDisponiveis->first());
        if (!$anosDisponiveis->contains($anoSelecionado)) {
            $anoSelecionado = (int) $anosDisponiveis->first();
        }

        $filtros = $request->all();
        $filtros['ano'] = $anoSelecionado;
        $perPage = max(1, (int) $request->get('familias_por_pagina', 10));
        
        // Gera o conjunto de dados estatísticos (timeline, totais e listagens)
        $resultado = $this->estatisticasService->gerar($filtros, $perPage);
        $filtrosNormalizados = $resultado['filtros'];
        $listaFamilias = $resultado['listaFamilias'];

        // Retorna a visão clara do estado da população residente estrangeira (Objetivo 21)
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

    // Permite a exportação de dados consistentes para suporte à intervenção pública (Objetivo 12, 15)
    public function export(Request $request)
    {
        $escopo = $request->get('escopo', 'freguesias');

        if ($escopo === 'escolas') {
            return $this->estatisticasService->exportarPdfEscolas($request->all());
        }

        return $this->estatisticasService->exportarPdf($request->all());
    }

    // API para atualização dinâmica de gráficos baseada em filtros customizados
    public function customChart(Request $request)
    {
        $resultado = $this->estatisticasService->contarFamilias($request->all());

        return response()->json([
            'totalFamilias' => $resultado['totalFamilias'],
            'filters' => $resultado['filters'],
        ]);
    }
}