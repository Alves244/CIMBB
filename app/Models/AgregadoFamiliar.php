<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo que detalha a composição demográfica do agregado (Objetivo 21)
class AgregadoFamiliar extends Model
{
    use HasFactory;

    // Define explicitamente a tabela para evitar conflitos de pluralização
    protected $table = 'agregado_familiars';
    
    // Desativado por não ser necessário rastrear criação/edição nesta tabela específica
    public $timestamps = false; 

    /**
     * Campos preenchíveis para análise estatística detalhada (Objetivo 22)
     * Permite segmentar a população por idade, género e situação eleitoral.
     */
    protected $fillable = [
        'familia_id',
        'adultos_laboral_m',      // Masculino em idade ativa
        'adultos_laboral_f',      // Feminino em idade ativa
        'adultos_65_mais_m',      // Idosos Masculinos
        'adultos_65_mais_f',      // Idosas Femininas
        'criancas_m',             // Crianças Masculino
        'criancas_f',             // Crianças Feminino
        'membros_sem_informacao', // Registos incompletos
        'eleitores_repenicados',  // Residentes registados nos cadernos eleitorais
        'estrutura_familiar',     // Campo flexível para notas sobre a tipologia familiar
    ];

    // Converte automaticamente dados da DB para formatos manipuláveis em PHP
    protected $casts = [
        'estrutura_familiar' => 'array',  // Permite guardar múltiplos dados como JSON
        'eleitores_repenicados' => 'integer',
    ];

    /**
     * Estabelece a ligação hierárquica (Objetivo 1).
     * Cada registo de agregado deve obrigatoriamente pertencer a uma Família.
     */
    public function familia(): BelongsTo
    {
        return $this->belongsTo(Familia::class);
    }
}