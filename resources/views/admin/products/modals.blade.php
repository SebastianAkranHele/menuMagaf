{{-- resources/views/admin/products/modals.blade.php --}}

<!-- Modal Criar Produto -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
        @csrf
        <input type="hidden" name="force_create" id="forceCreateInput" value="0">
        <div class="modal-header">
            <h5 class="modal-title" id="createProductLabel">Novo Produto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <label class="form-label">Nome do Produto</label>
            <input type="text" name="name" class="form-control" required>

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

            <label class="form-label mt-2">Imagem</label>
            <input type="file" name="image" id="createProductImage" class="form-control">

            <!-- Preview da imagem nova -->
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

            <label class="form-label mt-2">Imagem</label>
            <input type="file" name="image" id="editProductImage" class="form-control">

            <!-- Preview da imagem atual -->
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
