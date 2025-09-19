@extends('admin.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Produtos</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProductModal">
            Novo Produto
        </button>
    </div>

    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-md-3">
                <div class="card shadow-sm h-100">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" style="height:150px; object-fit:cover;" alt="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/150x150?text=Sem+Imagem" class="card-img-top" alt="Sem imagem">
                    @endif
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1">{{ $product->name }}</h6>
                        <p class="mb-1 fw-bold">KZ {{ number_format($product->price, 2, ',', '.') }}</p>
                        <p class="mb-0 text-muted small">{{ $product->category->name ?? 'Sem categoria' }}</p>
                    </div>
                    <div class="card-footer p-2 d-flex justify-content-between">
                        <button class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal"
                            data-bs-target="#editProductModal"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-description="{{ $product->description }}"
                            data-price="{{ $product->price }}"
                            data-category="{{ $product->category_id }}"
                            data-image="{{ $product->image ? asset('storage/'.$product->image) : '' }}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-product" data-id="{{ $product->id }}">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <p class="text-muted">Nenhum produto cadastrado.</p>
            </div>
        @endforelse
    </div>
</div>

@include('admin.products.modals') {{-- Inclui modais criar/editar --}}

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variável para controlar se já mostramos um alerta
let alertShown = false;

/**
 * Abrir modal de criação automaticamente se houver erro de duplicação
 */
@if(session('duplicate_create'))
    var createModal = new bootstrap.Modal(document.getElementById('createProductModal'));
    createModal.show();
@endif

/**
 * Função para mostrar alertas na ordem correta
 */
function showAlerts() {
    if (alertShown) return;

    // Primeiro verifica se há erro de duplicação na criação
    @if(session('duplicate_create'))
        alertShown = true;
        Swal.fire({
            title: 'Produto já existe!',
            text: "Já existe um produto com o nome '{{ session('duplicate_create') }}'. Deseja continuar mesmo assim?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('forceCreateInput').value = '1';
                document.getElementById('createProductForm').submit();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Cancelado',
                    text: 'O produto não foi criado.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });

    // Depois verifica se há erro de duplicação na edição
    @elseif(session('duplicate_update'))
        alertShown = true;
        Swal.fire({
            title: 'Produto já existe!',
            text: "Já existe um produto com o nome '{{ session('duplicate_update') }}'. Deseja continuar mesmo assim?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById('editProductForm');
                form.action = "{{ route('admin.products.update', ['product' => '__id__']) }}"
                              .replace('__id__', "{{ session('product_id') }}");

                if (!form.querySelector('input[name="_method"]')) {
                    let methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);
                }

                let forceInput = document.createElement('input');
                forceInput.type = 'hidden';
                forceInput.name = 'force_update';
                forceInput.value = '1';
                form.appendChild(forceInput);

                form.submit();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Cancelado',
                    text: 'A atualização foi cancelada.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });

    // Por último, mostra mensagem de sucesso (se não houver duplicação)
    @elseif(session('success'))
        alertShown = true;
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
}

// Executar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    showAlerts();
});

/**
 * Modal Editar (preenche os dados dinamicamente)
 */
var editModal = document.getElementById('editProductModal');
editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('editProductName').value = button.getAttribute('data-name');
    document.getElementById('editProductDescription').value = button.getAttribute('data-description');
    document.getElementById('editProductPrice').value = button.getAttribute('data-price');
    document.getElementById('editProductCategory').value = button.getAttribute('data-category');

    document.getElementById('editProductForm').action =
        "{{ route('admin.products.update', ['product' => '__id__']) }}".replace('__id__', button.getAttribute('data-id'));

    var preview = document.getElementById('editProductPreview');
    var image = button.getAttribute('data-image');
    if (image) {
        preview.src = image;
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
});

/**
 * Deletar Produto
 */
document.querySelectorAll('.delete-product').forEach(button => {
    button.addEventListener('click', function() {
        var productId = this.getAttribute('data-id');

        Swal.fire({
            title: 'Tem certeza?',
            text: "O produto será deletado permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, deletar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('admin.products.destroy', ['product' => '__id__']) }}".replace('__id__', productId);

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(token);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>
@endpush
