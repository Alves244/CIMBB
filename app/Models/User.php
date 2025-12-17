<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'perfil',        // 'admin', 'freguesia', 'cimbb', 'agrupamento'
        'freguesia_id',
        'agrupamento_id',
        'telemovel',
    ];

    /**
     * Os atributos que devem ser ocultados.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_criacao' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAÇÕES
    |--------------------------------------------------------------------------
    */

    // Um utilizador pode pertencer a uma Freguesia
    public function freguesia()
    {
        return $this->belongsTo(Freguesia::class);
    }

    public function agrupamento()
    {
        return $this->belongsTo(Agrupamento::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS AUXILIARES (Helpers)
    |--------------------------------------------------------------------------
    | Estes métodos são usados no blade e controladores para verificar permissões.
    */

    public function isAdmin()
    {
        return $this->perfil === 'admin';
    }

    public function isFreguesia()
    {
        return $this->perfil === 'freguesia';
    }

    public function isFuncionario()
    {
        // Pode ser 'cimbb' ou outro nome que tenhas definido para funcionário CIMBB
        return $this->perfil === 'cimbb'; 
    }

    public function isAgrupamento()
    {
        return $this->perfil === 'agrupamento';
    }
}