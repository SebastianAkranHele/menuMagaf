@extends('admin.layout')

@section('content')
<h2>Pedido #{{ $order->id }}</h2>
<p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>
<p>Total: KZ {{ number_format($order->total, 2, ',', '.') }}</p>

<h3>Produtos</h3>
<table class="table">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>KZ {{ number_format($product->pivot->price, 2, ',', '.') }}</td>
            <td>{{ $product->pivot->quantity }}</td>
            <td>KZ {{ number_format($product->pivot->price * $product->pivot->quantity, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-3">
    {{-- Botões de ação de status --}}
    @if($order->status !== 'completed')
        <form id="completeForm" action="{{ route('admin.orders.complete', $order) }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="button" class="btn btn-success" id="completeBtn">Marcar como concluído</button>
        </form>
    @else
        <form id="cancelCompleteForm" action="{{ route('admin.orders.cancel', $order) }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="button" class="btn btn-warning" id="cancelCompleteBtn">Cancelar conclusão</button>
        </form>
    @endif

    {{-- Botões de Exportação --}}
    <div class="mt-3">
        <a href="{{ route('admin.orders.export.single', $order) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-1"></i> Exportar PDF
        </a>

        <a href="{{ route('admin.orders.export.single.excel', $order) }}" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Exportar Excel
        </a>

        <a href="{{ route('admin.orders.export.single.csv', $order) }}" class="btn btn-secondary">
            <i class="fas fa-file-csv me-1"></i> Exportar CSV
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Botão concluir pedido
    const completeBtn = document.getElementById('completeBtn');
    if(completeBtn){
        completeBtn.addEventListener('click', function(){
            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja marcar este pedido como concluído?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, concluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed){
                    document.getElementById('completeForm').submit();
                }
            });
        });
    }

    // Botão cancelar conclusão
    const cancelBtn = document.getElementById('cancelCompleteBtn');
    if(cancelBtn){
        cancelBtn.addEventListener('click', function(){
            Swal.fire({
                title: 'Tem certeza?',
                text: "Deseja cancelar a conclusão deste pedido?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Manter concluído'
            }).then((result) => {
                if(result.isConfirmed){
                    document.getElementById('cancelCompleteForm').submit();
                }
            });
        });
    }

    // Mensagem de sucesso
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
});
</script>
@endpush
