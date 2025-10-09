@extends('admin.layout')

@section('content')
<div class="content-header" style="margin-bottom: 20px;">
    <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
    <h1 style="margin-top: 15px;">Relatório de Visitas</h1>
    <p>Período: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> até
       <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
    </p>
</div>

{{-- Filtro por datas --}}
<form method="GET" action="{{ route('admin.reports.visits') }}" class="filters" style="margin-bottom: 20px;">
    <label style="margin-right: 10px;">
        Data Início:
        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" style="display:inline-block; width:auto;">
    </label>
    <label style="margin-right: 10px;">
        Data Fim:
        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" style="display:inline-block; width:auto;">
    </label>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-filter me-1"></i> Filtrar
    </button>
</form>

{{-- Resumo --}}
<div class="report-summary" style="margin-bottom: 20px;">
    <h3>Total de Visitas: <strong>{{ $totalVisits }}</strong></h3>
</div>

{{-- Botões de Exportação --}}
<div class="export-buttons" style="margin: 20px 0;">
    <a href="{{ route('admin.reports.visits.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
       class="btn btn-danger" target="_blank" style="margin-right: 10px;">
       <i class="fas fa-file-pdf me-1"></i> Exportar PDF
    </a>

    <a href="{{ route('admin.reports.visits.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
       class="btn btn-success">
       <i class="fas fa-file-csv me-1"></i> Exportar CSV
    </a>
</div>

{{-- Gráfico --}}
<div class="graph-card" style="margin-top: 20px;">
    <canvas id="visitsReportChart" height="120"></canvas>
</div>

{{-- Tabela de Detalhes das Visitas --}}
<div class="table-responsive" style="margin-top: 30px;">
    <h5 style="margin-bottom: 15px;">Detalhes das Visitas</h5>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
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
            @php use Illuminate\Support\Str; @endphp
            @forelse($visits as $visit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $visit->created_at->format('d/m/Y') }}</td>
                    <td>{{ $visit->created_at->format('H:i') }}</td>
                    <td>{{ $visit->ip ?? '—' }}</td>
                    <td>{{ $visit->page ? ucfirst(str_replace(['-', '_'], ' ', $visit->page)) : '—' }}</td>
                    <td title="{{ $visit->user_agent }}">
                        {{ Str::limit($visit->user_agent, 60) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding: 15px;">
                        Nenhuma visita encontrada neste período.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 🔹 Paginação --}}
<div class="d-flex justify-content-center align-items-center mt-3">
    @if ($visits->hasPages())
        <nav>
            <ul class="pagination mb-0">
                {{-- Botão anterior --}}
                <li class="page-item {{ $visits->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $visits->previousPageUrl() }}" tabindex="-1">Anterior</a>
                </li>

                {{-- Indicador de página --}}
                <li class="page-item disabled">
                    <span class="page-link">
                        Página {{ $visits->currentPage() }} de {{ $visits->lastPage() }}
                    </span>
                </li>

                {{-- Botão próximo --}}
                <li class="page-item {{ $visits->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $visits->nextPageUrl() }}">Próximo</a>
                </li>
            </ul>
        </nav>
    @endif
</div>

</div>

{{-- Gráfico JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('visitsReportChart');
    if (!ctx) return;

    const visitsData = {!! json_encode($visitsByDay->values()) !!};
    const maxValue = Math.max(...visitsData);

    // Calcula o stepSize automaticamente
    // Aqui usamos 5 divisões do eixo Y como referência
    const stepSize = Math.ceil(maxValue / 5 * 2) / 2; // arredonda para 0.5 mais próximo

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($visitsByDay->keys()) !!},
            datasets: [{
                label: 'Visitas por Dia',
                data: visitsData,
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
            plugins: { legend: { display: true } },
            scales: {
                x: {
                    title: { display: true, text: 'Dias' }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Visitas' },
                    ticks: {
                        stepSize: stepSize,
                        callback: function(value) {
                            return value.toFixed(1);
                        }
                    }
                }
            }
        }
    });
});
</script>

@endsection
