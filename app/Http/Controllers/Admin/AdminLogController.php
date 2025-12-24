<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAcesso;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Controlador para visualização do histórico de atividades, garantindo a transparência e segurança do sistema
class AdminLogController extends Controller
{
    // Listagem centralizada de logs para auditoria de acessos e alterações de dados
    public function index(Request $request): View
    {
        // Recupera os registos ordenados pelos mais recentes para facilitar a monitorização contínua
        $logsQuery = LogAcesso::with('utilizador')->orderByDesc('data_hora');

        // Filtro por tipo de ação para identificar eventos específicos (ex: criação, edição ou remoção)
        if ($request->filled('acao')) {
            $logsQuery->where('acao', $request->acao);
        }

        // Motor de busca para encontrar atividades por descrição ou dados do utilizador (nome/email)
        if ($request->filled('pesquisa')) {
            $termo = $request->pesquisa;
            $logsQuery->where(function ($query) use ($termo) {
                $query->where('descricao', 'like', "%{$termo}%")
                    ->orWhereHas('utilizador', function ($userQuery) use ($termo) {
                        $userQuery->where('nome', 'like', "%{$termo}%")
                            ->orWhere('email', 'like', "%{$termo}%");
                    });
            });
        }

        // Filtro temporal para análise de eventos em períodos específicos de recolha de dados
        if ($request->filled('inicio')) {
            $logsQuery->whereDate('data_hora', '>=', $request->inicio);
        }

        if ($request->filled('fim')) {
            $logsQuery->whereDate('data_hora', '<=', $request->fim);
        }

        // Paginação densa para permitir a análise eficiente de grandes fluxos de informação
        $logs = $logsQuery->paginate(20)->withQueryString();

        // Extração de ações únicas da base de dados para preencher os filtros da interface
        $acoesDisponiveis = LogAcesso::select('acao')
            ->distinct()
            ->orderBy('acao')
            ->pluck('acao');

        return view('admin.logs.index', [
            'logs' => $logs,
            'acoesDisponiveis' => $acoesDisponiveis,
            'filtros' => $request->only(['acao', 'pesquisa', 'inicio', 'fim']),
        ]);
    }
}