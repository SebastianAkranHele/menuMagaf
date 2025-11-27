<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $client = auth('client')->user(); // Cliente autenticado

        // =====================
        // 1. Resumo (cards)
        // =====================
        $totalProducts   = $client->products()->count();
        $totalCategories = $client->categories()->count();
        $hero            = $client->homeHero;

        // =====================
        // 2. Produtos em estoque baixo
        // =====================
        $lowStockProducts = $client->products()->where('stock', '<=', 5)->get();

        // =====================
        // 3. Produtos e categorias para gráficos
        // =====================
        $categories = $client->categories()->with('products')->get();
        $productsByCategoryData = $categories->map(fn($cat) => $cat->products()->count());
        $categoriesLabels       = $categories->pluck('name');

        // =====================
        // Retornando para a view com $client
        // =====================
        return view('client.dashboard', compact(
            'client',                 // <-- importante para a view
            'totalProducts',
            'totalCategories',
            'hero',
            'lowStockProducts',
            'productsByCategoryData',
            'categoriesLabels'
        ));
    }
}
