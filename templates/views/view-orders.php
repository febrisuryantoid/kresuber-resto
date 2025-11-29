<?php
/**
 * Custom View: Card-Based Orders List
 * Used by templates/myaccount/orders.php
 */

defined( 'ABSPATH' ) || exit;

$customer_orders = wc_get_orders(
    apply_filters(
        'woocommerce_my_account_my_orders_query',
        [
            'customer' => get_current_user_id(),
            'paginate' => true,
            'paged'    => 1, // You might want to handle pagination here
            'status'   => 'any', // Default to all statuses, will filter via JS/CSS or further PHP
        ]
    )
);

// Get available order statuses for filtering
$order_statuses = wc_get_order_statuses();

?>

<div class="k-orders-view-wrap">
    <div class="k-orders-header">
        <h1 class="k-title">Pesanan Saya</h1>
    </div>

    <div class="k-order-filter-tabs">
        <div class="k-tab-item active" data-status="all">Semua</div>
        <div class="k-tab-item" data-status="wc-processing">Diproses</div>
        <div class="k-tab-item" data-status="wc-completed">Selesai</div>
        <div class="k-tab-item" data-status="wc-cancelled">Dibatalkan</div>
    </div>

    <div class="k-order-list">
        <?php if ( $customer_orders->orders ) : ?>
            <?php foreach ( $customer_orders->orders as $customer_order ) : ?>
                <?php
                $order      = wc_get_order( $customer_order );
                $order_id   = $order->get_id();
                $order_date = wc_format_datetime( $order->get_date_created() );
                $order_total = $order->get_formatted_order_total();
                $order_status = $order->get_status();
                $status_label = isset($order_statuses['wc-' . $order_status]) ? $order_statuses['wc-' . $order_status] : ucwords($order_status);

                $items = $order->get_items();
                $first_item_image = 'https://placehold.co/60x60/f0f0f0/cccccc?text=Produk'; // Default placeholder
                $first_item_name = 'Produk Tidak Ditemukan';
                
                if ( ! empty( $items ) ) {
                    $first_item = reset( $items );
                    $product = $first_item->get_product();
                    if ($product) {
                        $first_item_name = $first_item->get_name();
                        $image_id = $product->get_image_id();
                        if ($image_id) {
                            $first_item_image = wp_get_attachment_image_url($image_id, 'thumbnail');
                        }
                    }
                }
                
                $item_count = $order->get_item_count();
                $product_display_name = $item_count > 1 ? $first_item_name . ' +' . ($item_count - 1) . ' lainnya' : $first_item_name;
                ?>
                <div class="k-order-card woocommerce-order-details-card" data-status="<?php echo esc_attr($order_status); ?>">
                    <div class="k-order-card-header">
                        <img src="<?php echo esc_url($first_item_image); ?>" alt="Produk" class="k-order-thumbnail">
                        <div class="k-order-info">
                            <div class="k-order-title"><strong><?php echo esc_html($product_display_name); ?></strong></div>
                            <div class="k-order-id">Order ID - #<?php echo esc_html($order_id); ?></div>
                        </div>
                        <div class="k-order-total"><?php echo wp_kses_post($order_total); ?></div>
                    </div>
                    <div class="k-order-card-footer">
                        <span class="k-status-badge status-<?php echo esc_attr($order_status); ?>"><?php echo esc_html($status_label); ?></span>
                        <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="k-btn-view-order">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="woocommerce-info">Anda belum melakukan pemesanan.</p>
        <?php endif; ?>
    </div>
</div>
