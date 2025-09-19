@extends('admin.layout')

@section('content')
<h2>Pedidos</h2>

{{-- Filtros de status --}}
<div class="mb-3">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary {{ request('status') ? '' : 'active' }}">Todos</a>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning {{ request('status') === 'pending' ? 'active' : '' }}">Pendentes</a>
    <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="btn btn-sm btn-success {{ request('status') === 'completed' ? 'active' : '' }}">Concluídos</a>
    <a href="{{ route('admin.orders.index', ['status' => 'canceled']) }}" class="btn btn-sm btn-danger {{ request('status') === 'canceled' ? 'active' : '' }}">Cancelados</a>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Pedidos</h2>
    <a href="{{ route('admin.orders.export.pdf') }}" class="btn btn-danger">
        Exportar PDF
    </a>
</div>


<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Status</th>
            <th>Criado em</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>KZ {{ number_format($order->total, 2, ',', '.') }}</td>
            <td>{{ $order->status_label }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">Ver</a>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Deseja deletar este pedido?')">Deletar</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">Nenhum pedido encontrado.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
