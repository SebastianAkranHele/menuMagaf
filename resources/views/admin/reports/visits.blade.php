@extends('admin.layout')

@section('content')
<div class="content-header">
     <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
    <h1>Relatório de Visitas</h1>
    <p>Período: {{ $startDate }} até {{ $endDate }}</p>
</div>

<form method="GET" action="{{ route('admin.reports.visits') }}" class="filters" style="margin-bottom: 20px;">
    <label>Data Início:
        <input type="date" name="start_date" value="{{ $startDate }}">
    </label>
    <label>Data Fim:
        <input type="date" name="end_date" value="{{ $endDate }}">
    </label>
    <button type="submit" class="btn btn-primary">Filtrar</button>
</form>

<div class="report-summary">
    <h3>Total de Visitas: <strong>{{ $totalVisits }}</strong></h3>
</div>

{{-- Botões de Exportação --}}
<div class="export-buttons" style="margin: 20px 0;">
    <a href="{{ route('admin.reports.visits.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
       class="btn btn-danger" target="_blank">
       Exportar PDF
    </a>

    <a href="{{ route('admin.reports.visits.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
       class="btn btn-success">
       Exportar CSV
    </a>
</div>

<div class="graph-card" style="margin-top: 20px;">
    <canvas id="visitsReportChart" height="120"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('visitsReportChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($visitsByDay->keys()) !!},
            datasets: [{
                label: 'Visitas por Dia',
                data: {!! json_encode($visitsByDay->values()) !!},
                borderColor: '#db0505',
                backgroundColor: 'rgba(219,5,5,0.2)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#db0505'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                x: { title: { display: true, text: 'Dias' }},
                y: { beginAtZero: true, title: { display: true, text: 'Quantidade de Visitas' }}
            }
        }
    });
});
</script>
@endsection
