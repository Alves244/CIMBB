<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concelho;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// Gestão administrativa dos concelhos para a segmentação territorial da Beira Baixa [cite: 9, 14]
class AdminConcelhoController extends Controller
{
    // Listagem paginada com contagem de freguesias para apoio à decisão [cite: 11, 21]
    public function index(): View
    {
        $concelhos = Concelho::withCount('freguesias')
            ->orderBy('nome')
            ->paginate(10);

        return view('admin.concelhos.index', compact('concelhos'));
    }

    // Registo de novos concelhos garantindo a consistência dos dados geográficos [cite: 15]
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createConcelho', [
            'nome' => ['required', 'string', 'max:100', 'unique:concelhos,nome'],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $concelho = Concelho::create($data);
        
        // Log de auditoria para cumprir os requisitos de segurança do sistema [cite: 23]
        AuditLogger::log('admin_concelho_create', 'Criou o concelho '.$concelho->nome.'.');

        return back()->with('success', 'Concelho criado com sucesso.');
    }

    // Atualização de dados com validação de unicidade excetuando o próprio registo
    public function update(Request $request, Concelho $concelho): RedirectResponse
    {
        $data = $request->validateWithBag('editConcelho', [
            'nome' => ['required', 'string', 'max:100', Rule::unique('concelhos', 'nome')->ignore($concelho->id)],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $concelho->update($data);
        
        // Registo da alteração no histórico para rastreabilidade [cite: 23]
        AuditLogger::log('admin_concelho_update', 'Atualizou o concelho '.$concelho->nome.'.');

        return back()->with('success', 'Concelho atualizado com sucesso.');
    }

    // Remoção física protegida por integridade referencial
    public function destroy(Concelho $concelho): RedirectResponse
    {
        // Impede o delete se houver dependências para manter a qualidade dos dados [cite: 15]
        if ($concelho->freguesias()->exists()) {
            return back()->with('error', 'Não pode remover um concelho com freguesias associadas.');
        }

        $nome = $concelho->nome;
        $concelho->delete();
        
        // Audit log da remoção para controlo administrativo [cite: 14]
        AuditLogger::log('admin_concelho_delete', 'Removeu o concelho '.$nome.'.');

        return back()->with('success', 'Concelho removido com sucesso.');
    }
}