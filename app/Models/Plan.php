<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'max_products',
        'max_categories',
        'custom_domain',
    ];

    // Relação: um plano tem muitos clientes
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
