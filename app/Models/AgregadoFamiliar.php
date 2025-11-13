<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgregadoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'agregado_familiars';
    public $timestamps = false; 

    /**
     * Os atributos que podem ser preenchidos em massa.
     * (ATUALIZADO PARA OS 9 NOVOS CAMPOS)
     */
    protected $fillable = [
        'familia_id',
        'adultos_laboral_m',
        'adultos_laboral_f',
        'adultos_laboral_n',
        'adultos_65_mais_m',
        'adultos_65_mais_f',
        'adultos_65_mais_n',
        'criancas_m',
        'criancas_f',
        'criancas_n',
    ];

    /**
     * Define a relação inversa: Um Agregado Familiar pertence a uma Família.
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }
}