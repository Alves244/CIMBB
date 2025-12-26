<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que representa as unidades educativas do território (Objetivo 14)
class Agrupamento extends Model
{
    use HasFactory;

    // Nome da tabela que armazena os dados dos agrupamentos escolares
    protected $table = 'agrupamentos';

    // Atributos que podem ser preenchidos via formulários ou importações
    protected $fillable = [
        'nome',
        'codigo',      // Código oficial do Ministério da Educação (ex: DGEstE)
        'concelho_id', // Ligação ao concelho para análise territorial (Objetivo 3)
    ];

    /**
     * Relação com o Concelho.
     * Permite agrupar dados escolares por município para relatórios da CIMBB.
     */
    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class);
    }

    /**
     * Relação com Utilizadores.
     * Identifica quais os técnicos de educação autorizados a inserir inquéritos (Objetivo 4).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relação com Inquéritos.
     * Agrega as respostas qualitativas e quantitativas sobre a integração escolar (Objetivo 21).
     */
    public function inqueritos(): HasMany
    {
        return $this->hasMany(InqueritoAgrupamento::class);
    }
}