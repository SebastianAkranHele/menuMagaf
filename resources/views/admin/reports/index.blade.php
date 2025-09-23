@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fas fa-chart-line me-2"></i>Relatórios</h2>

    {{-- Botões de exportação geral --}}
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.export.csv', request()->only(['start_date','end_date'])) }}" class="btn btn-secondary">
            <i class="fas fa-file-csv me-1"></i> CSV
        </a>
        <a href="{{ route('admin.reports.export.pdf', request()->only(['start_date','end_date'])) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
         <a href="{{ route('admin.reports.products', request()->only(['start_date','end_date'])) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Productos
        </a>
    </div>
</div>

{{-- Filtros de datas --}}
<form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex gap-2 align-items-end mb-3">
    <div class="form-group">
        <label>De:</label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $start->format('Y-m-d')) }}">
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
        <p class="fs-4">KZ {{ number_format($totalRevenue,2,',','.') }}</p>
    </div>
    <div class="card text-center p-3 flex-fill">
        <h5>Ticket Médio</h5>
        <p class="fs-4">KZ {{ number_format($avgTicket,2,',','.') }}</p>
    </div>
    <div class="card text-center p-3 flex-fill">
        <h5>Pedidos por Status</h5>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            @foreach($ordersByStatus as $status => $count)
                @php
                    $color = match($status){
                        'pending'=>'warning',
                        'completed'=>'success',
                        'canceled'=>'danger',
                        default=>'secondary'
                    };
                @endphp
                <span class="badge bg-{{ $color }} p-2">{{ ucfirst($status) }}: {{ $count }}</span>
            @endforeach
        </div>
    </div>
</div>

{{-- Top Produtos e Categorias --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Top Produtos (Qtd)</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd Vendida</th>
                        <th>Receita</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->qty_sold ?? 0 }}</td>
                            <td>KZ {{ number_format($p->revenue ?? 0,2,',','.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Top Categorias</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Qtd Vendida</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCategories as $c)
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

{{-- Gráficos --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card p-3 bg-white shadow-sm" style="border: 2px solid #dc3545; border-radius: 0.5rem;">
            <h5>Vendas por Dia</h5>
            <canvas id="salesByDay"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 bg-white shadow-sm" style="border: 2px solid #dc3545; border-radius: 0.5rem;">
            <h5>Pedidos por Hora</h5>
            <canvas id="ordersByHour"></canvas>
        </div>
    </div>
</div>



{{-- Produtos nunca vendidos --}}
<div class="card p-3 mb-3">
    <h5>Produtos nunca vendidos</h5>
    @forelse($neverSold as $prod)
        <span class="badge bg-secondary me-1 mb-1">{{ $prod->name }}</span>
    @empty
        <p>Nenhum</p>
    @endforelse
</div>

{{-- Últimos Pedidos --}}
<div class="card p-3">
    <h5>Últimos Pedidos</h5>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentOrders as $order)
                @php
                    $color = match($order->status){
                        'pending'=>'warning',
                        'completed'=>'success',
                        'canceled'=>'danger',
                        default=>'secondary'
                    };
                @endphp
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->customer_name ?? ($order->user->name ?? 'Cliente Desconhecido') }}</td>
                    <td>KZ {{ number_format($order->total,2,',','.') }}</td>
                    <td><span class="badge bg-{{ $color }}">{{ $order->status_label }}</span></td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="d-flex gap-1">
                        {{-- Exportações individuais --}}
                        <a href="{{ route('admin.reports.export.pdf.single', $order) }}" class="btn btn-sm btn-danger" title="Exportar PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <a href="{{ route('admin.reports.export.excel.single', $order) }}" class="btn btn-sm btn-success" title="Exportar Excel">
                            <i class="fas fa-file-excel"></i>
                        </a>
                        <a href="{{ route('admin.reports.export.csv.single', $order) }}" class="btn btn-sm btn-secondary" title="Exportar CSV">
                            <i class="fas fa-file-csv"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Nenhum pedido</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const days = @json($days);
const dayCounts = @json($dayCounts);
const hours = @json($hours);
const ordersByHour = @json($ordersByHour);

// Gráfico de vendas por dia
new Chart(document.getElementById('salesByDay'), {
    type: 'line',
    data: {
        labels: days,
        datasets: [{
            label: 'Pedidos por dia',
            data: dayCounts,
            tension: 0.3,
            borderColor: '#db0505',
            backgroundColor: 'rgba(219,5,5,0.1)',
            fill: true
        }]
    },
    options: { responsive:true }
});

// Gráfico de pedidos por hora
new Chart(document.getElementById('ordersByHour'), {
    type: 'bar',
    data: {
        labels: hours,
        datasets: [{
            label: 'Pedidos',
            data: ordersByHour,
            backgroundColor: '#a73406'
        }]
    },
    options: { responsive:true }
});
</script>
@endpush
