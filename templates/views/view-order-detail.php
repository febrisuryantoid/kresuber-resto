<?php
/**
 * Custom View: Order Detail & Tracking
 * Used by templates/myaccount/view-order.php
 */

defined( 'ABSPATH' ) || exit;

$order_id = absint( get_query_var( 'view-order' ) );
$order    = wc_get_order( $order_id );

if ( ! $order ) {
	wp_die( esc_html__( 'Invalid order.', 'woocommerce' ) );
}

// Check if this order belongs to the current user
if ( ! current_user_can( 'manage_woocommerce' ) && $order->get_customer_id() !== get_current_user_id() ) {
    wp_die( esc_html__( 'You do not have permission to view this order.', 'woocommerce' ) );
}

$order_data = $order->get_data();
$order_status = $order->get_status();
$order_statuses = wc_get_order_statuses();
$status_label = isset($order_statuses['wc-' . $order_status]) ? $order_statuses['wc-' . $order_status] : ucwords($order_status);

// Dummy data for driver and tracking for demonstration
$driver_name = 'Budi Santoso';
$driver_rating = '4.8';
$driver_image = 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'; // Placeholder driver image

$timeline_events = [
    ['status' => 'Order Complete', 'icon' => 'ri-checkbox-circle-fill', 'class' => 'completed', 'date' => wc_format_datetime($order->get_date_completed() ? $order->get_date_completed() : $order->get_date_created(), get_option('date_format') . ' ' . get_option('time_format'))],
    ['status' => 'Being Sent', 'icon' => 'ri-motorbike-fill', 'class' => ('wc-completed' === $order_status || 'wc-processing' === $order_status) ? 'completed' : '', 'date' => ('wc-completed' === $order_status || 'wc-processing' === $order_status) ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $order->get_date_created() ) + (60 * 60 * 2) ) : '' ], // 2 hours after order created for demo
    ['status' => 'Waiting for Pick Up', 'icon' => 'ri-time-line', 'class' => ('wc-processing' === $order_status) ? 'completed' : '', 'date' => ('wc-processing' === $order_status) ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $order->get_date_created() ) + (60 * 60 * 1) ) : '' ], // 1 hour after order created for demo
    ['status' => 'Payment Verified', 'icon' => 'ri-bank-card-fill', 'class' => ('wc-pending' !== $order_status && 'wc-on-hold' !== $order_status) ? 'completed' : '', 'date' => ('wc-pending' !== $order_status && 'wc-on-hold' !== $order_status) ? wc_format_datetime( $order->get_date_created(), get_option('date_format') . ' ' . get_option('time_format') ) : '' ],
];

$timeline_events = array_reverse($timeline_events); // Display newest first

?>

<div class="k-order-detail-wrap">
    <div class="k-detail-header">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="k-btn-back"><i class="ri-arrow-left-s-line"></i></a>
        <h1 class="k-title">Order Tracking</h1>
        <i class="ri-shopping-bag-line k-icon-cart"></i>
    </div>

    <div class="k-map-section">
        <img src="https://www.google.com/maps/uv?pb=!1s0x2e69f37c223c6d67%3A0x1d4a0b27c1913f1c!3m1!7e115!4shttps%3A%2F%2Flh5.googleusercontent.com%2Fp%2FAF1QipPYQ_Yp2hM7y-z-lG2gJ9M8c3z9F0L0x0X9S0W0%3Dw213-h160-k-no!5smapa%20jakarta%20-%20Telusuri%20dengan%20Google&imagekey=!1e10!2sAF1QipPYQ_Yp2hM7y-z-lG2gJ9M8c3z9F0L0x0X9S0W0&hl=id&ved=1T_A_A_D" alt="Map" class="k-map-placeholder"> <!-- Placeholder map image -->
        <span class="k-live-tracking-badge">Live Tracking</span>
    </div>

    <div class="k-driver-info-card">
        <img src="<?php echo esc_url($driver_image); ?>" alt="Driver" class="k-driver-avatar">
        <div class="k-driver-details">
            <p class="k-driver-name"><strong><?php echo esc_html($driver_name); ?></strong></p>
            <p class="k-driver-rating"><i class="ri-star-fill"></i> <?php echo esc_html($driver_rating); ?> Driver</p>
        </div>
        <div class="k-driver-actions">
            <a href="#" class="k-driver-action-btn"><i class="ri-chat-3-line"></i></a>
            <a href="#" class="k-driver-action-btn"><i class="ri-phone-line"></i></a>
        </div>
    </div>

    <div class="k-order-timeline">
        <?php foreach ($timeline_events as $event) : ?>
            <div class="k-timeline-item <?php echo esc_attr($event['class']); ?>">
                <div class="k-timeline-icon"><i class="<?php echo esc_attr($event['icon']); ?>"></i></div>
                <div class="k-timeline-content">
                    <div class="k-timeline-title"><strong><?php echo esc_html($event['status']); ?></strong></div>
                    <div class="k-timeline-date"><?php echo esc_html($event['date']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="k-order-footer-actions">
        <button class="k-btn-primary k-btn-full-width">Order Collected</button>
    </div>
</div>
