<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',  'stock', 'slug', 'description', 'price', 'image', 'category_id'
    ];

        protected $appends = ['available']; // acessor appended
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    // Gera slug único automaticamente ao criar ou atualizar
    protected static function booted()
    {
        static::saving(function ($product) {
            $baseSlug = Str::slug($product->name);

            // Ignora o próprio produto no caso de update
            $query = Product::where('slug', 'LIKE', "{$baseSlug}%")
                            ->where('id', '!=', $product->id ?? 0);

            $count = $query->count();

            $product->slug = $count ? "{$baseSlug}-{$count}" : $baseSlug;
        });
    }

    // Acessor para disponibilidade
    public function getAvailableAttribute()
    {
        return $this->stock > 0;
    }

    public function client()
{
    return $this->belongsTo(Client::class);
}
}
