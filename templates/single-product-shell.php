<?php
/**
 * Single Product Full UI
 */
defined( 'ABSPATH' ) || exit;

$product_id = get_query_var('product_id');
$product = wc_get_product($product_id);

if (!$product) {
    wp_safe_redirect( home_url('/app') );
    exit;
}

$image = wp_get_attachment_image_url($product->get_image_id(), 'large') ?: 'https://placehold.co/400x400';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $product->get_name(); ?></title>
    <?php wp_head(); ?>
    <style>
        body { background: #fff; padding-bottom: 90px; }
        .k-p-img-box { width: 100%; height: 360px; background: #f0f0f0; position: relative; }
        .k-p-img { width: 100%; height: 100%; object-fit: cover; }
        .k-btn-back { position: absolute; top: 20px; left: 20px; width: 40px; height: 40px; background: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-decoration: none; color: #1a1a1a; font-size: 24px; }
        .k-p-info { padding: 24px; border-radius: 24px 24px 0 0; background: #fff; position: relative; top: -24px; margin-bottom: -24px; }
        .k-p-title { font-size: 24px; font-weight: 800; margin: 0 0 8px 0; }
        .k-p-price { font-size: 22px; font-weight: 700; color: #FF6B00; margin-bottom: 20px; }
        .k-p-desc { color: #666; line-height: 1.6; font-size: 14px; }
        
        .k-p-action { position: fixed; bottom: 0; left: 0; width: 100%; background: #fff; padding: 15px 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); display: flex; gap: 15px; align-items: center; z-index: 99; }
        .k-qty-box { display: flex; align-items: center; background: #f5f5f5; border-radius: 12px; padding: 5px; height: 50px; }
        .k-qty-btn { width: 40px; border: none; background: transparent; font-size: 18px; font-weight: bold; cursor: pointer; }
        .k-qty-val { width: 30px; text-align: center; font-weight: 700; }
        .k-btn-add { flex-grow: 1; background: #FF6B00; color: white; height: 50px; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body class="kresuber-app-mode">
    
    <div class="k-p-img-box">
        <a href="javascript:history.back()" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
        <img src="<?php echo esc_url($image); ?>" class="k-p-img">
    </div>

    <div class="k-p-info">
        <h1 class="k-p-title"><?php echo $product->get_name(); ?></h1>
        <div class="k-p-price"><?php echo $product->get_price_html(); ?></div>
        <div class="k-p-desc">
            <?php echo apply_filters('the_content', $product->get_description()); ?>
        </div>
    </div>

    <div class="k-p-action">
        <div class="k-qty-box">
            <button class="k-qty-btn" onclick="updQty(-1)">-</button>
            <span id="qty-val" class="k-qty-val">1</span>
            <button class="k-qty-btn" onclick="updQty(1)">+</button>
        </div>
        <button class="k-btn-add" id="btn-add" onclick="addNow(<?php echo $product_id; ?>)">
            Tambah - <span id="price-total"><?php echo $product->get_price(); ?></span>
        </button>
    </div>

    <script>
        let qty = 1;
        const price = <?php echo $product->get_price() ?: 0; ?>;
        
        function updQty(d) {
            qty += d; if(qty<1) qty=1;
            document.getElementById('qty-val').innerText = qty;
            document.getElementById('price-total').innerText = (price * qty).toLocaleString();
        }

        function addNow(id) {
            const btn = document.getElementById('btn-add');
            btn.innerHTML = 'Memproses...';
            if(window.triggerAddWithQty) {
                window.triggerAddWithQty(id, qty).then(() => {
                    btn.innerHTML = 'Berhasil!';
                    setTimeout(() => window.location.href = '<?php echo home_url('/app'); ?>', 500);
                });
            } else {
                alert('Silakan tunggu loading...');
            }
        }
    </script>

    <?php wp_footer(); ?>
</body>
</html>