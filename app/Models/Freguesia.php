<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Freguesia extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'freguesias'
    protected $table = 'freguesias';

    protected $fillable = [
        'nome',
        'concelho_id',
        'codigo',
    ];

    /**
    * Define a relação inversa: Uma Freguesia pertence a um Concelho.
     * (Relação A no ER [cite: 518])
     */
    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class);
    }

    /**
     * Define a relação: Uma Freguesia pode ter muitos Utilizadores (Users).
     * (Relação B no ER [cite: 519])
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class); // Usar o modelo User do Laravel
    }

    /**
     * Define a relação: Uma Freguesia pode ter muitas Famílias.
     * (Relação C no ER [cite: 520])
     */
    public function familias(): HasMany
    {
        return $this->hasMany(Familia::class);
    }
}