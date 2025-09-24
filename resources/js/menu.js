// ===============================
// MenuCart - Carrinho persistente
// ===============================
class MenuCart {
    constructor() {
        // Carrega o carrinho do localStorage ou cria um vazio
        this.cart = JSON.parse(localStorage.getItem('menuCart')) || [];
        this.init(); // inicializa eventos e estado
    }

    init() {
        this.bindCartModal();      // liga eventos do modal do carrinho
        this.updateCartDisplay();  // atualiza o contador e visual do carrinho
    }

    // Salva carrinho no localStorage
    saveCart() {
        localStorage.setItem('menuCart', JSON.stringify(this.cart));
    }

    // Liga os botões e eventos do modal do carrinho
    bindCartModal() {
        const floatingCart = document.getElementById('floatingCart'); // botão flutuante
        const closeModalBtn = document.querySelector('.close-modal'); // botão fechar modal
        const checkoutBtn = document.querySelector('.checkout-btn');  // botão finalizar pedido
        const cartModal = document.getElementById('cartModal');       // modal do carrinho

        if (floatingCart) floatingCart.addEventListener('click', () => this.openCartModal());
        if (closeModalBtn) closeModalBtn.addEventListener('click', () => this.closeCartModal());
        if (checkoutBtn) checkoutBtn.addEventListener('click', () => this.checkout());

        // Fecha o modal clicando fora do conteúdo
        document.addEventListener('click', (e) => {
            if (e.target === cartModal) this.closeCartModal();
        });
    }

    // Adiciona um item ao carrinho
    addToCart(menuItem) {
        const id = menuItem.dataset.id; // ID único do produto
        const title = menuItem.querySelector('.item-title span:first-child')?.textContent;
        // Extrai preço do texto e converte para número
        const priceText = menuItem.querySelector('.item-price')?.textContent
            .replace('KZ ', '').replace('.', '').replace(',', '.');
        const price = parseFloat(priceText);
        const image = menuItem.querySelector('.item-image img')?.src; // imagem do produto

        // Se não encontrou dados válidos, cancela
        if (!id || !title || isNaN(price)) return;

        // Procura se o produto já existe no carrinho
        const existingItem = this.cart.find(item => item.id === id);

        if (existingItem) {
            // Se já existe, aumenta a quantidade
            existingItem.quantity += 1;
        } else {
            // Se não existe, adiciona como novo
            this.cart.push({ id, title, price, image, quantity: 1 });
        }

        this.saveCart();           // salva no localStorage
        this.updateCartDisplay();  // atualiza o contador
        this.showAddedFeedback(menuItem); // mostra feedback visual
    }

    // Dá feedback no botão de adicionar (check verde temporário)
    showAddedFeedback(menuItem) {
        const button = menuItem.querySelector('.add-to-cart');
        if (!button) return;

        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Adicionado';
        button.style.background = '#27ae60';

        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '';
        }, 2000);
    }

    // Atualiza contador do carrinho e o modal se ele estiver aberto
    updateCartDisplay() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const totalItems = this.cart.reduce((total, item) => total + item.quantity, 0);
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
        }

        const cartModal = document.getElementById('cartModal');
        if (cartModal && cartModal.style.display === 'flex') {
            this.updateCartModal();
        }
    }

    // Atualiza a lista de produtos dentro do modal
    updateCartModal() {
        const cartItems = document.querySelector('.cart-items');
        const cartTotal = document.getElementById('cartTotal');

        if (!cartItems) return;

        // Se carrinho vazio
        if (!this.cart.length) {
            cartItems.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio.</p>';
            if (cartTotal) cartTotal.textContent = 'KZ 0,00';
            return;
        }

        cartItems.innerHTML = '';
        let total = 0;

        // Renderiza cada item
        this.cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <div class="cart-item-info">
                    <div class="cart-item-title">${item.title}</div>
                    <div class="cart-item-price">KZ ${item.price.toFixed(2).replace('.', ',')} × ${item.quantity}</div>
                </div>
                <div class="cart-item-actions">
                    <div class="quantity-controls">
                        <button class="quantity-btn minus" data-index="${index}">-</button>
                        <span>${item.quantity}</span>
                        <button class="quantity-btn plus" data-index="${index}">+</button>
                    </div>
                    <button class="remove-item" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            cartItems.appendChild(cartItem);
        });

        // Atualiza valor total
        if (cartTotal) cartTotal.textContent = `KZ ${total.toFixed(2).replace('.', ',')}`;

        this.bindCartItemButtons(); // liga botões de +, -, remover
    }

    // Liga eventos dos botões dentro do modal
    bindCartItemButtons() {
        document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
            btn.onclick = (e) => {
                const index = parseInt(e.currentTarget.dataset.index);
                this.updateQuantity(index, -1);
            };
        });

        document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
            btn.onclick = (e) => {
                const index = parseInt(e.currentTarget.dataset.index);
                this.updateQuantity(index, 1);
            };
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.onclick = (e) => {
                const index = parseInt(e.currentTarget.dataset.index);
                this.removeItem(index);
            };
        });
    }

    // Atualiza quantidade de um produto
    updateQuantity(index, change) {
        this.cart[index].quantity += change;
        if (this.cart[index].quantity <= 0) this.cart.splice(index, 1); // remove se <=0
        this.saveCart();
        this.updateCartDisplay();
    }

    // Remove item do carrinho
    removeItem(index) {
        this.cart.splice(index, 1);
        this.saveCart();
        this.updateCartDisplay();
    }

    // Abre o modal do carrinho
    openCartModal() {
        this.updateCartModal();
        const cartModal = document.getElementById('cartModal');
        if (cartModal) cartModal.style.display = 'flex';
    }

    // Fecha o modal do carrinho
    closeCartModal() {
        const cartModal = document.getElementById('cartModal');
        if (cartModal) cartModal.style.display = 'none';
    }

   // Finalizar pedido: salva no servidor e abre WhatsApp
