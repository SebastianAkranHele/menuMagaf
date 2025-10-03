@extends('admin.layout')

@section('content')
@php
if (!function_exists('categoryColor')) {
    function categoryColor($name)
    {
        $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark'];
        $hash = 0;
        for ($i = 0; $i < strlen($name); $i++) {
            $hash += ord($name[$i]);
        }
        return $colors[$hash % count($colors)];
    }
}
if (!function_exists('textColor')) {
    function textColor($bg)
    {
        $darkText = ['warning', 'info', 'secondary'];
        return in_array($bg, $darkText) ? 'text-dark' : 'text-white';
    }
}
@endphp

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2>Produtos</h2>
        <div class="d-flex gap-2 flex-wrap">
            <select id="categoryFilter" class="form-select form-select-sm">
                <option value="all">Todas Categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="text" id="productSearch" class="form-control form-control-sm" placeholder="Pesquisar por nome">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createProductModal">
                Novo Produto
            </button>
        </div>
    </div>

    <div class="row g-3" id="productContainer">
        @forelse($products as $product)
            @php
                $categoryName = $product->category->name ?? 'Sem categoria';
                $bgClass = categoryColor($categoryName);
                $textClass = textColor($bgClass);
                $lowStock = $product->stock < 5;
            @endphp

            <div class="col-md-3 product-card" data-name="{{ strtolower($product->name) }}" data-category="{{ $product->category_id ?? 'null' }}">
                <div class="card shadow-sm h-100 d-flex flex-column {{ $lowStock ? 'border-danger' : '' }}">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                            style="height:150px; object-fit:cover;" alt="{{ $product->name }}">
                    @else
                        <img src="https://via.placeholder.com/150x150?text=Sem+Imagem" class="card-img-top" alt="Sem imagem">
                    @endif

                    <div class="card-body p-2 flex-grow-1 d-flex flex-column">
                        <h6 class="card-title mb-1">{{ $product->name }}</h6>
                        <p class="mb-1 fw-bold">KZ {{ number_format($product->price, 2, ',', '.') }}</p>
                        <p class="mb-1 small">
                            <span class="badge bg-{{ $bgClass }} {{ $textClass }}">{{ $categoryName }}</span>
                        </p>

                        @php
                            $stockPercentage = min(100, $product->stock * 10);
                            $stockClass = $product->stock == 0 ? 'bg-danger' : ($product->stock < 5 ? 'bg-warning' : 'bg-success');
                        @endphp

                        <div class="mt-auto">
                            <p class="mb-1 small">
                                Estoque:
                                <span class="badge {{ $stockClass }}">{{ $product->stock }}</span>
                            </p>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $stockClass }}" role="progressbar"
                                    style="width: {{ $stockPercentage }}%;" aria-valuenow="{{ $product->stock }}"
                                    aria-valuemin="0" aria-valuemax="10"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer p-2 d-flex justify-content-between">
                        <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal"
                            data-bs-target="#editProductModal" data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}" data-description="{{ $product->description }}"
                            data-price="{{ $product->price }}" data-category="{{ $product->category_id }}"
                            data-stock="{{ $product->stock }}"
                            data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                            <i class="fas fa-edit"></i> Editar
                        </button>

                        <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-product"
                            data-id="{{ $product->id }}">
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

    <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
</div>

@include('admin.products.modals') {{-- Modal criar/editar --}}
@endsection

