<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMensagem extends Model
{
    use HasFactory;

    protected $table = 'ticket_mensagens';

    protected $fillable = [
        'ticket_id',
        'autor_id',
        'mensagem',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketSuporte::class, 'ticket_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
}
