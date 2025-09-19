@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="fas fa-list-alt me-2"></i>Pedidos</h2>
    {{-- Exportar PDF com o filtro atual --}}
    <a href="{{ route('admin.orders.export.pdf', ['status' => request('status')]) }}" class="btn btn-danger">
        <i class="fas fa-file-pdf me-1"></i> Exportar PDF
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
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> Ver
                </a>

                {{-- Botões conforme status --}}
                @if($order->status === 'pending')
                    <form action="{{ route('admin.orders.complete', $order) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="button" class="btn btn-sm btn-success complete-order" data-id="{{ $order->id }}">
                            <i class="fas fa-check"></i> Concluir
                        </button>
                    </form>
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="button" class="btn btn-sm btn-danger cancel-order" data-id="{{ $order->id }}">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </form>
                @elseif($order->status === 'completed')
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="button" class="btn btn-sm btn-danger cancel-order" data-id="{{ $order->id }}">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </form>
                @elseif($order->status === 'canceled')
                    <form action="{{ route('admin.orders.restorePending', $order) }}" method="POST" class="d-inline-block">
                        @csrf
                        <button type="button" class="btn btn-sm btn-warning restore-order" data-id="{{ $order->id }}">
                            <i class="fas fa-undo"></i> Restaurar
                        </button>
                    </form>
                @endif

                {{-- Botão de deletar --}}
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger delete-order" data-id="{{ $order->id }}">
                        <i class="fas fa-trash"></i> Deletar
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função genérica para confirmar ações
        function confirmAction(event, message, successMessage) {
            event.preventDefault();
            const form = event.target.closest('form');

            Swal.fire({
                title: 'Tem certeza?',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, confirmar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Aguarde um momento.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Enviar o formulário
                    form.submit();
                }
            });
        }

        // Concluir pedido
        document.querySelectorAll('.complete-order').forEach(button => {
            button.addEventListener('click', (e) => {
                confirmAction(e, 'Deseja marcar este pedido como concluído?', 'Pedido concluído com sucesso!');
            });
        });

        // Cancelar pedido
        document.querySelectorAll('.cancel-order').forEach(button => {
            button.addEventListener('click', (e) => {
                confirmAction(e, 'Deseja cancelar este pedido?', 'Pedido cancelado com sucesso!');
            });
        });

        // Restaurar pedido
        document.querySelectorAll('.restore-order').forEach(button => {
            button.addEventListener('click', (e) => {
                confirmAction(e, 'Deseja restaurar este pedido para pendente?', 'Pedido restaurado com sucesso!');
            });
        });

        // Deletar pedido
        document.querySelectorAll('.delete-order').forEach(button => {
            button.addEventListener('click', (e) => {
                confirmAction(e, 'Deseja deletar permanentemente este pedido? Esta ação não pode ser desfeita.', 'Pedido deletado com sucesso!');
            });
        });

        // Mensagens de sucesso do servidor
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush

@endsection
