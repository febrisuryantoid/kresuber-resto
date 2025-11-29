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
<body class="kresuber-app-mode">
    
    <div class="app-header">
        <div class="app-user">
            <div class="app-avatar">U</div>
            <div class="app-greeting">
                <span>Selamat Datang,</span>
                <h4><?php echo wp_get_current_user()->display_name ?: 'Pelanggan'; ?></h4>
            </div>
        </div>
        
        <div style="position: relative;">
            <i class="ri-search-line" style="position: absolute; left: 15px; top: 12px; color: #999;"></i>
            <input type="text" id="k-search" placeholder="Mau makan apa hari ini?" style="width: 100%; padding: 12px 12px 12px 40px; border: none; background: #f5f5f5; border-radius: 50px; outline: none;">
        </div>
    </div>

    <div id="k-grid" class="k-grid-products">
        <div style="grid-column:1/-1; text-align:center; padding:50px;">
            <i class="ri-loader-4-line ri-spin" style="font-size:30px; color:#FF6B00;"></i>
        </div>
    </div>

    <div class="app-nav">
        <i class="ri-home-5-fill nav-icon active"></i>
        <i class="ri-heart-3-line nav-icon"></i>
        <div style="background: #FF6B00; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-top: -30px; box-shadow: 0 5px 15px rgba(255,107,0,0.3);">
            <i class="ri-shopping-basket-2-fill"></i>
        </div>
        <i class="ri-file-list-3-line nav-icon"></i>
        <i class="ri-user-3-line nav-icon"></i>
    </div>

    <?php wp_footer(); ?>
</body>
</html>