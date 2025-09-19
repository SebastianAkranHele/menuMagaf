<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Exibe todos os pedidos, com filtro opcional por status.
     * Query string: ?status=pending ou ?status=completed
     */
    public function index(Request $request)
    {
        $query = Order::with('products')->latest();

        // Filtro opcional de status
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
     * Cancela a conclusão do pedido
     */
    public function cancelComplete(Order $order)
    {
        $order->status = 'pending';
        $order->save();

        return redirect()->back()->with('success', 'Conclusão do pedido cancelada.');
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

//eXPOR pdf
public function exportPdf()
{
    $orders = Order::latest()->get();

    // Traduzir status para PT (caso ainda não uses o accessor)
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

    return $pdf->download('pedidos.pdf');
}

}
