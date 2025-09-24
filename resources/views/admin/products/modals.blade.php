<!-- Modal Criar Produto -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="createProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createProductLabel">Novo Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nome do Produto</label>
                <input type="text" name="name" id="createProductName" class="form-control" required>

                <label class="form-label mt-2">Descrição</label>
                <textarea name="description" class="form-control"></textarea>

                <label class="form-label mt-2">Preço</label>
                <input type="number" step="0.01" name="price" class="form-control" required>

                <label class="form-label mt-2">Categoria</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <label class="form-label mt-2">Estoque</label>
                <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}" min="0">

                <label class="form-label mt-2">Imagem</label>
                <input type="file" name="image" id="createProductImage" class="form-control">

                <div class="mt-2">
                    <img id="createProductPreview" src="" alt="Preview" class="img-fluid rounded d-none" style="max-height: 120px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Produto -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="editProductForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editProductLabel">Editar Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nome do Produto</label>
                <input type="text" name="name" id="editProductName" class="form-control" required>

                <label class="form-label mt-2">Descrição</label>
                <textarea name="description" id="editProductDescription" class="form-control"></textarea>

                <label class="form-label mt-2">Preço</label>
                <input type="number" step="0.01" name="price" id="editProductPrice" class="form-control" required>

                <label class="form-label mt-2">Categoria</label>
                <select name="category_id" id="editProductCategory" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                <label class="form-label mt-2">Estoque</label>
                <input type="number" name="stock" id="editProductStock" class="form-control" value="0" min="0">

                <label class="form-label mt-2">Imagem</label>
                <input type="file" name="image" id="editProductImage" class="form-control">

                <div class="mt-2">
                    <img id="editProductPreview" src="" alt="Preview" class="img-fluid rounded d-none" style="max-height: 120px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Atualizar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ----------------- CRIAR PRODUTO -----------------
    let createForm = document.getElementById('createProductForm');
    createForm.addEventListener('submit', function(e) {
        e.preventDefault();
        let name = document.getElementById('createProductName').value.trim();

        @foreach($products as $p)
            if(name.toLowerCase() === '{{ $p->name }}'.toLowerCase()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Produto duplicado!',
                    text: "Já existe um produto com este nome.",
                });
                return;
            }
        @endforeach

        createForm.submit();
    });

    // ----------------- EDITAR PRODUTO -----------------
    let editForm = document.getElementById('editProductForm');
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        let name = document.getElementById('editProductName').value.trim();
        let currentId = editForm.action.split('/').pop();

        @foreach($products as $p)
            if(name.toLowerCase() === '{{ $p->name }}'.toLowerCase() && currentId != '{{ $p->id }}') {
                Swal.fire({
                    icon: 'error',
                    title: 'Produto duplicado!',
                    text: "Já existe um produto com este nome.",
                });
                return;
            }
        @endforeach

        editForm.submit();
    });

    // ----------------- PREVIEW IMAGEM -----------------
    let createImageInput = document.getElementById('createProductImage');
    let createPreview = document.getElementById('createProductPreview');
    createImageInput.addEventListener('change', function() {
        let file = this.files[0];
        if(file){
            createPreview.src = URL.createObjectURL(file);
            createPreview.classList.remove('d-none');
        } else {
            createPreview.classList.add('d-none');
        }
    });

    let editImageInput = document.getElementById('editProductImage');
    let editPreview = document.getElementById('editProductPreview');
    editImageInput.addEventListener('change', function() {
        let file = this.files[0];
        if(file){
            editPreview.src = URL.createObjectURL(file);
            editPreview.classList.remove('d-none');
        }
    });
});
</script>
@endpush
