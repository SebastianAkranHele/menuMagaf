<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $client->business_name ?? 'Menu Digital' }}</title>

    {{-- CSS do Admin --}}
    @vite(['resources/css/menu.css', 'resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">

        {{-- Header --}}
        <header class="d-flex justify-content-between align-items-center py-3">
            <div class="logo">
                <h1>{{ $client->business_name }}</h1>
                <p>MENU DIGITAL</p>
            </div>
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </header>

        {{-- Hero Section --}}
        <section class="menu-hero text-center py-5"
            @if($hero->background_image)
                style="background: url('{{ $hero->background_image_type == 'url' ? $hero->background_image : asset('storage/'.$hero->background_image) }}') center/cover no-repeat;"
            @endif
        >
            <div class="text-white">
                <h1>{{ $hero->title }}</h1>
                <p>{{ $hero->subtitle }}</p>
                @if($hero->profile_image)
                    <img src="{{ $hero->profile_image_type == 'url' ? $hero->profile_image : asset('storage/'.$hero->profile_image) }}"
                         class="rounded-circle mt-3" style="width:120px; height:120px;" alt="Perfil">
                @endif
                <h3>{{ $hero->profile_title }}</h3>
                <p>{{ $hero->profile_subtitle }}</p>

                {{-- Social Links --}}
                @if(!empty($hero->social_links))
                    <div class="mt-3">
                        @foreach($hero->social_links as $link)
                            <a href="{{ $link['url'] }}" target="{{ !empty($link['target_blank']) ? '_blank' : '_self' }}" class="mx-2">
                                <i class="{{ $link['icon_class'] ?? '' }} {{ $link['color_class'] ?? '' }}"></i>
                                {{ ucfirst($link['name']) }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Categories --}}
        <section class="categories py-5">
            <div class="category-filters mb-4 text-center">
                <button class="btn btn-primary category-filter active" data-category="all">Todos</button>
                @foreach ($categories as $category)
                    <button class="btn btn-outline-primary category-filter" data-category="{{ $category->name }}">{{ $category->name }}</button>
                @endforeach
            </div>

            {{-- Products --}}
            <div class="row">
                @foreach ($categories as $category)
                    @foreach ($category->products as $product)
                        @if($product->stock > 0)
                        <div class="col-md-3 mb-4 menu-item" data-category="{{ $category->name }}" data-id="{{ $product->id }}" data-description="{{ $product->description }}">
                            <div class="card h-100">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text">{{ Str::limit($product->description, 60) }}</p>
                                    <p class="text-success">Stock: {{ $product->stock }}</p>
                                    <p class="fw-bold item-price">Preço: {{ number_format($product->price, 2, ',', '.') }} Kz</p>
                                    <button class="btn btn-sm btn-outline-primary view-details-btn">Ver Detalhes</button>
                                    <button class="btn btn-sm btn-success add-to-cart">Adicionar</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </section>

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

                    {{-- Tipo de Pedido --}}
                    <div class="mb-3">
                        <label for="orderType">Tipo de Pedido:</label>
                        <select id="orderType" class="form-control">
                            <option value="presente" selected>Presente</option>
                            <option value="encomenda">Encomenda</option>
                        </select>
                    </div>

                    {{-- Campos Presente --}}
                    <div class="present-fields">
                        <div class="customer-name-field mb-2">
                            <label for="customerName">Seu Nome:</label>
                            <input type="text" id="customerName" placeholder="Digite seu nome" class="form-control">
                        </div>
                        <div class="customer-table-field mb-2">
                            <label for="customerTable">Mesa:</label>
                            <input type="text" id="customerTable" placeholder="Ex: Mesa 5" class="form-control">
                        </div>
                    </div>

                    {{-- Campos Encomenda --}}
                    <div class="order-fields d-none">
                        <div class="customer-name-field mb-2">
                            <label for="orderName">Nome:</label>
                            <input type="text" id="orderName" placeholder="Digite seu nome" class="form-control">
                        </div>
                        <div class="customer-address-field mb-2">
                            <label for="orderAddress">Endereço:</label>
                            <input type="text" id="orderAddress" placeholder="Digite o endereço" class="form-control">
                        </div>
                        <div class="customer-phone-field mb-2">
                            <label for="orderPhone">Número de Telefone:</label>
                            <input type="text" id="orderPhone" placeholder="Digite o telefone" class="form-control">
                        </div>
                    </div>

                    <button class="checkout-btn btn btn-primary mt-2">Finalizar Pedido pelo WhatsApp</button>
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
                    <button class="modal-add-to-cart btn btn-success mt-2">
                        <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="text-center py-4 bg-light mt-5">
            <p>{{ $hero->footer_text ?? 'Todos os direitos reservados.' }}</p>
        </footer>

    </div>

    {{-- JS do Admin --}}
    @vite(['resources/js/menu.js', 'resources/js/app.js'])

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        const orderTypeSelect = document.getElementById('orderType');
        const presentFields = document.querySelector('.present-fields');
        const orderFields = document.querySelector('.order-fields');

        // Alterna campos Presente/Encomenda
        orderTypeSelect.addEventListener('change', () => {
            if(orderTypeSelect.value === 'presente'){
                presentFields.classList.remove('d-none');
                orderFields.classList.add('d-none');
            } else {
                presentFields.classList.add('d-none');
                orderFields.classList.remove('d-none');
            }
        });

        // Checkout pelo WhatsApp
        document.querySelector('.checkout-btn').addEventListener('click', () => {
            const cart = window.menuCart?.cart || [];
            if(cart.length === 0){
                alert('Seu carrinho está vazio!');
                return;
            }

            let message = 'Olá! Gostaria de fazer o seguinte pedido:\n\n';
            let total = 0;

            cart.forEach(item => {
                message += `• ${item.quantity}x ${item.title} - KZ ${(item.price * item.quantity).toFixed(2).replace('.', ',')}\n`;
                total += item.price * item.quantity;
            });

            if(orderTypeSelect.value === 'presente'){
                const name = document.getElementById('customerName').value.trim();
                const table = document.getElementById('customerTable').value.trim();
                if(!name){ alert('Digite seu nome'); return; }
                if(!table){ alert('Digite a mesa'); return; }
                message += `\nTipo: Presente\nNome: ${name}\nMesa: ${table}`;
            } else {
                const name = document.getElementById('orderName').value.trim();
                const address = document.getElementById('orderAddress').value.trim();
                const phone = document.getElementById('orderPhone').value.trim();
                if(!name){ alert('Digite seu nome'); return; }
                if(!address){ alert('Digite o endereço'); return; }
                if(!phone){ alert('Digite o telefone'); return; }
                message += `\nTipo: Encomenda\nNome: ${name}\nEndereço: ${address}\nTelefone: ${phone}`;
            }

            message += `\n\nTotal: KZ ${total.toFixed(2).replace('.', ',')}`;

            const whatsappNumber = '258936351564'; // Substitua pelo número real
            window.open(`https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`, '_blank');

            // Limpa carrinho e campos
            if(window.menuCart){
                window.menuCart.cart = [];
                window.menuCart.saveCart();
                window.menuCart.updateCartDisplay();
            }
            document.querySelectorAll('#customerName,#customerTable,#orderName,#orderAddress,#orderPhone').forEach(input => input.value = '');
        });
    });
    </script>
</body>
</html>
