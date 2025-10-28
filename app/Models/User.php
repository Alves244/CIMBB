<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'nome', // Ajustado de 'name'
        'email',
        'password',
        'perfil',
        'freguesia_id',
        'telemovel',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define a relação inversa: Um Utilizador pertence a uma Freguesia (pode ser nulo).
     * (Relação B no ER [cite: 519])
     */
    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class);
    }

    /**
     * Define a relação: Um Utilizador (registante) pode registar muitas Famílias.
     * (Relação D no ER [cite: 521])
     */
    public function familiasRegistadas(): HasMany
    {
        // Especifica a chave estrangeira na tabela 'familias'
        return $this->hasMany(Familia::class, 'utilizador_registo_id');
    }

    /**
     * Define a relação: Um Utilizador (requerente) pode criar muitos Tickets.
     * (Relação I no ER [cite: 526])
     */
    public function ticketsCriados(): HasMany
    {
        // Especifica a chave estrangeira na tabela 'ticket_suportes'
        return $this->hasMany(TicketSuporte::class, 'utilizador_id');
    }

    /**
     * Define a relação: Um Utilizador (admin) pode responder a muitos Tickets.
     * (Relação I no ER [cite: 526])
     */
    public function ticketsRespondidos(): HasMany
    {
        // Especifica a chave estrangeira na tabela 'ticket_suportes'
        return $this->hasMany(TicketSuporte::class, 'administrador_id');
    }

     /**
      * Define a relação: Um Utilizador pode ter muitos Logs de Acesso.
      * (Relação K no ER [cite: 528])
      */
    public function logsAcesso(): HasMany
    {
        // Especifica a chave estrangeira na tabela 'log_acessos'
        return $this->hasMany(LogAcesso::class, 'utilizador_id');
    }
}