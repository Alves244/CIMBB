<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modelo que representa a unidade mínima de análise territorial (Objetivo 3)
class Freguesia extends Model
{
    use HasFactory;

    // Define explicitamente a tabela para garantir a integridade das migrações
    protected $table = 'freguesias';

    // Atributos fundamentais para a identificação e hierarquia geográfica
    protected $fillable = [
        'nome',
        'concelho_id', // Chave estrangeira para a hierarquia municipal (Objetivo 1)
        'codigo',      // Código DICO (Distrito/Concelho/Freguesia) para fins estatísticos
    ];

    /**
     * Relação: Uma Freguesia pertence a um Concelho.
     * Permite agregar dados locais para uma visão municipal e regional (Objetivo 2).
     */
    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class);
    }

    /**
     * Relação: Uma Freguesia possui vários Utilizadores.
     * Identifica os técnicos da Junta autorizados a registar agregados (Objetivo 4).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relação: Uma Freguesia contém várias Famílias.
     * Permite a monitorização do impacto da imigração a nível local (Objetivo 3).
     */
    public function familias(): HasMany
    {
        return $this->hasMany(Familia::class);
    }
}