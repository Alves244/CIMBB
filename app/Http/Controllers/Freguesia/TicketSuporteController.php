<?php

namespace App\Http\Controllers\Freguesia;

use App\Http\Controllers\Controller;
use App\Models\TicketSuporte; // O nosso Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Para gerar o código do ticket

class TicketSuporteController extends Controller
{
    /**
     * Mostra a lista de tickets submetidos pelo utilizador.
     */
    public function index()
    {
        $meusTickets = TicketSuporte::where('utilizador_id', Auth::id())
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10); // Paginação

        // Aponta para a sua nova pasta 'ticket'
        return view('freguesia.ticket.index', compact('meusTickets'));
    }

    /**
     * Mostra o formulário para criar um novo ticket.
     */
    public function create()
    {
        // Aponta para a sua nova pasta 'ticket'
        return view('freguesia.ticket.create');
    }

    /**
     * Guarda o novo ticket na base de dados.
     */
    public function store(Request $request)
    {
        // 1. Validar os dados do formulário
        $dadosValidados = $request->validate([
            'assunto' => 'required|string|max:200',
            'categoria' => 'required|in:duvida,erro,sugestao,outro',
            'descricao' => 'required|string|max:4000',
            'anexo' => 'nullable|file|mimes:pdf,jpg,png,zip|max:2048', // Max 2MB
        ]);

        // 2. Tratar do upload do ficheiro (se existir)
        $caminhoAnexo = null;
        if ($request->hasFile('anexo')) {
            // Lembre-se de correr 'php artisan storage:link'
            $caminhoAnexo = $request->file('anexo')->store('anexos_suporte', 'public');
        }

        // 3. Gerar um código único para o ticket
        $codigoTicket = 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // 4. Criar o ticket na BD
        try {
            TicketSuporte::create([
                'utilizador_id' => Auth::id(),
                'codigo' => $codigoTicket,
                'assunto' => $dadosValidados['assunto'],
                'categoria' => $dadosValidados['categoria'],
                'descricao' => $dadosValidados['descricao'],
                'anexo' => $caminhoAnexo,
                'estado' => 'aberto', // O estado inicial é sempre 'aberto'
            ]);
            
            // Redireciona para a nova rota 'freguesia.suporte.index'
            return redirect()->route('freguesia.suporte.index')
                             ->with('success', 'Ticket de suporte ('.$codigoTicket.') enviado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao enviar o ticket: '.$e->getMessage());
        }
    }

    /**
     * Mostra os detalhes de um ticket específico (e a resposta do admin).
     */
    public function show(TicketSuporte $ticket)
    {
        // 1. Verificar se o ticket pertence ao utilizador
        if ($ticket->utilizador_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        // 2. Carregar a relação 'administrador' (para vermos o nome de quem respondeu)
        $ticket->load('administrador');

        // 3. Aponta para a sua nova pasta 'ticket'
        return view('freguesia.ticket.show', compact('ticket'));
    }
}