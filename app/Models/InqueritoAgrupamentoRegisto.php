<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InqueritoAgrupamentoRegisto extends Model
{
    use HasFactory;

    protected $table = 'inquerito_agrupamento_registos';

    protected $fillable = [
        'inquerito_id',
        'nacionalidade',
        'ano_letivo',
        'estabelecimento_id',
        'concelho_id',
        'nivel_ensino',
        'numero_alunos',
    ];

    public function inquerito(): BelongsTo
    {
        return $this->belongsTo(InqueritoAgrupamento::class, 'inquerito_id');
    }

    public function estabelecimento(): BelongsTo
    {
        return $this->belongsTo(EstabelecimentoEnsino::class, 'estabelecimento_id');
    }

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class, 'concelho_id');
    }
}
