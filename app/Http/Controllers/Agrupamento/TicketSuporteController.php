<?php

namespace App\Http\Controllers\Agrupamento;

use App\Http\Controllers\Controller;
use App\Models\TicketMensagem;
use App\Models\TicketSuporte;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Gere a comunicação entre os agrupamentos e a administração para suporte técnico
class TicketSuporteController extends Controller
{
    // Lista os pedidos de suporte efetuados pelo utilizador do agrupamento
    public function index(Request $request)
    {
        // Filtra apenas os tickets pertencentes ao utilizador autenticado por segurança
        $ticketsQuery = TicketSuporte::where('utilizador_id', Auth::id());

        // Aplica filtro por estado (respondido ou em processamento) conforme a submissão
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';
            $ticketsQuery->where('estado', $estadoFiltro);
        }

        // Ordena por data e pagina os resultados para facilitar a consulta no portal
        $meusTickets = $ticketsQuery
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends(['estado' => $request->estado]);

        return view('agrupamento.ticket.index', compact('meusTickets'));
    }

    // Exibe o formulário de criação de um novo ticket de suporte
    public function create()
    {
        return view('agrupamento.ticket.create');
    }

    // Processa a submissão de um novo pedido de suporte
    public function store(Request $request)
    {
        // Valida os campos obrigatórios e o formato dos anexos permitidos
        $dadosValidados = $request->validate([
            'assunto' => 'required|string|max:200',
            'categoria' => 'required|in:duvida,erro,sugestao,outro',
            'descricao' => 'required|string|max:4000',
            'anexo' => 'nullable|file|mimes:pdf,jpg,png,zip|max:2048',
        ]);

        // Gere o armazenamento do anexo no disco público caso exista
        $caminhoAnexo = null;
        if ($request->hasFile('anexo')) {
            $caminhoAnexo = $request->file('anexo')->store('anexos_suporte', 'public');
        }

        // Gera um código único de rastreio para o ticket baseado na data e caracteres aleatórios
        $codigoTicket = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        try {
            // Regista o ticket principal na base de dados
            $ticket = TicketSuporte::create([
                'utilizador_id' => Auth::id(),
                'codigo' => $codigoTicket,
                'assunto' => $dadosValidados['assunto'],
                'categoria' => $dadosValidados['categoria'],
                'descricao' => $dadosValidados['descricao'],
                'anexo' => $caminhoAnexo,
                'estado' => 'em_processamento',
            ]);

            // Cria a mensagem inicial associada ao ticket acabado de abrir
            TicketMensagem::create([
                'ticket_id' => $ticket->id,
                'autor_id' => Auth::id(),
                'mensagem' => $dadosValidados['descricao'],
            ]);

            // Registo de auditoria para monitorizar pedidos de auxílio no sistema
            AuditLogger::log('ticket_create', 'Agrupamento abriu o ticket '.$ticket->codigo.'.');

            return redirect()->route('agrupamento.suporte.index')
                ->with('success', 'Ticket de suporte ('.$codigoTicket.') enviado com sucesso!');
        } catch (\Exception $e) {
            // Retorna ao formulário com os dados inseridos em caso de erro na persistência
            return back()->withInput()->with('error', 'Erro ao enviar o ticket: '.$e->getMessage());
        }
    }

    // Mostra os detalhes e histórico de mensagens de um ticket específico
    public function show(TicketSuporte $ticket)
    {
        // Garante que o utilizador só acede aos seus próprios dados de suporte
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // Carrega relações necessárias para exibir a conversa e o admin responsável
        $ticket->load(['administrador', 'mensagens.autor']);
        $podeResponder = $ticket->estado !== 'fechado';

        return view('agrupamento.ticket.show', compact('ticket', 'podeResponder'));
    }

    // Regista uma nova mensagem do utilizador numa conversa de suporte existente
    public function storeMessage(Request $request, TicketSuporte $ticket)
    {
        // Validação de segurança para confirmar a propriedade do ticket
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $dados = $request->validate([
            'mensagem' => 'required|string|max:4000',
        ]);

        // Adiciona a nova mensagem à thread de suporte
        TicketMensagem::create([
            'ticket_id' => $ticket->id,
            'autor_id' => Auth::id(),
            'mensagem' => $dados['mensagem'],
        ]);

        // Atualiza o estado do ticket para análise da CIMBB e limpa dados da resposta anterior
        $ticket->update([
            'estado' => 'em_processamento',
            'resposta_admin' => null,
            'administrador_id' => null,
            'data_resposta' => null,
        ]);

        // Registo de log para salvaguardar a rastreabilidade das interações
        AuditLogger::log('ticket_message', 'Agrupamento respondeu ao ticket '.$ticket->codigo.'.');

        return redirect()->route('agrupamento.suporte.show', $ticket)
            ->with('success', 'Mensagem enviada para o suporte.');
    }
}