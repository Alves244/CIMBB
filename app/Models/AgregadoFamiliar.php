<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgregadoFamiliar extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'agregado_familiars'
    protected $table = 'agregado_familiars';

    // Indica que não há timestamps created_at/updated_at nesta tabela
    public $timestamps = false;

    /**
     * Define a relação inversa: Um Agregado Familiar pertence a uma Família.
     * (Relação E no ER [cite: 522])
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }
}