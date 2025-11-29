<?php
/**
 * My Account Dashboard - Custom UI
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
?>

<div class="k-account-dashboard">
    <div class="k-profile-header">
        <div class="k-avatar-large">
            <?php echo get_avatar( $user->ID, 96 ); // Placeholder for larger avatar ?>
        </div>
        <h1 class="k-user-name"><?php echo esc_html( $user->display_name ); ?></h1>
        <p class="k-user-email"><?php echo esc_html( $user->user_email ); ?></p>
        <p class="k-member-level">Member Sejak: <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) ) ); ?></p>
    </div>

    <div class="k-account-menu-grid">
        <?php
        $dashboard_endpoints = wc_get_account_menu_items();
        unset($dashboard_endpoints['dashboard']); // Remove default dashboard link
        
        $menu_icons = [
            'orders'          => 'ri-file-list-3-line',
            'downloads'       => 'ri-download-line',
            'edit-address'    => 'ri-map-pin-line',
            'payment-methods' => 'ri-bank-card-line',
            'edit-account'    => 'ri-user-settings-line',
            'customer-logout' => 'ri-logout-box-r-line',
        ];

        foreach ( $dashboard_endpoints as $endpoint => $label ) :
            $icon = isset($menu_icons[$endpoint]) ? $menu_icons[$endpoint] : 'ri-arrow-right-s-line';
            ?>
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="k-account-menu-item">
                <i class="<?php echo esc_attr($icon); ?> k-icon-menu"></i>
                <span class="k-menu-label"><?php echo esc_html( $label ); ?></span>
                <i class="ri-arrow-right-s-line k-arrow-icon"></i>
            </a>
        <?php endforeach; ?>
    </div>
</div>