@push('styles')
<style>
    .product-card .card {
        min-height: 360px; /* altura mínima fixa para uniformidade */
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ----------------- ALERTAS -----------------
    @if (session('duplicate_create'))
        Swal.fire({ icon: 'error', title: 'Produto duplicado!', text: "Já existe um produto com o nome '{{ session('duplicate_create') }}'." });
    @endif
    @if (session('duplicate_update'))
        Swal.fire({ icon: 'error', title: 'Produto duplicado!', text: "Já existe um produto com o nome '{{ session('duplicate_update') }}'." });
    @endif
    @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Sucesso!', text: '{{ session('success') }}', timer: 2000, showConfirmButton: false });
    @endif

    // ----------------- MODAL EDITAR -----------------
    let editModal = document.getElementById('editProductModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        let button = event.relatedTarget;
        document.getElementById('editProductName').value = button.getAttribute('data-name');
        document.getElementById('editProductDescription').value = button.getAttribute('data-description');
        document.getElementById('editProductPrice').value = button.getAttribute('data-price');
        document.getElementById('editProductCategory').value = button.getAttribute('data-category');
        document.getElementById('editProductStock').value = button.getAttribute('data-stock');
        document.getElementById('editProductForm').action = "{{ url('admin/products') }}/" + button.getAttribute('data-id');

        let preview = document.getElementById('editProductPreview');
        let image = button.getAttribute('data-image');
        if(image){ preview.src = image; preview.classList.remove('d-none'); }
        else{ preview.classList.add('d-none'); }
    });

    // ----------------- DELETAR PRODUTO -----------------
    document.querySelectorAll('.delete-product').forEach(button=>{
        button.addEventListener('click', function(){
            let productId = this.getAttribute('data-id');
            Swal.fire({
                title:'Tem certeza?',
                text:"O produto será deletado permanentemente!",
                icon:'warning',
                showCancelButton:true,
                confirmButtonColor:'#d33',
                cancelButtonColor:'#3085d6',
                confirmButtonText:'Sim, deletar!',
                cancelButtonText:'Cancelar'
            }).then((result)=>{
                if(result.isConfirmed){
                    let form = document.createElement('form');
                    form.method='POST';
                    form.action="{{ url('admin/products') }}/"+productId;
                    let token = document.createElement('input'); token.type='hidden'; token.name='_token'; token.value='{{ csrf_token() }}';
                    let method = document.createElement('input'); method.type='hidden'; method.name='_method'; method.value='DELETE';
                    form.appendChild(token); form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // ----------------- PREVIEW IMAGEM -----------------
    let createImageInput = document.getElementById('createProductImage');
    let createPreview = document.getElementById('createProductPreview');
    createImageInput.addEventListener('change', function(){
        let file = this.files[0];
        if(file){ createPreview.src = URL.createObjectURL(file); createPreview.classList.remove('d-none'); }
        else{ createPreview.classList.add('d-none'); }
    });

    let editImageInput = document.getElementById('editProductImage');
    let editPreview = document.getElementById('editProductPreview');
    editImageInput.addEventListener('change', function(){
        let file = this.files[0];
        if(file){ editPreview.src = URL.createObjectURL(file); editPreview.classList.remove('d-none'); }
    });

    // ----------------- FILTRO + PAGINAÇÃO -----------------
    const products = Array.from(document.querySelectorAll('.product-card'));
    let filteredProducts = [...products];
    const itemsPerPage = 8;
    let currentPage = 1;
    const container = document.getElementById('productContainer');
    const paginationContainer = document.getElementById('paginationContainer');
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    function renderPage(page=1){
        currentPage = page;
        container.innerHTML='';
        const start = (page-1)*itemsPerPage;
        const end = start+itemsPerPage;
        const pageItems = filteredProducts.slice(start,end);
        pageItems.forEach(item=>container.appendChild(item));
        renderPagination();
    }

    function renderPagination(){
        paginationContainer.innerHTML='';
        const totalPages = Math.ceil(filteredProducts.length/itemsPerPage);
        if(totalPages<=1) return;

        const prevBtn = document.createElement('button');
        prevBtn.textContent='Anterior';
        prevBtn.className='btn btn-outline-secondary btn-sm';
        prevBtn.disabled = currentPage===1;
        prevBtn.onclick = ()=>renderPage(currentPage-1);

        const nextBtn = document.createElement('button');
        nextBtn.textContent='Próximo';
        nextBtn.className='btn btn-outline-secondary btn-sm';
        nextBtn.disabled = currentPage===totalPages;
        nextBtn.onclick = ()=>renderPage(currentPage+1);

        const pageInfo = document.createElement('span');
        pageInfo.className='align-self-center mx-2';
        pageInfo.textContent=`Página ${currentPage} de ${totalPages}`;

        paginationContainer.appendChild(prevBtn);
        paginationContainer.appendChild(pageInfo);
        paginationContainer.appendChild(nextBtn);
    }

    function filterProducts(){
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        filteredProducts = products.filter(p=>{
            const name = p.dataset.name;
            const category = p.dataset.category;
            const matchName = name.includes(searchTerm);
            const matchCategory = (categoryValue==='all') || (category===categoryValue);
            return matchName && matchCategory;
        });
        renderPage(1);
    }

    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);

    renderPage(1);
});
</script>
@endpush
