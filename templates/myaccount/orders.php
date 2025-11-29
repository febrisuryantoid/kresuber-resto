<?php
/**
 * Custom Orders List (Fixed)
 */
defined( 'ABSPATH' ) || exit;
?>

<div style="padding: 20px; border-bottom: 1px solid #f0f0f0; display:flex; align-items:center;">
    <a href="<?php echo wc_get_account_endpoint_url('dashboard'); ?>" style="font-size:24px; color:#333; text-decoration:none; margin-right:15px;"><i class="ri-arrow-left-s-line"></i></a>
    <h2 style="margin:0; font-size:18px; font-weight:700;">Pesanan Saya</h2>
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
            // Fallback image logic
            $img_url = 'https://placehold.co/100x100/eee/ccc?text=IMG';
            $items = $order->get_items();
            if(!empty($items)) {
                $first = reset($items);
                $prod = $first->get_product();
                if($prod) $img_url = wp_get_attachment_image_url($prod->get_image_id(), 'thumbnail') ?: $img_url;
            }
            ?>
            
            <div class="k-order-card-new">
                <div class="k-ord-head">
                    <span class="k-ord-id">#<?php echo $order->get_id(); ?></span>
                    <span class="k-ord-date"><?php echo wc_format_datetime( $order->get_date_created() ); ?></span>
                </div>
                <div class="k-ord-body">
                    <img src="<?php echo esc_url($img_url); ?>" class="k-ord-thumb">
                    <div class="k-ord-info">
                        <h4>Total: <?php echo $order->get_formatted_order_total(); ?></h4>
                        <span style="font-size:12px; color:#666;"><?php echo $item_count; ?> Item</span>
                    </div>
                </div>
                <div class="k-ord-footer">
                    <span class="k-ord-status status-<?php echo esc_attr($order->get_status()); ?>">
                        <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                    </span>
                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="k-btn-detail">Lihat Detail</a>
                </div>
            </div>

        <?php endforeach;
    else : ?>
        <div style="text-align:center; padding:50px 20px;">
            <p style="color:#999;">Belum ada pesanan.</p>
        </div>
    <?php endif; ?>
</div>