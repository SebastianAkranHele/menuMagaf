@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fas fa-list-alt me-2"></i>Pedidos</h2>
    {{-- Exportar PDF com o filtro atual --}}
    <a href="{{ route('admin.orders.export.pdf', ['status' => request('status')]) }}" class="btn btn-danger">
        <i class="fas fa-file-pdf me-1"></i> Exportar PDF
    </a>
</div>

<div class="mb-3">
    <a href="{{ route('admin.orders.export.excel') }}" class="btn btn-success">
        📊 Exportar Excel (Todos)
    </a>
    <a href="{{ route('admin.orders.export.csv') }}" class="btn btn-secondary">
        📑 Exportar CSV (Todos)
    </a>
</div>

{{-- Filtros de status --}}
<div class="mb-3">
    <a href="{{ route('admin.orders.index') }}"
       class="btn btn-sm btn-secondary {{ request('status') ? '' : 'active' }}">
       <i class="fas fa-list me-1"></i> Todos
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
       class="btn btn-sm btn-warning {{ request('status') === 'pending' ? 'active' : '' }}">
       <i class="fas fa-clock me-1"></i> Pendentes
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}"
       class="btn btn-sm btn-success {{ request('status') === 'completed' ? 'active' : '' }}">
       <i class="fas fa-check-circle me-1"></i> Concluídos
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'canceled']) }}"
       class="btn btn-sm btn-danger {{ request('status') === 'canceled' ? 'active' : '' }}">
       <i class="fas fa-times-circle me-1"></i> Cancelados
    </a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Total</th>
            <th>Status</th>
            <th>Data e Hora</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>KZ {{ number_format($order->total, 2, ',', '.') }}</td>
            <td>
                @if($order->status === 'pending')
                    <span class="badge bg-warning"><i class="fas fa-clock me-1"></i> Pendente</span>
                @elseif($order->status === 'completed')
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Concluído</span>
                @elseif($order->status === 'canceled')
                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Cancelado</span>
                @endif
            </td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>
    {{-- Ver pedido --}}
    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary" title="Ver Pedido">
        <i class="fas fa-eye"></i>
    </a>

    {{-- Exportações individuais --}}
    <a href="{{ route('admin.orders.export.single', $order) }}" class="btn btn-sm btn-danger" title="Exportar PDF">
        <i class="fas fa-file-pdf"></i>
    </a>
    <a href="{{ route('admin.orders.export.single.excel', $order) }}" class="btn btn-sm btn-success" title="Exportar Excel">
        <i class="fas fa-file-excel"></i>
    </a>
    <a href="{{ route('admin.orders.export.single.csv', $order) }}" class="btn btn-sm btn-secondary" title="Exportar CSV">
        <i class="fas fa-file-csv"></i>
    </a>

    {{-- Botões de status --}}
    @if($order->status === 'pending')
        <form action="{{ route('admin.orders.complete', $order) }}" method="POST" class="d-inline-block">
            @csrf
            <button type="button" class="btn btn-sm btn-success complete-order" title="Concluir Pedido">
                <i class="fas fa-check"></i>
            </button>
        </form>
        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline-block">
            @csrf
            <button type="button" class="btn btn-sm btn-danger cancel-order" title="Cancelar Pedido">
                <i class="fas fa-times"></i>
            </button>
        </form>
    @elseif($order->status === 'completed')
        <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline-block">
            @csrf
            <button type="button" class="btn btn-sm btn-danger cancel-order" title="Cancelar Pedido">
                <i class="fas fa-times"></i>
            </button>
        </form>
    @elseif($order->status === 'canceled')
        <form action="{{ route('admin.orders.restorePending', $order) }}" method="POST" class="d-inline-block">
            @csrf
            <button type="button" class="btn btn-sm btn-warning restore-order" title="Restaurar Pedido">
                <i class="fas fa-undo"></i>
            </button>
        </form>
    @endif

    {{-- Botão de deletar --}}
    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline-block">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-dark delete-order" title="Deletar Pedido">
            <i class="fas fa-trash"></i>
        </button>
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

{{-- Paginação --}}
<div class="d-flex justify-content-center mt-3">
   {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".complete-order, .cancel-order, .restore-order, .delete-order")
        .forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                const form = button.closest("form");

                let title = "Tem certeza?";
                let text = "Esta ação não pode ser desfeita.";
                let confirmButton = "Sim, confirmar!";
                let successMessage = "Ação realizada com sucesso!";

                if (button.classList.contains("delete-order")) {
                    title = "Deletar pedido?";
                    text = "O pedido será removido permanentemente!";
                    confirmButton = "Sim, deletar!";
                    successMessage = "Pedido deletado com sucesso!";
                } else if (button.classList.contains("cancel-order")) {
                    title = "Cancelar pedido?";
                    text = "O status será alterado para cancelado.";
                    confirmButton = "Sim, cancelar!";
                    successMessage = "Pedido cancelado com sucesso!";
                } else if (button.classList.contains("complete-order")) {
                    title = "Concluir pedido?";
                    text = "O status será alterado para concluído.";
                    confirmButton = "Sim, concluir!";
                    successMessage = "Pedido concluído com sucesso!";
                } else if (button.classList.contains("restore-order")) {
                    title = "Restaurar pedido?";
                    text = "O status será alterado para pendente.";
                    confirmButton = "Sim, restaurar!";
                    successMessage = "Pedido restaurado com sucesso!";
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmButton,
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
});
</script>
@endpush
