<?php
/**
 * Custom Pay for Order Form (Match UI Design)
 * Location: templates/woocommerce/checkout/form-pay.php
 */

defined( 'ABSPATH' ) || exit;

$order_id = $order->get_id();
$order_total = $order->get_formatted_order_total();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pembayaran - Order #<?php echo $order_id; ?></title>
    <?php wp_head(); ?>
    <style>
        /* CSS Inline Khusus Halaman ini untuk menimpa tema bawaan */
        body { background-color: #F8F9FD !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .k-checkout-wrap { max-width: 480px; margin: 0 auto; min-height: 100vh; background: #F8F9FD; padding-bottom: 100px; }
        
        /* Header */
        .k-checkout-header { padding: 20px; display: flex; align-items: center; justify-content: space-between; background: transparent; }
        .k-btn-back { font-size: 24px; color: #1a1a1a; text-decoration: none; }
        .k-page-title { font-size: 18px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .k-icon-bag { font-size: 24px; color: #1a1a1a; }

        /* Section Title */
        .k-section-header { display: flex; justify-content: space-between; align-items: center; padding: 0 20px 10px; margin-top: 20px; }
        .k-section-title { font-size: 16px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .k-link-action { color: #FF6B00; font-size: 14px; font-weight: 600; text-decoration: none; }

        /* Cards */
        .k-card-white { background: #fff; border-radius: 16px; padding: 16px; margin: 0 20px 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); display: flex; align-items: center; position: relative; }
        
        /* Address Card */
        .k-icon-loc-wrap { width: 40px; height: 40px; border-radius: 50%; background: #FFF0E6; display: flex; align-items: center; justify-content: center; margin-right: 15px; flex-shrink: 0; }
        .k-icon-loc { color: #FF6B00; font-size: 20px; }
        .k-addr-content { flex-grow: 1; }
        .k-addr-label { font-size: 14px; font-weight: 700; color: #1a1a1a; display: block; margin-bottom: 4px; }
        .k-addr-text { font-size: 13px; color: #888; line-height: 1.4; margin: 0; }
        .k-icon-edit { color: #4CAF50; font-size: 20px; }

        /* Payment Methods */
        ul.wc_payment_methods { list-style: none !important; padding: 0 !important; margin: 0 !important; }
        li.wc_payment_method { margin-bottom: 10px !important; }
        
        /* Menyembunyikan Radio Button Asli & Membuat Custom Card */
        li.wc_payment_method > input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        
        li.wc_payment_method > label {
            display: flex !important; align-items: center;
            background: #fff; border-radius: 16px; padding: 16px 20px;
            margin: 0 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            cursor: pointer; transition: 0.2s; border: 2px solid transparent;
            width: auto;
        }

        /* State Terpilih */
        li.wc_payment_method > input[type="radio"]:checked + label {
            border-color: #FF6B00;
            background-color: #FFFbf8;
        }

        /* Icon & Text di Payment */
        li.wc_payment_method > label img { max-height: 24px; margin-right: 15px; }
        li.wc_payment_method > label .payment_method_title { font-weight: 700; color: #1a1a1a; font-size: 15px; flex-grow: 1; }
        
        /* Custom Radio Circle */
        .k-custom-radio { width: 20px; height: 20px; border-radius: 50%; border: 2px solid #ddd; position: relative; }
        li.wc_payment_method > input[type="radio"]:checked + label .k-custom-radio { border-color: #FF6B00; background: #FF6B00; }
        li.wc_payment_method > input[type="radio"]:checked + label .k-custom-radio::after { content: '✔'; color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 12px; }

        /* Description Box (WooCommerce Default) */
        .payment_box { background: #f9f9f9; margin: 0 20px 15px; padding: 15px; border-radius: 12px; font-size: 13px; color: #666; display: none; }
        li.wc_payment_method > input[type="radio"]:checked ~ .payment_box { display: block; }

        /* Bottom Action */
        .k-bottom-action { position: fixed; bottom: 0; left: 0; width: 100%; background: #fff; padding: 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.05); z-index: 100; text-align: center; }
        .k-btn-confirm {
            background: #FF6B00; color: white; width: 100%; max-width: 440px;
            padding: 16px; border-radius: 16px; border: none; font-size: 16px; font-weight: 700;
            cursor: pointer; display: block; text-align: center; text-decoration: none;
            transition: background 0.2s;
        }
        .k-btn-confirm:hover { background: #e55e00; }
        
        /* Hide default elements */
        .woocommerce-error, .woocommerce-message { margin: 20px; border-radius: 12px; }
        header, footer { display: none !important; } /* Sembunyikan header/footer tema bawaan */
    </style>
</head>
<body <?php body_class(); ?>>

<div class="k-checkout-wrap">
    
    <div class="k-checkout-header">
        <a href="javascript:history.back()" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-page-title">Checkout</h1>
        <i class="ri-shopping-bag-3-line k-icon-bag"></i>
    </div>

    <form id="order_review" method="post">

        <div class="k-section-header">
            <h3 class="k-section-title">Delivery Location</h3>
            <span class="k-link-action">Table: <?php echo esc_html($order->get_meta('_kresuber_table_no') ?: '-'); ?></span>
        </div>

        <div class="k-card-white">
            <div class="k-icon-loc-wrap"><i class="ri-map-pin-fill k-icon-loc"></i></div>
            <div class="k-addr-content">
                <span class="k-addr-label">Lokasi Restoran</span>
                <p class="k-addr-text">
                    Order ID: #<?php echo $order_id; ?><br>
                    Total Tagihan: <strong style="color:#FF6B00;"><?php echo $order_total; ?></strong>
                </p>
            </div>
            </div>

        <div class="k-section-header">
            <h3 class="k-section-title">Payment Method</h3>
            </div>

        <div id="payment" class="woocommerce-checkout-payment">
            <?php if ( $order->needs_payment() ) : ?>
                <ul class="wc_payment_methods payment_methods methods">
                    <?php
                    if ( ! empty( $available_gateways ) ) {
                        foreach ( $available_gateways as $gateway ) {
                            wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                        }
                    } else {
                        echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ) . '</li>';
                    }
                    ?>
                </ul>
            <?php endif; ?>
            
            <div class="form-row" style="display:none;">
                <input type="hidden" name="woocommerce_pay" value="1" />
                <?php wc_get_template( 'checkout/terms.php' ); ?>
                <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>
                <?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); ?>
                <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>
                <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
            </div>
        </div>
        
        <div class="k-bottom-action">
            <button type="submit" class="k-btn-confirm" onclick="document.getElementById('place_order').click(); return false;">
                Confirm Payment - <?php echo $order_total; ?>
            </button>
        </div>

    </form>
</div>

</body>
</html>