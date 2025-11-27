<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // Relação com produtos
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relação com pedidos através de produtos (se tiver pivot order_product)
    public function orders()
    {
        return $this->hasManyThrough(Order::class, Product::class);
    }
    public function client()
{
    return $this->belongsTo(Client::class);
}
}
