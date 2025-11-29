<?php
/**
 * Account Shell Controller (Updated)
 */
defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) auth_redirect();

global $wp;
$is_view_order = isset( $wp->query_vars['view-order'] );
$is_orders     = isset( $wp->query_vars['orders'] );
$is_edit_account = isset( $wp->query_vars['edit-account'] );
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
        <?php 
        if ( $is_view_order ) {
            include KRESUBER_PATH . 'templates/myaccount/view-order.php';
        } elseif ( $is_orders ) {
            include KRESUBER_PATH . 'templates/myaccount/orders.php';
        } elseif ( $is_edit_account ) {
            include KRESUBER_PATH . 'templates/myaccount/edit-account.php';
        } else {
            include KRESUBER_PATH . 'templates/myaccount/dashboard.php';
        }
        ?>
        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; ?>
    </div>
    <?php wp_footer(); ?>
</body>
</html>