<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que gere os pedidos de assistência e suporte técnico (Objetivo 17)
class TicketSuporte extends Model
{
    use HasFactory;

    // Define a tabela que centraliza os incidentes e dúvidas do portal
    protected $table = 'ticket_suportes';

    // Atributos para gestão do ciclo de vida do suporte
    protected $fillable = [
        'codigo',           // Identificador único do processo (ex: TKT-2025-001)
        'utilizador_id',    // Quem reportou a dúvida (Técnico local)
        'assunto',          // Título breve do problema
        'descricao',        // Explicação detalhada da dificuldade sentida
        'estado',           // Gestão de fluxo: Aberto, Em Processamento, Fechado
        'resposta_admin',   // Campo para a solução final apresentada pela CIMBB
        'data_resposta',    // Registo temporal da resolução (para KPIs de suporte)
        'administrador_id', // Técnico da CIMBB responsável pela resolução
        'anexo',            // Caminho para capturas de ecrã ou documentos de erro
        'categoria',        // Classificação: Técnico, Erro de Dados, Sugestão
    ];

    // Trata a data de resposta como um objeto Carbon para cálculos de SLA
    protected $casts = [
        'data_resposta' => 'datetime',
    ];

    /**
     * Relação: Identifica o utilizador (stakeholder) que abriu o ticket.
     * Crucial para o apoio direto às entidades locais (Objetivo 17).
     */
    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }

    /**
     * Relação: Identifica o administrador responsável pela gestão do problema.
     * Garante a prestação de contas no suporte técnico (Objetivo 4).
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }

    /**
     * Relação: Liga o processo às várias mensagens de esclarecimento.
     * Permite um histórico completo da conversação até à resolução.
     */
    public function mensagens(): HasMany
    {
        return $this->hasMany(TicketMensagem::class, 'ticket_id')->orderBy('created_at');
    }
}