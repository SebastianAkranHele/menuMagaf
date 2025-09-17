<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // Pega todas as categorias
        $products = Product::with('category')->get(); // Pega produtos com relação de categoria

        return view('menu', compact('categories', 'products'));
    }
}
