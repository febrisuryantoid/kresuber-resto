<?php
/**
 * Custom Mobile Cart Template (UI Match)
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
</head>
<body class="kresuber-app-mode">

<div class="k-cart-wrap">
    <div class="k-header">
        <a href="<?php echo home_url('/app'); ?>" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-page-title">My Cart</h1>
        <i class="ri-shopping-bag-3-line k-icon-bag"></i>
    </div>

    <div style="padding: 0 20px;"><?php wc_print_notices(); ?></div>

    <?php if ( WC()->cart->is_empty() ) : ?>
        <div class="k-empty-state" style="text-align:center; padding: 80px 20px;">
            <i class="ri-shopping-cart-2-line" style="font-size:64px; color:#e0e0e0; margin-bottom:20px; display:inline-block;"></i>
            <h3 style="color:#333; margin:0 0 10px;">Your Cart is Empty</h3>
            <p style="color:#888; margin-bottom:30px;">Looks like you haven't added anything to your cart yet.</p>
            <a href="<?php echo home_url('/app'); ?>" class="k-btn-confirm" style="width:200px; margin:0 auto;">Shop Now</a>
        </div>
    <?php else : ?>
        
        <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
            <div class="k-cart-list" style="padding: 20px;">
                <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : 
                    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                    $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                    
                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                        $image = wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail') ?: 'https://placehold.co/100x100';
                        ?>
                        
                        <div class="k-cart-card">
                            <div class="k-c-img-wrap">
                                <img src="<?php echo esc_url($image); ?>" class="k-c-img">
                            </div>
                            
                            <div class="k-c-content">
                                <div class="k-c-top">
                                    <h4 class="k-c-title"><?php echo $_product->get_name(); ?></h4>
                                    <?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                        '<a href="%s" class="k-c-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="ri-close-line"></i></a>',
                                        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                        esc_html__( 'Remove this item', 'woocommerce' ),
                                        esc_attr( $product_id ),
                                        esc_attr( $_product->get_sku() )
                                    ), $cart_item_key ); ?>
                                </div>
                                
                                <div class="k-c-meta">
                                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); // Variation data ?>
                                </div>

                                <div class="k-c-bottom">
                                    <div class="k-c-price">
                                        <?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); ?>
                                    </div>
                                    
                                    <div class="k-c-qty">
                                        <button type="button" class="k-qty-btn minus" onclick="changeCartQty(this, -1)">-</button>
                                        <?php
                                            if ( $_product->is_sold_individually() ) {
                                                $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                            } else {
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                                        'min_value'    => '0',
                                                        'product_name' => $_product->get_name(),
                                                        'classes'      => 'k-qty-input', // Custom class
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                            }
                                            echo $product_quantity;
                                        ?>
                                        <button type="button" class="k-qty-btn plus" onclick="changeCartQty(this, 1)">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; 
                endforeach; ?>
            </div>

            <div class="k-promo-section">
                <?php if ( wc_coupons_enabled() ) : ?>
                    <div class="k-promo-wrap">
                        <div class="k-promo-icon"><i class="ri-coupon-3-fill"></i></div>
                        <input type="text" name="coupon_code" class="k-promo-input" id="coupon_code" value="" placeholder="Apply a promo code" /> 
                        <button type="submit" class="k-btn-apply" name="apply_coupon" value="Apply">Apply</button>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>" style="display:none;" id="k-update-cart-btn">Update</button>
            <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
        </form>

        <div style="height: 200px;"></div> 

        <div class="k-cart-summary">
            <div class="k-sum-row">
                <span>Subtotal</span>
                <span class="val"><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>
            
            <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                <div class="k-sum-row coupon">
                    <span>Coupon: <?php echo esc_html( $code ); ?></span>
                    <span class="val">-<?php wc_cart_totals_coupon_html( $coupon ); ?></span>
                </div>
            <?php endforeach; ?>

            <div class="k-sum-row">
                <span>Delivery</span>
                <span class="val">
                    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                        <?php wc_cart_totals_shipping_html(); ?>
                    <?php else: ?>
                        Free
                    <?php endif; ?>
                </span>
            </div>

            <div class="k-sum-divider"></div>

            <div class="k-sum-row total">
                <span>Total Cost</span>
                <span class="val"><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
            
            <a href="<?php echo wc_get_checkout_url(); ?>" class="k-btn-confirm">Checkout Now</a>
        </div>

    <?php endif; ?>
</div>

<?php wp_footer(); ?>

<script>
    // JS Sederhana untuk handle Qty Change langsung update cart
    function changeCartQty(btn, delta) {
        const wrapper = btn.closest('.k-c-qty');
        const input = wrapper.querySelector('input.qty');
        let val = parseInt(input.value) || 0;
        
        val += delta;
        if(val < 0) val = 0; // 0 usually removes item
        
        input.value = val;
        
        // Trigger Update Cart (Native WooCommerce)
        const updateBtn = document.getElementById('k-update-cart-btn');
        updateBtn.removeAttribute('disabled');
        updateBtn.click();
    }
</script>

</body>
</html>