<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu Restoran</title>
    <?php wp_head(); ?>
    <style>
        /* Mobile Specific Overrides */
        body { background: #fff; }
        .app-header { padding: 20px; position: sticky; top: 0; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); z-index: 50; }
        .app-user { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .app-avatar { width: 40px; height: 40px; background: #FF6B00; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .app-greeting h4 { margin: 0; font-size: 16px; color: #1a1a1a; }
        .app-greeting span { font-size: 12px; color: #888; }
        
        .k-grid-products { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 0 20px 100px 20px; }
        .k-card-prod { border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .k-card-img { height: 120px; }
        
        .app-nav { position: fixed; bottom: 0; left: 0; width: 100%; background: white; padding: 12px 20px; display: flex; justify-content: space-around; border-radius: 24px 24px 0 0; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); z-index: 100; }
        .nav-icon { font-size: 24px; color: #ccc; }
        .nav-icon.active { color: #FF6B00; }
    </style>
</head>
<body>
    
    <div id="k-app-container">
        <div class="k-header">
            <h1 class="k-title">Selamat Datang!</h1>
            <div class="k-header-actions">
                <a href="<?php echo wc_get_cart_url(); ?>" class="k-btn-cart">
                    <i class="ri-shopping-cart-2-line"></i>
                    <span id="k-cart-qty" class="k-badge">0</span>
                </a>
            </div>
        </div>
        <div class="k-content">
            <input type="text" id="k-search" placeholder="Cari menu..." class="k-search-input">
            <div id="k-grid" class="k-product-grid"></div>
        </div>
        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; // Include bottom navbar ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>