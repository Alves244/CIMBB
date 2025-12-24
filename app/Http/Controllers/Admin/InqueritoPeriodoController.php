<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InqueritoPeriodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Gestão dos períodos de inquérito para a monitorização quantitativa e qualitativa anual
class InqueritoPeriodoController extends Controller
{
    // Listagem dos intervalos temporais definidos para a recolha de dados na Beira Baixa
    public function index(): View
    {
        // Organiza os períodos por ano decrescente para facilitar a gestão das campanhas atuais
        $periodos = InqueritoPeriodo::orderByDesc('ano')
            ->orderBy('tipo')
            ->get();

        return view('admin.inqueritos.periodos.index', [
            'periodos' => $periodos,
            'tipos' => [
                InqueritoPeriodo::TIPO_FREGUESIA => 'Inquérito Freguesia',
                InqueritoPeriodo::TIPO_AGRUPAMENTO => 'Inquérito Agrupamento',
            ],
        ]);
    }

    // Configuração de uma nova janela temporal para preenchimento de inquéritos
    public function store(Request $request): RedirectResponse
    {
        // Validação rigorosa para evitar sobreposição de inquéritos do mesmo tipo no mesmo ano
        $dados = $request->validate([
            'tipo' => ['required', Rule::in([InqueritoPeriodo::TIPO_FREGUESIA, InqueritoPeriodo::TIPO_AGRUPAMENTO])],
            'ano' => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('inquerito_periodos')->where(fn ($q) => $q->where('tipo', $request->input('tipo')))],
            'abre_em' => ['required', 'date'],
            'fecha_em' => ['required', 'date', 'after:abre_em'], // Garante a coerência cronológica
        ]);

        // Associa o administrador responsável pela abertura deste ciclo de monitorização
        $dados['criado_por'] = $request->user()->id;

        InqueritoPeriodo::create($dados);

        return redirect()->route('admin.inqueritos.periodos.index')->with('success', 'Período criado com sucesso.');
    }

    // Ajuste das datas de abertura ou fecho de um período de recolha existente
    public function update(Request $request, InqueritoPeriodo $periodo): RedirectResponse
    {
        $dados = $request->validate([
            'abre_em' => ['required', 'date'],
            'fecha_em' => ['required', 'date', 'after:abre_em'],
        ]);

        $periodo->update($dados);

        return redirect()->route('admin.inqueritos.periodos.index')->with('success', 'Período atualizado.');
    }

    // Remoção de um período, permitindo a limpeza de janelas de teste ou configurações erradas
    public function destroy(InqueritoPeriodo $periodo): RedirectResponse
    {
        $periodo->delete();

        return redirect()->route('admin.inqueritos.periodos.index')->with('success', 'Período removido.');
    }
}