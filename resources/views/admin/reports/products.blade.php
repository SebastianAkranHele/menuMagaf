@extends('admin.layout')

@section('content')
<div class="content-header">
    <h1>Relatório de Produtos</h1>
</div>

<div class="content">
    <form method="GET" action="{{ route('admin.reports.products') }}" style="display:flex;gap:0.5rem;align-items:center;">
        <label>De:
            <input type="date" name="start_date" value="{{ old('start_date', $start->format('Y-m-d')) }}">
        </label>
        <label>Até:
            <input type="date" name="end_date" value="{{ old('end_date', $end->format('Y-m-d')) }}">
        </label>
        <button class="btn">Filtrar</button>
    </form>

    <div style="margin-top:1rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div class="card">
            <h4>Top Produtos</h4>
            <table class="table">
                <thead><tr><th>Produto</th><th>Qtd</th><th>Receita</th></tr></thead>
                <tbody>
                    @foreach($topProducts as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->qty_sold }}</td>
                        <td>KZ {{ number_format($p->revenue,2,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card">
            <h4>Produtos Nunca Vendidos</h4>
            <ul>
                @forelse($neverSold as $prod)
                    <li>{{ $prod->name }}</li>
                @empty
                    <li>Nenhum</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card" style="margin-top:1rem;">
        <h4>Vendas por Categoria</h4>
        <table class="table">
            <thead><tr><th>Categoria</th><th>Qtd Vendida</th></tr></thead>
            <tbody>
                @foreach($categories as $c)
                <tr><td>{{ $c->name }}</td><td>{{ $c->qty_sold }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top:1rem;">
        <h4>Produtos com Baixo Estoque</h4>
        <table class="table">
            <thead><tr><th>Produto</th><th>Estoque</th></tr></thead>
            <tbody>
                @foreach($lowStock as $p)
                <tr><td>{{ $p->name }}</td><td>{{ $p->stock }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