// Finalizar pedido: salva no servidor e abre WhatsApp
async checkout() {
    if (!this.cart.length) {
        Swal.fire({
            icon: 'warning',
            title: 'Carrinho vazio',
            text: 'Adicione produtos antes de finalizar o pedido.',
            confirmButtonColor: '#d33'
        });
        return;
    }

    const customerNameInput = document.getElementById('customerName');
    const customerTableInput = document.getElementById('customerTable');

    const customerName = customerNameInput?.value.trim();
    const customerTable = customerTableInput?.value.trim();

    // 🔹 Validação do nome
    if (!customerName) {
        Swal.fire({
            icon: 'error',
            title: 'Nome obrigatório',
            text: 'Por favor, digite seu nome.',
            confirmButtonColor: '#d33'
        });
        return;
    }

    // 🔹 Validação da mesa
    if (!customerTable) {
        Swal.fire({
            icon: 'error',
            title: 'Mesa obrigatória',
            text: 'Por favor, informe a mesa em que você está.',
            confirmButtonColor: '#d33'
        });
        return;
    }

    try {
        // Prepara dados para API
        const items = this.cart.map(i => {
            if (!i.id) throw new Error('Todos os produtos precisam ter um ID válido para salvar o pedido.');
            return { product_id: i.id, quantity: i.quantity, price: i.price };
        });

        const total = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
        let orderSaved = false;

        // Tenta salvar no backend
        try {
            const res = await fetch('/api/orders', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ items, total, customer_name: customerName, customer_table: customerTable })
            });

            if (!res.ok) throw new Error(`Erro HTTP! status: ${res.status}`);
            const data = await res.json();
            if (data.success) {
                orderSaved = true;
                console.log('Pedido salvo com sucesso no servidor. ID:', data.order_id);
            } else console.warn('Erro ao salvar pedido:', data.message || data);
        } catch (err) {
            console.warn('Não foi possível salvar o pedido no servidor:', err);
        }

        // Monta mensagem para WhatsApp
        let message = `Olá! Meu nome é ${customerName} (Mesa ${customerTable}).\nGostaria de fazer o seguinte pedido:\n\n`;
        this.cart.forEach(item => {
            message += `• ${item.quantity}x ${item.title} - KZ ${(item.price * item.quantity).toFixed(2).replace('.', ',')}\n`;
        });
        message += `\nTotal: KZ ${total.toFixed(2).replace('.', ',')}\n\n`;
        if (!orderSaved) message += 'Observação: Pedido não registrado no sistema.\n\n';
        message += 'Agradeço desde já!';

        // Abre WhatsApp com mensagem
        window.open(`https://wa.me/+244936351564?text=${encodeURIComponent(message)}`, '_blank');

        // Limpa carrinho e campos
        this.cart = [];
        this.saveCart();
        this.updateCartDisplay();
        if (customerNameInput) customerNameInput.value = '';
        if (customerTableInput) customerTableInput.value = '';

        // SweetAlert de sucesso
        Swal.fire({
            icon: 'success',
            title: 'Pedido enviado!',
            text: 'Seu pedido foi enviado com sucesso para o WhatsApp.',
            confirmButtonColor: '#28a745'
        });

    } catch (err) {
        console.error('Erro no checkout:', err);
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Ocorreu um erro ao processar o pedido.',
            confirmButtonColor: '#d33'
        });
    }
}



}

