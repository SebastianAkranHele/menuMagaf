// ===============================
// MenuCart - Carrinho persistente (Laravel-ready)
// ===============================
class MenuCart {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('menuCart')) || [];
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
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
        const id = menuItem.dataset.id;
        const title = menuItem.querySelector('.item-title span:first-child')?.textContent;
        const priceText = menuItem.querySelector('.item-price')?.textContent
            .replace('KZ ', '').replace('.', '').replace(',', '.');
        const price = parseFloat(priceText);
        const image = menuItem.querySelector('.item-image img')?.src;

        if (!id || !title || isNaN(price)) return;

        const existingItem = this.cart.find(item => item.id === id);
        if (existingItem) existingItem.quantity += 1;
        else this.cart.push({ id, title, price, image, quantity: 1 });

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
        if (cartModal && cartModal.style.display === 'flex') this.updateCartModal();
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
            btn.onclick = e => this.updateQuantity(parseInt(e.currentTarget.dataset.index), -1);
        });
        document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
            btn.onclick = e => this.updateQuantity(parseInt(e.currentTarget.dataset.index), 1);
        });
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.onclick = e => this.removeItem(parseInt(e.currentTarget.dataset.index));
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

    // ===============================
    // Checkout Laravel-ready (com CSRF)
    // ===============================
    async checkout() {
        if (!this.cart.length) {
            Swal.fire({ icon:'warning', title:'Carrinho vazio', text:'Adicione produtos antes de finalizar o pedido.', confirmButtonColor:'#d33' });
            return;
        }

        const customerNameInput = document.getElementById('customerName');
        const customerTableInput = document.getElementById('customerTable');

        const customerName = customerNameInput?.value.trim();
        const customerTable = customerTableInput?.value.trim();

        if (!customerName) { Swal.fire({ icon:'error', title:'Nome obrigatório', text:'Digite seu nome.', confirmButtonColor:'#d33' }); return; }
        if (!customerTable) { Swal.fire({ icon:'error', title:'Mesa obrigatória', text:'Informe a mesa.', confirmButtonColor:'#d33' }); return; }

        try {
            const items = this.cart.map(i => ({ product_id: parseInt(i.id), quantity: i.quantity, price: i.price }));
            const total = items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            let orderSaved = false;

            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type':'application/json',
                        'Accept':'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({ items, total, customer_name: customerName, customer_table: customerTable })
                });

                if (!res.ok) throw new Error(`Erro HTTP! status: ${res.status}`);
                const data = await res.json();
                if (data.success) { orderSaved = true; console.log('Pedido salvo. ID:', data.order_id); }
            } catch (err) {
                console.warn('Não foi possível salvar no servidor:', err);
            }

            let message = `Olá! Meu nome é ${customerName} (Mesa ${customerTable}).\nGostaria de fazer o seguinte pedido:\n\n`;
            this.cart.forEach(item => message += `• ${item.quantity}x ${item.title} - KZ ${(item.price*item.quantity).toFixed(2).replace('.',',')}\n`);
            message += `\nTotal: KZ ${total.toFixed(2).replace('.',',')}\n\n`;
            if (!orderSaved) message += 'Observação: Pedido não registrado no sistema.\n\n';
            message += 'Obrigado!';

            window.open(`https://wa.me/+244936351564?text=${encodeURIComponent(message)}`, '_blank');

            this.cart = [];
            this.saveCart();
            this.updateCartDisplay();
            if (customerNameInput) customerNameInput.value = '';
            if (customerTableInput) customerTableInput.value = '';

            Swal.fire({ icon:'success', title:'Pedido enviado!', text:'Pedido enviado com sucesso para WhatsApp.', confirmButtonColor:'#28a745' });

        } catch (err) {
            console.error('Erro no checkout:', err);
            Swal.fire({ icon:'error', title:'Erro', text:'Ocorreu um erro ao processar o pedido.', confirmButtonColor:'#d33' });
        }
    }
}

// ===============================
// Inicialização do menu
// ===============================
document.addEventListener('DOMContentLoaded', () => {
    window.menuCart = window.menuCart || new MenuCart();
    loadMenuData();
});
