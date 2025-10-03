<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Relatório de Visitas</title>
    <style>
        /* Fonte DejaVu é recomendada para UTF-8 com DomPDF */
        body { font-family: "DejaVu Sans", DejaVu, sans-serif; color:#222; font-size:12px; }
        header { margin-bottom: 10px; }
        .header-row { display:flex; justify-content:space-between; align-items:center; }
        .title { font-size:18px; font-weight:700; color:#222; }
        .meta { text-align:right; font-size:11px; color:#555; }
        table { width:100%; border-collapse: collapse; margin-top:12px; }
        th, td { border:1px solid #ddd; padding:6px 8px; font-size:11px; vertical-align:top; }
        th { background:#f5f5f5; text-align:left; }
        .small { font-size:11px; color:#666; }
        .summary { margin-top:10px; }
        .visits-by-day { margin-top:12px; width:40%; }
        .footer { position:fixed; bottom:10px; width:100%; text-align:center; font-size:11px; color:#666; }
        .wrap-ua { max-width:400px; word-wrap:break-word; white-space:normal; }
    </style>
</head>
<body>
    <header>
        <div class="header-row">
            <div>
                <div class="title">Relatório de Visitas</div>
                <div class="small">Período: {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }}</div>
            </div>
            <div class="meta">
                Gerado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>
                Total de visitas: <strong>{{ $totalVisits }}</strong>
            </div>
        </div>
    </header>

    {{-- Agrupar por dia (caso o controller não forneça) --}}
    @php
        $visitsByDay = $visits->groupBy(function($v){
            return $v->created_at->format('Y-m-d');
        })->map->count()->sortKeys();
    @endphp

    <section class="summary">
        <table class="visits-by-day">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Visitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($visitsByDay as $date => $count)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                        <td>{{ $count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <h4 style="margin-top:18px;">Detalhamento de Visitas</h4>
    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:15%;">IP</th>
                <th style="width:50%;">User Agent</th>
                <th style="width:20%;">Data / Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $v)
                <tr>
                    <td>{{ $v->id }}</td>
                    <td>{{ $v->ip ?? 'N/A' }}</td>
                    <td class="wrap-ua">{{ Str::limit($v->user_agent ?? 'N/A', 400) }}</td>
                    <td>{{ $v->created_at->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach

            @if($visits->isEmpty())
                <tr><td colspan="4" class="small text-center">Nenhuma visita encontrada no período.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        Garrafeira MAGAVI — Relatório de Visitas • Gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
