import { CartManager } from './modules/cart.js';
import { UIManager } from './modules/ui.js';

jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    
    let productsCache = [];
    let tablesCache = [];

    // --- GLOBAL HELPERS (Untuk onclick di HTML) ---
    window.triggerAdd = (id) => { const p = productsCache.find(x => x.id == id); if(p) UI.renderCart(Cart.add(p)); };
    window.triggerUpdate = (id, delta) => { UI.renderCart(Cart.updateQty(id, delta)); };
    window.triggerRemove = (id) => { UI.renderCart(Cart.remove(id)); };
    
    // --- TABLE CRUD GLOBALS ---
    window.triggerAddTable = () => {
        const name = prompt("Nama Meja Baru:");
        if (name) {
            const newId = Date.now(); // Gunakan timestamp untuk ID unik sederhana
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
        // Klik tab POS Terminal secara programatis
        $('.nav-link[data-nav="pos-main"]').click();
    };

    // --- INITIALIZATION ---
    async function init() {
        console.log("POS Initializing...");
        
        // 1. Products
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
            if (res.success) {
                productsCache = res.data;
                UI.renderProducts(productsCache);
                // Render cart awal (kosong atau dari local storage jika ada fitur itu)
                UI.renderCart(Cart.getTotals());
            } else {
                $('#k-grid').html('<p style="padding:20px; text-align:center;">Gagal memuat produk.</p>');
            }
        } catch (e) { console.error("Product Error:", e); }

        // 2. Categories
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' });
            if (res.success) UI.renderCategories(res.data);
        } catch (e) { console.error("Category Error:", e); }

        // 3. Tables
        loadTables();
    }
    init();

    // --- NAVIGATION LOGIC ---
    $('.nav-link').on('click', function(e) {
        const target = $(this).data('nav');
        if (target) {
            e.preventDefault();
            // Active State Sidebar
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Show Section
            $('.pos-view-section').removeClass('active');
            $('#view-' + target).addClass('active');

            // Load Data Specific
            if (target === 'orders-history') {
                loadOrdersHistory();
            }
        }
    });

    // --- SEARCH & FILTER ---
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
            const catMatch = catFilter === 'all' || (p.category && JSON.stringify(p.category).toLowerCase().includes(catFilter));
            return nameMatch && catMatch;
        });
        UI.renderProducts(filtered);
    }

    // --- TABLE LOGIC ---
    async function loadTables() {
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_manage_tables', nonce: KRESUBER.nonce, mode: 'get' });
            if (res.success) {
                tablesCache = res.data;
                renderTablesSync();
            }
        } catch (e) { console.error("Table Error:", e); }
    }

    async function saveTables() {
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_manage_tables', nonce: KRESUBER.nonce, mode: 'save', tables: JSON.stringify(tablesCache) });
            if (res.success) renderTablesSync();
        } catch (e) { alert("Gagal menyimpan meja"); }
    }

    function renderTablesSync() {
        // Render Dropdown Cart
        const $select = $('#k-select-table');
        const curr = $select.val();
        let opt = '<option value="">Pilih Meja</option>';
        tablesCache.forEach(t => { opt += `<option value="${t.name}">${t.name}</option>`; });
        $select.html(opt);
        if(curr) $select.val(curr);

        // Render Grid Management
        const $grid = $('#k-table-grid');
        let html = `
        <div class="table-card add-new" onclick="triggerAddTable()">
            <i class="ri-add-circle-line" style="font-size:32px; color:var(--k-primary);"></i>
            <h3 class="text-sm">Tambah</h3>
        </div>`;
        
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

    // --- ORDER HISTORY LOGIC ---
    $('#btn-refresh-orders').on('click', loadOrdersHistory);
    
    async function loadOrdersHistory() {
        const $body = $('#k-orders-list-body');
        $body.html('<tr><td colspan="5" style="text-align:center;">Memuat data...</td></tr>');
        
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_orders_history', nonce: KRESUBER.nonce });
            if (res.success && res.data.length > 0) {
                const rows = res.data.map(o => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.date}</td>
                        <td>${o.table}</td>
                        <td>${o.total}</td>
                        <td><span class="k-status-badge status-${o.status}">${o.status}</span></td>
                    </tr>
                `).join('');
                $body.html(rows);
            } else {
                $body.html('<tr><td colspan="5" style="text-align:center;">Tidak ada pesanan.</td></tr>');
            }
        } catch (e) {
            $body.html('<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat. Coba refresh.</td></tr>');
        }
    }

    // --- CHECKOUT INTEGRATION (BILL & PAYMENT) ---
    $('.btn-bill-payment').on('click', async function() {
        const tableNo = $('#k-select-table').val();
        
        if (!tableNo) {
            alert('Silakan pilih Meja terlebih dahulu!');
            return;
        }

        if (confirm('Lanjut ke Pembayaran? Anda akan diarahkan ke halaman Checkout.')) {
            const $btn = $(this);
            $btn.prop('disabled', true).text('Memproses...');

            try {
                // Panggil fungsi checkout di Cart Manager (lihat file modules/cart.js)
                // Pastikan cart.js juga diupdate untuk return response lengkap
                const res = await Cart.checkout(tableNo, 'dine_in');
                
                if (res.payment_url) {
                    // REDIRECT KE HALAMAN PEMBAYARAN WOOCOMMERCE
                    window.location.href = res.payment_url;
                } else {
                    alert('Order dibuat tapi URL pembayaran tidak ditemukan.');
                    loadOrdersHistory(); // Reload history aja
                    UI.renderCart(Cart.getTotals());
                    $btn.prop('disabled', false).text('Bill & Payment');
                }

            } catch (error) {
                alert('Gagal memproses: ' + error);
                $btn.prop('disabled', false).text('Bill & Payment');
            }
        }
    });

    // Dummy Buttons
    $('.btn-kot-print').on('click', () => window.print());
    $('.btn-draft').on('click', () => alert("Fitur draft tersimpan lokal."));
});