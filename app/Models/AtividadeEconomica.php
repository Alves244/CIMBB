<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtividadeEconomica extends Model
{
    use HasFactory;

    protected $table = 'atividade_economicas';

    /**
     * Ação obrigatória: Adicionar os campos que podem ser preenchidos.
     */
    protected $fillable = [
        'familia_id',
        'tipo',
        'setor_id',
        'descricao',
    ];


    /* --- Relações --- */

    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }

    // Corresponde a $atividade->setorAtividade
    public function setorAtividade(): BelongsTo
    {
        return $this->belongsTo(SetorAtividade::class, 'setor_id');
    }
}