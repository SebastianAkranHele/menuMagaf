<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Produto do Pedido #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Pedido #{{ $order->id }}</h2>
    <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
    <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $product->name }}</td>
                <td>KZ {{ number_format($product->pivot->price, 2, ',', '.') }}</td>
                <td>{{ $product->pivot->quantity }}</td>
                <td>KZ {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
