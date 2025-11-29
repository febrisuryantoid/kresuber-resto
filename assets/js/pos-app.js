import { CartManager } from './modules/cart.js';
import { UIManager } from './modules/ui.js';

jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    let productsCache = [];

    // Global Handlers
    window.triggerAdd = (id) => {
        const p = productsCache.find(x => x.id == id);
        if(p) UI.renderCart(Cart.add(p));
    };
    
    window.triggerUpdate = (id, delta) => {
        UI.renderCart(Cart.updateQty(id, delta));
    };

    window.triggerRemove = (id) => {
        UI.renderCart(Cart.remove(id));
    };

    // Load Data
    async function init() {
        console.log("POS Initializing...");
        
        // Fetch Products
        try {
            const productRes = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
            if (productRes.success) {
                productsCache = productRes.data;
                UI.renderProducts(productsCache);
                UI.renderCart(Cart.getTotals());
            } else {
                $('#k-grid').html('<div style="grid-column:1/-1;text-align:center;padding:40px;">Gagal memuat produk.</div>');
            }
        } catch (error) {
            console.error(error);
            $('#k-grid').html('<div style="grid-column:1/-1;text-align:center;padding:40px;">Error koneksi server.</div>');
        }

        // Fetch Categories
        try {
            const categoryRes = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' });
            if (categoryRes.success) {
                UI.renderCategories(categoryRes.data);
            }
        } catch (error) {
            console.error("Error categories:", error);
        }
    }

    init();

    // --- Search & Filter Logic ---
    $('#k-search').on('input', function() { filterProducts(); });
    $('.pos-category-tabs').on('click', '.pos-tab-item', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active');
        $(this).addClass('active');
        filterProducts();
    });
    $('.pos-category-dropdown').on('change', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active');
        filterProducts();
    });

    function filterProducts() {
        const term = $('#k-search').val().toLowerCase();
        const activeTab = $('.pos-category-tabs .pos-tab-item.active').data('slug');
        const dropdownVal = $('.pos-category-dropdown').val();
        
        // Prioritas filter: Tab > Dropdown (jika tab tidak active)
        let catFilter = 'all';
        if (activeTab) catFilter = activeTab;
        else if (dropdownVal) catFilter = dropdownVal;

        const filtered = productsCache.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(term);
            // Cek kategori (handle string HTML atau raw slug)
            let pCats = p.category; 
            if(typeof pCats === 'string' && pCats.includes('<')) {
                pCats = $(`<div>${p.category}</div>`).text().toLowerCase();
            } else if (typeof pCats === 'string') {
                pCats = pCats.toLowerCase();
            }
            
            const matchesCat = catFilter === 'all' || (pCats && pCats.includes(catFilter));
            return matchesSearch && matchesCat;
        });
        UI.renderProducts(filtered);
    }

    // --- BUTTON ACTIONS (FUNGSIONALITAS DIAKTIFKAN) ---

    // 1. Bill & Payment (CHECKOUT)
    $('.btn-bill-payment').on('click', async function() {
        const $btn = $(this);
        const tableNo = $('#k-select-table').val();
        const diningType = $('#k-select-dining-type').val();

        if (!tableNo) {
            alert('Mohon pilih Nomor Meja terlebih dahulu!');
            return;
        }

        if(confirm('Proses pembayaran dan buat pesanan?')) {
            $btn.prop('disabled', true).text('Memproses...');
            
            try {
                const result = await Cart.checkout(tableNo, diningType);
                alert('SUKSES! \nOrder ID: #' + result.order_id + '\nTotal: ' + result.total);
                
                // Reset UI
                UI.renderCart(Cart.getTotals()); 
                $('#k-select-table').val(''); // Reset meja
            } catch (error) {
                alert('GAGAL: ' + error);
            } finally {
                $btn.prop('disabled', false).text('Bill & Payment');
            }
        }
    });

    // 2. Print Functions
    $('.btn-kot-print, .btn-bill-print').on('click', function() {
        // Simple browser print for now
        window.print();
    });

    // 3. Draft (Simpan sementara di localStorage - Sederhana)
    $('.btn-draft').on('click', function() {
        alert('Fitur Draft disimpan (Simulasi).');
    });
});