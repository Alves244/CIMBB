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
        'utilizador_registo_id',
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

    // Corresponde a $familia->atividadesEconomicas
    public function atividadesEconomicas(): HasMany
    {
        return $this->hasMany(AtividadeEconomica::class);
    }

    // Relação para o Inquérito (se for por família, o que não é o caso)
    // public function inqueritosAnuais(): HasMany
    // {
    //     return $this->hasMany(InqueritoFreguesia::class); //
    // }
}