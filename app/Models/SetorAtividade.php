<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SetorAtividade extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'setor_atividades'
    protected $table = 'setor_atividades';

    // Indica que não há timestamps created_at/updated_at nesta tabela
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'macro_grupo',
        'ativo',
    ];

    /**
     * Define a relação: Um Setor pode classificar muitas Atividades Económicas.
     * (Relação G no ER [cite: 524])
     */
    public function atividadesEconomicas(): HasMany
    {
        return $this->hasMany(AtividadeEconomica::class, 'setor_id');
    }
}