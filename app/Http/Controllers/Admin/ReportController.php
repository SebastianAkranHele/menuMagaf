<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Visit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Helper para obter intervalo de datas
     */
    private function getDateRange(Request $request)
    {
        $start = $request->input('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $end = $request->input('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        return [$start, $end];
    }

    /**
     * Relatório de Vendas (Pedidos)
     */
    public function index(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);

        $totalOrders  = $ordersQuery->count();
        $totalRevenue = (clone $ordersQuery)->where('status', 'completed')->sum('total');
        $avgTicket    = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $ordersByStatus = (clone $ordersQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Top produtos
        $topProducts = Product::select(
            'products.id',
            'products.name',
            'products.cost',
            DB::raw('SUM(order_product.quantity) as qty_sold'),
            DB::raw('SUM(order_product.quantity * order_product.price) as revenue')
        )
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name', 'products.cost')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        $totalProfit = $topProducts->sum(fn($p) => ($p->revenue ?? 0) - ($p->cost ?? 0) * ($p->qty_sold ?? 0));

        // Top categorias
        $topCategories = Category::select(
            'categories.id',
            'categories.name',
            DB::raw('SUM(order_product.quantity) as qty_sold')
        )
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        // Pedidos por hora
        $ordersByHourRaw = (clone $ordersQuery)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        $hours = range(0, 23);
        $ordersByHour = array_map(fn($h) => $ordersByHourRaw[$h] ?? 0, $hours);

        // Pedidos por dia
        $ordersByDayRaw = (clone $ordersQuery)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $days = [];
        $dayCounts = [];
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay());
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $days[] = $d;
            $dayCounts[] = $ordersByDayRaw[$d] ?? 0;
        }

        // Produtos menos vendidos no período
        $leastSold = Product::withSum(['orders as qty_sold' => function ($query) use ($start, $end) {
            $query->whereBetween('orders.created_at', [$start, $end]);
        }], 'order_product.quantity')
            ->orderBy('qty_sold', 'asc')
            ->take(10)
            ->get();

        // Últimos pedidos
        $recentOrders = Order::latest()->take(10)->get();

        // Clientes frequentes
        $frequentCustomers = Order::select('customer_name', DB::raw('COUNT(*) as orders_count'))
            ->groupBy('customer_name')
            ->having('orders_count', '>', 3)
            ->get();

        // Horário e dia de pico
        $peakHour = array_search(max($ordersByHour), $ordersByHour);
        $peakDay  = array_search(max($dayCounts), $dayCounts);

        return view('admin.reports.index', compact(
            'start',
            'end',
            'totalOrders',
            'totalRevenue',
            'avgTicket',
            'totalProfit',
            'ordersByStatus',
            'topProducts',
            'topCategories',
            'ordersByHour',
            'hours',
            'days',
            'dayCounts',
            'leastSold',
            'recentOrders',
            'frequentCustomers',
            'peakHour',
            'peakDay'
        ));
    }

    /**
     * Relatório de Produtos
     */
    public function products(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $topProducts = Product::select(
            'products.id',
            'products.name',
            DB::raw('SUM(order_product.quantity) as qty_sold'),
            DB::raw('SUM(order_product.quantity * order_product.price) as revenue')
        )
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        // Produtos nunca vendidos
        $neverSold = Product::whereDoesntHave('orders')->get();

        $categories = Category::select(
            'categories.name',
            DB::raw('SUM(order_product.quantity) as qty_sold')
        )
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('qty_sold')
            ->get();

        $lowStock = Product::where('stock', '<', 5)->orderBy('stock')->get();

        return view('admin.reports.products', compact(
            'start',
            'end',
            'topProducts',
            'neverSold',
            'categories',
            'lowStock'
        ));
    }

    /**
     * Relatório de Visitas
     */
    public function visits(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $visitsQuery = Visit::whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc');

        $visits = $visitsQuery->paginate(5);

        $allVisits = Visit::whereBetween('created_at', [$start, $end])->get();

        $uniqueVisits = $allVisits->unique(function ($v) {
            return $v->ip . '|' . $v->page . '|' . $v->created_at->format('Y-m-d');
        });

        $totalVisits = $uniqueVisits->count();

        $visitsGrouped = $uniqueVisits
            ->groupBy(fn($v) => $v->created_at->format('Y-m-d'))
            ->map->count()
            ->sortKeys();

        $visitsByDay = $visitsGrouped->mapWithKeys(
            fn($count, $date) => [Carbon::parse($date)->format('d/m') => $count]
        );

        return view('admin.reports.visits', [
            'startDate'   => $start->toDateString(),
            'endDate'     => $end->toDateString(),
            'totalVisits' => $totalVisits,
            'visitsByDay' => $visitsByDay,
            'visits'      => $visits, // usado na tabela com paginação
        ]);
    }

    /**
     * Exportar PDF de Visitas
     */
    public function exportVisitsPdf(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);
        $visits = Visit::whereBetween('created_at', [$start, $end])->get();
        $totalVisits = $visits->count();

        $pdf = Pdf::loadView('admin.reports.pdf.visits', compact(
            'visits',
            'start',
            'end',
            'totalVisits'
        ));

        return $pdf->download('visitas_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf');
    }

    /**
     * Exportar CSV de Visitas
     */
    public function exportVisitsCsv(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);
        $visits = Visit::whereBetween('created_at', [$start, $end])->get();

        $filename = 'visitas_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($visits) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'IP', 'User Agent', 'Data']);
            foreach ($visits as $visit) {
                fputcsv($handle, [
                    $visit->id,
                    $visit->ip ?? 'N/A',
                    $visit->user_agent ?? 'N/A',
                    $visit->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export PDF geral de pedidos
     */
    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);

        $totalOrders  = $ordersQuery->count();
        $totalRevenue = (clone $ordersQuery)->where('status', 'completed')->sum('total');
        $avgTicket    = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $topProducts = Product::select(
            'products.id',
            'products.name',
            DB::raw('SUM(order_product.quantity) as qty_sold'),
            DB::raw('SUM(order_product.quantity * order_product.price) as revenue')
        )
            ->join('order_product', 'products.id', '=', 'order_product.product_id')
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get();

        $leastSold = Product::withSum(['orders as qty_sold' => function ($query) use ($start, $end) {
            $query->whereBetween('orders.created_at', [$start, $end]);
        }], 'order_product.quantity')
            ->orderBy('qty_sold', 'asc')
            ->take(10)
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf.pdfRelator', compact(
            'start',
            'end',
            'totalOrders',
            'totalRevenue',
            'avgTicket',
            'topProducts',
            'leastSold'
        ));

        return $pdf->download('relatorio_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf');
    }
}
