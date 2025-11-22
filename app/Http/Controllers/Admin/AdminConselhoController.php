<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conselho;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminConselhoController extends Controller
{
    public function index(): View
    {
        $conselhos = Conselho::withCount('freguesias')
            ->orderBy('nome')
            ->paginate(10);

        return view('admin.concelhos.index', compact('conselhos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createConselho', [
            'nome' => ['required', 'string', 'max:100', 'unique:conselhos,nome'],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $conselho = Conselho::create($data);
        AuditLogger::log('admin_conselho_create', 'Criou o conselho '.$conselho->nome.'.');

        return back()->with('success', 'Conselho criado com sucesso.');
    }

    public function update(Request $request, Conselho $conselho): RedirectResponse
    {
        $data = $request->validateWithBag('editConselho', [
            'nome' => ['required', 'string', 'max:100', Rule::unique('conselhos', 'nome')->ignore($conselho->id)],
            'codigo' => ['nullable', 'string', 'max:10'],
        ]);

        $conselho->update($data);
        AuditLogger::log('admin_conselho_update', 'Atualizou o conselho '.$conselho->nome.'.');

        return back()->with('success', 'Conselho atualizado com sucesso.');
    }

    public function destroy(Conselho $conselho): RedirectResponse
    {
        if ($conselho->freguesias()->exists()) {
            return back()->with('error', 'Não pode remover um conselho com freguesias associadas.');
        }

        $nome = $conselho->nome;
        $conselho->delete();
        AuditLogger::log('admin_conselho_delete', 'Removeu o conselho '.$nome.'.');

        return back()->with('success', 'Conselho removido com sucesso.');
    }
}
