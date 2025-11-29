<?php
/**
 * Custom Mobile Cart Template - Fixed Layout
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
</head>
<body class="kresuber-app-mode">

<div class="k-cart-wrap">
    <div class="k-header">
        <a href="<?php echo home_url('/app'); ?>" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-page-title">Keranjang Saya</h1>
        <div style="width:24px;"></div>
    </div>

    <div style="padding: 0 20px; margin-top:10px;"><?php wc_print_notices(); ?></div>

    <?php if ( WC()->cart->is_empty() ) : ?>
        <div style="text-align:center; padding: 80px 20px;">
            <i class="ri-shopping-cart-2-line" style="font-size:80px; color:#eee; margin-bottom:20px; display:inline-block;"></i>
            <h3 style="color:#333; margin:0 0 10px;">Keranjang Kosong</h3>
            <p style="color:#888;">Belum ada menu yang dipilih.</p>
            <a href="<?php echo home_url('/app'); ?>" class="k-btn-confirm" style="width:200px; margin:20px auto;">Mulai Pesan</a>
        </div>
    <?php else : ?>
        
        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <div class="k-cart-list">
                <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : 
                    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    
                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) :
                        $image = wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: 'https://placehold.co/100x100';
                        ?>
                        
                        <div class="k-cart-card">
                            <div class="k-c-img-wrap">
                                <img src="<?php echo esc_url($image); ?>" class="k-c-img" alt="Product">
                            </div>
                            
                            <div class="k-c-content">
                                <div class="k-c-top">
                                    <h4 class="k-c-title"><?php echo $_product->get_name(); ?></h4>
                                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="k-c-remove">
                                        <i class="ri-delete-bin-line"></i>
                                    </a>
                                </div>

                                <div class="k-c-bottom">
                                    <div class="k-c-price">
                                        <?php echo WC()->cart->get_product_price( $_product ); ?>
                                    </div>
                                    
                                    <div class="k-c-qty">
                                        <button type="button" class="k-qty-btn" onclick="changeCartQty(this, -1)">-</button>
                                        
                                        <input 
                                            type="number" 
                                            name="cart[<?php echo $cart_item_key; ?>][qty]" 
                                            value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" 
                                            class="k-qty-input" 
                                            min="0" 
                                            step="1" 
                                            readonly 
                                        />

                                        <button type="button" class="k-qty-btn" onclick="changeCartQty(this, 1)">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; 
                endforeach; ?>
            </div>

            <button type="submit" name="update_cart" value="Update cart" style="display:none;" id="k-update-cart-btn">Update</button>
            <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
        </form>

        <div style="height:150px;"></div>

        <div class="k-cart-summary">
            <div class="k-sum-row">
                <span>Subtotal</span>
                <span class="val"><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>
            <div class="k-sum-row total">
                <span>Total</span>
                <span class="val" style="color:#FF6B00;"><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
            <a href="<?php echo wc_get_checkout_url(); ?>" class="k-btn-confirm">Checkout</a>
        </div>

    <?php endif; ?>
</div>

<?php wp_footer(); ?>

<script>
    function changeCartQty(btn, delta) {
        const wrapper = btn.closest('.k-c-qty');
        const input = wrapper.querySelector('input.k-qty-input');
        let val = parseInt(input.value) || 0;
        
        val += delta;
        if(val < 0) val = 0;
        
        input.value = val;
        
        // Trigger Update Native WooCommerce
        const updateBtn = document.getElementById('k-update-cart-btn');
        updateBtn.removeAttribute('disabled');
        updateBtn.click();
    }
</script>

</body>
</html>