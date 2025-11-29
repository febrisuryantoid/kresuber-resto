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
});