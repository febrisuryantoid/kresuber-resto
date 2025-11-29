import { CartManager } from './modules/cart.js';

// --- UI MANAGER CLASS (Inline untuk kemudahan) ---
class UIManager {
    constructor() {
        this.grid = document.getElementById('k-grid');
        this.cartList = document.getElementById('k-cart-list');
        this.cartBadge = document.getElementById('k-cart-qty');
    }

    formatMoney(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }

    renderProducts(products) {
        if(!this.grid) return; 
        
        if(products.length === 0) {
            // Teks Dinamis via KRESUBER.current_lang (Dari wp_localize_script)
            const emptyText = (typeof KRESUBER !== 'undefined' && KRESUBER.current_lang === 'en') ? 'Product not found.' : 'Produk tidak ditemukan.';
            this.grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:#999;">${emptyText}</div>`;
            return;
        }

        const isUserApp = window.location.href.includes('/app');
        
        this.grid.innerHTML = products.map(p => {
            // Aksi Kartu: Jika App -> Buka Detail. Jika POS -> Tidak ada aksi
            const cardAction = isUserApp ? `onclick="window.location.href='${KRESUBER.site_url}/app/product/${p.id}'"` : ''; 
            
            // Logic Ikon Hati
            const heartClass = p.is_favorite ? 'ri-heart-fill active' : 'ri-heart-line';
            const btnClass = p.is_favorite ? 'k-btn-fav active' : 'k-btn-fav';

            return `
            <div class="k-card-prod" ${cardAction} data-product-id="${p.id}">
                <div class="${btnClass}" onclick="event.stopPropagation(); window.triggerFavorite(${p.id}, this)">
                    <i class="${heartClass}"></i>
                </div>

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
        if(this.cartBadge) {
            const totalQty = state.items.reduce((acc, item) => acc + item.qty, 0);
            this.cartBadge.innerText = totalQty;
            this.cartBadge.style.display = totalQty > 0 ? 'flex' : 'none';
        }

        if(!this.cartList) return;

        const checkoutBtn = document.getElementById('k-btn-checkout');

        if (state.items.length === 0) {
            const emptyTxt = (typeof KRESUBER !== 'undefined' && KRESUBER.current_lang === 'en') ? 'Cart is empty.' : 'Keranjang kosong.';
            this.cartList.innerHTML = `<div style="text-align:center; padding:40px; color:#999;"><p>${emptyTxt}</p></div>`;
            
            if(checkoutBtn) checkoutBtn.style.display = 'none';
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
            
            if(document.getElementById('k-subtotal')) document.getElementById('k-subtotal').innerText = this.formatMoney(state.subtotal);
            if(document.getElementById('k-total')) document.getElementById('k-total').innerText = this.formatMoney(state.total);
            
            if(checkoutBtn) checkoutBtn.style.display = 'block';
            if(document.querySelector('.btn-bill-payment')) document.querySelector('.btn-bill-payment').disabled = false;
        }
    }

    renderCategories(categories) {
        const categoryDropdown = document.querySelector('.pos-category-dropdown');
        const categoryTabs = document.querySelector('.pos-category-tabs');
        
        if (categoryDropdown) { 
            let html = '<option value="all">Semua Kategori</option>'; 
            categories.forEach(cat => { html += `<option value="${cat.slug}">${cat.name}</option>`; }); 
            categoryDropdown.innerHTML = html; 
        }
        
        if (categoryTabs) { 
            let html = '<div class="pos-tab-item active" data-slug="all">All</div>'; 
            categories.forEach(cat => { html += `<div class="pos-tab-item" data-slug="${cat.slug}">${cat.name}</div>`; }); 
            categoryTabs.innerHTML = html; 
        }
    }
}

// --- MAIN EXECUTION ---
jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    
    let productsCache = [];
    let tablesCache = [];

    // --- 1. GLOBAL FUNCTIONS ---

    // A. LANGUAGE SWITCHER (Fitur Baru)
    window.switchLang = (lang) => {
        // Set Cookie 30 hari
        document.cookie = `k_app_lang=${lang}; path=/; max-age=${60*60*24*30}`;
        // Reload halaman untuk apply bahasa PHP
        window.location.reload();
    };

    // B. ADD TO CART
    window.triggerAdd = (id) => { 
        const p = productsCache.find(x => x.id == id);
        if(p) {
            UI.renderCart(Cart.add(p));
            // Visual Feedback
            const btn = document.querySelector(`button[onclick*="triggerAdd(${id})"] i`);
            if(btn) { 
                const cls = btn.className; 
                btn.className = 'ri-check-line'; 
                setTimeout(()=> btn.className = cls, 1000); 
            }
        }
    };

    window.triggerAddWithQty = async (id, qty) => {
        let product = productsCache.find(x => x.id == id);
        // Fallback jika direct link detail page
        if(!product) product = { id: id, name: 'Produk', price: 0, image: '', qty: 0 }; 
        
        await Cart.add(product, qty);
        return true;
    };

    window.triggerUpdate = (id, delta) => { UI.renderCart(Cart.updateQty(id, delta)); };
    window.triggerRemove = (id) => { UI.renderCart(Cart.remove(id)); };

    // C. FAVORITE LOGIC
    window.triggerFavorite = async (id, btnElem) => {
        const p = productsCache.find(x => x.id == id);
        if(p) {
            p.is_favorite = !p.is_favorite;
            const icon = btnElem.querySelector('i');
            
            if(p.is_favorite) {
                btnElem.classList.add('active'); 
                icon.className = 'ri-heart-fill active';
            } else {
                btnElem.classList.remove('active'); 
                icon.className = 'ri-heart-line';
                // Jika di halaman Favorit, hapus kartu
                if(window.location.href.includes('/favorites')) {
                    const card = btnElem.closest('.k-card-prod');
                    if(card) card.remove();
                }
            }
            try { await $.post(KRESUBER.ajax_url, { action: 'kresuber_toggle_favorite', id: id }); } catch(e) {}
        }
    };

    // D. TABLE MANAGEMENT
    window.triggerAddTable = () => { const name = prompt("Nama Meja:"); if(name) { tablesCache.push({id:Date.now(), name:name}); saveTables(); } };
    window.triggerEditTable = (i) => { const n = prompt("Ubah Nama:", tablesCache[i].name); if(n) { tablesCache[i].name = n; saveTables(); } };
    window.triggerDeleteTable = (i) => { if(confirm("Hapus?")) { tablesCache.splice(i, 1); saveTables(); } };
    window.selectStartTable = (n) => { $('#k-select-table').val(n); $('.nav-link[data-nav="pos-main"]').click(); };

    // --- 2. INIT & DATA LOADING ---
    async function init() {
        console.log("App Init...");

        // Load Products
        if(window.location.href.includes('/favorites')) {
            initFavoritesPage();
        } else {
            try {
                const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
                if (res.success) {
                    productsCache = res.data;
                    UI.renderProducts(productsCache);
                }
            } catch (e) {}
        }
        
        // Initial Cart Render
        UI.renderCart(Cart.getTotals());

        // Load Categories
        try { const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' }); if (res.success) UI.renderCategories(res.data); } catch (e) {}
        
        // Load Tables
        loadTables();
    }
    init();

    // Helper: Favorites Page Loader
    async function initFavoritesPage() {
        const grid = document.getElementById('k-grid');
        if(!grid) return;
        
        grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;"><i class="ri-loader-4-line ri-spin" style="font-size:24px; color:#FF6B00;"></i></div>';
        
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_favorites' });
            if(res.success) {
                productsCache = res.data;
                if(productsCache.length === 0) {
                    grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:50px;"><i class="ri-heart-line" style="font-size:48px; color:#ddd;"></i><p>Belum ada favorit.</p></div>`;
                } else {
                    UI.renderProducts(productsCache);
                }
            }
        } catch(e) { grid.innerHTML = '<p style="text-align:center;">Gagal memuat favorit.</p>'; }
    }

    // --- 3. UI EVENTS ---
    $('.nav-link').on('click', function(e) {
        const target = $(this).data('nav');
        if (target) {
            e.preventDefault();
            $('.nav-link').removeClass('active'); $(this).addClass('active');
            $('.pos-view-section').removeClass('active'); $('#view-' + target).addClass('active');
            if (target === 'orders-history') loadOrdersHistory();
        }
    });

    $('#k-search').on('input', function() {
        const term = $(this).val().toLowerCase();
        const activeTab = $('.pos-category-tabs .pos-tab-item.active').data('slug') || 'all';
        const dropdownVal = $('.pos-category-dropdown').val();
        let catFilter = (activeTab !== 'all') ? activeTab : dropdownVal;
        
        const filtered = productsCache.filter(p => {
            const nameMatch = p.name.toLowerCase().includes(term);
            const pCat = (typeof p.category === 'string') ? p.category.toLowerCase() : '';
            const catMatch = catFilter === 'all' || pCat.includes(catFilter);
            return nameMatch && catMatch;
        });
        UI.renderProducts(filtered);
    });

    // --- 4. BACKEND SYNC ---
    async function loadTables() { try { const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_manage_tables', nonce: KRESUBER.nonce, mode: 'get' }); if (res.success) { tablesCache = res.data; renderTablesSync(); } } catch (e) {} }
    async function saveTables() { try { await $.post(KRESUBER.ajax_url, { action: 'kresuber_manage_tables', nonce: KRESUBER.nonce, mode: 'save', tables: JSON.stringify(tablesCache) }); renderTablesSync(); } catch (e) {} }
    
    function renderTablesSync() {
        const $select = $('#k-select-table');
        if($select.length) {
            const curr = $select.val();
            let opt = '<option value="">Pilih Meja</option>';
            tablesCache.forEach(t => { opt += `<option value="${t.name}">${t.name}</option>`; });
            $select.html(opt); if(curr) $select.val(curr);
        }
        const $grid = $('#k-table-grid');
        if($grid.length) {
            let html = `<div class="table-card add-new" onclick="triggerAddTable()"><i class="ri-add-circle-line" style="font-size:32px; color:var(--k-primary);"></i><h3 class="text-sm">Tambah</h3></div>`;
            tablesCache.forEach((t, i) => {
                html += `<div class="table-card"><div class="table-actions"><i class="ri-pencil-line" onclick="triggerEditTable(${i})"></i><i class="ri-close-circle-line delete" onclick="triggerDeleteTable(${i})"></i></div><div onclick="selectStartTable('${t.name}')"><i class="ri-restaurant-2-line" style="font-size:24px; color:#ccc;"></i><h3 class="text-sm">${t.name}</h3></div></div>`;
            });
            $grid.html(html);
        }
    }

    async function loadOrdersHistory() {
        const $body = $('#k-orders-list-body');
        if(!$body.length) return;
        
        $body.html('<tr><td colspan="5" style="text-align:center;">Memuat...</td></tr>');
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_orders_history', nonce: KRESUBER.nonce });
            if (res.success && res.data.length > 0) {
                const rows = res.data.map(o => `<tr><td>#${o.id}</td><td>${o.date}</td><td>${o.table}</td><td>${o.total}</td><td><span class="k-status-badge status-${o.status}">${o.status}</span></td></tr>`).join('');
                $body.html(rows);
            } else {
                $body.html('<tr><td colspan="5" style="text-align:center;">Tidak ada pesanan.</td></tr>');
            }
        } catch (e) {
            $body.html('<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat.</td></tr>');
        }
    }
    $('#btn-refresh-orders').on('click', loadOrdersHistory);

    // --- 5. CHECKOUT (POS TERMINAL) ---
    $('.btn-bill-payment').on('click', async function() {
        const tableNo = $('#k-select-table').val();
        if (!tableNo) { alert('Silakan pilih Meja terlebih dahulu!'); return; }
        
        if (confirm('Lanjut ke Pembayaran?')) {
            const $btn = $(this); $btn.prop('disabled', true).text('Memproses...');
            try {
                const res = await Cart.checkout(tableNo, 'dine_in');
                if (res.payment_url) { 
                    window.location.href = res.payment_url; 
                } else { 
                    alert('Order berhasil.'); 
                    UI.renderCart(Cart.getTotals()); 
                    $btn.prop('disabled', false).text('Bill & Payment'); 
                }
            } catch (error) { 
                alert('Gagal: ' + error); 
                $btn.prop('disabled', false).text('Bill & Payment'); 
            }
        }
    });
});