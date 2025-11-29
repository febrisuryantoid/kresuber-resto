<?php
/**
 * Full Page Checkout - Mobile App Style
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
    <style>
        /* Override Global Layout untuk Checkout agar Fokus (Max 480px) */
        body { background-color: #F8F9FD !important; margin: 0; padding: 0; }
        .k-checkout-layout { max-width: 480px; margin: 0 auto; min-height: 100vh; background: #fff; position: relative; padding-bottom: 100px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        
        /* Header */
        .k-header-simple { padding: 20px; display: flex; align-items: center; background: #fff; position: sticky; top: 0; z-index: 50; border-bottom: 1px solid #f0f0f0; }
        .k-header-title { flex-grow: 1; text-align: center; font-size: 18px; font-weight: 800; margin: 0; color: #1a1a1a; }
        .k-btn-icon { font-size: 24px; color: #1a1a1a; text-decoration: none; }

        /* Form Styles */
        .k-content-pad { padding: 20px; }
        .woocommerce-form-login-toggle, .woocommerce-form-coupon-toggle { display: none !important; } /* Sembunyikan login/coupon toggle default */
        
        /* Styling Form Fields */
        .form-row { margin-bottom: 15px; display: block; width: 100% !important; }
        .form-row label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 5px; color: #555; }
        .form-row input, .form-row textarea, .form-row select { 
            width: 100%; padding: 12px; border: 1px solid #eee; border-radius: 10px; font-size: 14px; background: #fcfcfc; box-sizing: border-box;
        }
        .form-row input:focus { border-color: #FF6B00; background: #fff; outline: none; }

        /* Order Review Box */
        #order_review { background: #fcfcfc; border-radius: 12px; padding: 15px; border: 1px solid #eee; margin-top: 20px; }
        
        /* Payment Methods Styling */
        #payment { background: transparent; }
        #payment ul.payment_methods { list-style: none; padding: 0; margin: 20px 0; }
        #payment li.payment_method { background: #fff; border: 1px solid #eee; padding: 15px; border-radius: 12px; margin-bottom: 10px; cursor: pointer; }
        #payment li.payment_method input[type=radio] { margin-right: 10px; }
        
        /* Footer Action */
        .k-footer-sticky {
            position: fixed; bottom: 0; left: 0; width: 100%; 
            display: flex; justify-content: center; z-index: 100;
        }
        .k-footer-inner {
            width: 100%; max-width: 480px; background: #fff; 
            padding: 15px 20px; border-radius: 20px 20px 0 0; 
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
        }
        
        /* Tombol Place Order */
        #place_order {
            width: 100%; display: block; background: #FF6B00; color: white;
            font-weight: 700; padding: 15px; border-radius: 12px; border: none;
            font-size: 16px; cursor: pointer; transition: 0.2s; text-align: center;
        }
        #place_order:hover { background: #e55e00; }

        /* Sembunyikan footer/header tema jika masih bocor */
        header, footer, #colophon, #masthead { display: none !important; }
    </style>
</head>
<body>
    <div class="k-checkout-layout">
        
        <div class="k-header-simple">
            <a href="<?php echo wc_get_cart_url(); ?>" class="k-btn-icon"><i class="ri-arrow-left-s-line"></i></a>
            <h1 class="k-header-title">Checkout Pengiriman</h1>
            <div style="width:24px;"></div>
        </div>

        <div class="k-content-pad">
            
            <?php wc_print_notices(); ?>

            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                <?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

                    <div id="customer_details">
                        <h3 style="font-size:16px; margin-bottom:15px;">Data Pemesan</h3>
                        <?php do_action( 'woocommerce_checkout_billing' ); ?>
                        
                        <div style="display:none;">
                            <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                        </div>
                    </div>

                    <h3 style="font-size:16px; margin:20px 0 15px;">Ringkasan & Pembayaran</h3>
                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                    </div>

                <?php endif; ?>

            </form>
        </div>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html>