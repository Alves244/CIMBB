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

class AdminAgrupamentoController extends Controller
{
    /**
     * Listagem principal dos agrupamentos.
     * Inclui contagens para análise rápida do fluxo de dados e utilizadores por entidade.
     */
    public function index(Request $request): View
    {
        $concelhoId = $request->input('concelho_id');

        // Carrega relações e contadores para evitar o problema de N+1 queries
        $agrupamentosQuery = Agrupamento::with('concelho')
            ->withCount(['users', 'inqueritos'])
            ->orderBy('nome');

        // Filtro geográfico solicitado pelos stakeholders (CIMBB)
        if ($concelhoId) {
            $agrupamentosQuery->where('concelho_id', $concelhoId);
        }

        $agrupamentos = $agrupamentosQuery->paginate(12)->withQueryString();
        $concelhos = Concelho::orderBy('nome')->get();

        return view('admin.agrupamentos.index', [
            'agrupamentos' => $agrupamentos,
            'concelhos' => $concelhos,
            'concelhoSelecionado' => $concelhoId,
        ]);
    }

    /**
     * Persistência de novos agrupamentos.
     * O código e o nome devem ser únicos para garantir a consistência do sistema.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createAgrupamento', [
            'nome' => ['required', 'string', 'max:150', 'unique:agrupamentos,nome'],
            'codigo' => ['nullable', 'string', 'max:20', 'unique:agrupamentos,codigo'],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $agrupamento = Agrupamento::create($data);

        // Registo de atividade para histórico de segurança
        AuditLogger::log('admin_agrupamento_create', 'Criou o agrupamento '.$agrupamento->nome.'.');

        return back()->with('success', 'Agrupamento criado com sucesso.');
    }

    /**
     * Atualização dos dados do agrupamento.
     * Validamos a unicidade ignorando o registo atual.
     */
    public function update(Request $request, Agrupamento $agrupamento): RedirectResponse
    {
        $data = $request->validateWithBag('editAgrupamento', [
            'nome' => ['required', 'string', 'max:150', Rule::unique('agrupamentos', 'nome')->ignore($agrupamento->id)],
            'codigo' => ['nullable', 'string', 'max:20', Rule::unique('agrupamentos', 'codigo')->ignore($agrupamento->id)],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $agrupamento->update($data);

        // Audit log para rastrear modificações administrativas
        AuditLogger::log('admin_agrupamento_update', 'Atualizou o agrupamento '.$agrupamento->nome.'.');

        return back()->with('success', 'Agrupamento atualizado com sucesso.');
    }

    /**
     * Remoção de agrupamentos.
     * Bloqueia a eliminação se houver dependências (Integridade Referencial).
     */
    public function destroy(Agrupamento $agrupamento): RedirectResponse
    {
        // Impede a eliminação se existirem utilizadores ou inquéritos vinculados
        if ($agrupamento->users()->exists()) {
            return back()->with('error', 'Não pode remover um agrupamento com utilizadores associados.');
        }

        if ($agrupamento->inqueritos()->exists()) {
            return back()->with('error', 'Não pode remover um agrupamento com inquéritos associados.');
        }

        $nome = $agrupamento->nome;
        $agrupamento->delete();

        // Log de remoção para segurança do sistema
        AuditLogger::log('admin_agrupamento_delete', 'Removeu o agrupamento '.$nome.'.');

        return back()->with('success', 'Agrupamento removido com sucesso.');
    }
}