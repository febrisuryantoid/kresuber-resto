import { CartManager } from './modules/cart.js';
import { UIManager } from './modules/ui.js';

jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    
    let productsCache = [];
    let tablesCache = []; // Menyimpan data meja lokal

    // --- GLOBAL HELPERS ---
    window.triggerAdd = (id) => { const p = productsCache.find(x => x.id == id); if(p) UI.renderCart(Cart.add(p)); };
    window.triggerUpdate = (id, delta) => { UI.renderCart(Cart.updateQty(id, delta)); };
    window.triggerRemove = (id) => { UI.renderCart(Cart.remove(id)); };

    // --- INIT ---
    async function init() {
        console.log("POS Initializing...");
        
        // 1. Load Products
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
            if (res.success) {
                productsCache = res.data;
                UI.renderProducts(productsCache);
                UI.renderCart(Cart.getTotals());
            }
        } catch (e) { console.error(e); }

        // 2. Load Categories
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' });
            if (res.success) UI.renderCategories(res.data);
        } catch (e) { console.error(e); }

        // 3. Load Tables (Sinkronisasi Awal)
        loadTables();
    }
    init();

    // --- TABLE MANAGEMENT LOGIC (CRUD & SYNC) ---
    
    // Load data dari server
    async function loadTables() {
        try {
            const res = await $.post(KRESUBER.ajax_url, { 
                action: 'kresuber_manage_tables', 
                nonce: KRESUBER.nonce,
                mode: 'get'
            });
            if (res.success) {
                tablesCache = res.data;
                renderTablesSync(); // Render ke Grid DAN Dropdown
            }
        } catch (e) { console.error("Gagal memuat meja", e); }
    }

    // Save data ke server
    async function saveTables() {
        try {
            const res = await $.post(KRESUBER.ajax_url, { 
                action: 'kresuber_manage_tables', 
                nonce: KRESUBER.nonce,
                mode: 'save',
                tables: JSON.stringify(tablesCache)
            });
            if (res.success) {
                renderTablesSync();
            }
        } catch (e) { alert("Gagal menyimpan perubahan meja."); }
    }

    // Fungsi Rendering Utama (Menjamin Sinkronisasi)
    function renderTablesSync() {
        // 1. Render Dropdown di Cart (Pilih Meja)
        const $select = $('#k-select-table');
        const currentVal = $select.val(); // Simpan pilihan saat ini agar tidak reset
        
        let optionsHtml = '<option value="">Pilih Meja</option>';
        tablesCache.forEach(t => {
            optionsHtml += `<option value="${t.name}">${t.name}</option>`;
        });
        $select.html(optionsHtml);
        if(currentVal) $select.val(currentVal); // Restore pilihan

        // 2. Render Grid Management (Edit/Hapus)
        const $grid = $('#k-table-grid');
        let gridHtml = '';
        
        // Tombol Tambah Meja
        gridHtml += `
            <div class="table-card add-new" onclick="window.triggerAddTable()">
                <i class="ri-add-circle-line" style="font-size:32px; color:var(--k-primary);"></i>
                <h3 class="text-sm">Tambah Meja</h3>
            </div>
        `;

        // List Meja
        tablesCache.forEach((t, index) => {
            gridHtml += `
                <div class="table-card">
                    <div class="table-actions">
                        <i class="ri-pencil-line" onclick="window.triggerEditTable(${index})" title="Edit Nama"></i>
                        <i class="ri-close-circle-line delete" onclick="window.triggerDeleteTable(${index})" title="Hapus"></i>
                    </div>
                    <div onclick="window.selectStartTable('${t.name}')">
                        <i class="ri-restaurant-2-line" style="font-size:24px; color:#ccc;"></i>
                        <h3 class="text-sm">${t.name}</h3>
                    </div>
                </div>`;
        });
        $grid.html(gridHtml);
    }

    // --- GLOBAL TABLE ACTIONS (Exposed to Window) ---
    
    window.triggerAddTable = () => {
        const name = prompt("Masukkan Nama Meja Baru (Contoh: Meja 15, VIP 1):");
        if (name) {
            const newId = tablesCache.length > 0 ? Math.max(...tablesCache.map(t => t.id)) + 1 : 1;
            tablesCache.push({ id: newId, name: name });
            saveTables(); // Simpan ke DB
        }
    };

    window.triggerEditTable = (index) => {
        const oldName = tablesCache[index].name;
        const newName = prompt("Ubah Nama Meja:", oldName);
        if (newName && newName !== oldName) {
            tablesCache[index].name = newName;
            saveTables(); // Simpan ke DB
        }
    };

    window.triggerDeleteTable = (index) => {
        if (confirm(`Yakin ingin menghapus ${tablesCache[index].name}?`)) {
            tablesCache.splice(index, 1);
            saveTables(); // Simpan ke DB
        }
    };

    window.selectStartTable = (tableName) => {
        $('#k-select-table').val(tableName); // Update dropdown cart
        $('.nav-link[data-nav="pos-main"]').click(); // Pindah ke tab POS
        // Optional: Beri visual feedback
        const $select = $('#k-select-table');
        $select.css('border-color', 'var(--k-primary)');
        setTimeout(() => $select.css('border-color', ''), 1000);
    };

    // --- OTHER LOGIC (Search, Filter, Checkout, Orders History) ---
    // (Kode di bawah ini sama seperti sebelumnya, pastikan tetap ada)
    
    $('.nav-link').on('click', function(e) {
        const targetView = $(this).data('nav');
        if (targetView) {
            e.preventDefault();
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            $('.pos-view-section').removeClass('active');
            $('#view-' + targetView).addClass('active');
            if (targetView === 'orders-history') loadOrdersHistory();
        }
    });

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

    $('.btn-bill-payment').on('click', async function() {
        const tableNo = $('#k-select-table').val();
        if (!tableNo) { alert('Pilih Nomor Meja dahulu!'); return; }
        if(confirm('Proses Pesanan?')) {
            $(this).prop('disabled', true).text('Proses...');
            try {
                const res = await Cart.checkout(tableNo, 'dine_in');
                alert('Sukses! Order ID: #' + res.order_id);
                UI.renderCart(Cart.getTotals());
                $('#k-select-table').val('');
            } catch (err) { alert('Gagal: ' + err); }
            $(this).prop('disabled', false).text('Bill & Payment');
        }
    });

    // Orders History Logic
    $('#btn-refresh-orders').on('click', loadOrdersHistory);
    async function loadOrdersHistory() {
        // ... (Kode sama seperti sebelumnya) ...
        const tbody = $('#k-orders-list-body');
        tbody.html('<tr><td colspan="5" style="text-align:center;">Mengambil data...</td></tr>');
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_orders_history', nonce: KRESUBER.nonce });
            if (res.success && res.data.length > 0) {
                const rows = res.data.map(o => `<tr><td>#${o.id}</td><td>${o.date}</td><td><strong>${o.table}</strong></td><td>${o.total}</td><td><span class="k-status-badge status-${o.status}">${o.status}</span></td></tr>`).join('');
                tbody.html(rows);
            } else { tbody.html('<tr><td colspan="5" style="text-align:center;">Belum ada pesanan.</td></tr>'); }
        } catch (error) { tbody.html('<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat data.</td></tr>'); }
    }
});