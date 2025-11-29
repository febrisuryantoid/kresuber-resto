<?php
/**
 * Custom Mobile Cart Template
 * Location: templates/woocommerce/cart/cart.php
 */
defined( 'ABSPATH' ) || exit;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Keranjang Saya</title>
    <?php wp_head(); ?>
    <style>
        /* CSS Inline Darurat agar tidak berantakan jika CSS utama gagal load */
        body { background: #F8F9FD !important; margin: 0; padding-bottom: 120px; }
        .k-cart-wrap { max-width: 480px; margin: 0 auto; min-height: 100vh; background: #fff; position: relative; }
        .k-cart-header { padding: 15px 20px; display: flex; align-items: center; background: #fff; position: sticky; top: 0; z-index: 10; border-bottom: 1px solid #f0f0f0; }
        .k-cart-title { flex-grow: 1; text-align: center; font-size: 18px; font-weight: 700; margin: 0; color: #1a1a1a; }
        
        .k-empty-state { text-align: center; padding: 60px 20px; }
        .k-empty-icon { font-size: 64px; color: #ddd; margin-bottom: 15px; }
        .k-btn-shop { display: inline-block; padding: 12px 25px; background: #FF6B00; color: white; text-decoration: none; border-radius: 12px; font-weight: 600; margin-top: 20px; }
        
        .k-cart-list { padding: 20px; }
        .k-cart-item { display: flex; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #eee; }
        .k-cart-img { width: 70px; height: 70px; border-radius: 10px; object-fit: cover; margin-right: 15px; background: #eee; }
        .k-cart-info h4 { margin: 0 0 5px 0; font-size: 14px; font-weight: 700; color: #333; }
        .k-cart-price { color: #FF6B00; font-weight: 700; font-size: 14px; }
        .k-remove-btn { color: #ff4444; font-size: 20px; margin-left: auto; text-decoration: none; }
        
        .k-cart-summary { position: fixed; bottom: 0; left: 0; width: 100%; background: #fff; padding: 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); z-index: 100; box-sizing: border-box; }
        @media(min-width: 768px) { .k-cart-summary { max-width: 480px; left: 50%; transform: translateX(-50%); } }
        
        .k-sum-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; }
        .k-sum-total { font-weight: 800; font-size: 18px; margin: 10px 0 15px; border-top: 1px solid #eee; padding-top: 10px; }
        .k-btn-checkout { display: block; width: 100%; padding: 15px; background: #FF6B00; color: white; text-align: center; border-radius: 12px; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body class="kresuber-app-mode">

<div class="k-cart-wrap">
    <div class="k-cart-header">
        <a href="<?php echo home_url('/app'); ?>" style="color:#333; font-size:24px; text-decoration:none;"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-cart-title">Keranjang</h1>
        <div style="width:24px;"></div>
    </div>

    <?php if ( WC()->cart->is_empty() ) : ?>
        <div class="k-empty-state">
            <i class="ri-shopping-basket-2-line k-empty-icon"></i>
            <h3>Keranjang Kosong</h3>
            <p style="color:#888;">Belum ada menu yang dipilih.</p>
            <a href="<?php echo home_url('/app'); ?>" class="k-btn-shop">Pesan Sekarang</a>
        </div>
    <?php else : ?>
        <div class="k-cart-list">
            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : 
                $_product = $cart_item['data'];
                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) :
                    $image = wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: 'https://placehold.co/100x100';
                    ?>
                    <div class="k-cart-item">
                        <img src="<?php echo esc_url($image); ?>" class="k-cart-img">
                        <div class="k-cart-info">
                            <h4><?php echo $_product->get_name(); ?></h4>
                            <div class="k-cart-price">
                                <?php echo WC()->cart->get_product_price( $_product ); ?> x <?php echo $cart_item['quantity']; ?>
                            </div>
                        </div>
                        <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="k-remove-btn"><i class="ri-delete-bin-line"></i></a>
                    </div>
                <?php endif; 
            endforeach; ?>
        </div>

        <div style="height: 150px;"></div> <div class="k-cart-summary">
            <div class="k-sum-row">
                <span>Subtotal</span>
                <span><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>
            <div class="k-sum-row k-sum-total">
                <span>Total</span>
                <span><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
            <a href="<?php echo wc_get_checkout_url(); ?>" class="k-btn-checkout">Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php wp_footer(); ?>
</body>
</html>