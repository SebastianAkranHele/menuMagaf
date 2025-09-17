@extends('admin.layout')

@section('content')
<div class="content-header">
    <h1>Dashboard</h1>
</div>

<div class="content">

    <!-- Cards de Resumo -->
    <div class="dashboard-cards">
        <div class="card summary-card bg-red">
            <h3>Produtos</h3>
            <p class="card-value">{{ $totalProducts ?? 0 }}</p>
        </div>
        <div class="card summary-card bg-dark-red">
            <h3>Categorias</h3>
            <p class="card-value">{{ $totalCategories ?? 0 }}</p>
        </div>
        <div class="card summary-card bg-bright-red">
            <h3>Pedidos Hoje</h3>
            <p class="card-value">{{ $ordersToday ?? 0 }}</p>
        </div>
        <div class="card summary-card bg-red">
            <h3>Visitas</h3>
            <p class="card-value">{{ $totalVisits ?? 0 }}</p>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="dashboard-graphs">
        <div class="graph-card">
            <h3>Visitas do Site (Últimos 7 dias)</h3>
            <canvas id="visitsWeekChart"></canvas>
        </div>

        <div class="graph-card">
            <h3>Pedidos por Categoria</h3>
            <canvas id="ordersChart"></canvas>
        </div>

        <div class="graph-card">
            <h3>Distribuição de Produtos</h3>
            <canvas id="productsChart"></canvas>
        </div>

        <div class="graph-card">
            <h3>Visitas e Pedidos (Últimas 12 Horas)</h3>
            <canvas id="visitsHoursChart"></canvas>
        </div>
    </div>

</div>

<style>
.dashboard-cards { display: flex; flex-wrap: wrap; gap: 1rem; }
.dashboard-cards .card { flex: 1 1 calc(25% - 1rem); min-width: 200px; }
.dashboard-graphs { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 2rem; }
.dashboard-graphs .graph-card { flex: 1 1 calc(50% - 1rem); min-width: 300px; background: #fff; border-radius: 8px; padding: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
@media (max-width: 1024px) { .dashboard-cards .card { flex: 1 1 calc(50% - 1rem); } .dashboard-graphs .graph-card { flex: 1 1 100%; } }
@media (max-width: 600px) { .dashboard-cards .card { flex: 1 1 100%; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tooltipOptions = {
        enabled: true,
        backgroundColor: '#db0505',
        titleColor: '#fff',
        bodyColor: '#fff',
        cornerRadius: 6,
        padding: 10,
        displayColors: false
    };

    // ===== Visitas últimos 7 dias =====
    new Chart(document.getElementById('visitsWeekChart'), {
        type: 'line',
        data: {
            labels: @json($visitsWeekLabels ?? []),
            datasets: [{
                label:'Visitas',
                data: @json($visitsWeekData ?? []),
                borderColor:'#db0505',
                backgroundColor:'rgba(219,5,5,0.2)',
                tension:0.4,
                fill:true,
                pointRadius:6,
                pointBackgroundColor:'#db0505'
            }]
        },
        options:{ responsive:true, plugins:{ legend:{position:'top'}, tooltip:tooltipOptions }, scales:{ y:{ beginAtZero:true } } }
    });

    // ===== Pedidos por categoria =====
    new Chart(document.getElementById('ordersChart'), {
        type:'bar',
        data:{
            labels: @json($ordersByCategoryLabels ?? []),
            datasets:[{
                label:'Pedidos',
                data: @json($ordersByCategoryData ?? []),
                backgroundColor:['#db0505','#a73406','#f00505','#8d2626']
            }]
        },
        options:{ responsive:true, plugins:{ legend:{ display:false }, tooltip:tooltipOptions }, scales:{ y:{ beginAtZero:true } } }
    });

    // ===== Distribuição de produtos =====
    new Chart(document.getElementById('productsChart'), {
        type:'doughnut',
        data:{
            labels: @json($ordersByCategoryLabels ?? []),
            datasets:[{
                label:'Produtos',
                data: @json($productsByCategoryData ?? []),
                backgroundColor:['#db0505','#a73406','#f00505','#8d2626']
            }]
        },
        options:{ responsive:true, plugins:{ legend:{ position:'bottom' }, tooltip:tooltipOptions } }
    });

    // ===== Visitas e pedidos últimas 12 horas =====
    new Chart(document.getElementById('visitsHoursChart'), {
        type:'line',
        data:{
            labels: @json($visitsHoursLabels ?? []),
            datasets:[
                {
                    label:'Visitas',
                    data: @json($visitsHoursData ?? []),
                    borderColor:'#db0505',
                    backgroundColor:'rgba(219,5,5,0.2)',
                    tension:0.4,
                    fill:true
                },
                {
                    label:'Pedidos',
                    data: @json($ordersHoursData ?? []),
                    borderColor:'#a73406',
                    backgroundColor:'rgba(167,52,6,0.2)',
                    tension:0.4,
                    fill:true
                }
            ]
        },
        options:{ responsive:true, plugins:{ legend:{ position:'top' }, tooltip: tooltipOptions } }
    });
});
</script>
@endsection
