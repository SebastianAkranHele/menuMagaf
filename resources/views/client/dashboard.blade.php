@extends('client.layout')

@section('content')
<div class="content-header">
    <h1>Bem-vindo, {{ auth()->guard('client')->user()->business_name }}</h1>
</div>

<div class="content">

    <!-- Cards de Resumo -->
    <div class="dashboard-cards">
        <a href="{{ route('client.products.index') }}" class="card summary-card bg-red text-decoration-none text-white">
            <h3>Produtos</h3>
            <p class="card-value">{{ $totalProducts ?? 0 }}</p>
        </a>

        <a href="{{ route('client.categories.index') }}" class="card summary-card bg-dark-red text-decoration-none text-white">
            <h3>Categorias</h3>
            <p class="card-value">{{ $totalCategories ?? 0 }}</p>
        </a>

        @if($hero)
        <a href="{{ route('client.home.edit') }}" class="card summary-card bg-bright-red text-decoration-none text-white">
            <h3>Hero Home</h3>
            <p class="card-value">Editar</p>
        </a>
        @endif
    </div>

    <!-- Produtos com estoque baixo -->
    <div class="low-stock-products">
        <h3>Produtos com estoque baixo</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Estoque</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts ?? [] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->stock }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Gráfico de produtos por categoria -->
    <div class="dashboard-graphs">
        <div class="graph-card">
            <h3>Distribuição de Produtos por Categoria</h3>
            <canvas id="productsChart"></canvas>
        </div>
    </div>

</div>

<style>
/* Cards */
.dashboard-cards { display: flex; flex-wrap: wrap; gap: 1rem; }
.dashboard-cards .card { flex: 1 1 calc(33% - 1rem); min-width: 200px; padding: 1rem; border-radius: 8px; color: #fff; text-align:center;}
.bg-red { background: #db0505; }
.bg-dark-red { background: #a73406; }
.bg-bright-red { background: #f00505; }
.card-value { font-size: 2rem; font-weight: bold; }

/* Tabela de estoque baixo */
.low-stock-products { margin-top:2rem; background:#fff; padding:1rem; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.low-stock-products table { width:100%; border-collapse: collapse;}
.low-stock-products th, .low-stock-products td { padding:0.75rem; border-bottom:1px solid #eee; }

/* Gráfico */
.dashboard-graphs { margin-top:2rem; }
.graph-card { background:#fff; padding:1rem; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('productsChart'), {
        type: 'doughnut',
        data: {
            labels: @json($categoriesLabels ?? []),
            datasets: [{
                label: 'Produtos',
                data: @json($productsByCategoryData ?? []),
                backgroundColor: ['#db0505','#a73406','#f00505','#8d2626','#c0392b','#e74c3c']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endsection
