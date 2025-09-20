<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class OrderExportController extends Controller
{
    public function exportProductPdf($orderId, $productId)
    {
        $order = Order::with('products')->findOrFail($orderId);
        $product = $order->products()->where('product_id', $productId)->firstOrFail();

       $pdf = Pdf::loadView('admin.orders.exports.product-pdf', compact('order', 'product'));
        return $pdf->download("pedido-{$order->id}-produto-{$product->id}.pdf");
    }

    public function exportProductExcel($orderId, $productId)
    {
        $order = Order::with('products')->findOrFail($orderId);
        $product = $order->products()->where('product_id', $productId)->firstOrFail();

        return Excel::download(new \App\Exports\ProductExport($order, $product), "pedido-{$order->id}-produto-{$product->id}.xlsx");
    }

    public function exportProductCsv($orderId, $productId)
    {
        $order = Order::with('products')->findOrFail($orderId);
        $product = $order->products()->where('product_id', $productId)->firstOrFail();

        return Excel::download(new \App\Exports\ProductExport($order, $product), "pedido-{$order->id}-produto-{$product->id}.csv");
    }
}
