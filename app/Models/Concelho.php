<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que representa a divisão municipal do território (Objetivo 3)
class Concelho extends Model
{
    use HasFactory;

    // Atributos fundamentais para a identificação geográfica
    protected $fillable = [
        'nome',
        'codigo',
    ];

    /**
     * Relação com as Freguesias.
     * Permite a descida na granularidade dos dados: do Município para a Localidade.
     */
    public function freguesias(): HasMany
    {
        return $this->hasMany(Freguesia::class);
    }
}