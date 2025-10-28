<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoSistema extends Model
{
    use HasFactory;

    // Garante que o Laravel usa a tabela 'configuracao_sistemas'
    protected $table = 'configuracao_sistemas';

    // Indica que só há updated_at (se não usar timestamps())
    const CREATED_AT = null; // Desativa created_at se só tiver updated_at na migration
     // Ou define public $timestamps = false; se nem created_at nem updated_at existirem.

    // Relação J [cite: 527] (inversa) é conceptual, pode não ser necessária aqui.
}