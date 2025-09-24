<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'customer_name', 'customer_table', 'total', 'status'];



    // Relação com produtos
    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    // Método para retornar status em português
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'completed' => 'Concluído',
            'canceled' => 'Cancelado',
            default => ucfirst($this->status),
        };
    }
}

