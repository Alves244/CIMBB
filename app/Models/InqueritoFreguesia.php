<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo para inquéritos anuais de diagnóstico local (Objetivo 1 e 3)
class InqueritoFreguesia extends Model
{
    use HasFactory;

    protected $table = 'inquerito_freguesias';

    /**
     * Atributos que cobrem o diagnóstico demográfico, laboral e social.
     * Estruturado para responder às métricas de integração da CIMBB.
     */
    protected $fillable = [
        'freguesia_id',
        'utilizador_id',    // Rastreabilidade do técnico que respondeu (Objetivo 4)
        'ano',              // Referência temporal para análise evolutiva
        
        // Distribuição Geográfica (Perguntas 11-13)
        'total_nucleo_urbano', // Concentração em vilas/cidades
        'total_aldeia_anexa',  // Dispersão por aldeias
        'total_agroflorestal', // Isolamento em zonas rurais
        
        // Demografia (Pergunta 14)
        'total_adultos',
        'total_criancas',
        
        // Habitação (Pergunta 15)
        'total_propria',
        'total_arrendada',
        
        // Mercado de Trabalho (Perguntas 16-19 - JSON para flexibilidade)
        'total_por_setor_propria',  // Empreendedorismo imigrante
        'total_por_setor_outrem',   // Mão de obra empregada
        'total_trabalhadores_outrem',

        // Avaliação de Impacto e Integração (Perguntas 20-24)
        'escala_integracao',    // Métrica quantitativa de sucesso social
        'aspectos_positivos',   // Dados qualitativos sobre benefícios
        'aspectos_negativos',   // Identificação de conflitos ou carências
        'satisfacao_global',    // Índice de felicidade da comunidade
        'sugestoes',            // Base para futuras políticas públicas (Objetivo 2)
    ];

    /**
     * Casts: Converte colunas JSON da base de dados em arrays PHP.
     * Permite armazenar dados complexos de setores sem múltiplas tabelas.
     */
    protected $casts = [
        'total_por_setor_propria' => 'array',
        'total_por_setor_outrem' => 'array',
    ];

    /**
     * Relação: Liga o inquérito à respetiva Freguesia.
     * Essencial para o mapeamento da "Visão Territorial" da Beira Baixa.
     */
    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }
}