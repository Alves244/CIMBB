<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketSuporte extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'ticket_suportes'
    protected $table = 'ticket_suportes';

    protected $fillable = [
        'codigo',
        'utilizador_id',
        'assunto',
        'descricao',
        'estado',
        'resposta_admin',
        'data_resposta',
        'administrador_id',
        'anexo',
        'categoria',
    ];

    protected $casts = [
        'data_resposta' => 'datetime',
    ];

    /**
     * Define a relação inversa: Um Ticket foi criado por um Utilizador (User).
     * (Relação I no ER [cite: 526])
     */
    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }

    /**
     * Define a relação inversa: Um Ticket pode ter sido respondido por um Administrador (User).
     * (Relação I no ER [cite: 526])
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }

    /**
     * Mensagens trocadas dentro do ticket.
     */
    public function mensagens(): HasMany
    {
        return $this->hasMany(TicketMensagem::class, 'ticket_id')->orderBy('created_at');
    }

    /**
     * Define a relação: Um Ticket usa uma Configuração (pode ser mais complexo).
     * (Relação J no ER [cite: 527]) - Esta relação parece conceptual,
     * não há chave estrangeira direta. Pode não ser necessária no Eloquent
     * ou exigir uma relação mais complexa dependendo do uso.
     * Deixaremos comentada por agora.
     */
    // public function configuracao() { ... }

}