<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que agrega os dados anuais de cada agrupamento escolar (Objetivo 1)
class InqueritoAgrupamento extends Model
{
    use HasFactory;

    // Nome da tabela que armazena os metadados dos inquéritos
    protected $table = 'inquerito_agrupamentos';

    // Campos que permitem o controlo de submissão e integridade dos dados
    protected $fillable = [
        'agrupamento_id',  // Identificação da escola/agrupamento (Objetivo 19)
        'utilizador_id',   // Técnico responsável pela submissão (Objetivo 4)
        'ano_referencia',  // Ano letivo ou civil para análise temporal (Objetivo 21)
        'total_registos',  // Quantidade de linhas detalhadas anexadas
        'total_alunos',    // Soma total de alunos estrangeiros reportada
        'submetido_em',    // Data de validação e fecho do inquérito
    ];

    // Converte a data de submissão para um objeto manipulável em PHP
    protected $casts = [
        'submetido_em' => 'datetime',
    ];

    /**
     * Relação com o Agrupamento.
     * Permite saber a que unidade educativa pertence este conjunto de dados.
     */
    public function agrupamento(): BelongsTo
    {
        return $this->belongsTo(Agrupamento::class);
    }

    /**
     * Relação com o Utilizador.
     * Garante a rastreabilidade e auditoria (Objetivo 4) da submissão.
     */
    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }

    /**
     * Relação com os Registos Detalhados.
     * Liga este cabeçalho aos dados individuais de cada nacionalidade/nível de ensino.
     */
    public function registos(): HasMany
    {
        return $this->hasMany(InqueritoAgrupamentoRegisto::class, 'inquerito_id');
    }
}