<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtividadeEconomica extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'atividade_economicas'
    protected $table = 'atividade_economicas';

    /**
     * Define a relação inversa: Uma Atividade Económica pertence a uma Família.
     * (Relação F no ER [cite: 523])
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }

    /**
     * Define a relação inversa: Uma Atividade Económica pertence a um Setor de Atividade.
     * (Relação G no ER [cite: 524])
     */
    public function setorAtividade(): BelongsTo
    {
        return $this->belongsTo(SetorAtividade::class, 'setor_id');
    }
}