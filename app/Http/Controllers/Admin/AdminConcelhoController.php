<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concelho;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controlador para gestão administrativa de concelhos.
 */
class AdminConcelhoController extends Controller
{
    // Listagem paginada de concelhos com contagem de freguesias associadas
    public function index(): View
    {
        // Consulta dos concelhos com contagem de freguesias
        $concelhos = Concelho::withCount('freguesias')
            ->orderBy('nome')
            ->paginate(10);

        return view('admin.concelhos.index', compact('concelhos'));
    }

    // Armazenamento de um novo concelho após validação
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createConcelho', [
            'nome' => ['required', 'string', 'max:100', 'unique:concelhos,nome'],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $concelho = Concelho::create($data);
        
        // Registo da criação no histórico para rastreabilidade
        AuditLogger::log('admin_concelho_create', 'Criou o concelho '.$concelho->nome.'.');

        return back()->with('success', 'Concelho criado com sucesso.');
    }

    // Atualização de um concelho existente após validação
    public function update(Request $request, Concelho $concelho): RedirectResponse
    {
        // Validação dos dados de entrada
        $data = $request->validateWithBag('editConcelho', [
            'nome' => ['required', 'string', 'max:100', Rule::unique('concelhos', 'nome')->ignore($concelho->id)],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $concelho->update($data);
        
        // Registo da atualização no histórico para rastreabilidade
        AuditLogger::log('admin_concelho_update', 'Atualizou o concelho '.$concelho->nome.'.');

        return back()->with('success', 'Concelho atualizado com sucesso.');
    }

    // Remoção de um concelho, se não houver freguesias associadas
    public function destroy(Concelho $concelho): RedirectResponse
    {
        // Verifica vínculos com freguesias antes de permitir a remoção
        if ($concelho->freguesias()->exists()) {
            return back()->with('error', 'Não pode remover um concelho com freguesias associadas.');
        }

        $nome = $concelho->nome;
        $concelho->delete();
        
        // Registo da remoção no histórico para rastreabilidade
        AuditLogger::log('admin_concelho_delete', 'Removeu o concelho '.$nome.'.');

        return back()->with('success', 'Concelho removido com sucesso.');
    }
}