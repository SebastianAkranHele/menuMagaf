<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - Garrafeira das 5 Curvas</title>

    {{-- CSS e JS via Vite --}}
    @vite([
        'resources/css/menu.css',
        'resources/css/app.css',
        'resources/js/menu.js',
        'resources/js/app.js'
    ])

    {{-- Font Awesome --}}
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

            {{-- Hero do Menu --}}
            <section class="menu-hero">
                <h2>Cardápio Completo</h2>
                <p>Descubra nossas especialidades</p>
            </section>

            {{-- Filtros de Categoria (populados via JS) --}}
            <div class="category-filters">
                <button class="category-filter active" data-category="all">Todos</button>
            </div>

            {{-- Itens do Menu (populados via JS) --}}
            <section class="menu-items">
                <p>Carregando produtos...</p>
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
                        <span id="cartTotal"> 0,00</span>
                    </div>
                    <button class="checkout-btn">Finalizar Pedido pelo WhatsApp</button>
                </div>
            </div>
        </div>
{{-- Modal do Produto --}}
<div id="productModal" class="product-modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>

        <!-- 1. Imagem -->
        <div class="modal-image">
            <img src="" alt="">
        </div>

        <!-- 2. Preço -->
        <div class="modal-price">KZ 0,00</div>

        <!-- 3. Detalhes completos -->
        <div class="modal-info">
            <h2 class="modal-title"></h2>
            <p class="modal-description"></p>
        </div>

        <!-- 4. Botão de adicionar ao carrinho -->
        <button class="modal-add-to-cart">Adicionar ao carrinho</button>
    </div>
</div>


        {{-- Footer --}}
        <footer>
            <p>Garrafeira das 5 Curvas © 2025 - Todos os direitos reservados</p>
        </footer>

    </div>

    {{-- Inicializar JS do Menu --}}
    <script>
        // Inicialização feita no menu.js
        document.addEventListener('DOMContentLoaded', () => loadMenuData());
    </script>
</body>
</html>
