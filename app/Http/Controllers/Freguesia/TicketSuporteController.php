<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\TicketMensagem;
use App\Models\TicketSuporte; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; 
use App\Services\AuditLogger;

// Controlador para gestão de tickets de suporte abertos pelas Juntas de Freguesia
class TicketSuporteController extends Controller
{
    // Lista os pedidos de suporte efetuados pela freguesia autenticada
    public function index(Request $request)
    {
        // Garante que o utilizador apenas acede aos seus próprios registos de suporte
        $ticketsQuery = TicketSuporte::where('utilizador_id', Auth::id());

        // Filtro por estado para monitorizar respostas da administração
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';
            $ticketsQuery->where('estado', $estadoFiltro);
        }

        // Ordenação cronológica para facilitar o acompanhamento dos processos ativos
        $meusTickets = $ticketsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['estado' => $request->estado]);

        return view('freguesia.ticket.index', compact('meusTickets'));
    }

    // Apresenta o formulário de criação de nova incidência ou dúvida técnica
    public function create()
    {
        return view('freguesia.ticket.create');
    }

    // Processa a submissão de um novo pedido de suporte no portal
    public function store(Request $request)
    {
        // Validação dos dados para assegurar clareza no reporte do problema
        $dadosValidados = $request->validate([
            'assunto' => 'required|string|max:200',
            'categoria' => 'required|in:duvida,erro,sugestao,outro',
            'descricao' => 'required|string|max:4000',
            'anexo' => 'nullable|file|mimes:pdf,jpg,png,zip|max:2048', 
        ]);

        // Gestão de ficheiros anexos para auxiliar na resolução de erros visuais
        $caminhoAnexo = null;
        if ($request->hasFile('anexo')) {
            $caminhoAnexo = $request->file('anexo')->store('anexos_suporte', 'public');
        }

        // Geração de código único para rastreabilidade administrativa (ex: TKT-20251226-ABCDEF)
        $codigoTicket = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        try {
            // Persistência do ticket principal com estado inicial de processamento
            $ticket = TicketSuporte::create([
                'utilizador_id' => Auth::id(),
                'codigo' => $codigoTicket,
                'assunto' => $dadosValidados['assunto'],
                'categoria' => $dadosValidados['categoria'],
                'descricao' => $dadosValidados['descricao'],
                'anexo' => $caminhoAnexo,
                'estado' => 'em_processamento',
            ]);
            
            // Registo da mensagem inicial na thread de suporte
            TicketMensagem::create([
                'ticket_id' => $ticket->id,
                'autor_id' => Auth::id(),
                'mensagem' => $dadosValidados['descricao'],
            ]);

            // Registo de auditoria para salvaguarda de segurança administrativa
            AuditLogger::log('ticket_create', 'Criou o ticket '.$ticket->codigo.'.');
            
            return redirect()->route('freguesia.suporte.index')
                             ->with('success', 'Ticket de suporte ('.$codigoTicket.') enviado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao enviar o ticket: '.$e->getMessage());
        }
    }

    // Exibe os detalhes e o histórico de mensagens trocadas com a CIMBB
    public function show(TicketSuporte $ticket)
    {
        // Verificação de propriedade para impedir acesso indevido a dados de suporte
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega relações para identificar o administrador e autores das mensagens
        $ticket->load(['administrador', 'mensagens.autor']);

        // Bloqueia novas respostas caso o ticket tenha sido dado como concluído
        $podeResponder = $ticket->state !== 'fechado';

        return view('freguesia.ticket.show', compact('ticket', 'podeResponder'));
    }

    // Permite à freguesia adicionar novas mensagens ou esclarecimentos a um ticket aberto
    public function storeMessage(Request $request, TicketSuporte $ticket)
    {
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $dados = $request->validate([
            'mensagem' => 'required|string|max:4000',
        ]);

        // Grava a nova mensagem na conversa de suporte
        TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'autor_id' => Auth::id(),
            'mensagem' => $dados['mensagem'],
        ]);

        // Reverte o estado para processamento para alertar a administração
        $ticket->update([
            'estado' => 'em_processamento',
            'resposta_admin' => null,
            'administrador_id' => null,
            'data_resposta' => null,
        ]);

        // Log de auditoria para rastrear interações de suporte no portal
        AuditLogger::log('ticket_message', 'Enviou mensagem no ticket '.$ticket->codigo.'.');

        return redirect()->route('freguesia.suporte.show', $ticket)
                             ->with('success', 'Mensagem enviada para o suporte.');
    }
}