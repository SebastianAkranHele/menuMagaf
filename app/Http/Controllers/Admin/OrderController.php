<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Exports\OrdersExport; // Todos os pedidos
use App\Exports\OrderExport;  // Pedido único
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Lista pedidos (com filtro de status)
     */
    public function index(Request $request)
    {
        $query = Order::with('products')->latest();

        if ($request->has('status') && in_array($request->status, ['pending', 'completed', 'canceled'])) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Mostra detalhes de um pedido
     */
    public function show(Order $order)
    {
        $order->load('products');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Atualizações de status
     */
    public function complete(Order $order)
    {
        $order->update(['status' => 'completed']);
        return back()->with('success', 'Pedido marcado como concluído.');
    }

    public function cancel(Order $order)
    {
        $order->update(['status' => 'canceled']);
        return back()->with('success', 'Pedido cancelado.');
    }

    public function restorePending(Order $order)
    {
        $order->update(['status' => 'pending']);
        return back()->with('success', 'Pedido movido para pendentes.');
    }

    /**
     * Remover pedido
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->back()->with('success', 'Pedido deletado com sucesso!');

    }

    /**
     * Exportar todos os pedidos PDF
     */
    public function exportPdf(Request $request)
    {
        $status = $request->get('status');

        $query = Order::latest();
        if ($status && in_array($status, ['pending', 'completed', 'canceled'])) {
            $query->where('status', $status);
        }

        $orders = $query->get();

        // traduz status
        $orders->transform(function ($order) {
            $map = [
                'pending' => 'Pendente',
                'completed' => 'Concluído',
                'canceled' => 'Cancelado'
            ];
            $order->status_pt = $map[$order->status] ?? ucfirst($order->status);
            return $order;
        });

        $pdf = Pdf::loadView('admin.orders.pdf', compact('orders'))
                  ->setPaper('a4', 'portrait');

        $fileName = $status ? "pedidos-{$status}.pdf" : "pedidos-todos.pdf";
        return $pdf->download($fileName);
    }

    /**
     * Exportar pedido único PDF
     */
    public function exportSinglePdf(Order $order)
    {
        $order->load('products');

        $pdf = Pdf::loadView('admin.orders.pdf_single', compact('order'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("pedido-{$order->id}.pdf");
    }

    /**
     * Exportar fatura PDF (layout separado)
     */
    public function exportOrderPdf(Order $order)
    {
        $order->load('products');

        $pdf = Pdf::loadView('admin.orders.invoice-pdf', compact('order'))
                  ->setPaper('a4');

        return $pdf->download("fatura-pedido-{$order->id}.pdf");
    }

    /**
     * Exportar TODOS pedidos Excel / CSV
     */
    public function exportExcel()
    {
        return Excel::download(new OrdersExport, 'pedidos.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new OrdersExport, 'pedidos.csv');
    }

    /**
     * Exportar UM pedido Excel / CSV
     */
    public function exportSingleExcel(Order $order)
    {
        return Excel::download(new OrderExport($order), "pedido-{$order->id}.xlsx");
    }

    public function exportSingleCsv(Order $order)
    {
        return Excel::download(new OrderExport($order), "pedido-{$order->id}.csv");
    }
}