// ===============================
// Carregar categorias e produtos
// ===============================
window.loadMenuData = async function() {
    const categoryContainer = document.querySelector('.category-filters');
    const productContainer = document.querySelector('.menu-items');
    if (!categoryContainer || !productContainer) return;

    try {
        // Mostra estado de carregando
        productContainer.innerHTML = `<div class="loading-state"><i class="fas fa-spinner fa-spin"></i><p>Carregando menu...</p></div>`;

        // Busca categorias e produtos da API
        const [catRes, prodRes] = await Promise.allSettled([fetch('/api/categories'), fetch('/api/products')]);

        let categories = [], products = [];

        // Trata categorias
        if (catRes.status === 'fulfilled' && catRes.value.ok) {
            const catJson = await catRes.value.json();
            categories = Array.isArray(catJson) ? catJson : (catJson.data || []);
        } else categories = await loadFallbackCategories();

        // Trata produtos
        if (prodRes.status === 'fulfilled' && prodRes.value.ok) {
            const prodJson = await prodRes.value.json();
            products = Array.isArray(prodJson) ? prodJson : (prodJson.data || []);
        } else products = await loadFallbackProducts();

        // Renderiza
        renderCategories(categories, categoryContainer);
        renderProducts(products, productContainer);

    } catch (err) {
        console.error('Erro ao carregar menu:', err);
        productContainer.innerHTML = `<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Erro ao carregar produtos.</p><button onclick="loadMenuData()">Tentar Novamente</button></div>`;
    }
}

// ===============================
// Fallbacks - dados simulados
// ===============================
async function loadFallbackCategories() {
    return [
        { id: 1, name: "Bebidas", slug: "bebidas" },
        { id: 2, name: "Petiscos", slug: "petiscos" },
        { id: 3, name: "Pratos", slug: "pratos" },
        { id: 4, name: "Sobremesas", slug: "sobremesas" }
    ];
}

async function loadFallbackProducts() {
    return [
        { id: 1, name: "Vinho Tinto Reserva", description: "Vinho tinto selecionado da casa", price: 25.90, image: "/images/vinho.jpg", category: { slug: "bebidas" } },
        { id: 2, name: "Cerveja Artesanal", description: "Cerveja premium 500ml", price: 18.50, image: "/images/cerveja.jpg", category: { slug: "bebidas" } },
        { id: 3, name: "Tabua de Queijos", description: "Seleção de queijos especiais", price: 45.00, image: "/images/queijos.jpg", category: { slug: "petiscos" } },
        { id: 4, name: "Azeitonas Temperadas", description: "Azeitonas com ervas finas", price: 12.00, image: "/images/azeitonas.jpg", category: { slug: "petiscos" } }
    ];
}

// ===============================
// Renderização de categorias
// ===============================
function renderCategories(categories, container) {
    // Botão "Todos"
    container.innerHTML = '<button class="category-filter active" data-category="all">Todos</button>';
    // Cria um botão para cada categoria
    categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'category-filter';
        btn.dataset.category = cat.slug;
        btn.textContent = cat.name;
        container.appendChild(btn);
    });
}

// Renderização dos produtos
function renderProducts(products, container) {
    container.innerHTML = '';
    if (!products.length) {
        container.innerHTML = '<p class="no-products">Nenhum produto disponível.</p>';
        return;
    }

    products.forEach(prod => {
        const div = createProductElement(prod);
        container.appendChild(div);
    });

    initializeMenuFeatures();
}

