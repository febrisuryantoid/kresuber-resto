<?php
/**
 * Account Shell - Full Page
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Akun Saya</title>
    <?php wp_head(); ?>
    <style>
        body { background: #F8F9FD; padding-bottom: 80px; }
        .k-account-shell-wrap { max-width: 480px; margin: 0 auto; background: #fff; min-height: 100vh; }
        .k-acc-header { padding: 30px 20px; background: #fff; text-align: center; }
        .k-acc-title { margin: 0; font-size: 20px; font-weight: 800; }
        .k-content-area { padding: 20px; }
    </style>
</head>
<body class="kresuber-app-mode">
    <div class="k-account-shell-wrap">
        <div class="k-acc-header">
            <h1 class="k-acc-title">Profil Saya</h1>
        </div>
        
        <div class="k-content-area">
            <?php echo do_shortcode('[woocommerce_my_account]'); ?>
        </div>
        
        <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; ?>
    </div>
    <?php wp_footer(); ?>
</body>
</html>