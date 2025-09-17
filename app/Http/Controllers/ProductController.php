<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Retorna todos os produtos com a categoria relacionada
     */
    public function index()
    {
        $products = Product::with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
