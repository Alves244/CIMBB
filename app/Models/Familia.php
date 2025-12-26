<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo central que representa o fluxo de instalação e integração de famílias (Objetivo 1)
class Familia extends Model
{
    use HasFactory;

    protected $table = 'familias';

    /**
     * Atributos que permitem a caracterização qualitativa e quantitativa (Objetivo 3).
     * Inclui indicadores de habitação, saúde, educação e fluxos migratórios.
     */
    protected $fillable = [
        'codigo',                    // Identificador único (importante para anonimização - Objetivo 5)
        'ano_instalacao',            // Permite criar a Timeline de evolução (Objetivo 21)
        'estado_acompanhamento',     // Monitorização do processo de integração
        'data_desinstalacao',        // Registo de abandono ou saída da região
        'ano_desinstalacao',
        'nacionalidade',             // Base para o gráfico de origens da população
        'freguesia_id',              // Georreferenciação por localidade (Objetivo 3)
        'tipologia_habitacao',
        'tipologia_propriedade',
        'localizacao_tipo',          // Urbano vs Rural
        'localizacao_detalhe',       // Nome da rua/lugar (campo sensível)
        'condicao_alojamento',
        'inscrito_centro_saude',     // Avaliação do acesso a serviços públicos
        'inscrito_escola',           // Cruzamento de dados com Agrupamentos
        'necessidades_apoio',        // Identificação de carências para suporte institucional
        'necessidades_apoio_outro',
        'observacoes',
        'utilizador_registo_id',     // Rastreabilidade de quem inseriu os dados (Objetivo 4)
    ];

    // Converte dados complexos para formatos de fácil manipulação no portal
    protected $casts = [
        'necessidades_apoio' => 'array', // Permite selecionar múltiplas carências num formulário
        'data_desinstalacao' => 'date',
    ];


    /* --- Relações: O Coração do Sistema de Informação --- */

    /**
     * Ligação à Freguesia.
     * Crucial para a "Visão Territorial" dos técnicos da CIMBB.
     */
    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }

    /**
     * Ligação ao utilizador que efetuou o registo.
     * Garante o cumprimento do Objetivo 4 (Salvaguarda e auditoria).
     */
    public function utilizadorRegisto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_registo_id');
    }

    /**
     * Ligação à demografia do agregado.
     * Permite contar membros por faixa etária e género (Objetivo 22).
     */
    public function agregadoFamiliar(): HasOne
    {
        return $this->hasOne(AgregadoFamiliar::class);
    }

    /**
     * Ligação às atividades económicas.
     * Analisa o impacto produtivo da família no território (Objetivo 3).
     */
    public function atividadesEconomicas(): HasMany
    {
        return $this->hasMany(AtividadeEconomica::class);
    }
}