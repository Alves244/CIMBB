<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAcesso;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    public function index(Request $request): View
    {
        $logsQuery = LogAcesso::with('utilizador')->orderByDesc('data_hora');

        if ($request->filled('acao')) {
            $logsQuery->where('acao', $request->acao);
        }

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

        if ($request->filled('inicio')) {
            $logsQuery->whereDate('data_hora', '>=', $request->inicio);
        }

        if ($request->filled('fim')) {
            $logsQuery->whereDate('data_hora', '<=', $request->fim);
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

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
