<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Fatura Pedido #{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 30px;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company, .customer {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .company h2 {
            margin: 0;
            font-size: 18px;
        }

        .customer {
            text-align: right;
        }

        h1 {
            text-align: center;
            margin: 10px 0;
            font-size: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #f2f2f2;
            text-align: left;
        }

        .summary {
            margin-top: 20px;
            width: 40%;
            float: right;
        }

        .summary table {
            border: none;
        }

        .summary th, .summary td {
            border: none;
            padding: 6px;
        }

        .summary th {
            text-align: left;
        }

        .footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <div class="company">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" width="120"><br>
                <h2>Garrafeira das 5 Curvas</h2>
                <p>Rua Exemplo, nº 123<br>
                Luanda - Angola</p>
                <p>NIF: 500123456</p>
                <p>Tel: +244 999 999 999<br>
                Email: contacto@garrafeira.com</p>
            </div>

            <div class="customer">
                <p><strong>Cliente:</strong> {{ $order->customer_name ?? '---' }}</p>
                <p><strong>NIF:</strong> {{ $order->customer_nif ?? '---' }}</p>
                <p><strong>Endereço:</strong> {{ $order->customer_address ?? '---' }}</p>
                <p><strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Título -->
        <h1>Fatura nº {{ $order->id }}</h1>

        <!-- Tabela de Produtos -->
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Unitário</th>
                    <th>Quantidade</th>
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

        <!-- Resumo -->
        <div class="summary">
            <table>
                <tr>
                    <th>Subtotal:</th>
                    <td>KZ {{ number_format($order->total, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Imposto (IVA 14%):</th>
                    <td>KZ {{ number_format($order->total * 0.14, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Total:</th>
                    <td><strong>KZ {{ number_format($order->total * 1.14, 2, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        <p>Documento processado automaticamente. Não necessita de assinatura.</p>
        <p>Garrafeira das 5 Curvas © {{ date('Y') }}</p>
    </div>
</body>
</html>
