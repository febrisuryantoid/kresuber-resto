<?php
/**
 * Custom Payment Page - Full App Responsive
 * Location: templates/woocommerce/checkout/form-pay.php
 */

defined( 'ABSPATH' ) || exit;

// Setup Data Order
global $wp;
$order_id = isset( $wp->query_vars['order-pay'] ) ? absint( $wp->query_vars['order-pay'] ) : 0;
$order = wc_get_order( $order_id );

if ( ! $order ) {
    wp_safe_redirect( wc_get_checkout_url() );
    exit;
}

// Setup Payment Gateways
$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
if ( sizeof( $available_gateways ) ) {
    current( $available_gateways )->set_current();
}

$order_button_text = __( 'Konfirmasi Bayar', 'woocommerce' );
$order_total = $order->get_formatted_order_total();
$table_no = $order->get_meta('_kresuber_table_no') ?: '-';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bayar Pesanan #<?php echo $order_id; ?></title>
    <?php wp_head(); ?>
</head>
<body>

<div class="k-checkout-layout">
    
    <div class="k-header">
        <a href="<?php echo home_url('/app'); ?>" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-page-title">Checkout</h1>
        <div style="width:24px;"></div> </div>

    <div style="padding:0 20px; margin-top:10px;">
        <?php wc_print_notices(); ?>
    </div>

    <form id="order_review" method="post">

        <div style="padding: 20px 20px 0;">
            <h3 style="font-size:14px; font-weight:700; margin-bottom:10px;">Dining Location</h3>
            <div style="display:flex; align-items:center; background:#FFF8F0; padding:15px; border-radius:12px; border:1px solid #FFEDD5;">
                <div style="width:40px; height:40px; background:#FF6B00; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:20px; margin-right:15px;">
                    <i class="ri-map-pin-user-fill"></i>
                </div>
                <div>
                    <div style="font-weight:700; font-size:14px;">Meja: <?php echo esc_html($table_no); ?></div>
                    <div style="font-size:12px; color:#666;">Order #<?php echo $order_id; ?> • <?php echo date_i18n('d M Y, H:i'); ?></div>
                </div>
                <i class="ri-checkbox-circle-fill" style="margin-left:auto; color:#4CAF50; font-size:20px;"></i>
            </div>
        </div>

        <div style="padding: 20px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <h3 style="font-size:14px; font-weight:700; margin:0;">Payment Method</h3>
                <span style="font-size:12px; color:#FF6B00; font-weight:600;">Choose One</span>
            </div>

            <div id="payment" class="woocommerce-checkout-payment">
                <?php if ( $order->needs_payment() ) : ?>
                    <ul class="wc_payment_methods payment_methods methods">
                        <?php
                        if ( ! empty( $available_gateways ) ) {
                            foreach ( $available_gateways as $gateway ) {
                                ?>
                                <li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                                    <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
                                    
                                    <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                                        <?php if ( $gateway->get_icon() ) : ?>
                                            <?php echo $gateway->get_icon(); ?>
                                        <?php else: ?>
                                            <i class="ri-bank-card-line" style="font-size:24px; margin-right:15px; color:#ccc;"></i>
                                        <?php endif; ?>
                                        
                                        <span><?php echo $gateway->get_title(); ?></span> 
                                    </label>
                                    
                                    <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                                        <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>">
                                            <?php $gateway->payment_fields(); ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                                <?php
                            }
                        } else {
                            echo '<li>Tidak ada metode pembayaran.</li>';
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
        </div>

        <div class="k-bottom-bar">
            <button type="submit" class="k-btn-confirm" onclick="document.getElementById('place_order').click(); return false;">
                Confirm Payment - <?php echo $order_total; ?>
            </button>
        </div>

    </form>
</div>

<?php wp_footer(); ?>
</body>
</html>