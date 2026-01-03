<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAcesso;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para gestão administrativa de logs de acesso e ações do sistema.
 */
class AdminLogController extends Controller
{
    // Listagem paginada de logs com filtros avançados
    public function index(Request $request): View
    {
        // Consulta base com relacionamentos necessários
        $logsQuery = LogAcesso::with('utilizador')->orderByDesc('data_hora');

        // Filtro por ação específica para análise detalhada
        if ($request->filled('acao')) {
            $logsQuery->where('acao', $request->acao);
        }

        // Filtro de pesquisa textual para investigação de eventos específicos
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

        // Filtros de intervalo de datas para delimitar o período de análise
        if ($request->filled('inicio')) {
            $logsQuery->whereDate('data_hora', '>=', $request->inicio);
        }

        if ($request->filled('fim')) {
            $logsQuery->whereDate('data_hora', '<=', $request->fim);
        }

        // Paginação dos resultados com preservação dos parâmetros de consulta
        $logs = $logsQuery->paginate(20)->withQueryString();

        // Obtenção das ações distintas para o filtro dropdown
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