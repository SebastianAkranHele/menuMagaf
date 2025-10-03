<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Visit; // <- modelo de visitas
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================
        // 1. Resumo (cards)
        // =====================
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $ordersToday     = Order::whereDate('created_at', Carbon::today())->count();
        $totalVisits = Visit::whereDate('created_at', today())->count();// total de visitas registradas

        // =====================
        // 2. Resumo de pedidos
        // =====================
        $ordersCompleted = Order::where('status', 'completed')->count();
        $ordersPending   = Order::where('status', 'pending')->count();
        $ordersCanceled  = Order::where('status', 'canceled')->count();

        // =====================
        // 3. Dados dos gráficos
        // =====================

        // --- Visitas últimos 7 dias
        $visitsWeekLabels = [];
        $visitsWeekData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $visitsWeekLabels[] = $date->format('D'); // Ex: Seg, Ter
            $visitsWeekData[]   = Visit::whereDate('created_at', $date)->count();
        }

        // --- Pedidos por categoria
        $categories = Category::with('products')->get();
        $ordersByCategoryLabels = $categories->pluck('name');
        $ordersByCategoryData = $categories->map(function ($cat) {
            // contar quantos pedidos existem com produtos dessa categoria
            return Order::whereHas('products', function ($q) use ($cat) {
                $q->where('category_id', $cat->id);
            })->count();
        });

        // --- Distribuição de produtos por categoria
        $productsByCategoryData = $categories->map(function ($cat) {
            return $cat->products()->count();
        });

        // --- Visitas e pedidos últimas 12 horas
        $visitsHoursLabels = [];
        $visitsHoursData   = [];
        $ordersHoursData   = [];

        for ($i = 0; $i < 12; $i++) {
            $hourStart = Carbon::now()->subHours(11 - $i)->startOfHour();
            $hourEnd   = Carbon::now()->subHours(11 - $i)->endOfHour();
            $label     = $hourStart->format('H:00');

            $visitsHoursLabels[] = $label;
            $visitsHoursData[]   = Visit::whereBetween('created_at', [$hourStart, $hourEnd])->count();
            $ordersHoursData[]   = Order::whereBetween('created_at', [$hourStart, $hourEnd])->count();
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'ordersToday',
            'totalVisits',
            'ordersCompleted',
            'ordersPending',
            'ordersCanceled',
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
