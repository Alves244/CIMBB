<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Conselho;
use App\Models\Familia;
use App\Models\Freguesia;
use App\Models\SetorAtividade;
use App\Services\EstatisticasService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function __construct(private EstatisticasService $estatisticasService)
    {
        $this->middleware(['auth', 'check_funcionario']);
    }

    public function index(Request $request)
    {
        $filtros = $request->all();
        $perPage = max(1, (int) $request->get('familias_por_pagina', 10));
        $resultado = $this->estatisticasService->gerar($filtros, $perPage);
        $filtrosNormalizados = $resultado['filtros'];
        $listaFamilias = $resultado['listaFamilias'];

        return view('funcionario.relatorios.index', [
            'title' => 'Estatísticas & Exportações',
            'anoSelecionado' => $filtrosNormalizados['ano'],
            'anosDisponiveis' => collect(range(date('Y'), date('Y') - 5)),
            'filters' => $filtrosNormalizados,
            'totais' => $resultado['totais'],
            'distribuicoes' => $resultado['distribuicoes'],
            'freguesiasResumo' => $resultado['freguesias'],
            'listaFamilias' => $listaFamilias,
            'familiasPerPage' => $perPage,
            'chartTimeline' => $resultado['timeline'],
            'chartHeatmap' => $resultado['heatmap'],
            'concelhos' => Conselho::with('freguesias:id,nome,conselho_id')->orderBy('nome')->get(),
            'freguesias' => Freguesia::orderBy('nome')->get(['id', 'nome', 'conselho_id']),
            'setores' => SetorAtividade::orderBy('nome')->get(['id', 'nome']),
            'nacionalidades' => Familia::select('nacionalidade')->distinct()->orderBy('nacionalidade')->pluck('nacionalidade'),
        ]);
    }

    public function export(Request $request)
    {
        return $this->estatisticasService->exportarPdf($request->all());
    }
}
