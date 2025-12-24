<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketMensagem;
use App\Models\TicketSuporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

// Gestão de tickets de suporte para apoio aos utilizadores do sistema de monitorização
class AdminTicketController extends Controller
{
    // Listagem global de pedidos de suporte com filtragem por estado e prioridade visual
    public function index(Request $request)
    {
        // Eager loading para obter os dados geográficos do autor do pedido
        $ticketsQuery = TicketSuporte::with('utilizador.freguesia');

        // Filtro dinâmico para separar processos concluídos de processos pendentes
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';
            $ticketsQuery->where('estado', $estadoFiltro);
        }

        // Ordenação personalizada para destacar tickets que requerem atenção imediata
        $tickets = $ticketsQuery
            ->orderByRaw("FIELD(estado, 'em_processamento', 'respondido', 'aberto', 'resolvido', 'fechado')")
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['estado' => $request->estado]);

        return view('admin.tickets.index', compact('tickets'));
    }

    // Exibição detalhada do ticket incluindo o histórico completo de interação
    public function show(TicketSuporte $ticket)
    {
        // Carrega as relações necessárias para o contexto administrativo da resposta
        $ticket->load(['utilizador.freguesia', 'administrador', 'mensagens.autor']);
        
        return view('admin.tickets.show', compact('ticket'));
    }

    // Processamento da resposta do administrador e atualização do ciclo de vida do ticket
    public function reply(Request $request, TicketSuporte $ticket)
    {
        // Validação da mensagem para garantir a qualidade da comunicação oficial
        $request->validate([
            'mensagem' => 'required|string|max:4000',
            'estado' => 'nullable|in:respondido,resolvido,fechado'
        ]);

        // Registo da nova mensagem na thread de suporte
        TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'autor_id' => Auth::id(),
            'mensagem' => $request->mensagem,
        ]);

        // Definição do novo estado com fallback de segurança para 'respondido'
        $novoEstado = $request->estado ?? 'respondido';
        if (! in_array($novoEstado, ['respondido', 'resolvido', 'fechado'])) {
            $novoEstado = 'respondido';
        }

        // Atualização dos metadados do ticket e identificação do admin responsável
        $ticket->update([
            'resposta_admin' => $request->mensagem,
            'estado' => $novoEstado,
            'administrador_id' => Auth::id(),
            'data_resposta' => now(),
        ]);

        // Log de auditoria para rastrear o suporte prestado aos utilizadores do território
        AuditLogger::log('ticket_reply', 'Respondeu ao ticket '.$ticket->codigo.' para '.$ticket->utilizador->nome.'.');

        return redirect()->route('admin.tickets.index')
                         ->with('success', 'O ticket ' . $ticket->codigo . ' foi respondido com sucesso.');
    }
}