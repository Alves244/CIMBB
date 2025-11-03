<?php

namespace App\Models;

// CORRIGIDO: Usar barras invertidas '\'
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Adiciona esta linha

class InqueritoFreguesia extends Model
{
    use HasFactory;

    // Adiciona a propriedade $fillable (para evitar erros ao guardar)
    protected $fillable = [
        'freguesia_id',
        'utilizador_id',
        'ano',
        'escala_integracao',
        'aspectos_positivos',
        'aspectos_negativos',
        'satisfacao_global',
        'sugestoes',
    ];

    // Relação (para sabermos a que freguesia pertence)
    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }
}