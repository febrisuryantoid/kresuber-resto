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
    async function init() {
        console.log("POS Initializing...");
        
        // Fetch Products
        try {
            const productRes = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_products', nonce: KRESUBER.nonce });
            if (productRes.success) {
                console.log("Products Loaded:", productRes.data.length);
                productsCache = productRes.data;
                UI.renderProducts(productsCache);
            } else {
                console.error("Failed to load products", productRes.data);
                $('#k-grid').html('<p style="color:red; padding:20px;">Gagal memuat produk.</p>');
            }
        } catch (error) {
            console.error("Error fetching products:", error);
            $('#k-grid').html('<p style="color:red; padding:20px;">Terjadi kesalahan saat memuat produk.</p>');
        }

        // Fetch Categories
        try {
            const categoryRes = await $.post(KRESUBER.ajax_url, { action: 'kresuber_get_product_categories' });
            if (categoryRes.success) {
                console.log("Categories Loaded:", categoryRes.data.length);
                UI.renderCategories(categoryRes.data);
            } else {
                console.error("Failed to load categories", categoryRes.data);
            }
        } catch (error) {
            console.error("Error fetching categories:", error);
        }
    }

    init();

    // Search Handler
    $('#k-search').on('input', function() {
        const term = $(this).val().toLowerCase();
        const filtered = productsCache.filter(p => p.name.toLowerCase().includes(term));
        UI.renderProducts(filtered);
    });

    // Category Tabs Handler
    $('.pos-category-tabs').on('click', '.pos-tab-item', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active');
        $(this).addClass('active');
        filterAndRenderProducts();
    });

    // Category Dropdown Handler
    $('.pos-category-dropdown').on('change', function() {
        $('.pos-category-tabs .pos-tab-item').removeClass('active'); // Deselect tabs when dropdown used
        filterAndRenderProducts();
    });

    // Brand Filter Handler (Basic)
    $('.pos-brand-filter').on('change', function() {
        filterAndRenderProducts();
    });

    function filterAndRenderProducts() {
        const searchTerm = $('#k-search').val().toLowerCase();
        const activeCategoryTab = $('.pos-category-tabs .pos-tab-item.active').data('slug');
        const selectedCategoryDropdown = $('.pos-category-dropdown').val();
        const selectedBrand = $('.pos-brand-filter').val(); // Assuming a 'brand' property exists

        let filtered = productsCache.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(searchTerm);
            
            const productCategories = typeof p.category === 'string' ? $(p.category).text().toLowerCase() : ''; // Extract text from HTML string
            const matchesCategoryTab = activeCategoryTab === 'all' || productCategories.includes(activeCategoryTab);
            const matchesCategoryDropdown = selectedCategoryDropdown === 'all' || productCategories.includes(selectedCategoryDropdown);
            
            const matchesBrand = selectedBrand === 'all' || (p.brand && p.brand.toLowerCase() === selectedBrand);

            return matchesSearch && matchesCategoryTab && matchesCategoryDropdown && matchesBrand;
        });

        UI.renderProducts(filtered);
    }

    // Initial render of cart
    UI.renderCart(Cart.getTotals());

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