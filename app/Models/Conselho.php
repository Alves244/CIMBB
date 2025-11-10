<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conselho extends Model
{
    use HasFactory;


    public function freguesias(): HasMany
    {
        return $this->hasMany(Freguesia::class);
    }
}