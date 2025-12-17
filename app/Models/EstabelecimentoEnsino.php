<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstabelecimentoEnsino extends Model
{
    use HasFactory;

    protected $table = 'estabelecimentos_ensino';

    protected $fillable = [
        'nome',
        'codigo',
        'concelho_id',
        'agrupamento_id',
    ];

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class);
    }

    public function agrupamento(): BelongsTo
    {
        return $this->belongsTo(Agrupamento::class);
    }

    public function registos(): HasMany
    {
        return $this->hasMany(InqueritoAgrupamentoRegisto::class, 'estabelecimento_id');
    }
}
