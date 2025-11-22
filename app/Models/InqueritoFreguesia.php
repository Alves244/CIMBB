<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InqueritoFreguesia extends Model
{
    use HasFactory;

    protected $table = 'inquerito_freguesias';

    /**
     * Os atributos que podem ser preenchidos em massa.
     * (VERSÃO DETALHADA RESTAURADA)
     */
    protected $fillable = [
        'freguesia_id',
        'utilizador_id',
        'ano',
        
        // Perguntas 11-13
        'total_nucleo_urbano',
        'total_aldeia_anexa',
        'total_agroflorestal',
        
        // Pergunta 14 (Restaurado)
        'total_adultos',
        'total_criancas',
        
        // Pergunta 15 (Restaurado)
        'total_propria',
        'total_arrendada',
        
        // Perguntas 16-19 (JSON - Restaurado)
        'total_por_setor_propria',
        'total_por_setor_outrem',

        // Perguntas 20-24
        'escala_integracao',
        'aspectos_positivos',
        'aspectos_negativos',
        'satisfacao_global',
        'sugestoes',
    ];

    /**
     * Define os "casts" para que o Laravel trate o JSON
     * como um array automaticamente. (Restaurado)
     */
    protected $casts = [
        'total_por_setor_propria' => 'array',
        'total_por_setor_outrem' => 'array',
    ];

    /**
     * Relação: Um inquérito pertence a uma Freguesia.
     */
    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }
}