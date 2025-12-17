<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InqueritoAgrupamento extends Model
{
    use HasFactory;

    protected $table = 'inquerito_agrupamentos';

    protected $fillable = [
        'agrupamento_id',
        'utilizador_id',
        'ano_referencia',
        'total_registos',
        'total_alunos',
        'submetido_em',
    ];

    protected $casts = [
        'submetido_em' => 'datetime',
    ];

    public function agrupamento(): BelongsTo
    {
        return $this->belongsTo(Agrupamento::class);
    }

    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }

    public function registos(): HasMany
    {
        return $this->hasMany(InqueritoAgrupamentoRegisto::class, 'inquerito_id');
    }
}
