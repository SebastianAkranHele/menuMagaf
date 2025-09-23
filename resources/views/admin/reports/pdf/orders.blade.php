<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Pedidos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-bottom: 20px; }
        .summary p { margin: 0; }
    </style>
</head>
<body>
    <h2>Relatório de Pedidos</h2>

    <div class="summary">
        <p><strong>Período:</strong> {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</p>
        <p><strong>Total de Pedidos:</strong> {{ $totalOrders }}</p>
        <p><strong>Total Receita:</strong> KZ {{ number_format($totalRevenue,2,',','.') }}</p>
        <p><strong>Ticket Médio:</strong> KZ {{ number_format($avgTicket,2,',','.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
                <th>Itens</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->customer_name ?? 'Cliente Desconhecido' }}</td>
                    <td>KZ {{ number_format($order->total,2,',','.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @foreach($order->products as $p)
                            {{ $p->name }} (x{{ $p->pivot->quantity }})<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
