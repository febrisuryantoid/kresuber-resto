import { CartManager } from './modules/cart.js';
import { UIManager } from './modules/ui.js';

jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    
    // Cache data
    let productsCache = [];
    let tablesCache = [];

    // --- 1. GLOBAL FUNCTIONS (Diakses oleh HTML onclick="") ---

    // A. Tambah Produk (Default Qty 1) - Dipakai di List Produk
    window.triggerAdd = (id) => { 
        const p = productsCache.find(x => x.id == id);
        if(p) {
            UI.renderCart(Cart.add(p));
            // Feedback Visual Kecil pada tombol
            const btn = document.querySelector(`button[onclick*="triggerAdd(${id})"] i`);
            if(btn) { 
                const originalClass = btn.className;
                btn.className = 'ri-check-line'; 
                setTimeout(()=> btn.className = originalClass, 1000); 
            }
        }
    };

    // B. Tambah Produk dengan Qty (Custom) - Dipakai di Halaman Detail Produk
    window.triggerAddWithQty = async (id, qty) => {
        // Cari produk di cache
        let product = productsCache.find(x => x.id == id);
        
        // Jika tidak ada di cache (misal: user langsung buka link produk), buat dummy object
        // Data lengkap akan disinkronkan backend via ID nanti
        if(!product) {
            product = { 
                id: id, 
                name: 'Produk', // Placeholder
                price: 0, 
                image: '',
                qty: 0 
            }; 
        }

        if(product) {
            // Tambahkan ke Cart Manager (ini akan memicu Sync ke WooCommerce)
            await Cart.add(product, qty);
            return true; // Signal sukses ke tombol HTML
        }
    };

    // C. Update Qty di Cart Panel (POS Terminal)
    window.triggerUpdate = (id, delta) => { 
        UI.renderCart(Cart.updateQty(id, delta)); 
    };

    // D. Hapus Item di Cart Panel (POS Terminal)
    window.triggerRemove = (id) => { 
        UI.renderCart(Cart.remove(id)); 
    };

    // E. Manajemen Meja (CRUD)
    window.triggerAddTable = () => {
        const name = prompt("Masukkan Nama Meja Baru:");
        if (name) {
            const newId = Date.now();
            tablesCache.push({ id: newId, name: name });
            saveTables();
        }
    };
    window.triggerEditTable = (idx) => {
        const newName = prompt("Ubah Nama Meja:", tablesCache[idx].name);
        if (newName) { tablesCache[idx].name = newName; saveTables(); }
    };
    window.triggerDeleteTable = (idx) => {
        if(confirm("Hapus meja ini?")) { tablesCache.splice(idx, 1); saveTables(); }
    };
    window.selectStartTable = (name) => {
        $('#k-select-table').val(name);
        $('.nav-link[data-nav="pos-main"]').click(); // Kembali ke tab POS (khusus mode Terminal)
    };

    // --- 2. INITIALIZATION (Load Data) ---
    async function init() {
        console.log("POS App Initializing...");
        
        // Load Products
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
            if (res.success) {
                productsCache = res.data;
                UI.renderProducts(productsCache); // Render Grid
                UI.renderCart(Cart.getTotals());  // Render Cart Badge & Panel
            }
        } catch (e) { console.error("Product Load Error:", e); }

        // Load Categories
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' });
            if (res.success) UI.renderCategories(res.data);
        } catch (e) {}

        // Load Tables
        loadTables();
    }
    
    // Jalankan Init
    init();

    // --- 3. NAVIGATION & UI LOGIC ---

    // Tab Navigation (POS Terminal Sidebar)
    $('.nav-link').on('click', function(e) {
        const target = $(this).data('nav');
        if (target) {
            e.preventDefault();
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('.pos-view-section').removeClass('active');
            $('#view-' + target).addClass('active');

            if (target === 'orders-history') loadOrdersHistory();
        }
    });

    // Search & Filter Logic
    $('#k-search').on('input', filterProducts);
    $('.pos-category-tabs').on('click', '.pos-tab-item', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active');
        $(this).addClass('active');
        filterProducts();
    });
    $('.pos-category-dropdown').on('change', filterProducts);

    function filterProducts() {
        const term = $('#k-search').val().toLowerCase();
        const activeTab = $('.pos-category-tabs .pos-tab-item.active').data('slug') || 'all';
        const dropdownVal = $('.pos-category-dropdown').val();
        let catFilter = (activeTab !== 'all') ? activeTab : dropdownVal;

        const filtered = productsCache.filter(p => {
            const nameMatch = p.name.toLowerCase().includes(term);
            // Handle kategori yang mungkin string HTML atau slug
            const pCat = (typeof p.category === 'string') ? p.category.toLowerCase() : '';
            const catMatch = catFilter === 'all' || pCat.includes(catFilter);
            return nameMatch && catMatch;
        });
        UI.renderProducts(filtered);
    }

    // --- 4. BACKEND SYNC LOGIC (Tables & Orders) ---

    async function loadTables() {
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_manage_tables', nonce: KRESUBER.nonce, mode: 'get' });
            if (res.success) {
                tablesCache = res.data;
                renderTablesSync();
            }
        } catch (e) {}
    }

    async function saveTables() {
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_manage_tables', nonce: KRESUBER.nonce, mode: 'save', tables: JSON.stringify(tablesCache) });
            if (res.success) renderTablesSync();
        } catch (e) { alert("Gagal menyimpan meja"); }
    }

    function renderTablesSync() {
        // Render Dropdown (di Cart Panel)
        const $select = $('#k-select-table');
        if($select.length) {
            const curr = $select.val();
            let opt = '<option value="">Pilih Meja</option>';
            tablesCache.forEach(t => { opt += `<option value="${t.name}">${t.name}</option>`; });
            $select.html(opt);
            if(curr) $select.val(curr);
        }

        // Render Grid (di Halaman Manajemen Meja)
        const $grid = $('#k-table-grid');
        if($grid.length) {
            let html = `<div class="table-card add-new" onclick="triggerAddTable()"><i class="ri-add-circle-line" style="font-size:32px; color:var(--k-primary);"></i><h3 class="text-sm">Tambah</h3></div>`;
            tablesCache.forEach((t, i) => {
                html += `
                <div class="table-card">
                    <div class="table-actions">
                        <i class="ri-pencil-line" onclick="triggerEditTable(${i})"></i>
                        <i class="ri-close-circle-line delete" onclick="triggerDeleteTable(${i})"></i>
                    </div>
                    <div onclick="selectStartTable('${t.name}')">
                        <i class="ri-restaurant-2-line" style="font-size:24px; color:#ccc;"></i>
                        <h3 class="text-sm">${t.name}</h3>
                    </div>
                </div>`;
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

    // --- 5. CHECKOUT (POS Terminal Bill & Payment) ---
    $('.btn-bill-payment').on('click', async function() {
        const tableNo = $('#k-select-table').val();
        if (!tableNo) { alert('Silakan pilih Meja terlebih dahulu!'); return; }

        if (confirm('Lanjut ke Pembayaran?')) {
            const $btn = $(this);
            $btn.prop('disabled', true).text('Memproses...');

            try {
                // Checkout via Cart Manager
                const res = await Cart.checkout(tableNo, 'dine_in');
                
                if (res.payment_url) {
                    // Redirect ke Custom Payment Page
                    window.location.href = res.payment_url;
                } else {
                    alert('Order berhasil dibuat, silakan cek menu Orders.');
                    UI.renderCart(Cart.getTotals());
                    $btn.prop('disabled', false).text('Bill & Payment');
                }
            } catch (error) {
                alert('Gagal: ' + error);
                $btn.prop('disabled', false).text('Bill & Payment');
            }
        }
    });

    // Helper Actions
    $('.btn-kot-print').on('click', () => window.print());
});