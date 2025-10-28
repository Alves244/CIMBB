<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conselho extends Model
{
    use HasFactory;

    /**
     * Define a relação: Um Conselho tem muitas Freguesias.
     * (Relação A no ER [cite: 518])
     */
    public function freguesias(): HasMany
    {
        return $this->hasMany(Freguesia::class);
    }
}