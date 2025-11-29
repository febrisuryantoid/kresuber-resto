<?php
/**
 * Account Shell Controller (Fixed Layout)
 */
defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
    auth_redirect();
}

global $wp;
// Deteksi apakah sedang melihat detail pesanan atau list pesanan
$is_view_order = isset( $wp->query_vars['view-order'] );
$is_orders     = isset( $wp->query_vars['orders'] );
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Akun Saya</title>
    <?php wp_head(); ?>
    <style>
        /* Force Reset */
        body { background: #F8F9FD !important; margin: 0; padding: 0; }
        .k-account-shell-wrap { 
            width: 100%; max-width: 480px; margin: 0 auto; 
            background: #fff; min-height: 100vh; position: relative; 
            padding-bottom: 80px; /* Space for Navbar */
        }
    </style>
</head>
<body class="kresuber-app-mode">
    
    <div class="k-account-shell-wrap">
        <?php 
        if ( $is_view_order ) {
            include KRESUBER_PATH . 'templates/myaccount/view-order.php';
        } elseif ( $is_orders ) {
            include KRESUBER_PATH . 'templates/myaccount/orders.php';
        } else {
            include KRESUBER_PATH . 'templates/myaccount/dashboard.php';
        }
        ?>
        
        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; ?>
    </div>

    <?php wp_footer(); ?>
</body>
</html>