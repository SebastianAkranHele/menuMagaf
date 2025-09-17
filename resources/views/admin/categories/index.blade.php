@extends('admin.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Categorias</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            Nova Categoria
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->created_at->format('d/m/Y') }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editCategoryModal"
                                data-id="{{ $category->id }}"
                                data-name="{{ $category->name }}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-danger btn-sm delete-category" data-id="{{ $category->id }}">
                                <i class="fas fa-trash-alt"></i> Deletar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('admin.categories.modals')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // SweetAlert para sucesso de criação/atualização
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '{{ session('success') }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    // Modal Editar
    var editModal = document.getElementById('editCategoryModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('editCategoryName').value = button.getAttribute('data-name');
        document.getElementById('editCategoryForm').action = '/admin/categories/' + button.getAttribute('data-id');
    });

    // Deletar categoria com SweetAlert
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            var categoryId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Tem certeza?',
                text: "A categoria será deletada permanentemente!",
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
                    form.action = '/admin/categories/' + categoryId;

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
