<?php

namespace App\Http\Controllers\Agrupamento;

use App\Http\Controllers\Controller;
use App\Models\TicketMensagem;
use App\Models\TicketSuporte;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controlador para gestão de tickets de suporte pelos agrupamentos.
 */
class TicketSuporteController extends Controller
{
    // Listagem dos tickets de suporte do agrupamento autenticado
    public function index(Request $request)
    {
        $ticketsQuery = TicketSuporte::where('utilizador_id', Auth::id());
        // Filtro por estado do ticket para facilitar a gestão pelo agrupamento
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';
            $ticketsQuery->where('estado', $estadoFiltro);
        }

        // Ordenação por data de criação para exibir os tickets mais recentes primeiro
        $meusTickets = $ticketsQuery
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends(['estado' => $request->estado]);

        return view('agrupamento.ticket.index', compact('meusTickets'));
    }

    // Formulário para criação de um novo ticket de suporte
    public function create()
    {
        return view('agrupamento.ticket.create');
    }

    // Armazenamento de um novo ticket de suporte com validação
    public function store(Request $request)
    {
        // Validação dos dados de entrada
        $dadosValidados = $request->validate([
            'assunto' => 'required|string|max:200',
            'categoria' => 'required|in:duvida,erro,sugestao,outro',
            'descricao' => 'required|string|max:4000',
            'anexo' => 'nullable|file|mimes:pdf,jpg,png,zip|max:2048',
        ]);

        // Processamento do anexo se fornecido
        $caminhoAnexo = null;
        if ($request->hasFile('anexo')) {
            $caminhoAnexo = $request->file('anexo')->store('anexos_suporte', 'public');
        }

        $codigoTicket = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Criação do ticket e da mensagem inicial dentro de um bloco try-catch para tratamento de erros
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

            TicketMensagem::create([
                'ticket_id' => $ticket->id,
                'autor_id' => Auth::id(),
                'mensagem' => $dadosValidados['descricao'],
            ]);

            AuditLogger::log('ticket_create', 'Agrupamento abriu o ticket '.$ticket->codigo.'.');

            return redirect()->route('agrupamento.suporte.index')
                ->with('success', 'Ticket de suporte ('.$codigoTicket.') enviado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao enviar o ticket: '.$e->getMessage());
        }
    }

    // Exibição detalhada de um ticket específico
    public function show(TicketSuporte $ticket)
    {
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $ticket->load(['administrador', 'mensagens.autor']);
        $podeResponder = $ticket->estado !== 'fechado';

        return view('agrupamento.ticket.show', compact('ticket', 'podeResponder'));
    }

    // Armazenamento de uma nova mensagem no ticket de suporte
    public function storeMessage(Request $request, TicketSuporte $ticket)
    {
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $dados = $request->validate([
            'mensagem' => 'required|string|max:4000',
        ]);

        TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'autor_id' => Auth::id(),
            'mensagem' => $dados['mensagem'],
        ]);

        $ticket->update([
            'estado' => 'em_processamento',
            'resposta_admin' => null,
            'administrador_id' => null,
            'data_resposta' => null,
        ]);

        AuditLogger::log('ticket_message', 'Agrupamento respondeu ao ticket '.$ticket->codigo.'.');

        return redirect()->route('agrupamento.suporte.show', $ticket)
            ->with('success', 'Mensagem enviada para o suporte.');
    }
}
