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

{{-- Gráfico --}}
<div class="graph-card" style="margin-top: 20px;">
    <canvas id="visitsReportChart" height="120"></canvas>
</div>

{{-- Tabela Detalhada --}}
<div class="table-responsive" style="margin-top: 40px;">
    <h3>Detalhes das Visitas</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Data</th>
                <th>Hora</th>
                <th>IP</th>
                <th>Página Visitada</th>
                <th>Dispositivo / Navegador</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visits as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ $v->created_at->format('d/m/Y') }}</td>
                    <td>{{ $v->created_at->format('H:i:s') }}</td>
                    <td>{{ $v->ip ?? 'N/A' }}</td>
                    <td>{{ $v->page ?? 'N/A' }}</td>
                    <td style="max-width: 300px; white-space: normal;">
                        {{ Str::limit($v->user_agent ?? 'N/A', 200) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Nenhuma visita encontrada neste período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
