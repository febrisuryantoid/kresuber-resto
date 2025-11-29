<?php 
/**
 * User App Shell - Fixed Styling & Full Width
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
        /* Fix Style untuk Search & Dropdown (Agar tidak perlu update CSS global lagi) */
        .app-search-wrap { padding: 10px 20px; background: #fff; }
        .app-search-input {
            width: 100%; padding: 12px 15px 12px 40px;
            border-radius: 12px; border: 1px solid #eee;
            background: #F8F9FD url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23999" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>') no-repeat 12px center;
            font-size: 14px; outline: none;
        }
        .app-search-input:focus { border-color: #FF6B00; background-color: #fff; }
        
        .pos-category-dropdown {
            width: 100%; padding: 12px; 
            border-radius: 12px; border: 1px solid #eee; 
            background: #fff; font-size: 14px; color: #555;
            outline: none; appearance: none;
            /* Custom Arrow Icon */
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right .7em top 50%;
            background-size: .65em auto;
        }
    </style>
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

        <div class="app-search-wrap">
            <input type="text" id="k-search" placeholder="Cari menu favoritmu..." class="app-search-input">
        </div>

        <div style="padding: 0 20px; margin-bottom: 5px;">
            <select class="pos-category-dropdown">
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