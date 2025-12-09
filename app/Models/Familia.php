<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    use HasFactory;

    protected $table = 'familias';

    /**
     * Os atributos que podem ser preenchidos em massa (mass assignable).
     */
    protected $fillable = [
        'codigo',
        'ano_instalacao',
        'nacionalidade',
        'freguesia_id',
        'tipologia_habitacao',
        'tipologia_propriedade',
        'localizacao_tipo',
        'localizacao_detalhe',
        'condicao_alojamento',
        'inscrito_centro_saude',
        'inscrito_escola',
        'necessidades_apoio',
        'observacoes',
        'utilizador_registo_id',
    ];

    protected $casts = [
        'inscrito_centro_saude' => 'boolean',
        'necessidades_apoio' => 'array',
    ];


    /* --- Relações --- */

    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }

    public function utilizadorRegisto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_registo_id');
    }

    public function agregadoFamiliar(): HasOne
    {
        return $this->hasOne(AgregadoFamiliar::class);
    }

    public function atividadesEconomicas(): HasMany
    {
        return $this->hasMany(AtividadeEconomica::class);
    }
}