{{-- resources/views/admin/categories/modals.blade.php --}}

<!-- Modal Criar Categoria -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="modal-content">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="createCategoryLabel">Nova Categoria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <label for="categoryName" class="form-label">Nome da Categoria</label>
            <input type="text" name="name" id="categoryName" class="form-control" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success">Salvar</button>
        </div>
    </form>
  </div>
</div>

<!-- Modal Editar Categoria -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content" id="editCategoryForm">
        @csrf
        @method('PUT')
        <div class="modal-header">
            <h5 class="modal-title" id="editCategoryLabel">Editar Categoria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <label for="editCategoryName" class="form-label">Nome da Categoria</label>
            <input type="text" name="name" id="editCategoryName" class="form-control" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </div>
    </form>
  </div>
</div>
