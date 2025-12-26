<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

// Modelo que gere as janelas de tempo para submissão de inquéritos (Objetivo 2)
class InqueritoPeriodo extends Model
{
    use HasFactory;

    // Constantes para definir os tipos de entidades que respondem a inquéritos
    public const TIPO_FREGUESIA = 'freguesia';
    public const TIPO_AGRUPAMENTO = 'agrupamento';

    protected $table = 'inquerito_periodos';

    protected $fillable = [
        'tipo',        // Identifica se o período é para Freguesias ou Escolas
        'ano',         // O ano civil ou letivo a que os dados dizem respeito
        'abre_em',     // Data e hora de início da permissão de submissão
        'fecha_em',    // Data e hora de encerramento do portal para aquele ano
        'criado_por',  // ID do administrador da CIMBB que definiu o período (Objetivo 4)
    ];

    // Converte automaticamente as strings da DB em objetos Carbon para cálculos de datas
    protected $casts = [
        'abre_em' => 'datetime',
        'fecha_em' => 'datetime',
    ];

    /**
     * Relação com o Administrador.
     * Garante a auditabilidade: saber quem definiu o calendário de recolha.
     */
    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    /* --- Scopes: Facilitadores de Consultas à Base de Dados --- */

    // Filtra períodos por tipo (ex: apenas períodos de Agrupamento)
    public function scopeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Filtra apenas as janelas que estão abertas no momento atual
    public function scopeAtivos($query)
    {
        $agora = now();
        return $query->where('abre_em', '<=', $agora)
            ->where('fecha_em', '>=', $agora);
    }

    /* --- Métodos de Lógica de Negócio --- */

    /**
     * Verifica em tempo real se o período está acessível para escrita.
     * Crucial para validar submissões via API ou formulário.
     */
    public function estaAberto(): bool
    {
        $agora = now();
        return $this->abre_em instanceof CarbonInterface
            && $this->fecha_em instanceof CarbonInterface
            && $this->abre_em->lessThanOrEqualTo($agora)
            && $this->fecha_em->greaterThanOrEqualTo($agora);
    }

    /**
     * Obtém o período ativo atual para um determinado tipo de utilizador.
     */
    public static function periodoAtivo(string $tipo): ?self
    {
        return self::query()
            ->tipo($tipo)
            ->ativos()
            ->orderByDesc('ano')
            ->first();
    }

    /**
     * Lista todos os anos que já tiveram ou têm períodos de recolha definidos.
     * Útil para gerar menus de seleção em relatórios históricos.
     */
    public static function anosDisponiveis(string $tipo): Collection
    {
        return self::query()
            ->tipo($tipo)
            ->orderByDesc('ano')
            ->pluck('ano');
    }
}