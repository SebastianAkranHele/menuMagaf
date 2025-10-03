<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Pedidos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h2, h3 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-center { text-align: center; }
        .section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Relatório de Pedidos</h2>
    <p>Período: {{ $start->format('d/m/Y') }} até {{ $end->format('d/m/Y') }}</p>

    <div class="section">
        <h3>Resumo</h3>
        <table>
            <tr>
                <th>Total de Pedidos</th>
                <th>Receita Total</th>
                <th>Ticket Médio</th>
            </tr>
            <tr>
                <td class="text-center">{{ $totalOrders }}</td>
                <td class="text-center">KZ {{ number_format($totalRevenue, 2, ',', '.') }}</td>
                <td class="text-center">KZ {{ number_format($avgTicket, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Top Produtos</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Vendida</th>
                    <th>Receita</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProducts as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td class="text-center">{{ $p->qty_sold ?? 0 }}</td>
                        <td class="text-center">KZ {{ number_format($p->revenue ?? 0, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Nenhum produto vendido</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Produtos Menos Vendidos</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade Vendida</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leastSold as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td class="text-center">{{ $p->qty_sold ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center">Nenhum produto</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>
