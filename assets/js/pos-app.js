import { CartManager } from './modules/cart.js';
import { UIManager } from './modules/ui.js';

jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    let productsCache = [];

    // --- GLOBAL FUNCTIONS ---
    window.triggerAdd = (id) => { const p = productsCache.find(x => x.id == id); if(p) UI.renderCart(Cart.add(p)); };
    window.triggerUpdate = (id, delta) => { UI.renderCart(Cart.updateQty(id, delta)); };
    window.triggerRemove = (id) => { UI.renderCart(Cart.remove(id)); };

    // --- INITIALIZATION ---
    async function init() {
        console.log("POS Initializing...");
        // Fetch Products
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
            if (res.success) {
                productsCache = res.data;
                UI.renderProducts(productsCache);
                UI.renderCart(Cart.getTotals());
            }
        } catch (e) { console.error(e); }

        // Fetch Categories
        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' });
            if (res.success) UI.renderCategories(res.data);
        } catch (e) { console.error(e); }

        // Render Tables (Static for now)
        renderTables();
    }
    init();

    // --- SIDEBAR NAVIGATION LOGIC ---
    $('.nav-link').on('click', function(e) {
        // Jika memiliki data-nav, berarti ini menu internal
        const targetView = $(this).data('nav');
        if (targetView) {
            e.preventDefault();
            
            // 1. Update Active Sidebar
            $('.nav-link').removeClass('active');
            $(this).addClass('active');

            // 2. Switch View Container
            $('.pos-view-section').removeClass('active');
            $('#view-' + targetView).addClass('active');

            // 3. Load Special Data if needed
            if (targetView === 'orders-history') {
                loadOrdersHistory();
            }
        }
    });

    // --- FEATURE: SEARCH & FILTER ---
    $('#k-search').on('input', filterProducts);
    $('.pos-category-tabs').on('click', '.pos-tab-item', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active');
        $(this).addClass('active');
        filterProducts();
    });
    $('.pos-category-dropdown').on('change', filterProducts);

    function filterProducts() {
        const term = $('#k-search').val().toLowerCase();
        // Logika filter sederhana...
        // (Sama seperti kode sebelumnya, disederhanakan untuk ringkas)
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

    // --- FEATURE: TABLE MANAGEMENT ---
    function renderTables() {
        let html = '';
        for(let i=1; i<=12; i++) {
            html += `<div class="table-card" onclick="selectStartTable(${i})">
                        <i class="ri-restaurant-2-line" style="font-size:32px; color:#ccc;"></i>
                        <h3 style="margin:10px 0 0;">Meja ${i}</h3>
                    </div>`;
        }
        $('#k-table-grid').html(html);
    }
    
    // Fungsi Global agar bisa diakses onclick HTML
    window.selectStartTable = (no) => {
        // Otomatis pilih meja di dropdown cart dan kembali ke POS
        $('#k-select-table').val(no);
        alert(`Meja ${no} dipilih. Silakan masukkan pesanan.`);
        $('.nav-link[data-nav="pos-main"]').click(); // Kembali ke menu utama
    };

    // --- FEATURE: ORDERS HISTORY ---
    $('#btn-refresh-orders').on('click', loadOrdersHistory);

    async function loadOrdersHistory() {
        const tbody = $('#k-orders-list-body');
        tbody.html('<tr><td colspan="5" style="text-align:center;">Mengambil data...</td></tr>');

        try {
            const res = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_orders_history', nonce: KRESUBER.nonce });
            if (res.success && res.data.length > 0) {
                const rows = res.data.map(o => `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${o.date}</td>
                        <td><strong>${o.table}</strong></td>
                        <td>${o.total}</td>
                        <td><span class="k-status-badge status-${o.status}">${o.status}</span></td>
                    </tr>
                `).join('');
                tbody.html(rows);
            } else {
                tbody.html('<tr><td colspan="5" style="text-align:center;">Belum ada pesanan.</td></tr>');
            }
        } catch (error) {
            tbody.html('<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat data.</td></tr>');
        }
    }

    // --- BILL & PAYMENT (Checkout) ---
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
                loadOrdersHistory(); // Refresh history jika sedang di tab history
            } catch (err) { alert('Gagal: ' + err); }
            $(this).prop('disabled', false).text('Bill & Payment');
        }
    });
});