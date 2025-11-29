<?php
/**
 * View Order - POS Bill Style
 */
defined( 'ABSPATH' ) || exit;

$order_id = absint( get_query_var( 'view-order' ) );
$order    = wc_get_order( $order_id );

if ( ! $order ) {
    echo '<div style="padding:20px;">Order tidak ditemukan.</div>';
    return;
}
?>

<div class="k-view-order-wrap">
    <div style="padding: 20px; display:flex; align-items:center; border-bottom:1px solid #f0f0f0;">
        <a href="<?php echo wc_get_account_endpoint_url('orders'); ?>" style="font-size:24px; color:#333; margin-right:15px;"><i class="ri-arrow-left-s-line"></i></a>
        <h2 style="margin:0; font-size:18px; font-weight:800;">Detail Pesanan #<?php echo $order->get_id(); ?></h2>
    </div>

    <div style="padding: 20px; text-align:center;">
        <div style="font-size:13px; color:#888; margin-bottom:5px;">Tanggal Pesanan</div>
        <div style="font-weight:700; font-size:15px; color:#333;"><?php echo wc_format_datetime( $order->get_date_created() ); ?></div>
        <div style="margin-top:10px;">
            <span class="k-ord-status status-<?php echo esc_attr($order->get_status()); ?>">
                <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
            </span>
        </div>
    </div>

    <div class="k-bill-card">
        <h3 style="font-size:15px; font-weight:700; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #eee;">Rincian Menu</h3>
        
        <?php foreach ( $order->get_items() as $item_id => $item ) : 
            $product = $item->get_product();
            ?>
            <div class="k-bill-item">
                <div>
                    <div class="k-bill-item-name"><?php echo $item->get_name(); ?></div>
                    <div class="k-bill-item-meta"><?php echo $item->get_quantity(); ?> x <?php echo wc_price( $order->get_item_total( $item, false, true ) ); ?></div>
                </div>
                <div style="font-weight:700;"><?php echo wc_price( $item->get_subtotal() ); ?></div>
            </div>
        <?php endforeach; ?>

        <div class="k-bill-total">
            <div class="k-bill-row">
                <span>Subtotal</span>
                <span><?php echo $order->get_subtotal_to_display(); ?></span>
            </div>
            <div class="k-bill-row" style="font-size:18px; font-weight:800; color:#FF6B00; margin-top:10px;">
                <span>Total</span>
                <span><?php echo $order->get_formatted_order_total(); ?></span>
            </div>
        </div>
    </div>

    <?php if ( $order->needs_payment() ) : ?>
        <div style="padding:0 20px;">
            <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="k-btn-save" style="display:block; text-align:center; text-decoration:none;">Bayar Sekarang</a>
        </div>
    <?php endif; ?>
</div>