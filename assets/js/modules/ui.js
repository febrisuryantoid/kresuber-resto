export class UIManager {
    constructor() {
        this.grid = document.getElementById('k-grid');
        this.cartList = document.getElementById('k-cart-list');
    }
    formatMoney(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

    renderProducts(products, onAdd) {
        if(!this.grid) return; // Guard clause
        
        if(products.length === 0) {
            this.grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;">Tidak ada produk.</div>';
            return;
        }

        this.grid.innerHTML = products.map(p => `
            <div class="k-card-prod" onclick="window.triggerAdd(${p.id})">
                <img src="${p.image}" class="k-card-img" loading="lazy">
                <div class="k-card-title">${p.name}</div>
                <div class="k-card-price">${this.formatMoney(p.price)}</div>
                <div class="k-btn-add-float"><i class="ri-add-line"></i></div>
            </div>
        `).join('');
    }

    renderCart(state, handlers) {
        if(!this.cartList) return;

        if (state.items.length === 0) {
            this.cartList.innerHTML = `<div style="text-align:center; padding:40px; color:#999;"><i class="ri-shopping-basket-2-line" style="font-size:32px;"></i><p>Kosong</p></div>`;
            if(document.getElementById('k-btn-checkout')) document.getElementById('k-btn-checkout').disabled = true;
        } else {
            this.cartList.innerHTML = state.items.map(item => `
                <div class="k-cart-item">
                    <img src="${item.image}" class="k-cart-thumb">
                    <div class="k-cart-info">
                        <span class="k-cart-name">${item.name}</span>
                        <span class="k-cart-price">${this.formatMoney(item.price)}</span>
                    </div>
                    <div class="k-stepper">
                        <div class="k-step-btn" onclick="window.triggerUpdate(${item.id}, -1)">-</div>
                        <div class="k-step-val">${item.qty}</div>
                        <div class="k-step-btn" onclick="window.triggerUpdate(${item.id}, 1)">+</div>
                    </div>
                </div>
            `).join('');
            if(document.getElementById('k-btn-checkout')) document.getElementById('k-btn-checkout').disabled = false;
        }

        if(document.getElementById('k-subtotal')) {
            document.getElementById('k-subtotal').innerText = this.formatMoney(state.subtotal);
            document.getElementById('k-total').innerText = this.formatMoney(state.total);
        }
    }
}