<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\TicketMensagem;

use App\Models\TicketSuporte;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;



class AdminTicketController extends Controller

{

    /**

     * Lista todos os tickets do sistema (de todas as freguesias).

     */

    public function index(Request $request)

    {

        $ticketsQuery = TicketSuporte::with('utilizador.freguesia');

        if ($request->filled('estado')) {

            $estadoFiltro = $request->estado === 'respondido' ? 'respondido' : 'em_processamento';

            $ticketsQuery->where('estado', $estadoFiltro);

        }

        $tickets = $ticketsQuery

            ->orderByRaw("FIELD(estado, 'em_processamento', 'respondido', 'aberto', 'resolvido', 'fechado')")

            ->orderBy('created_at', 'desc')

            ->paginate(15)

            ->appends(['estado' => $request->estado]);



        return view('admin.tickets.index', compact('tickets'));

    }



    /**

     * Mostra os detalhes de um ticket específico para o Admin responder.

     */

    public function show(TicketSuporte $ticket)

    {

        // Carrega os dados do autor, administrador e histórico de mensagens

        $ticket->load(['utilizador.freguesia', 'administrador', 'mensagens.autor']);

        

        return view('admin.tickets.show', compact('ticket'));

    }



    /**

     * Grava a resposta do Admin e atualiza o estado do ticket.

     */

    public function reply(Request $request, TicketSuporte $ticket)

    {

        // 1. Validação

        $request->validate([

            'mensagem' => 'required|string|max:4000',

            'estado' => 'nullable|in:respondido,resolvido,fechado'

        ]);



        TicketMensagem::create([

            'ticket_id' => $ticket->id,

            'autor_id' => Auth::id(),

            'mensagem' => $request->mensagem,

        ]);



        // 2. Atualizar o Ticket

        $novoEstado = $request->estado ?? 'respondido';

        if (! in_array($novoEstado, ['respondido', 'resolvido', 'fechado'])) {

            $novoEstado = 'respondido';

        }



        $ticket->update([

            'resposta_admin' => $request->mensagem,

            'estado' => $novoEstado,

            'administrador_id' => Auth::id(), // Regista quem respondeu (o admin atual)

            'data_resposta' => now(),

        ]);

        AuditLogger::log('ticket_reply', 'Respondeu ao ticket '.$ticket->codigo.' para '.$ticket->utilizador->nome.'.');



        return redirect()->route('admin.tickets.index')

                         ->with('success', 'O ticket ' . $ticket->codigo . ' foi respondido com sucesso.');

    }

}