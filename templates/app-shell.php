<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal</title>
    <?php wp_head(); ?>
    <style>body { display: grid; grid-template-columns: 80px 1fr 400px; height: 100vh; overflow: hidden; }</style>
</head>
<body class="kresuber-app-mode">
    <div class="k-sidebar" style="background:#fff; border-right:1px solid #eee; display:flex; flex-direction:column; align-items:center; padding:20px 0;">
        <i class="ri-store-3-fill" style="font-size:32px; color:#FF6B00; margin-bottom:40px;"></i>
        <i class="ri-apps-fill" style="font-size:24px; color:#FF6B00; background:#FFF0E5; padding:12px; border-radius:12px; margin-bottom:15px;"></i>
        <i class="ri-history-line" style="font-size:24px; color:#ccc; padding:12px;"></i>
    </div>
    
    <main class="k-main" style="padding:24px; overflow-y:auto;">
        <div style="display:flex; gap:15px; margin-bottom:24px;">
            <input type="text" id="k-search" placeholder="Cari menu..." style="flex:1; padding:12px 20px; border-radius:50px; border:1px solid #eee; outline:none;">
        </div>
        <div id="k-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap:16px;"></div>
    </main>

    <aside class="k-cart-panel" style="background:#fff; border-left:1px solid #eee; display:flex; flex-direction:column;">
        <div style="padding:24px; border-bottom:1px solid #eee;">
            <h2 style="margin:0 0 15px 0;">Pesanan Baru</h2>
            <div style="display:flex; gap:10px;"><input type="text" placeholder="Meja" style="flex:1; padding:10px; border-radius:8px; border:1px solid #eee;"><select style="flex:1; padding:10px; border-radius:8px; border:1px solid #eee;"><option>Dine In</option></select></div>
        </div>
        <div id="k-cart-list" style="flex:1; overflow-y:auto; padding:20px;"></div>
        <div style="padding:24px; background:#f9f9f9; border-top:1px solid #eee;">
            <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:18px; margin-bottom:20px;"><span>Total</span> <span id="k-total">Rp 0</span></div>
            <button id="k-btn-checkout" style="width:100%; background:#FF6B00; color:white; padding:15px; border-radius:12px; font-weight:bold;" disabled>Checkout</button>
        </div>
    </aside>
    <?php wp_footer(); ?>
</body>
</html>