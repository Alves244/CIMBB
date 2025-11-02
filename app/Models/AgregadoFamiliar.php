<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgregadoFamiliar extends Model
{
    use HasFactory;

    protected $table = 'agregado_familiars';
    public $timestamps = false; // (Já tinhas isto)

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'familia_id', // Embora estejamos a usar a relação, é boa prática tê-lo
        'adultos_laboral',
        'adultos_65_mais',
        'criancas',
        // 'total_membros' não é necessário porque é gerado pelo MySQL
    ];


    /**
     * Define a relação inversa: Um Agregado Familiar pertence a uma Família.
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }
}