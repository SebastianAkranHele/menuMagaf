@extends('admin.layout')

@section('content')
<div class="content-header mb-3">
     <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
    <h1>Relatório de Produtos</h1>
</div>

{{-- Filtros --}}
<div class="card p-3 mb-3">
    <form method="GET" action="{{ route('admin.reports.products') }}" class="d-flex flex-wrap gap-3 align-items-end">
        <div class="form-group">
            <label>De:</label>
            <input type="date" name="start_date" class="form-control"
                   value="{{ old('start_date', $start->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label>Até:</label>
            <input type="date" name="end_date" class="form-control"
                   value="{{ old('end_date', $end->format('Y-m-d')) }}">
        </div>
        <button class="btn btn-primary">Filtrar</button>
    </form>
</div>

{{-- Grid de cards --}}
<div class="row g-3">
    {{-- Top Produtos --}}
    <div class="col-lg-6 col-md-12">
        <div class="card p-3 h-100">
            <h4>Top Produtos</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
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
                            <td>{{ $p->qty_sold }}</td>
                            <td>KZ {{ number_format($p->revenue,2,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Produtos Nunca Vendidos --}}
    <div class="col-lg-6 col-md-12">
        <div class="card p-3 h-100">
            <h4>Produtos Nunca Vendidos</h4>
            @forelse($neverSold as $prod)
                <span class="badge bg-secondary me-1 mb-1">{{ $prod->name }}</span>
            @empty
                <p>Nenhum</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Vendas por Categoria --}}
<div class="card p-3 mt-3">
    <h4>Vendas por Categoria</h4>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Qtd Vendida</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->qty_sold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Produtos com Baixo Estoque --}}
<div class="card p-3 mt-3">
    <h4>Produtos com Baixo Estoque</h4>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Estoque</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStock as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>
                        <span class="badge {{ $p->stock < 3 ? 'bg-danger' : 'bg-warning' }}">
                            {{ $p->stock }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
