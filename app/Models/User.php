<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Classe mestre de autenticação e autorização (Objetivo 23)
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Comunicação de Segurança.
     * Envia a notificação de redefinição de password personalizada para o idioma local.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }

    /**
     * Atributos que definem o perfil e vínculo institucional (Objetivo 1).
     */
    protected $fillable = [
        'nome',
        'email',
        'password',
        'perfil',        // 'admin' (CIMBB), 'freguesia' (Juntas), 'cimbb' (Técnicos), 'agrupamento' (Escolas)
        'freguesia_id',   // Vínculo territorial obrigatório para perfil freguesia
        'agrupamento_id', // Vínculo institucional para perfil educativo
        'telemovel',
    ];

    /**
     * Proteção de Dados (RGPD).
     * Oculta dados sensíveis de segurança em exportações e respostas de API.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'data_criacao' => 'datetime',
    ];

    /* --- RELAÇÕES TERRITORIAIS --- */

    /**
     * Relação: Define a que Freguesia o utilizador está "preso" para recolha de dados.
     */
    public function freguesia()
    {
        return $this->belongsTo(Freguesia::class);
    }

    /**
     * Relação: Define a que Agrupamento Escolar o técnico pertence.
     */
    public function agrupamento()
    {
        return $this->belongsTo(Agrupamento::class);
    }

    /* --- MÉTODOS DE CONTROLO DE ACESSO (RBAC) --- */

    // Verifica se o utilizador tem poderes de gestão total do portal
    public function isAdmin()
    {
        return $this->perfil === 'admin';
    }

    // Verifica se é um técnico de Junta (acesso à gestão de famílias e agregados)
    public function isFreguesia()
    {
        return $this->perfil === 'freguesia';
    }

    // Verifica se é um técnico regional da CIMBB (acesso a relatórios e análises macro)
    public function isFuncionario()
    {
        return $this->perfil === 'cimbb'; 
    }

    // Verifica se é um técnico escolar (acesso aos inquéritos de educação)
    public function isAgrupamento()
    {
        return $this->perfil === 'agrupamento';
    }
}