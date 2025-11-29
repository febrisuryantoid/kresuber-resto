<?php 
/**
 * User App Shell - Kresuber Resto
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
    <style>
        html { margin-top: 0 !important; }
        body { background-color: #fff; margin: 0; padding: 0; -webkit-tap-highlight-color: transparent; }
        #wpadminbar { display: none !important; }
        
        /* Header Sticky */
        .app-header-sticky {
            position: sticky; top: 0; z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            display: flex; justify-content: space-between; align-items: center;
        }
        .app-logo-text { font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0; }
        
        /* Cart Icon */
        .k-btn-cart { position: relative; text-decoration: none; color: #1a1a1a; font-size: 24px; }
        .k-badge {
            position: absolute; top: -5px; right: -5px;
            background: #FF6B00; color: white;
            font-size: 10px; font-weight: bold;
            height: 16px; min-width: 16px; padding: 0 4px;
            border-radius: 10px;
            display: none;
            align-items: center; justify-content: center;
        }

        /* Search & Filter */
        .app-search-wrap { padding: 10px 20px; background: #fff; }
        .app-search-input {
            width: 100%; padding: 12px 15px 12px 40px;
            border-radius: 12px; border: 1px solid #eee;
            background: #F8F9FD url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23999" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 12px center;
            font-size: 14px; outline: none;
        }
        .app-search-input:focus { border-color: #FF6B00; background-color: #fff; }
    </style>
</head>
<body class="kresuber-user-app">
    
    <div id="k-app-container">
        <div class="app-header-sticky">
            <h1 class="app-logo-text">Selamat Datang!</h1>
            <a href="<?php echo home_url('/app/cart'); ?>" class="k-btn-cart">
                <i class="ri-shopping-cart-2-line"></i>
                <span id="k-cart-qty" class="k-badge">0</span>
            </a>
        </div>

        <div class="app-search-wrap">
            <input type="text" id="k-search" placeholder="Cari menu..." class="app-search-input">
        </div>

        <div style="padding: 0 20px; margin-bottom: 10px;">
            <select class="pos-category-dropdown" style="width:100%; padding:10px; border-radius:8px; border:1px solid #eee; background:#fff;">
                <option value="all">Semua Kategori</option>
            </select>
        </div>

        <div id="k-grid" class="k-product-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <i class="ri-loader-4-line ri-spin" style="font-size: 24px; color: #FF6B00;"></i>
            </div>
        </div>

        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>