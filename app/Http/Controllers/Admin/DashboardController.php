<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================
        // 1. Resumo (cards)
        // =====================
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $totalVisits = 0; // se tiver sistema de analytics, colocar aqui

        // =====================
        // 2. Dados dos gráficos
        // =====================

        // Visitas últimos 7 dias (exemplo random, substituir com real)
        $visitsWeekLabels = [];
        $visitsWeekData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $visitsWeekLabels[] = $date->format('D'); // Seg, Ter...
            $visitsWeekData[] = rand(100, 250); // exemplo random
        }

        // Pedidos por categoria
        $categories = Category::withCount('products')->get(); // ou orders se quiser por pedidos
        $ordersByCategoryLabels = $categories->pluck('name');
        $ordersByCategoryData = $categories->map(function($cat){
            return $cat->products()->count(); // ou contar pedidos da categoria
        });

        // Distribuição de produtos
        $productsByCategoryData = $categories->map(function($cat){
            return $cat->products()->count();
        });

        // Visitas e pedidos últimas 12 horas (exemplo random)
        $visitsHoursLabels = [];
        $visitsHoursData = [];
        $ordersHoursData = [];
        for ($i = 0; $i < 12; $i++) {
            $visitsHoursLabels[] = ($i+1).'h';
            $visitsHoursData[] = rand(10, 50);
            $ordersHoursData[] = rand(5, 20);
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'ordersToday',
            'totalVisits',
            'visitsWeekLabels',
            'visitsWeekData',
            'ordersByCategoryLabels',
            'ordersByCategoryData',
            'productsByCategoryData',
            'visitsHoursLabels',
            'visitsHoursData',
            'ordersHoursData'
        ));
    }
}
