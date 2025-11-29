import { CartManager } from './modules/cart.js';
import { UIManager } from './modules/ui.js';

jQuery(document).ready(function($) {
    const Cart = new CartManager();
    const UI = new UIManager();
    let productsCache = [];

    // Make global for onclick events in HTML strings
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
    function init() {
        console.log("POS Initializing...");
        $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce }, (res) => {
            if (res.success) {
                console.log("Products Loaded:", res.data.length);
                productsCache = res.data;
                UI.renderProducts(productsCache);
            } else {
                console.error("Failed to load products");
                $('#k-grid').html('<p style="color:red; padding:20px;">Gagal memuat produk.</p>');
            }
        });
    }

    init();

    // Search Handler
    $('#k-search').on('input', function() {
        const term = $(this).val().toLowerCase();
        const filtered = productsCache.filter(p => p.name.toLowerCase().includes(term));
        UI.renderProducts(filtered);
    });

    // Category Tabs Handler
    $('.pos-category-tabs .pos-tab-item').on('click', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active');
        $(this).addClass('active');
        const category = $(this).text().toLowerCase(); // Simplified for demo
        const filtered = productsCache.filter(p => category === 'all' || (p.category && p.category.toLowerCase().includes(category)));
        UI.renderProducts(filtered);
    });

    // Category Dropdown Handler (Basic)
    $('.pos-category-dropdown').on('change', function() {
        const category = $(this).val().toLowerCase();
        const filtered = productsCache.filter(p => category === 'all' || (p.category && p.category.toLowerCase().includes(category)));
        UI.renderProducts(filtered);
    });

    // Brand Filter Handler (Basic)
    $('.pos-brand-filter').on('change', function() {
        const brand = $(this).val().toLowerCase(); // Assuming products have a 'brand' property
        const filtered = productsCache.filter(p => brand === 'all' || (p.brand && p.brand.toLowerCase().includes(brand)));
        UI.renderProducts(filtered);
    });

    // Dining Type & Table Selection
    $('#k-select-table').on('change', function() {
        console.log('Meja dipilih:', $(this).val());
        // Further logic for table selection (e.g., saving to order)
    });

    $('#k-select-dining-type').on('change', function() {
        console.log('Tipe santap dipilih:', $(this).val());
        // Further logic for dining type (e.g., adjusting tax/service fee)
    });

    // Cart Action Buttons (Placeholders)
    $('.btn-kot-print').on('click', function() {
        console.log('KOT & Print clicked!');
        alert('Fungsionalitas KOT & Print akan datang!');
    });

    $('.btn-draft').on('click', function() {
        console.log('Draft clicked!');
        alert('Fungsionalitas Draft akan datang!');
    });

    $('.btn-bill-payment').on('click', function() {
        console.log('Bill & Payment clicked!');
        alert('Fungsionalitas Bill & Payment akan datang!');
        // Trigger WooCommerce checkout process here
    });

    $('.btn-bill-print').on('click', function() {
        console.log('Bill & Print clicked!');
        alert('Fungsionalitas Bill & Print akan datang!');
    });
});