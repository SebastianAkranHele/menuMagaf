<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Para autenticação
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'business_name',
        'email',
        'password',
        'plan_id',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // Relação: cliente pertence a um plano
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Exemplo: home_hero relacionado ao cliente
    public function homeHero()
    {
        return $this->hasOne(HomeHero::class);
    }
}
