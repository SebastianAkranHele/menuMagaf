<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;


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

        // Produtos nunca vendidos
        $neverSold = Product::whereDoesntHave('orders')->take(10)->get();

        // Últimos pedidos (sem eager load de user)
        $recentOrders = Order::latest()->take(10)->get();

        return view('admin.reports.index', compact(
            'start', 'end',
            'totalOrders', 'totalRevenue', 'avgTicket', 'ordersByStatus',
            'topProducts', 'topCategories', 'ordersByHour', 'hours',
            'days', 'dayCounts', 'neverSold', 'recentOrders'
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
            'start', 'end',
            'topProducts', 'neverSold', 'categories', 'lowStock'
        ));
    }

    /**
     * Exportar CSV
     */
    public function exportCsv(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $orders = Order::with('products') // removido 'user'
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $filename = 'orders_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID','Cliente','Total','Status','Data','Itens']);
            foreach ($orders as $order) {
                $items = $order->products->map(function ($p) {
                    $qty = $p->pivot->quantity ?? 1;
                    $price = $p->pivot->price ?? $p->price ?? 0;
                    return "{$p->name} (x{$qty} @ {$price})";
                })->implode(' | ');

                fputcsv($handle, [
                    $order->id,
                    $order->customer_name ?? 'N/A', // sem user
                    $order->total,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $items
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exportar PDF
     */
    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->getDateRange($request);

        $orders = Order::with('products') // removido 'user'
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $totalOrders  = $orders->count();
        $totalRevenue = $orders->where('status', 'completed')->sum('total');
        $avgTicket    = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $pdf = Pdf::loadView('admin.reports.pdf.orders', compact(
            'orders','start','end','totalOrders','totalRevenue','avgTicket'
        ));

        return $pdf->download('orders_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.pdf');
    }

    // Exportar PDF de um pedido específico
public function exportPdfSingle(Order $order)
{
    $pdf = Pdf::loadView('admin.reports.pdf_single', compact('order'));
    return $pdf->download("pedido_{$order->id}.pdf");
}

// Exportar CSV de um pedido específico
public function exportCsvSingle(Order $order)
{
    $filename = "pedido_{$order->id}.csv";
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$filename}",
    ];

    $callback = function() use ($order) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Produto','Quantidade','Preço']);
        foreach($order->products as $p){
            fputcsv($handle, [$p->name, $p->pivot->quantity, $p->pivot->price]);
        }
        fclose($handle);
    };

    return response()->stream($callback, 200, $headers);
}

// Exportar Excel de um pedido específico (usando Maatwebsite Excel)
public function exportExcelSingle(Order $order)
{
    return Excel::download(new OrderExport($order), "pedido_{$order->id}.xlsx");
}

}
