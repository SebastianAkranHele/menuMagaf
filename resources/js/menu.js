// ===============================
// MenuCart - Carrinho persistente
// ===============================
class MenuCart {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('menuCart')) || [];
        this.init();
    }

    init() {
        this.bindCartModal();
        this.updateCartDisplay();
    }

    saveCart() {
        localStorage.setItem('menuCart', JSON.stringify(this.cart));
    }

    bindCartModal() {
        const floatingCart = document.getElementById('floatingCart');
        const closeModalBtn = document.querySelector('.close-modal');
        const checkoutBtn = document.querySelector('.checkout-btn');
        const cartModal = document.getElementById('cartModal');

        if (floatingCart) floatingCart.addEventListener('click', () => this.openCartModal());
        if (closeModalBtn) closeModalBtn.addEventListener('click', () => this.closeCartModal());
        if (checkoutBtn) checkoutBtn.addEventListener('click', () => this.checkout());

        document.addEventListener('click', (e) => {
            if (e.target === cartModal) this.closeCartModal();
        });
    }

    addToCart(menuItem) {
        const id = menuItem.dataset.id; // <- ESSENCIAL
        const title = menuItem.querySelector('.item-title span:first-child')?.textContent;
        const priceText = menuItem.querySelector('.item-price')?.textContent
            .replace('KZ ', '').replace('.', '').replace(',', '.');
        const price = parseFloat(priceText);
        const image = menuItem.querySelector('.item-image img')?.src;

        if (!id || !title || isNaN(price)) return;

        const existingItem = this.cart.find(item => item.id === id);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            this.cart.push({ id, title, price, image, quantity: 1 });
        }

        this.saveCart();
        this.updateCartDisplay();
        this.showAddedFeedback(menuItem);
    }

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

    updateCartModal() {
        const cartItems = document.querySelector('.cart-items');
        const cartTotal = document.getElementById('cartTotal');

        if (!cartItems) return;

        if (!this.cart.length) {
            cartItems.innerHTML = '<p class="empty-cart-message">Seu carrinho está vazio.</p>';
            if (cartTotal) cartTotal.textContent = 'KZ 0,00';
            return;
        }

        cartItems.innerHTML = '';
        let total = 0;

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

        if (cartTotal) cartTotal.textContent = `KZ ${total.toFixed(2).replace('.', ',')}`;

        this.bindCartItemButtons();
    }

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

    updateQuantity(index, change) {
        this.cart[index].quantity += change;
        if (this.cart[index].quantity <= 0) this.cart.splice(index, 1);
        this.saveCart();
        this.updateCartDisplay();
    }

    removeItem(index) {
        this.cart.splice(index, 1);
        this.saveCart();
        this.updateCartDisplay();
    }

    openCartModal() {
        this.updateCartModal();
        const cartModal = document.getElementById('cartModal');
        if (cartModal) cartModal.style.display = 'flex';
    }

    closeCartModal() {
        const cartModal = document.getElementById('cartModal');
        if (cartModal) cartModal.style.display = 'none';
    }

    async checkout() {
        if (!this.cart.length) {
            alert('Seu carrinho está vazio.');
            return;
        }

        try {
            const items = this.cart.map(i => {
                if (!i.id) throw new Error('Todos os produtos precisam ter um ID válido para salvar o pedido.');
                return { product_id: i.id, quantity: i.quantity, price: i.price };
            });

            const total = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            let orderSaved = false;

            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ items, total })
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

            // Montar mensagem WhatsApp
            let message = 'Olá! Gostaria de fazer o seguinte pedido:\n\n';
            this.cart.forEach(item => {
                message += `• ${item.quantity}x ${item.title} - KZ ${(item.price * item.quantity).toFixed(2).replace('.', ',')}\n`;
            });
            message += `\nTotal: KZ ${total.toFixed(2).replace('.', ',')}\n\n`;
            if (!orderSaved) message += 'Observação: Pedido não registrado no sistema.\n\n';
            message += 'Agradeço desde já!';
            window.open(`https://wa.me/+244936351564?text=${encodeURIComponent(message)}`, '_blank');

            this.cart = [];
            this.saveCart();
            this.updateCartDisplay();

        } catch (err) {
            console.error('Erro no checkout:', err);
            alert('Ocorreu um erro ao processar o pedido.');
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
        productContainer.innerHTML = `<div class="loading-state"><i class="fas fa-spinner fa-spin"></i><p>Carregando menu...</p></div>`;
        const [catRes, prodRes] = await Promise.allSettled([fetch('/api/categories'), fetch('/api/products')]);

        let categories = [], products = [];

        if (catRes.status === 'fulfilled' && catRes.value.ok) {
            const catJson = await catRes.value.json();
            categories = Array.isArray(catJson) ? catJson : (catJson.data || []);
        } else categories = await loadFallbackCategories();

        if (prodRes.status === 'fulfilled' && prodRes.value.ok) {
            const prodJson = await prodRes.value.json();
            products = Array.isArray(prodJson) ? prodJson : (prodJson.data || []);
        } else products = await loadFallbackProducts();

        renderCategories(categories, categoryContainer);
        renderProducts(products, productContainer);

    } catch (err) {
        console.error('Erro ao carregar menu:', err);
        productContainer.innerHTML = `<div class="error-state"><i class="fas fa-exclamation-triangle"></i><p>Erro ao carregar produtos.</p><button onclick="loadMenuData()">Tentar Novamente</button></div>`;
    }
}

