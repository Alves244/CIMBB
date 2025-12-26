<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Modelo responsável pelo registo histórico de atividades e segurança (Objetivo 4)
class LogAcesso extends Model
{
    use HasFactory;

    // Define a tabela de histórico de auditoria
    protected $table = 'log_acessos';

    // Desativa os timestamps padrão para usar uma coluna personalizada de 'data_hora'
    public $timestamps = false;

    /**
     * Campos que permitem rastrear o "Quem, Quando, Onde e O Quê".
     * Essencial para detetar acessos indevidos ou erros de operação.
     */
    protected $fillable = [
        'utilizador_id', // Identifica o autor da ação (Freguesia, Escola ou Admin)
        'acao',          // Tipo de evento (ex: Login, Criar Família, Exportar PDF)
        'data_hora',     // Momento exato da interação
        'ip',            // Endereço de origem para segurança de rede (via TrustProxies)
        'descricao',     // Detalhes contextuais (ex: "Exportou dados do Concelho de Idanha-a-Nova")
    ];

    // Garante que a data seja tratada como um objeto Carbon para relatórios cronológicos
    protected $casts = [
        'data_hora' => 'datetime',
    ];

    /**
     * Relação: Um log pertence a um utilizador.
     * Permite à CIMBB gerar relatórios de atividade por entidade ou funcionário.
     */
    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_id');
    }
}