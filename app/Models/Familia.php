<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Familia extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'familias'
    protected $table = 'familias';

    /**
     * Define a relação inversa: Uma Família pertence a uma Freguesia.
     * (Relação C no ER [cite: 520])
     */
    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }

    /**
     * Define a relação inversa: Uma Família foi registada por um Utilizador (User).
     * (Relação D no ER [cite: 521])
     */
    public function utilizadorRegisto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilizador_registo_id');
    }

    /**
     * Define a relação: Uma Família tem um Agregado Familiar.
     * (Relação E no ER [cite: 522])
     */
    public function agregadoFamiliar(): HasOne
    {
        return $this->hasOne(AgregadoFamiliar::class);
    }

    /**
     * Define a relação: Uma Família pode ter muitas Atividades Económicas.
     * (Relação F no ER [cite: 523])
     */
    public function atividadesEconomicas(): HasMany
    {
        return $this->hasMany(AtividadeEconomica::class);
    }

    /**
     * Define a relação: Uma Família pode ter muitos Inquéritos Anuais.
     * (Relação H no ER [cite: 525])
     */
    public function inqueritosAnuais(): HasMany
    {
        return $this->hasMany(InqueritoAnual::class);
    }
}