// ===============================
// Fallbacks
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
// Renderização
// ===============================
function renderCategories(categories, container) {
    container.innerHTML = '<button class="category-filter active" data-category="all">Todos</button>';
    categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'category-filter';
        btn.dataset.category = cat.slug;
        btn.textContent = cat.name;
        container.appendChild(btn);
    });
}

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

function createProductElement(product) {
    const div = document.createElement('div');
    div.className = 'menu-item';
    div.dataset.id = product.id; // <- ID necessário
    div.dataset.category = product.category?.slug || 'sem-categoria';

    const price = parseFloat(product.price) || 0;
    const imageUrl = product.image ? ('/storage/' + product.image) : 'https://via.placeholder.com/300x200?text=Sem+Imagem';

    div.innerHTML = `
        <div class="item-image">
            <img src="${imageUrl}" alt="${product.name}" onerror="this.src='https://via.placeholder.com/300x200?text=Imagem+Não+Encontrada'">
        </div>
        <div class="item-info">
            <div class="item-title">
                <span>${product.name}</span>
                <span class="item-price">KZ ${price.toFixed(2).replace('.', ',')}</span>
            </div>
            <p class="item-desc">${product.description?.substring(0,50) || 'Sem descrição'}...</p>
            <div class="item-actions">
                <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Adicionar</button>
            </div>
        </div>
    `;
    return div;
}

// ===============================
// Inicializar funcionalidades
// ===============================
function initializeMenuFeatures() {
    const menuCart = window.menuCart || new MenuCart();
    window.menuCart = menuCart;

    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.onclick = (e) => {
            const item = e.target.closest('.menu-item');
            if (item) menuCart.addToCart(item);
        };
    });

    document.querySelectorAll('.category-filter').forEach(btn => {
        btn.onclick = (e) => {
            const category = e.currentTarget.dataset.category;
            filterItems(category);
            document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('active'));
            e.currentTarget.classList.add('active');
        };
    });

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.oninput = (e) => filterItemsBySearch(e.target.value);
    }

    initializeProductModal(menuCart);
}

// ===============================
// Filtros
// ===============================
function filterItems(category) {
    document.querySelectorAll('.menu-item').forEach(item => {
        if (category === 'all' || item.dataset.category === category) item.style.display = 'block';
        else item.style.display = 'none';
    });
}

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
    const modalDescription = modal.querySelector('.modal-description');
    const modalPrice = modal.querySelector('.modal-price');
    const modalAddBtn = modal.querySelector('.modal-add-to-cart');
    const closeBtn = modal.querySelector('.close-modal');

    document.querySelectorAll('.menu-item').forEach(item => {
        item.onclick = (e) => {
            if (e.target.closest('.add-to-cart')) return;

            const title = item.querySelector('.item-title span:first-child')?.textContent;
            const priceText = item.querySelector('.item-price')?.textContent.replace('KZ ', '').replace('.', '').replace(',', '.');
            const price = parseFloat(priceText);
            const image = item.querySelector('.item-image img')?.src;
            const desc = item.querySelector('.item-desc')?.textContent || 'Sem descrição';

            modalImage.src = image;
            modalTitle.textContent = title;
            modalDescription.textContent = desc;
            modalPrice.textContent = `KZ ${price.toFixed(2).replace('.', ',')}`;
            modal.style.display = 'flex';

            modalAddBtn.onclick = () => {
                menuCart.addToCart(item);
                modal.style.display = 'none';
            };
        };
    });

    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };
}

// ===============================
// Inicializar
// ===============================
document.addEventListener('DOMContentLoaded', () => {
    console.log('Menu Digital inicializando...');
    loadMenuData();
});
