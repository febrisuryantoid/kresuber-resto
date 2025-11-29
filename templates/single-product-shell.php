<?php
/**
 * Single Product Immersive UI
 */
defined( 'ABSPATH' ) || exit;

$product_id = get_query_var('product_id');
$product = wc_get_product($product_id);

if (!$product) { wp_safe_redirect( home_url('/app') ); exit; }
$image = wp_get_attachment_image_url($product->get_image_id(), 'full') ?: 'https://placehold.co/600x800';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo $product->get_name(); ?></title>
    <?php wp_head(); ?>
</head>
<body class="kresuber-app-mode">
    
    <div class="k-p-img-box">
        <a href="javascript:history.back()" class="k-btn-back-float"><i class="ri-arrow-left-s-line"></i></a>
        <img src="<?php echo esc_url($image); ?>" class="k-p-img">
    </div>

    <div class="k-p-info-sheet">
        <h1 class="k-p-title" style="font-size:26px; font-weight:800; margin:0 0 5px;"><?php echo $product->get_name(); ?></h1>
        <div class="k-p-price" style="font-size:24px; font-weight:800; color:#FF6B00; margin-bottom:20px;">
            <?php echo $product->get_price_html(); ?>
        </div>
        
        <div style="height:1px; background:#eee; margin-bottom:20px;"></div>

        <h3 style="font-size:16px; font-weight:700; margin-bottom:10px;">Deskripsi</h3>
        <div class="k-p-desc" style="color:#666; line-height:1.6; font-size:15px;">
            <?php echo apply_filters('the_content', $product->get_description()); ?>
        </div>
    </div>

    <div class="k-p-action" style="position:fixed; bottom:0; left:0; width:100%; background:#fff; padding:15px 20px; box-shadow:0 -5px 20px rgba(0,0,0,0.05); display:flex; gap:15px; z-index:100;">
        <div class="k-qty-box" style="display:flex; align-items:center; background:#f5f5f5; border-radius:12px; padding:5px; height:50px;">
            <button class="k-qty-btn" style="width:40px; border:none; background:transparent; font-size:20px;" onclick="updQty(-1)">-</button>
            <span id="qty-val" style="width:30px; text-align:center; font-weight:700;">1</span>
            <button class="k-qty-btn" style="width:40px; border:none; background:transparent; font-size:20px;" onclick="updQty(1)">+</button>
        </div>
        <button class="k-btn-add" id="btn-add" onclick="addNow(<?php echo $product_id; ?>)" style="flex-grow:1; background:#FF6B00; color:white; height:50px; border-radius:14px; font-weight:800; font-size:16px; border:none; cursor:pointer;">
            Tambah - <span id="price-total"><?php echo $product->get_price(); ?></span>
        </button>
    </div>

    <script>
        let qty = 1; const price = <?php echo $product->get_price() ?: 0; ?>;
        function updQty(d) { qty += d; if(qty<1) qty=1; document.getElementById('qty-val').innerText = qty; document.getElementById('price-total').innerText = (price * qty).toLocaleString(); }
        function addNow(id) {
            const btn = document.getElementById('btn-add'); btn.innerHTML = 'Menyimpan...';
            if(window.triggerAddWithQty) { window.triggerAddWithQty(id, qty).then(() => { btn.innerHTML = 'Berhasil!'; setTimeout(() => window.location.href = '<?php echo home_url('/app'); ?>', 500); }); }
        }
    </script>

    <?php wp_footer(); ?>
</body>
</html>