<?php 
/**
 * User App Shell - Kresuber Resto (Full Width Fix)
 * URL: /app/
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu Restoran</title>
    <?php wp_head(); ?>
</head>
<body class="kresuber-user-app">
    
    <div id="k-app-container">
        <div class="app-header-sticky k-header">
            <h1 class="app-logo-text" style="font-size:20px; margin:0;">Selamat Datang!</h1>
            
            <a href="<?php echo home_url('/app/cart'); ?>" class="k-btn-cart">
                <i class="ri-shopping-cart-2-line"></i>
                <span id="k-cart-qty" class="k-badge">0</span>
            </a>
        </div>

        <div style="padding: 10px 20px; background:#fff;">
            <input type="text" id="k-search" placeholder="Cari menu favoritmu..." style="width:100%; padding:12px 15px; border-radius:12px; border:1px solid #eee; background:#F8F9FD; font-size:14px; outline:none;">
        </div>

        <div style="padding: 0 20px; margin-bottom: 5px;">
            <select class="pos-category-dropdown" style="width:100%; padding:12px; border-radius:12px; border:1px solid #eee; background:#fff; font-size:14px; color:#555;">
                <option value="all">Semua Kategori</option>
            </select>
        </div>

        <div id="k-grid" class="k-product-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 60px;">
                <i class="ri-loader-4-line ri-spin" style="font-size: 32px; color: #FF6B00;"></i>
            </div>
        </div>

        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>