<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo que armazena as mensagens trocadas no suporte técnico (Objetivo 17)
class TicketMensagem extends Model
{
    use HasFactory;

    // Tabela que guarda o histórico de conversação do suporte
    protected $table = 'ticket_mensagens';

    // Atributos fundamentais para o registo da comunicação
    protected $fillable = [
        'ticket_id', // Ligação ao processo de suporte principal
        'autor_id',  // Identificação de quem escreveu a mensagem (User)
        'mensagem',  // Conteúdo textual da dúvida ou esclarecimento
    ];

    /**
     * Relação: Uma mensagem pertence a um Ticket de Suporte.
     * Permite agrupar toda a conversa cronologicamente dentro de um pedido de ajuda.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketSuporte::class, 'ticket_id');
    }

    /**
     * Relação: Uma mensagem é escrita por um Utilizador.
     * Crucial para a auditabilidade (Objetivo 4), permitindo distinguir respostas do suporte de questões do utilizador.
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
}