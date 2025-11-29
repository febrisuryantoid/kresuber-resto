<?php
/**
 * Custom Mobile Cart Template
 * Override: templates/woocommerce/cart/cart.php
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
        body { background: #F8F9FD; padding-bottom: 120px; }
        .k-cart-header { padding: 20px; display: flex; align-items: center; background: #fff; position: sticky; top: 0; z-index: 10; }
        .k-page-title { flex-grow: 1; text-align: center; font-size: 18px; font-weight: 700; margin: 0; }
        
        .k-cart-list { padding: 20px; }
        .k-cart-item { background: #fff; padding: 15px; border-radius: 16px; display: flex; align-items: center; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .k-cart-img { width: 60px; height: 60px; border-radius: 12px; object-fit: cover; margin-right: 15px; background: #eee; }
        .k-cart-info { flex-grow: 1; }
        .k-cart-name { font-weight: 700; font-size: 14px; margin-bottom: 4px; color: #1a1a1a; }
        .k-cart-meta { font-size: 13px; color: #FF6B00; font-weight: 600; }
        
        .k-cart-actions { display: flex; align-items: center; gap: 10px; }
        .k-qty-btn-mini { width: 28px; height: 28px; background: #f0f0f0; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        
        .k-cart-summary {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #fff; padding: 20px;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
            border-radius: 24px 24px 0 0; z-index: 100;
        }
        .k-sum-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px; color: #666; }
        .k-sum-row.total { font-size: 18px; font-weight: 800; color: #1a1a1a; margin-top: 10px; margin-bottom: 20px; }
        
        .k-btn-checkout {
            display: block; width: 100%; padding: 16px;
            background: #FF6B00; color: white; text-align: center;
            border-radius: 16px; text-decoration: none; font-weight: 700; font-size: 16px;
        }
    </style>
</head>
<body class="kresuber-app-mode">

    <div class="k-cart-header">
        <a href="<?php echo home_url('/app'); ?>" style="color:#1a1a1a; font-size:24px; text-decoration:none;"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-page-title">Keranjang Saya</h1>
        <div style="width:24px;"></div> </div>

    <div class="k-cart-list">
        <?php if ( WC()->cart->is_empty() ) : ?>
            <div style="text-align:center; padding:50px 20px;">
                <i class="ri-shopping-cart-line" style="font-size:48px; color:#ddd;"></i>
                <h3 style="color:#999;">Keranjang masih kosong</h3>
                <a href="<?php echo home_url('/app'); ?>" class="k-link-add">Mulai Belanja</a>
            </div>
        <?php else : ?>
            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : 
                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                
                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                    $image = wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail');
                    if(!$image) $image = 'https://placehold.co/60x60';
                    ?>
                    <div class="k-cart-item">
                        <img src="<?php echo esc_url($image); ?>" class="k-cart-img">
                        <div class="k-cart-info">
                            <div class="k-cart-name"><?php echo $_product->get_name(); ?></div>
                            <div class="k-cart-meta">
                                <?php echo WC()->cart->get_product_price( $_product ); ?> x <?php echo $cart_item['quantity']; ?>
                            </div>
                        </div>
                        <div class="k-cart-actions">
                            <?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                                '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="ri-delete-bin-line" style="color:red; font-size:20px;"></i></a>',
                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                esc_html__( 'Remove this item', 'woocommerce' ),
                                esc_attr( $product_id ),
                                esc_attr( $_product->get_sku() )
                            ), $cart_item_key ); ?>
                        </div>
                    </div>
                <?php endif; 
            endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ( ! WC()->cart->is_empty() ) : ?>
    <div class="k-cart-summary">
        <div class="k-sum-row">
            <span>Subtotal</span>
            <span><?php wc_cart_totals_subtotal_html(); ?></span>
        </div>
        <div class="k-sum-row total">
            <span>Total</span>
            <span><?php wc_cart_totals_order_total_html(); ?></span>
        </div>
        <a href="<?php echo wc_get_checkout_url(); ?>" class="k-btn-checkout">Checkout Now</a>
    </div>
    <?php endif; ?>

    <?php wp_footer(); ?>
</body>
</html>