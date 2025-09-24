<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Fatura - Pedido #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; }
        .header img { max-height: 60px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 0; font-size: 12px; }
        .info { margin-bottom: 20px; }
        .info p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { text-align: right; font-weight: bold; font-size: 14px; }
        .footer { text-align: center; font-size: 11px; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 20px; }
    </style>
</head>
<body>

    <!-- Cabeçalho -->
    <div class="header">
         <img src="{{ public_path('/assets/magaf1.jpg') }}" alt="Logo">
        <h1>Garrafeira das 5 Curvas</h1>
        <p>Luanda - Angola | Tel: +244 936 351 564</p>
        <p><strong>FATURA</strong></p>
    </div>

    <!-- Informações -->
    <div class="info">
        <p><strong>Pedido #:</strong> {{ $order->id }}</p>
        <p><strong>Status:</strong> {{ $order->status_label }}</p>
        <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

        {{-- Cliente vindo do pedido --}}
        <p><strong>Cliente:</strong> {{ $order->customer_name ?? ($order->user->name ?? 'Cliente Desconhecido') }}</p>

        {{-- Mesa --}}
        <p><strong>Mesa:</strong> {{ $order->customer_table ?? '-' }}</p>

        {{-- Caso tenha usuário vinculado --}}
        @if($order->user)
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
        @endif
    </div>

    <!-- Produtos -->
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Qtd</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>KZ {{ number_format($product->pivot->price, 2, ',', '.') }}</td>
                <td>{{ $product->pivot->quantity }}</td>
                <td>KZ {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <p class="total">TOTAL: KZ {{ number_format($order->total, 2, ',', '.') }}</p>

    <!-- Rodapé -->
    <div class="footer">
        <p>Obrigado pela sua preferência!</p>
        <p>Documento gerado automaticamente - não necessita de assinatura</p>
    </div>

</body>
</html>
