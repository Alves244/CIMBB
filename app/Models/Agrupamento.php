<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agrupamento extends Model
{
    use HasFactory;

    protected $table = 'agrupamentos';

    protected $fillable = [
        'nome',
        'codigo',
        'concelho_id',
    ];

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function inqueritos(): HasMany
    {
        return $this->hasMany(InqueritoAgrupamento::class);
    }
}
