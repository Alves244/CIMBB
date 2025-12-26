<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo que regista a inserção laboral e económica dos residentes (Objetivo 3)
class AtividadeEconomica extends Model
{
    use HasFactory;

    // Nome da tabela que armazena os dados de emprego e setores
    protected $table = 'atividade_economicas';

    /**
     * Atributos que permitem traçar o perfil profissional do agregado (Objetivo 22).
     * Essencial para identificar se a mão de obra é qualificada ou para setores específicos.
     */
    protected $fillable = [
        'familia_id',     // Ligação à unidade familiar
        'identificador',  // Nome ou ID (anonimizado) do membro da família
        'tipo',           // Ex: Conta própria, Trabalhador por conta de outrem
        'setor_id',       // Ligação ao setor (Agricultura, Serviços, Indústria, etc.)
        'descricao',      // Detalhes sobre a função desempenhada
        'vinculo',        // Tipo de contrato (Permanente, Temporário, Sazonal)
        'local_trabalho', // Concelho ou Freguesia onde exerce atividade
    ];


    /* --- Relações --- */

    /**
     * Relação com a Família.
     * Permite cruzar dados de rendimento/trabalho com a dimensão do agregado.
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }

    /**
     * Relação com o Setor de Atividade.
     * Crucial para gerar estatísticas sobre quais os setores que mais empregam estrangeiros (Objetivo 21).
     */
    public function setorAtividade(): BelongsTo
    {
        return $this->belongsTo(SetorAtividade::class, 'setor_id');
    }
}