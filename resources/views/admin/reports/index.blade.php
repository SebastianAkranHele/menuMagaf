@extends('admin.layout')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h2><i class="fas fa-chart-line me-2"></i>Relatórios</h2>

        {{-- Botões de exportação geral --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.reports.export.csv', request()->only(['start_date', 'end_date'])) }}"
                class="btn btn-secondary">
                <i class="fas fa-file-csv me-1"></i> CSV
            </a>
            <a href="{{ route('admin.reports.export.pdf', request()->only(['start_date', 'end_date'])) }}"
                class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('admin.reports.products', request()->only(['start_date', 'end_date'])) }}"
                class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Produtos
            </a>
        </div>
    </div>

    {{-- Filtros de datas --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex flex-wrap gap-2 align-items-end mb-3">
        <div class="form-group">
            <label>De:</label>
            <input type="date" name="start_date" class="form-control"
                value="{{ old('start_date', $start->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label>Até:</label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $end->format('Y-m-d')) }}">
        </div>
        <button class="btn btn-primary">Filtrar</button>
    </form>

    {{-- Cards resumo --}}
    <div class="d-flex gap-3 flex-wrap mb-3">
        <div class="card text-center p-3 flex-fill">
            <h5>Total Pedidos</h5>
            <p class="fs-4">{{ $totalOrders }}</p>
        </div>
        <div class="card text-center p-3 flex-fill">
            <h5>Total Receita</h5>
            <p class="fs-4">KZ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
        </div>
        <div class="card text-center p-3 flex-fill">
            <h5>Ticket Médio</h5>
            <p class="fs-4">KZ {{ number_format($avgTicket, 2, ',', '.') }}</p>
        </div>
        <div class="card text-center p-3 flex-fill">
            <h5>Pedidos por Status</h5>
            <div class="d-flex justify-content-center flex-wrap gap-1 mt-2">
                @foreach ($ordersByStatus as $status => $count)
                    @php
                        $color = match ($status) {
                            'pending' => 'warning',
                            'completed' => 'success',
                            'canceled' => 'danger',
                            default => 'secondary',
                        };
                        $statusLabel = match ($status) {
                            'pending' => 'Pendente',
                            'completed' => 'Concluído',
                            'canceled' => 'Cancelado',
                            default => ucfirst($status),
                        };
                    @endphp
                    <span class="badge bg-{{ $color }} p-2">{{ $statusLabel }}: {{ $count }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top Produtos e Categorias --}}
   <div class="row mb-3 g-3">
    <div class="col-12 col-md-6 d-flex">
        <div class="card p-3 flex-fill d-flex flex-column h-100">
            <h5>Top Produtos (Qtd)</h5>
            <div class="table-responsive flex-fill">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Qtd Vendida</th>
                            <th>Receita</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $p)
                            <tr>
                                <td>{{ $p->name }}</td>
                                <td>{{ $p->qty_sold ?? 0 }}</td>
                                <td>KZ {{ number_format($p->revenue ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 d-flex">
        <div class="card p-3 flex-fill d-flex flex-column h-100">
            <h5>Top Categorias</h5>
            <div class="table-responsive flex-fill">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Qtd Vendida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topCategories as $c)
                            <tr>
                                <td>{{ $c->name }}</td>
                                <td>{{ $c->qty_sold }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    {{-- Gráficos --}}
     {{-- Gráficos --}}
    <div class="row mb-3 g-3">
    <div class="col-12 col-md-6 d-flex">
        <div class="card flex-fill p-3 h-100 shadow-sm bg-white" style="border: 1px solid #dee2e6; border-radius:0.5rem;">
            <h5>Vendas por Dia</h5>
            <canvas id="salesByDay" style="min-height:200px;"></canvas>
        </div>
    </div>
    <div class="col-12 col-md-6 d-flex">
        <div class="card flex-fill p-3 h-100 shadow-sm bg-white" style="border: 1px solid #dee2e6; border-radius:0.5rem;">
            <h5>Pedidos por Hora</h5>
            <canvas id="ordersByHour" style="min-height:200px;"></canvas>
        </div>
    </div>
</div>

    {{-- Produtos nunca vendidos --}}
    <div class="card p-3 mb-3">
        <h5>Produtos nunca vendidos</h5>
        <div class="d-flex flex-wrap gap-1">
            @forelse($neverSold as $prod)
                <span class="badge bg-secondary">{{ $prod->name }}</span>
            @empty
                <p>Nenhum</p>
            @endforelse
        </div>
    </div>

    {{-- Últimos Pedidos --}}
    <div class="card p-3 mb-3">
        <h5>Últimos Pedidos</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        @php
                            $color = match ($order->status) {
                                'pending' => 'warning',
                                'completed' => 'success',
                                'canceled' => 'danger',
                                default => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->customer_name ?? ($order->user->name ?? 'Cliente Desconhecido') }}</td>
                            <td>KZ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td><span class="badge bg-{{ $color }}">{{ $order->status_label }}</span></td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum pedido</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const days = @json($days);
        const dayCounts = @json($dayCounts);
        const hours = @json($hours);
        const ordersByHour = @json($ordersByHour);

        new Chart(document.getElementById('salesByDay'), {
            type: 'line',
            data: { labels: days, datasets: [{ label: 'Pedidos por dia', data: dayCounts, tension: 0.3, borderColor: '#db0505', backgroundColor: 'rgba(219,5,5,0.1)', fill: true }] },
            options: { responsive: true }
        });

        new Chart(document.getElementById('ordersByHour'), {
            type: 'bar',
            data: { labels: hours, datasets: [{ label: 'Pedidos', data: ordersByHour, backgroundColor: '#a73406' }] },
            options: { responsive: true }
        });
    </script>
@endpush
