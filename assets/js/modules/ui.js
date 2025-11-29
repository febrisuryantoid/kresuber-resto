export class UIManager {
    constructor() {
        this.grid = document.getElementById('k-grid');
        this.cartList = document.getElementById('k-cart-list');
        this.subtotalElement = document.getElementById('k-subtotal');
        this.taxElement = document.getElementById('k-tax');
        this.totalElement = document.getElementById('k-total');
        this.checkoutButton = document.getElementById('k-btn-checkout');
    }
    formatMoney(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

    renderProducts(products) {
        if(!this.grid) return; // Guard clause
        
        if(products.length === 0) {
            this.grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;">Tidak ada produk.</div>';
            return;
        }

        this.grid.innerHTML = products.map(p => `
            <div class="k-card-prod" data-product-id="${p.id}">
                <img src="${p.image}" class="k-card-img" loading="lazy" alt="${p.name}">
                <div class="k-card-title">${p.name}</div>
                <div class="k-card-price">${this.formatMoney(p.price)}</div>
                <button class="k-btn-add-float" onclick="window.triggerAdd(${p.id})"><i class="ri-add-line"></i></button>
            </div>
        `).join('');
    }

    renderCart(state) {
        if(!this.cartList) return;

        if (state.items.length === 0) {
            this.cartList.innerHTML = `<div style="text-align:center; padding:40px; color:#999;"><i class="ri-shopping-basket-2-line" style="font-size:32px;"></i><p>Keranjang kosong. Tambahkan produk!</p></div>`;
            if(this.checkoutButton) this.checkoutButton.disabled = true;
        } else {
            this.cartList.innerHTML = state.items.map(item => `
                <div class="pos-cart-item">
                    <img src="${item.image}" class="pos-cart-item-thumbnail" alt="${item.name}">
                    <div class="pos-cart-item-details">
                        <div class="pos-cart-item-name">${item.name}</div>
                        <div class="pos-cart-item-price-qty">${this.formatMoney(item.price)} x ${item.qty} = ${this.formatMoney(item.price * item.qty)}</div>
                    </div>
                    <div class="pos-cart-item-actions">
                        <div class="pos-qty-stepper">
                            <button onclick="window.triggerUpdate(${item.id}, -1)">-</button>
                            <span>${item.qty}</span>
                            <button onclick="window.triggerUpdate(${item.id}, 1)">+</button>
                        </div>
                        <!-- <button class="pos-add-notes-btn">Add Notes</button> -->
                        <button class="pos-remove-item-btn" onclick="window.triggerRemove(${item.id})"><i class="ri-delete-bin-line"></i></button>
                    </div>
                </div>
            `).join('');
            if(this.checkoutButton) this.checkoutButton.disabled = false;
        }

        if(this.subtotalElement) this.subtotalElement.innerText = this.formatMoney(state.subtotal);
        if(this.taxElement) this.taxElement.innerText = this.formatMoney(state.tax);
        if(this.totalElement) this.totalElement.innerText = this.formatMoney(state.total);
    }
}