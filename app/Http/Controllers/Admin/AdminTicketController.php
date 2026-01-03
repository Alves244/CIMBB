<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketMensagem;
use App\Models\TicketSuporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

/**
 * Controlador para gestão administrativa de tickets de suporte.
 */
class AdminTicketController extends Controller
{
    // Listagem paginada de tickets com filtros e ordenação personalizados
    public function index(Request $request)
    {
        // Consulta base com relacionamentos necessários
        $ticketsQuery = TicketSuporte::with('utilizador.freguesia');

        // Filtro por estado do ticket para gestão eficiente do suporte
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';
            $ticketsQuery->where('estado', $estadoFiltro);
        }

        // Ordenação personalizada para priorizar tickets em estados críticos
        $tickets = $ticketsQuery
            ->orderByRaw("FIELD(estado, 'em_processamento', 'respondido', 'aberto', 'resolvido', 'fechado')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['estado' => $request->estado]);

        return view('admin.tickets.index', compact('tickets'));
    }

    // Exibição detalhada de um ticket específico com contexto administrativo
    public function show(TicketSuporte $ticket)
    {
        // Carregamento dos relacionamentos necessários para a visualização completa do ticket
        $ticket->load(['utilizador.freguesia', 'administrador', 'mensagens.autor']);
        
        return view('admin.tickets.show', compact('ticket'));
    }

    // Resposta a um ticket de suporte com validação e atualização de estado
    public function reply(Request $request, TicketSuporte $ticket)
    {
        // Validação dos dados de entrada
        $request->validate([
            'mensagem' => 'required|string|max:4000',
            'estado' => 'nullable|in:respondido,resolvido,fechado'
        ]);

        // Criação da nova mensagem associada ao ticket
        TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'autor_id' => Auth::id(),
            'mensagem' => $request->mensagem,
        ]);

        // Determinação do novo estado do ticket com fallback para 'respondido'
        $novoEstado = $request->estado ?? 'respondido';
        if (! in_array($novoEstado, ['respondido', 'resolvido', 'fechado'])) {
            $novoEstado = 'respondido';
        }

        // Atualização do ticket com a resposta do administrador
        $ticket->update([
            'resposta_admin' => $request->mensagem,
            'estado' => $novoEstado,
            'administrador_id' => Auth::id(),
            'data_resposta' => now(),
        ]);

        // Registo da ação no histórico para rastreabilidade
        AuditLogger::log('ticket_reply', 'Respondeu ao ticket '.$ticket->codigo.' para '.$ticket->utilizador->nome.'.');

        return redirect()->route('admin.tickets.index')
                         ->with('success', 'O ticket ' . $ticket->codigo . ' foi respondido com sucesso.');
    }
}