<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InqueritoPeriodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controlador para gestão administrativa dos períodos de inquérito.
 */
class InqueritoPeriodoController extends Controller
{
    // Listagem de todos os períodos de inquérito configurados
    public function index(): View
    {
        // Consulta ordenada por ano e tipo de inquérito
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

    // Criação de um novo período de inquérito com validação rigorosa
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados de entrada
        $dados = $request->validate([
            'tipo' => ['required', Rule::in([InqueritoPeriodo::TIPO_FREGUESIA, InqueritoPeriodo::TIPO_AGRUPAMENTO])],
            'ano' => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('inquerito_periodos')->where(fn ($q) => $q->where('tipo', $request->input('tipo')))],
            'abre_em' => ['required', 'date'],
            'fecha_em' => ['required', 'date', 'after:abre_em'],
        ]);

        // Regista o utilizador que criou o período para fins de auditoria
        $dados['criado_por'] = $request->user()->id;

        InqueritoPeriodo::create($dados);

        return redirect()->route('admin.inqueritos.periodos.index')->with('success', 'Período criado com sucesso.');
    }

    // Atualização de um período existente com validação
    public function update(Request $request, InqueritoPeriodo $periodo): RedirectResponse
    {
        $dados = $request->validate([
            'abre_em' => ['required', 'date'],
            'fecha_em' => ['required', 'date', 'after:abre_em'],
        ]);

        $periodo->update($dados);

        return redirect()->route('admin.inqueritos.periodos.index')->with('success', 'Período atualizado.');
    }

    // Remoção de um período de inquérito
    public function destroy(InqueritoPeriodo $periodo): RedirectResponse
    {
        $periodo->delete();

        return redirect()->route('admin.inqueritos.periodos.index')->with('success', 'Período removido.');
    }
}