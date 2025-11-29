<?php
/**
 * Single Product Shell - Mobile App Style
 */
defined( 'ABSPATH' ) || exit;

$product_id = get_query_var('product_id');
$product = wc_get_product($product_id);

if (!$product) {
    wp_redirect(home_url('/app'));
    exit;
}

$image_url = wp_get_attachment_image_url($product->get_image_id(), 'large');
if (!$image_url) $image_url = 'https://placehold.co/400x400/orange/white?text=' . $product->get_name();
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
        .k-detail-img-wrap { width: 100%; height: 350px; position: relative; background: #eee; }
        .k-detail-img { width: 100%; height: 100%; object-fit: cover; }
        .k-btn-back-float {
            position: absolute; top: 20px; left: 20px;
            width: 40px; height: 40px; background: rgba(255,255,255,0.9);
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            color: #1a1a1a; font-size: 24px; cursor: pointer; text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .k-detail-content { padding: 24px; position: relative; top: -20px; background: #fff; border-radius: 24px 24px 0 0; }
        .k-detail-title { font-size: 24px; font-weight: 800; margin: 0 0 10px 0; color: #1a1a1a; }
        .k-detail-price { font-size: 22px; font-weight: 700; color: #FF6B00; margin-bottom: 20px; }
        .k-detail-desc { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 30px; }
        
        /* Sticky Action Bar */
        .k-action-bar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #fff; padding: 16px 24px;
            box-shadow: 0 -5px 30px rgba(0,0,0,0.05);
            display: flex; gap: 15px; align-items: center; z-index: 100;
        }
        .k-qty-control {
            display: flex; align-items: center; background: #F5F5F5;
            padding: 5px; border-radius: 12px; height: 50px;
        }
        .k-qty-btn {
            width: 40px; height: 100%; border: none; background: transparent;
            font-size: 20px; font-weight: bold; color: #1a1a1a; cursor: pointer;
        }
        .k-qty-val { width: 40px; text-align: center; font-weight: 700; font-size: 16px; }
        
        .k-btn-add-cart {
            flex-grow: 1; background: #FF6B00; color: white;
            height: 50px; border-radius: 12px; border: none;
            font-size: 16px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .k-btn-add-cart:active { transform: scale(0.98); }
    </style>
</head>
<body class="kresuber-app-mode">

    <div class="k-detail-img-wrap">
        <a href="javascript:history.back()" class="k-btn-back-float"><i class="ri-arrow-left-s-line"></i></a>
        <img src="<?php echo esc_url($image_url); ?>" class="k-detail-img" alt="Product">
    </div>

    <div class="k-detail-content">
        <h1 class="k-detail-title"><?php echo $product->get_name(); ?></h1>
        <div class="k-detail-price"><?php echo $product->get_price_html(); ?></div>
        
        <div class="k-detail-desc">
            <?php echo apply_filters('the_content', $product->get_description()); ?>
        </div>
    </div>

    <div class="k-action-bar">
        <div class="k-qty-control">
            <button class="k-qty-btn" onclick="updateDetailQty(-1)">-</button>
            <span id="detail-qty" class="k-qty-val">1</span>
            <button class="k-qty-btn" onclick="updateDetailQty(1)">+</button>
        </div>
        <button class="k-btn-add-cart" id="btn-add-to-cart" onclick="addToCartDetailed(<?php echo $product_id; ?>)">
            <i class="ri-shopping-cart-2-fill"></i> Tambah - <span id="detail-total"><?php echo $product->get_price(); ?></span>
        </button>
    </div>

    <script>
        let currentQty = 1;
        const basePrice = <?php echo $product->get_price(); ?>;

        function updateDetailQty(delta) {
            currentQty += delta;
            if (currentQty < 1) currentQty = 1;
            document.getElementById('detail-qty').innerText = currentQty;
            
            // Update harga di tombol
            const total = basePrice * currentQty;
            document.getElementById('detail-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Fungsi JS global (pos-app.js) akan menangani logika add to cart
        function addToCartDetailed(id) {
            const btn = document.getElementById('btn-add-to-cart');
            btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i> Menyimpan...';
            
            // Panggil fungsi global dari pos-app.js
            if(window.triggerAddWithQty) {
                window.triggerAddWithQty(id, currentQty).then(() => {
                    btn.innerHTML = '<i class="ri-check-line"></i> Berhasil!';
                    setTimeout(() => { 
                        window.location.href = '<?php echo home_url('/app'); ?>'; // Balik ke menu
                    }, 500);
                });
            } else {
                // Fallback jika JS belum load
                alert('Tunggu sebentar, aplikasi sedang memuat...');
            }
        }
    </script>

    <?php wp_footer(); ?>
</body>
</html>