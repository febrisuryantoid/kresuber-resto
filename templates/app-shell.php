<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal</title>
    <?php wp_head(); ?>
</head>
<body id="pos-app-wrapper" class="kresuber-app-mode">
    <?php include KRESUBER_PATH . 'templates/sidebar-nav.php'; ?>
    
    <main class="pos-main-content">
        <div class="pos-catalog-header">
            <div class="pos-search-bar">
                <i class="ri-search-line"></i>
                <input type="text" id="k-search" placeholder="Cari menu...">
            </div>
            <select class="pos-category-dropdown">
                <option value="all">Semua Kategori</option>
                <option value="rice">Rice</option>
                <option value="beverages">Beverages</option>
            </select>
            <select class="pos-brand-filter">
                <option value="all">Semua Brand</option>
            </select>
        </div>
        <div class="pos-category-tabs">
            <div class="pos-tab-item active">All</div>
            <div class="pos-tab-item">Rice</div>
            <div class="pos-tab-item">Noodles</div>
            <div class="pos-tab-item">Beverages</div>
            <div class="pos-tab-item">Desserts</div>
        </div>
        <div id="k-grid" class="pos-product-grid">
            <!-- Products will be loaded here by JavaScript -->
            <div style="grid-column:1/-1; text-align:center; padding:50px;">
                <i class="ri-loader-4-line ri-spin" style="font-size:30px; color:var(--k-primary);"></i>
            </div>
        </div>
    </main>

    <?php include KRESUBER_PATH . 'templates/views/view-cart.php'; ?>

    <?php wp_footer(); ?>
</body>
</html>