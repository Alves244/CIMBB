<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogAcesso extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'log_acessos'
    protected $table = 'log_acessos';

    // Indica que não há timestamps created_at/updated_at (tem data_hora)
    public $timestamps = false;

    /**
     * Define a relação inversa: Um Log de Acesso pertence a um Utilizador (User).
     * (Relação K no ER [cite: 528])
     */
    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }
}