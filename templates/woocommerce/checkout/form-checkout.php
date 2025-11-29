<?php
/**
 * Full Page Checkout - Resto Mode (No Shipping)
 * Location: templates/woocommerce/checkout/form-checkout.php
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout</title>
    <?php wp_head(); ?>
</head>
<body>
    <div class="k-checkout-layout">
        
        <div class="k-header">
            <a href="<?php echo wc_get_cart_url(); ?>" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
            <h1 class="k-page-title">Checkout</h1>
            <div style="width:24px;"></div>
        </div>

        <div style="padding: 20px;">
            
            <?php wc_print_notices(); ?>

            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                <?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

                    <div id="customer_details">
                        <h3 style="font-size:16px; margin-bottom:15px; font-weight:800;">Data Pemesan</h3>
                        <?php do_action( 'woocommerce_checkout_billing' ); ?>
                        
                        <div style="display:none !important;">
                            <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                        </div>
                    </div>

                    <div style="height:20px;"></div>

                    <h3 style="font-size:16px; margin:0 0 15px; font-weight:800;">Ringkasan & Pembayaran</h3>
                    
                    <div id="order_review" class="woocommerce-checkout-review-order" style="background:#fff; border:1px solid #eee; border-radius:16px; padding:20px;">
                        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                    </div>

                <?php endif; ?>

            </form>
        </div>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>