<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agrupamento;
use App\Models\Concelho;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controlador para gestão administrativa de agrupamentos.
 */
class AdminAgrupamentoController extends Controller
{
    // Listagem paginada de agrupamentos com filtros e contadores
    public function index(Request $request): View
    {
        // Filtro por concelho
        $concelhoId = $request->input('concelho_id');

        // Consulta base com contadores e ordenação
        $agrupamentosQuery = Agrupamento::with('concelho')
            ->withCount(['users', 'inqueritos'])
            ->orderBy('nome');

        // Aplica filtro se fornecido
        if ($concelhoId) {
            $agrupamentosQuery->where('concelho_id', $concelhoId);
        }

        $agrupamentos = $agrupamentosQuery->paginate(12)->withQueryString();
        $concelhos = Concelho::orderBy('nome')->get();

        // Renderiza a vista com os dados necessários
        return view('admin.agrupamentos.index', [
            'agrupamentos' => $agrupamentos,
            'concelhos' => $concelhos,
            'concelhoSelecionado' => $concelhoId,
        ]);
    }

    /**
     * Armazenamento de um novo agrupamento após validação.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados de entrada
        $data = $request->validateWithBag('createAgrupamento', [
            'nome' => ['required', 'string', 'max:150', 'unique:agrupamentos,nome'],
            'codigo' => ['nullable', 'string', 'max:20', 'unique:agrupamentos,codigo'],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $agrupamento = Agrupamento::create($data);

        // Log de auditoria para rastrear a criação de novos agrupamentos
        AuditLogger::log('admin_agrupamento_create', 'Criou o agrupamento '.$agrupamento->nome.'.');

        return back()->with('success', 'Agrupamento criado com sucesso.');
    }

    /**
     * Atualização de um agrupamento existente após validação.
     */
    public function update(Request $request, Agrupamento $agrupamento): RedirectResponse
    {
        // Validação dos dados de entrada
        $data = $request->validateWithBag('editAgrupamento', [
            'nome' => ['required', 'string', 'max:150', Rule::unique('agrupamentos', 'nome')->ignore($agrupamento->id)],
            'codigo' => ['nullable', 'string', 'max:20', Rule::unique('agrupamentos', 'codigo')->ignore($agrupamento->id)],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $agrupamento->update($data);

        // Log de auditoria para rastrear atualizações de agrupamentos
        AuditLogger::log('admin_agrupamento_update', 'Atualizou o agrupamento '.$agrupamento->nome.'.');

        return back()->with('success', 'Agrupamento atualizado com sucesso.');
    }

    /**
     * Remoção de um agrupamento, se não houver vínculos existentes.
     */
    public function destroy(Agrupamento $agrupamento): RedirectResponse
    {
        // Verifica vínculos com utilizadores e inquéritos antes de permitir a remoção
        if ($agrupamento->users()->exists()) {
            return back()->with('error', 'Não pode remover um agrupamento com utilizadores associados.');
        }

        if ($agrupamento->inqueritos()->exists()) {
            return back()->with('error', 'Não pode remover um agrupamento com inquéritos associados.');
        }

        $nome = $agrupamento->nome;
        $agrupamento->delete();

        // Log de auditoria para rastrear remoções de agrupamentos
        AuditLogger::log('admin_agrupamento_delete', 'Removeu o agrupamento '.$nome.'.');

        return back()->with('success', 'Agrupamento removido com sucesso.');
    }
}