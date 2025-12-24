<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concelho;
use App\Models\Freguesia;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Gestão das freguesias para monitorização detalhada do impacto local nos diversos territórios [cite: 14, 22]
class AdminFreguesiaController extends Controller
{
    // Listagem com suporte a filtros geográficos e métricas de utilizadores e famílias [cite: 21]
    public function index(Request $request): View
    {
        $concelhoId = $request->input('concelho_id');

        // Carregamento otimizado com contagens para análise qualitativa e quantitativa [cite: 15, 21]
        $freguesiasQuery = Freguesia::with('concelho')
            ->withCount(['users', 'familias'])
            ->orderBy('nome');

        // Aplicação do filtro por concelho para análise de fluxos populacionais específicos [cite: 11]
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

    // Criação de novos registos garantindo que pertencem a um concelho válido da CIMBB [cite: 14]
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('createFreguesia', [
            'nome' => ['required', 'string', 'max:100'],
            'codigo' => ['nullable', 'string', 'max:10'],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $freguesia = Freguesia::create($data);
        
        // Registo de auditoria para salvaguardar a segurança das operações administrativas [cite: 23]
        AuditLogger::log('admin_freguesia_create', 'Criou a freguesia '.$freguesia->nome.'.');

        return back()->with('success', 'Freguesia criada com sucesso.');
    }

    // Atualização de dados da freguesia mantendo a integridade referencial
    public function update(Request $request, Freguesia $freguesia): RedirectResponse
    {
        $data = $request->validateWithBag('editFreguesia', [
            'nome' => ['required', 'string', 'max:100'],
            'codigo' => ['nullable', 'string', 'max:10'],
            'concelho_id' => ['required', 'exists:concelhos,id'],
        ]);

        $freguesia->update($data);
        
        // Histórico de alterações para controlo dos administradores e stakeholders [cite: 17, 23]
        AuditLogger::log('admin_freguesia_update', 'Atualizou a freguesia '.$freguesia->nome.'.');

        return back()->with('success', 'Freguesia atualizada com sucesso.');
    }

    // Remoção protegida para evitar perda de dados históricos de residentes [cite: 10, 11]
    public function destroy(Freguesia $freguesia): RedirectResponse
    {
        // Verifica se existem utilizadores ou agregados familiares antes de permitir a eliminação
        if ($freguesia->users()->exists()) {
            return back()->with('error', 'Não pode remover uma freguesia com utilizadores associados.');
        }

        if ($freguesia->familias()->exists()) {
            return back()->with('error', 'Não pode remover uma freguesia com famílias associadas.');
        }

        $nome = $freguesia->nome;
        $freguesia->delete();
        
        // Audit log para rastreabilidade de remoções no portal [cite: 23]
        AuditLogger::log('admin_freguesia_delete', 'Removeu a freguesia '.$nome.'.');

        return back()->with('success', 'Freguesia removida com sucesso.');
    }
}