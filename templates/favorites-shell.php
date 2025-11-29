<?php
/**
 * Favorites Shell - Mobile App Style
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Favorit Saya</title>
    <?php wp_head(); ?>
    <style>
        /* Force Full App UI */
        html { margin-top: 0 !important; }
        body { background-color: #fff; margin: 0; padding: 0; }
        #wpadminbar { display: none !important; }
        
        .k-fav-header {
            position: sticky; top: 0; z-index: 50;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            text-align: center;
        }
        .k-fav-title { font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0; }
        
        /* Loading grid akan menggunakan style k-product-grid dari pos-app.css */
    </style>
</head>
<body class="kresuber-user-app">
    
    <div id="k-app-container">
        <div class="k-fav-header">
            <h1 class="k-fav-title">Favorit Saya</h1>
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