<?php
/**
 * Custom Orders List - Card Style
 */
defined( 'ABSPATH' ) || exit;

// Header Sederhana
?>
<div style="padding: 20px; border-bottom: 1px solid #f0f0f0; display:flex; align-items:center;">
    <a href="<?php echo wc_get_account_endpoint_url('dashboard'); ?>" style="font-size:24px; color:#333; text-decoration:none; margin-right:15px;"><i class="ri-arrow-left-s-line"></i></a>
    <h2 style="margin:0; font-size:18px;">Pesanan Saya</h2>
</div>

<div class="k-order-list-wrap">
    <?php
    $customer_orders = wc_get_orders([
        'customer' => get_current_user_id(),
        'limit'    => 10,
        'paginate' => true,
    ]);

    if ( ! empty( $customer_orders->orders ) ) :
        foreach ( $customer_orders->orders as $customer_order ) :
            $order      = wc_get_order( $customer_order );
            $item_count = $order->get_item_count();
            $items      = $order->get_items();
            $first_item = !empty($items) ? reset($items) : null;
            
            // Gambar Produk Pertama
            $img_url = 'https://placehold.co/100x100/eee/ccc?text=IMG';
            $prod_name = 'Pesanan #' . $order->get_id();
            
            if ($first_item) {
                $prod = $first_item->get_product();
                if($prod) {
                    $img_url = wp_get_attachment_image_url($prod->get_image_id(), 'thumbnail') ?: $img_url;
                    $prod_name = $prod->get_name();
                }
            }
            if ($item_count > 1) $prod_name .= ' (+' . ($item_count - 1) . ' lainnya)';
            ?>
            
            <div class="k-order-card-new">
                <div class="k-ord-head">
                    <span class="k-ord-id">Order #<?php echo $order->get_id(); ?></span>
                    <span class="k-ord-date"><?php echo wc_format_datetime( $order->get_date_created() ); ?></span>
                </div>
                <div class="k-ord-body">
                    <img src="<?php echo esc_url($img_url); ?>" class="k-ord-thumb">
                    <div class="k-ord-info">
                        <h4><?php echo esc_html($prod_name); ?></h4>
                        <span class="k-ord-total"><?php echo $order->get_formatted_order_total(); ?></span>
                    </div>
                </div>
                <div class="k-ord-footer">
                    <span class="k-ord-status status-<?php echo esc_attr($order->get_status()); ?>">
                        <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                    </span>
                    
                    <?php if ( $order->needs_payment() ) : ?>
                        <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="k-btn-detail" style="background:var(--k-primary); color:white;">Bayar</a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="k-btn-detail">Detail</a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach;
    else : ?>
        <div style="text-align:center; padding:40px;">
            <i class="ri-file-list-3-line" style="font-size:48px; color:#ddd;"></i>
            <p style="color:#999; margin-top:10px;">Belum ada pesanan.</p>
            <a href="<?php echo home_url('/app'); ?>" class="k-btn-detail" style="background:var(--k-primary); color:white;">Belanja Sekarang</a>
        </div>
    <?php endif; ?>
</div>