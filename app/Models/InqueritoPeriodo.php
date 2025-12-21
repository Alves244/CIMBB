<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class InqueritoPeriodo extends Model
{
    use HasFactory;

    public const TIPO_FREGUESIA = 'freguesia';
    public const TIPO_AGRUPAMENTO = 'agrupamento';

    protected $table = 'inquerito_periodos';

    protected $fillable = [
        'tipo',
        'ano',
        'abre_em',
        'fecha_em',
        'criado_por',
    ];

    protected $casts = [
        'abre_em' => 'datetime',
        'fecha_em' => 'datetime',
    ];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeAtivos($query)
    {
        $agora = now();

        return $query->where('abre_em', '<=', $agora)
            ->where('fecha_em', '>=', $agora);
    }

    public function estaAberto(): bool
    {
        $agora = now();

        return $this->abre_em instanceof CarbonInterface
            && $this->fecha_em instanceof CarbonInterface
            && $this->abre_em->lessThanOrEqualTo($agora)
            && $this->fecha_em->greaterThanOrEqualTo($agora);
    }

    public static function periodoAtivo(string $tipo): ?self
    {
        return self::query()
            ->tipo($tipo)
            ->ativos()
            ->orderByDesc('ano')
            ->first();
    }

    public static function periodoParaAno(string $tipo, int $ano): ?self
    {
        return self::query()
            ->tipo($tipo)
            ->where('ano', $ano)
            ->first();
    }

    public static function anosDisponiveis(string $tipo): Collection
    {
        return self::query()
            ->tipo($tipo)
            ->orderByDesc('ano')
            ->pluck('ano');
    }
}
