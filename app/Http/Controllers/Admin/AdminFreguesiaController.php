<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concelho;
use App\Models\Freguesia;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para gestão administrativa de freguesias.
 */
class AdminFreguesiaController extends Controller
{
    // Listagem paginada de freguesias com filtros e contadores associados
    public function index(Request $request): View
    {
        // Filtro por concelho
        $concelhoId = $request->input('concelho_id');

        // Consulta base com contadores e ordenação alfabética
        $freguesiasQuery = Freguesia::with('concelho')
            ->withCount(['users', 'familias'])
            ->orderBy('nome');

        // Aplica filtro se fornecido
        if ($concelhoId) {
            $freguesiasQuery->where('concelho_id', $concelhoId);
        }

        $freguesias = $freguesiasQuery->paginate(12)->withQueryString();
        $concelhos = Concelho::orderBy('nome')->get();

        return view('admin.freguesias.index', [
            'freguesias' => $freguesias,
            'concelhos' => $concelhos,
            'concelhoSelecionado' => $concelhoId,
        ]);
    }

    // Criação de nova freguesia com validação dos dados
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createFreguesia', [
            'nome' => ['required', 'string', 'max:100'],
            'codigo' => ['nullable', 'string', 'max:10'],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $freguesia = Freguesia::create($data);
        
        // Histórico de criação para auditoria e rastreabilidade
        AuditLogger::log('admin_freguesia_create', 'Criou a freguesia '.$freguesia->nome.'.');

        return back()->with('success', 'Freguesia criada com sucesso.');
    }

    // Atualização de freguesia existente com validação
    public function update(Request $request, Freguesia $freguesia): RedirectResponse
    {
        $data = $request->validateWithBag('editFreguesia', [
            'nome' => ['required', 'string', 'max:100'],
            'codigo' => ['nullable', 'string', 'max:10'],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $freguesia->update($data);
        
        // Registro de atualização no histórico para rastreabilidade
        AuditLogger::log('admin_freguesia_update', 'Atualizou a freguesia '.$freguesia->nome.'.');

        return back()->with('success', 'Freguesia atualizada com sucesso.');
    }

    // Remoção de freguesia com verificações de dependências
    public function destroy(Freguesia $freguesia): RedirectResponse
    {
        // Verifica vínculos antes de permitir a remoção para manter a integridade dos dados
        if ($freguesia->users()->exists()) {
            return back()->with('error', 'Não pode remover uma freguesia com utilizadores associados.');
        }

        if ($freguesia->familias()->exists()) {
            return back()->with('error', 'Não pode remover uma freguesia com famílias associadas.');
        }

        $nome = $freguesia->nome;
        $freguesia->delete();
        
        // Registro de remoção no histórico para rastreabilidade
        AuditLogger::log('admin_freguesia_delete', 'Removeu a freguesia '.$nome.'.');

        return back()->with('success', 'Freguesia removida com sucesso.');
    }
}