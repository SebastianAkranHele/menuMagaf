<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - Garrafeira das 5 Curvas</title>

    @vite(['resources/css/menu.css', 'resources/css/app.css', 'resources/js/menu.js', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">

        {{-- Header --}}
        <header>
            <div class="logo">
                <h1>Garrafeira das 5 Curvas</h1>
                <p>MENU DIGITAL</p>
            </div>
            <a href="{{ url('/') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </header>

        {{-- Main --}}
        <main class="menu-container">

            {{-- Hero --}}
            <section class="menu-hero">
                <h2>Cardápio Completo</h2>
                <p>Descubra nossas especialidades</p>
            </section>

            {{-- Filtros de Categoria --}}
            <div class="category-filters">
                <button class="category-filter active" data-category="all">Todos</button>
                @foreach ($categories as $category)
                    <button class="category-filter" data-category="{{ $category->name }}">{{ $category->name }}</button>
                @endforeach
            </div>

            {{-- Itens do Menu --}}
            <section class="menu-items">
                @foreach ($products as $product)
                    <div class="menu-item"
                         data-id="{{ $product->id }}"
                         data-title="{{ $product->name }}"
                         data-category="{{ $product->category->name }}"
                         data-description="{{ $product->description }}"
                         data-price="{{ $product->price }}"
                         data-image="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=Sem+Imagem' }}">

                        <div class="item-image">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=Sem+Imagem' }}"
                                 alt="{{ $product->name }}">
                        </div>

                        <div class="item-info">
                            <div class="item-title">
                                <span>{{ $product->name }}</span>
                                <span class="item-price">KZ {{ number_format($product->price, 2, ',', '.') }}</span>
                            </div>
                            <div class="item-category">{{ $product->category->name }}</div>
                            <div class="item-desc-short">{{ Str::limit($product->description, 60) }}</div>
                            <div class="item-actions">
                                <button class="view-details-btn">
                                    <i class="fas fa-eye"></i> Ver Detalhes
                                </button>
                                <button class="add-to-cart">
                                    <i class="fas fa-cart-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        </main>

        {{-- Carrinho Flutuante --}}
        <div class="floating-cart" id="floatingCart">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count">0</span>
        </div>

        {{-- Modal do Carrinho --}}
        <div class="modal" id="cartModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Seu Pedido</h3>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="cart-items">
                        <p class="empty-cart-message">Seu carrinho está vazio.</p>
                    </div>
                    <div class="cart-total">
                        <span>Total:</span>
                        <span id="cartTotal">0,00</span>
                    </div>
                    <div class="customer-name-field mb-2">
                        <label for="customerName">Seu Nome:</label>
                        <input type="text" id="customerName" placeholder="Digite seu nome" class="form-control">
                        <small class="text-danger d-none" id="nameError">Por favor, digite seu nome.</small>
                    </div>
                    <div class="customer-table-field mb-2">
                        <label for="customerTable">Mesa:</label>
                        <input type="text" id="customerTable" placeholder="Ex: Mesa 5" class="form-control">
                        <small class="text-danger d-none" id="tableError">Por favor, informe a mesa.</small>
                    </div>
                    <button class="checkout-btn">Finalizar Pedido pelo WhatsApp</button>
                </div>
            </div>
        </div>

        {{-- Modal do Produto --}}
        <div id="productModal" class="product-modal">
            <div class="modal-content">
                <button class="close-modal">&times;</button>
                <div class="modal-image"><img src="" alt="Produto"></div>
                <div class="modal-info">
                    <h2 class="modal-title"></h2>
                    <p class="modal-category"></p>
                    <p class="modal-description"></p>
                    <div class="modal-price">KZ 0,00</div>
                    <div class="modal-quantity">
                        <button class="qty-btn minus">-</button>
                        <span class="qty-value">1</span>
                        <button class="qty-btn plus">+</button>
                    </div>
                    <button class="modal-add-to-cart">
                        <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer>
            <p>Garrafeira das 5 Curvas © 2025 - Todos os direitos reservados</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => loadMenuData());
    </script>
</body>
</html>
