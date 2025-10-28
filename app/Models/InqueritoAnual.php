<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InqueritoAnual extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'inquerito_anuals'
    protected $table = 'inquerito_anuals';

    // Indica que não há timestamps created_at/updated_at nesta tabela (tem data_preenchimento)
    public $timestamps = false;

    /**
     * Define a relação inversa: Um Inquérito Anual pertence a uma Família.
     * (Relação H no ER [cite: 525])
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }
}