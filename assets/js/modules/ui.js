export class UIManager {
    constructor() {
        this.grid = document.getElementById('k-grid');
        this.cartList = document.getElementById('k-cart-list');
        this.cartBadge = document.getElementById('k-cart-qty'); // Badge di header App
    }

    formatMoney(n) { 
        return 'Rp ' + parseInt(n).toLocaleString('id-ID'); 
    }

    renderProducts(products) {
        if(!this.grid) return; 
        
        if(products.length === 0) {
            this.grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#999;">Produk tidak ditemukan.</div>';
            return;
        }

        // Deteksi apakah sedang di User App (/app/) atau POS Terminal
        // Ini penting untuk menentukan aksi klik kartu
        const isUserApp = window.location.href.includes('/app');
        
        this.grid.innerHTML = products.map(p => {
            // Aksi Kartu: Jika App -> Buka Detail. Jika POS -> Tidak ada aksi (atau bisa tambah ke cart)
            const cardAction = isUserApp 
                ? `onclick="window.location.href='${KRESUBER.site_url}/app/product/${p.id}'"` 
                : ''; 

            return `
            <div class="k-card-prod" ${cardAction} data-product-id="${p.id}">
                <img src="${p.image}" class="k-card-img" loading="lazy" alt="${p.name}">
                <div class="k-card-title">${p.name}</div>
                <div class="k-card-price">${this.formatMoney(p.price)}</div>
                
                <button class="k-btn-add-float" onclick="event.stopPropagation(); window.triggerAdd(${p.id})">
                    <i class="ri-add-line"></i>
                </button>
            </div>
            `;
        }).join('');
    }

    renderCart(state) {
        // 1. Update Badge di Header App
        if(this.cartBadge) {
            const totalQty = state.items.reduce((acc, item) => acc + item.qty, 0);
            this.cartBadge.innerText = totalQty;
            this.cartBadge.style.display = totalQty > 0 ? 'flex' : 'none';
        }

        // 2. Render List di Panel POS / Cart Page
        // Jika elemen list tidak ada, skip (misal di halaman detail)
        if(!this.cartList) return;

        const checkoutBtn = document.getElementById('k-btn-checkout'); // Tombol di halaman Cart App

        if (state.items.length === 0) {
            this.cartList.innerHTML = `<div style="text-align:center; padding:40px; color:#999;"><p>Keranjang kosong.</p></div>`;
            
            // Disable tombol checkout di berbagai tempat
            if(checkoutBtn) checkoutBtn.style.display = 'none'; // Sembunyikan di Cart App
            if(document.querySelector('.btn-bill-payment')) document.querySelector('.btn-bill-payment').disabled = true;

        } else {
            this.cartList.innerHTML = state.items.map(item => `
                <div class="pos-cart-item">
                    <img src="${item.image}" class="pos-cart-item-thumbnail" alt="${item.name}">
                    <div class="pos-cart-item-details">
                        <div class="pos-cart-item-name">${item.name}</div>
                        <div class="pos-cart-item-price-qty">${this.formatMoney(item.price)} x ${item.qty}</div>
                    </div>
                    <div class="pos-qty-stepper">
                        <button onclick="window.triggerUpdate(${item.id}, -1)">-</button>
                        <span>${item.qty}</span>
                        <button onclick="window.triggerUpdate(${item.id}, 1)">+</button>
                    </div>
                </div>
            `).join('');
            
            // Update Totals
            if(document.getElementById('k-subtotal')) document.getElementById('k-subtotal').innerText = this.formatMoney(state.subtotal);
            if(document.getElementById('k-total')) document.getElementById('k-total').innerText = this.formatMoney(state.total);
            
            // Enable Checkout
            if(checkoutBtn) checkoutBtn.style.display = 'block';
            if(document.querySelector('.btn-bill-payment')) document.querySelector('.btn-bill-payment').disabled = false;
        }
    }

    renderCategories(categories) {
        const categoryDropdown = document.querySelector('.pos-category-dropdown');
        const categoryTabs = document.querySelector('.pos-category-tabs');

        // Render Dropdown (User App)
        if (categoryDropdown) {
            let dropdownHtml = '<option value="all">Semua Kategori</option>';
            categories.forEach(cat => { dropdownHtml += `<option value="${cat.slug}">${cat.name}</option>`; });
            categoryDropdown.innerHTML = dropdownHtml;
        }

        // Render Tabs (POS Terminal)
        if (categoryTabs) {
            let tabsHtml = '<div class="pos-tab-item active" data-slug="all">All</div>';
            categories.forEach(cat => { tabsHtml += `<div class="pos-tab-item" data-slug="${cat.slug}">${cat.name}</div>`; });
            categoryTabs.innerHTML = tabsHtml;
        }
    }
}