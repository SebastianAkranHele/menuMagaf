<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Exports\OrdersExport;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Exibe todos os pedidos, com filtro opcional por status.
     * Query string: ?status=pending|completed|canceled
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
     * Marca o pedido como concluído
     */
    public function complete(Order $order)
    {
        $order->status = 'completed';
        $order->save();

        return redirect()->back()->with('success', 'Pedido marcado como concluído.');
    }

    /**
     * Cancela (manda para cancelados)
     */
    public function cancel(Order $order)
    {
        $order->status = 'canceled';
        $order->save();

        return redirect()->back()->with('success', 'Pedido cancelado.');
    }

    /**
     * Restaura um pedido cancelado para pendente
     */
    public function restorePending(Order $order)
    {
        $order->status = 'pending';
        $order->save();

        return redirect()->back()->with('success', 'Pedido movido para pendentes.');
    }

    /**
     * Cria um novo pedido com produtos
     */
    public function store(Request $request)
    {
        $order = Order::create([
            'user_id' => auth()->id() ?? null,
            'total'   => $request->total,
            'status'  => 'pending',
        ]);

        $attachData = [];
        foreach ($request->items as $item) {
            if (!empty($item['product_id'])) {
                $attachData[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'price'    => $item['price']
                ];
            }
        }

        $order->products()->attach($attachData);

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    /**
     * Deleta um pedido
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->back()->with('success', 'Pedido deletado.');
    }

    /**
     * Exporta pedidos para PDF (respeitando filtro atual)
     */
    public function exportPdf(Request $request)
    {
        $status = $request->get('status'); // pending|completed|canceled|null

        $query = Order::latest();

        if ($status && in_array($status, ['pending', 'completed', 'canceled'])) {
            $query->where('status', $status);
        }

        $orders = $query->get();

        // Traduzir status
        $orders->transform(function ($order) {
            switch ($order->status) {
                case 'pending':
                    $order->status_pt = 'Pendente';
                    break;
                case 'completed':
                    $order->status_pt = 'Concluído';
                    break;
                case 'canceled':
                    $order->status_pt = 'Cancelado';
                    break;
                default:
                    $order->status_pt = ucfirst($order->status);
                    break;
            }
            return $order;
        });

        $pdf = Pdf::loadView('admin.orders.pdf', compact('orders'))
                  ->setPaper('a4', 'portrait');

        $fileName = $status ? "pedidos-{$status}.pdf" : "pedidos-todos.pdf";

        return $pdf->download($fileName);
    }

    /**
 * Exporta um pedido específico para PDF (detalhes + produtos)
 */
public function exportSinglePdf(Order $order)
{
    $order->load('products');

    $pdf = Pdf::loadView('admin.orders.pdf_single', compact('order'))
              ->setPaper('a4', 'portrait');

    return $pdf->download("pedido-{$order->id}.pdf");
}

public function exportOrderPdf(Order $order)
{
    $order->load('products');

    $pdf = Pdf::loadView('admin.orders.invoice-pdf', compact('order'))
              ->setPaper('a4'); // tamanho da página A4

    return $pdf->download("fatura-pedido-{$order->id}.pdf");
}

public function exportExcel()
{
    return Excel::download(new OrdersExport, 'orders.xlsx');
}

public function exportCsv()
{
    return Excel::download(new OrdersExport, 'orders.csv');
}

public function exportSingleExcel(Order $order)
{
    return Excel::download(new \App\Exports\OrderExport($order), "pedido-{$order->id}.xlsx");
}

public function exportSingleCsv(Order $order)
{
    return Excel::download(new \App\Exports\OrderExport($order), "pedido-{$order->id}.csv");
}

}
