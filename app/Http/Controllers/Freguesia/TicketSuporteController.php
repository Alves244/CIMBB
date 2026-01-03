<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\TicketMensagem;
use App\Models\TicketSuporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\AuditLogger;

/**
 * Controlador para gestão de tickets de suporte pela freguesia.
 */
class TicketSuporteController extends Controller
{
    // Listagem dos tickets de suporte da freguesia autenticada
    public function index(Request $request)
    {
        $ticketsQuery = TicketSuporte::where('utilizador_id', Auth::id());
        // Filtro por estado do ticket para facilitar a gestão pela freguesia
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';
            $ticketsQuery->where('estado', $estadoFiltro);
        }
        // Ordenação por data de criação para exibir os tickets mais recentes primeiro
        $meusTickets = $ticketsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['estado' => $request->estado]);

        // Aponta para a sua nova pasta 'ticket'
        return view('freguesia.ticket.index', compact('meusTickets'));
    }

    // Formulário para criação de um novo ticket de suporte
    public function create()
    {
        // Aponta para a sua nova pasta 'ticket'
        return view('freguesia.ticket.create');
    }

    // Armazenamento de um novo ticket de suporte com validação
    public function store(Request $request)
    {
        // Validar os dados de entrada
        $dadosValidados = $request->validate([
            'assunto' => 'required|string|max:200',
            'categoria' => 'required|in:duvida,erro,sugestao,outro',
            'descricao' => 'required|string|max:4000',
            'anexo' => 'nullable|file|mimes:pdf,jpg,png,zip|max:2048', // Max 2MB
        ]);

        // Processar o anexo se fornecido
        $caminhoAnexo = null;
        if ($request->hasFile('anexo')) {
            
            $caminhoAnexo = $request->file('anexo')->store('anexos_suporte', 'public');
        }

        // Gerar um código único para o ticket
        $codigoTicket = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Tenta criar o ticket e a mensagem inicial
        try {
            $ticket = TicketSuporte::create([
                'utilizador_id' => Auth::id(),
                'codigo' => $codigoTicket,
                'assunto' => $dadosValidados['assunto'],
                'categoria' => $dadosValidados['categoria'],
                'descricao' => $dadosValidados['descricao'],
                'anexo' => $caminhoAnexo,
                'estado' => 'em_processamento',
            ]);
            // Cria a mensagem inicial do ticket
            TicketMensagem::create([
                'ticket_id' => $ticket->id,
                'autor_id' => Auth::id(),
                'mensagem' => $dadosValidados['descricao'],
            ]);
            // Log de auditoria
            AuditLogger::log('ticket_create', 'Criou o ticket '.$ticket->codigo.'.');
            
            // Redireciona para a lista de tickets com mensagem de sucesso
            return redirect()->route('freguesia.suporte.index')
                             ->with('success', 'Ticket de suporte ('.$codigoTicket.') enviado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao enviar o ticket: '.$e->getMessage());
        }
    }

    // Exibição detalhada de um ticket específico
    public function show(TicketSuporte $ticket)
    {
        // Verifica se o ticket pertence ao utilizador autenticado
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega as relações necessárias: administrador e mensagens com autores
        $ticket->load(['administrador', 'mensagens.autor']);

        // Verifica se o ticket pode receber respostas (não está fechado)
        $podeResponder = $ticket->estado !== 'fechado';

        return view('freguesia.ticket.show', compact('ticket', 'podeResponder'));
    }

    // Armazenamento de uma nova mensagem no ticket de suporte
    public function storeMessage(Request $request, TicketSuporte $ticket)
    {
        // Verifica se o ticket pertence ao utilizador autenticado
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }
        // Validação da mensagem
        $dados = $request->validate([
            'mensagem' => 'required|string|max:4000',
        ]);
        // Criação da nova mensagem associada ao ticket
        TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'autor_id' => Auth::id(),
            'mensagem' => $dados['mensagem'],
        ]);
        // Atualiza o estado do ticket para "em processamento" caso tenha sido respondido
        $ticket->update([
            'estado' => 'em_processamento',
            'resposta_admin' => null,
            'administrador_id' => null,
            'data_resposta' => null,
        ]);
        // Log de auditoria
        AuditLogger::log('ticket_message', 'Enviou mensagem no ticket '.$ticket->codigo.'.');
        // Redireciona de volta para a visualização do ticket com mensagem de sucesso
        return redirect()->route('freguesia.suporte.show', $ticket)
                         ->with('success', 'Mensagem enviada para o suporte.');
    }
}