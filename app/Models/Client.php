<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'business_name',
        'email',
        'password',
        'plan_id',
        'active',
        'slug',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Gera o slug automaticamente ao criar o cliente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->slug)) {
                $client->slug = Str::slug($client->business_name);
            }
        });

        static::updating(function ($client) {
            if ($client->isDirty('business_name')) {
                $client->slug = Str::slug($client->business_name);
            }
        });
    }

    /**
     * Relação: Cliente pertence a um plano
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relação: HomeHero
     */
    public function homeHero()
    {
        return $this->hasOne(HomeHero::class);
    }

    /**
     * Relação: Categorias do cliente
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Relação: Produtos do cliente
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
