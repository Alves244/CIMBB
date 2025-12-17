<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concelho;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminConcelhoController extends Controller
{
    public function index(): View
    {
        $concelhos = Concelho::withCount('freguesias')
            ->orderBy('nome')
            ->paginate(10);

        return view('admin.concelhos.index', compact('concelhos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createConcelho', [
            'nome' => ['required', 'string', 'max:100', 'unique:concelhos,nome'],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $concelho = Concelho::create($data);
        AuditLogger::log('admin_concelho_create', 'Criou o concelho '.$concelho->nome.'.');

        return back()->with('success', 'Concelho criado com sucesso.');
    }

    public function update(Request $request, Concelho $concelho): RedirectResponse
    {
        $data = $request->validateWithBag('editConcelho', [
            'nome' => ['required', 'string', 'max:100', Rule::unique('concelhos', 'nome')->ignore($concelho->id)],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $concelho->update($data);
        AuditLogger::log('admin_concelho_update', 'Atualizou o concelho '.$concelho->nome.'.');

        return back()->with('success', 'Concelho atualizado com sucesso.');
    }

    public function destroy(Concelho $concelho): RedirectResponse
    {
        if ($concelho->freguesias()->exists()) {
            return back()->with('error', 'Não pode remover um concelho com freguesias associadas.');
        }

        $nome = $concelho->nome;
        $concelho->delete();
        AuditLogger::log('admin_concelho_delete', 'Removeu o concelho '.$nome.'.');

        return back()->with('success', 'Concelho removido com sucesso.');
    }
}
