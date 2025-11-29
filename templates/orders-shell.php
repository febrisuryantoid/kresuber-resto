<?php
/**
 * Orders Shell Template
 * Integrates with WooCommerce My Account -> Orders
 */

defined( 'ABSPATH' ) || exit;

// Load necessary assets (CSS, JS) from Kresuber_POS_Core::assets()

get_header(); // Or your custom header if needed

?>

<div id="k-app-container">
    <div class="k-header">
        <h1 class="k-title">Pesanan Anda</h1>
    </div>
    <div class="k-content" style="padding: 20px;">
        <?php
            // Ensure WooCommerce shortcode works
            echo do_shortcode('[woocommerce_my_account order_count="10"]');
        ?>
    </div>
    <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; // Include bottom navbar ?>
</div>

<?php
get_footer(); // Or your custom footer if needed
