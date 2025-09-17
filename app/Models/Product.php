<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'image', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Cria slug automaticamente ao criar ou atualizar
    protected static function booted()
    {
        static::saving(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

}
