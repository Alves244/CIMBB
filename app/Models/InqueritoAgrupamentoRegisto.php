<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo que armazena os dados estatísticos detalhados por escola (Objetivo 14)
class InqueritoAgrupamentoRegisto extends Model
{
    use HasFactory;

    // Tabela que guarda a distribuição de alunos estrangeiros
    protected $table = 'inquerito_agrupamento_registos';

    // Atributos que permitem a monitorização do sucesso e integração escolar
    protected $fillable = [
        'inquerito_id',    // Ligação ao cabeçalho da submissão
        'nacionalidade',   // Permite identificar a diversidade cultural na escola
        'ano_letivo',      // Filtro temporal para análise de evolução (Objetivo 21)
        'concelho_id',     // Localização geográfica para mapeamento regional (Objetivo 3)
        'nivel_ensino',    // Ex: Pré-Escolar, 1º Ciclo, Secundário, etc.
        'numero_alunos',   // Dado quantitativo para cálculo de rácios e necessidades
    ];

    /**
     * Relação com o Inquérito (Pai).
     * Garante a integridade referencial dos dados submetidos pelo agrupamento.
     */
    public function inquerito(): BelongsTo
    {
        return $this->belongsTo(InqueritoAgrupamento::class, 'inquerito_id');
    }

    /**
     * Relação com o Concelho.
     * Permite à CIMBB cruzar dados escolares com os limites municipais.
     */
    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class, 'concelho_id');
    }
}