// ===============================
// Cria o HTML de um produto
// ===============================
function createProductElement(product) {
    const div = document.createElement('div');
    div.className = 'menu-item';
    div.dataset.id = product.id; // ID necessário para o carrinho
    div.dataset.category = product.category?.slug || 'sem-categoria';
    div.dataset.categoryName = product.category?.name || 'Sem categoria';
    div.dataset.description = product.description || 'Sem descrição';

    const price = parseFloat(product.price) || 0;
    const imageUrl = product.image
        ? ('/storage/' + product.image)
        : 'https://via.placeholder.com/300x200?text=Sem+Imagem';

    div.innerHTML = `
        <div class="item-image">
            <img src="${imageUrl}" alt="${product.name}"
                 onerror="this.src='https://via.placeholder.com/300x200?text=Imagem+Não+Encontrada'">
        </div>
        <div class="item-info">
            <div class="item-title">
                <span>${product.name}</span>
                <span class="item-price">KZ ${price.toFixed(2).replace('.', ',')}</span>
            </div>
            <div class="item-category">${product.category?.name || 'Sem categoria'}</div>
            <p class="item-desc">${product.description?.substring(0, 60) || 'Sem descrição'}...</p>
            <div class="item-actions">
                <button class="view-details-btn"><i class="fas fa-eye"></i> Ver Detalhes</button>
                <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Adicionar</button>
            </div>
        </div>
    `;
    return div;
}

// ===============================
// Inicializar funcionalidades gerais
// ===============================
function initializeMenuFeatures() {
    const menuCart = window.menuCart || new MenuCart();
    window.menuCart = menuCart;

    // Clique no botão de adicionar ao carrinho
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.onclick = (e) => {
            const item = e.target.closest('.menu-item');
            if (item) menuCart.addToCart(item);
        };
    });

    // Filtros por categoria
    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.onclick = (e) => {
            const category = e.currentTarget.dataset.category;
            filterItems(category);
            document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('active'));
            e.currentTarget.classList.add('active');
        };
    });

    // Filtro por pesquisa
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.oninput = (e) => filterItemsBySearch(e.target.value);
    }

    // Inicializa modal de produto
    initializeProductModal(menuCart);
}

// ===============================
// Filtros
// ===============================

// Filtro por categoria
function filterItems(category) {
    document.querySelectorAll('.menu-item').forEach(item => {
        if (category === 'all' || item.dataset.category === category) item.style.display = 'block';
        else item.style.display = 'none';
    });
}

// Filtro por pesquisa (nome e descrição)
function filterItemsBySearch(term) {
    const searchTerm = term.toLowerCase().trim();
    document.querySelectorAll('.menu-item').forEach(item => {
        const title = item.querySelector('.item-title span:first-child')?.textContent.toLowerCase() || '';
        const desc = item.querySelector('.item-desc')?.textContent.toLowerCase() || '';
        if (title.includes(searchTerm) || desc.includes(searchTerm)) item.style.display = 'block';
        else item.style.display = 'none';
    });
}

// ===============================
// Modal de Produto
// ===============================
function initializeProductModal(menuCart) {
    const modal = document.getElementById('productModal');
    if (!modal) return;

    const modalImage = modal.querySelector('.modal-image img');
    const modalTitle = modal.querySelector('.modal-title');
    const modalCategory = modal.querySelector('.modal-category');
    const modalDescription = modal.querySelector('.modal-description');
    const modalPrice = modal.querySelector('.modal-price');
    const modalAddBtn = modal.querySelector('.modal-add-to-cart');
    const closeBtn = modal.querySelector('.close-modal');

    // Clique no botão "Ver Detalhes"
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.onclick = (e) => {
            const item = e.target.closest('.menu-item');
            if (!item) return;

            const title = item.querySelector('.item-title span:first-child')?.textContent;
            const priceText = item.querySelector('.item-price')?.textContent
                .replace('KZ ', '').replace('.', '').replace(',', '.');
            const price = parseFloat(priceText);
            const image = item.querySelector('.item-image img')?.src;
            const desc = item.dataset.description || 'Sem descrição';
            const category = item.dataset.categoryName || 'Sem categoria';

            // Preenche modal
            modalImage.src = image;
            modalTitle.textContent = title;
            modalCategory.textContent = `Categoria: ${category}`;
            modalDescription.textContent = desc;
            modalPrice.textContent = `KZ ${price.toFixed(2).replace('.', ',')}`;
            modal.style.display = 'flex';

            // Botão adicionar dentro do modal
            modalAddBtn.onclick = () => {
                menuCart.addToCart(item);
                modal.style.display = 'none';
            };
        };
    });

    // Fechar modal
    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };
}

// ===============================
// Inicializar ao carregar página
// ===============================
document.addEventListener('DOMContentLoaded', () => {
    console.log('Menu Digital inicializando...');
    loadMenuData(); // carrega categorias e produtos
});
