<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        // Carregar produtos com categoria
        $products = Product::with('category')->get()->map(function($prod) {
            return [
                'id' => $prod->id,
                'name' => $prod->name,
                'stock' => 'required|integer|min:0', // ADICIONAR
                'description' => $prod->description,
                'price' => $prod->price,
                'image' => $prod->image,
                'category' => $prod->category ? [
                    'id' => $prod->category->id,
                    'name' => $prod->category->name,
                    'slug' => $prod->category->slug,
                ] : null
            ];
        });

        return response()->json($products);
    }
}
