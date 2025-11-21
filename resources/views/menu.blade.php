<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - Garrafeira das 5 Curvas</title>

    @hasSection('auto-refresh')
        <meta http-equiv="refresh" content="@yield('auto-refresh', 30)">
    @endif

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

            {{-- Filtros --}}
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
        <div class="floating-cart" id="floatingCart" style="right: 20px;">
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

    {{-- 💬 Botão Flutuante do Chat --}}
    <button id="open-chatbot" class="btn btn-primary shadow"
        style="position: fixed; bottom: 20px; left: 20px; border-radius: 50%;
               width: 60px; height: 60px; font-size: 26px; display: flex;
               align-items: center; justify-content: center; z-index: 999;">
        <i class="fas fa-comment-dots"></i>
    </button>

    {{-- Janela Flutuante do Chatbot --}}
    <div id="chatWindow"
         style="position: fixed; bottom: 90px; left: 20px; width: 320px; background: white;
                border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                overflow: hidden; display: none; z-index: 1000;">
        <div style="background-color: #0d6efd; color: white; padding: 10px 15px;
                    display: flex; justify-content: space-between; align-items: center;">
            <strong>🤖 Assistente</strong>
            <button id="closeChat"
                    style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">×</button>
        </div>

        <div id="chat-box"
             style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px;">
            <div class="text-muted text-center small">Olá 👋! Posso te ajudar com os produtos do menu. 🍷</div>
        </div>

        <form id="chat-form" style="padding: 10px; border-top: 1px solid #ddd; background: #fff;">
            <div style="display: flex; gap: 5px;">
                <input type="text" id="user-input" class="form-control"
                       placeholder="Escreve tua pergunta..." required>
                <button class="btn btn-primary" type="submit">Enviar</button>
            </div>
        </form>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    const openChat = document.getElementById('open-chatbot');
    const chatWindow = document.getElementById('chatWindow');
    const closeChat = document.getElementById('closeChat');
    const chatForm = document.getElementById('chat-form');
    const chatBox = document.getElementById('chat-box');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const chatbotUrl = "{{ url('/chatbot') }}";

    // Abrir e fechar chat
    openChat.addEventListener('click', () => {
        chatWindow.style.display = 'block';
        openChat.style.display = 'none';
    });

    closeChat.addEventListener('click', () => {
        chatWindow.style.display = 'none';
        openChat.style.display = 'flex';
    });

    // Envio de mensagem
    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const input = document.getElementById('user-input');
        const userMessage = input.value.trim();
        if (!userMessage) return;

        chatBox.innerHTML += `<div class="text-end mb-2"><span class="badge bg-primary p-2">${userMessage}</span></div>`;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        try {
            const response = await fetch(chatbotUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: userMessage })
            });

            const data = await response.json();
            let botMsg = data.message || "Desculpe, ocorreu um erro.";

            botMsg = botMsg.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>');

            chatBox.innerHTML += `<div class="text-start mt-2"><span class="badge bg-light text-dark p-2" style="white-space: pre-line;">${botMsg}</span></div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        } catch (error) {
            chatBox.innerHTML += `<div class="text-start mt-2"><span class="badge bg-danger text-white p-2">Erro de conexão.</span></div>`;
        }
    });

    // Inicializa menu
    document.addEventListener('DOMContentLoaded', () => {
        if(typeof loadMenuData === 'function') loadMenuData();
    });
    </script>

</body>
</html>
