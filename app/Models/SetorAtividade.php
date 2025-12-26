<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que define as categorias económicas do território (Objetivo 3 e 21)
class SetorAtividade extends Model
{
    use HasFactory;

    // Define a tabela de referência para os setores (ex: CAE - Classificação de Atividades Económicas)
    protected $table = 'setor_atividades';

    // Desativa timestamps por ser uma tabela de dicionário/referência estática
    public $timestamps = false;

    /**
     * Atributos para organização da estrutura económica regional.
     */
    protected $fillable = [
        'nome',        // Ex: "Agricultura e Pecuária", "Construção Civil"
        'descricao',   // Detalhes sobre o que abrange o setor
        'macro_grupo', // Permite agrupar por Setor Primário, Secundário ou Terciário
        'ativo',       // Controlo para desativar setores que deixem de ser usados
    ];

    /**
     * Relação: Um Setor pode classificar muitas Atividades Económicas.
     * Permite à CIMBB filtrar o impacto da imigração por ramo de atividade.
     */
    public function atividadesEconomicas(): HasMany
    {
        return $this->hasMany(AtividadeEconomica::class, 'setor_id');
    }
}