<?php
/**
 * Account Shell Controller
 * Menggantikan standard WC Account endpoint
 */
defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
    auth_redirect(); // Atau redirect ke custom login page
}

// Deteksi Endpoint (Orders vs Dashboard)
global $wp;
$is_orders = isset( $wp->query_vars['orders'] );
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Akun Saya</title>
    <?php wp_head(); ?>
</head>
<body class="kresuber-app-mode">
    
    <div class="k-account-shell-wrap">
        
        <?php if ( $is_orders ) : ?>
            <?php include KRESUBER_PATH . 'templates/myaccount/orders.php'; ?>
        <?php else : ?>
            <?php include KRESUBER_PATH . 'templates/myaccount/dashboard.php'; ?>
        <?php endif; ?>

        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>