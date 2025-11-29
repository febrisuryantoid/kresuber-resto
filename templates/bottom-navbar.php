<?php
/**
 * Bottom Navigation Bar
 * Location: templates/bottom-navbar.php
 */
defined( 'ABSPATH' ) || exit;

// Helper logic untuk mendeteksi halaman aktif
$endpoint = get_query_var('kresuber_endpoint');
$is_home = $endpoint === 'app' || (empty($endpoint) && !is_woocommerce() && !is_account_page());
$is_fav = $endpoint === 'app_favorites';
$is_orders = function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('orders');
$is_account = function_exists('is_account_page') && is_account_page() && !$is_orders;
?>

<div class="k-bottom-navbar">
    
    <a href="<?php echo home_url('/app'); ?>" class="k-nav-item <?php echo $is_home ? 'active' : ''; ?>">
        <i class="<?php echo $is_home ? 'ri-home-4-fill' : 'ri-home-4-line'; ?>"></i>
        <span>Beranda</span>
    </a>
    
    <a href="<?php echo home_url('/app/favorites'); ?>" class="k-nav-item <?php echo $is_fav ? 'active' : ''; ?>">
        <i class="<?php echo $is_fav ? 'ri-heart-3-fill' : 'ri-heart-3-line'; ?>"></i>
        <span>Favorit</span>
    </a>

    <a href="<?php echo wc_get_account_endpoint_url( 'orders' ); ?>" class="k-nav-item <?php echo $is_orders ? 'active' : ''; ?>">
        <i class="<?php echo $is_orders ? 'ri-file-list-3-fill' : 'ri-file-list-3-line'; ?>"></i>
        <span>Pesanan</span>
    </a>
    
    <a href="<?php echo wc_get_account_endpoint_url( 'dashboard' ); ?>" class="k-nav-item <?php echo $is_account ? 'active' : ''; ?>">
        <i class="<?php echo $is_account ? 'ri-user-3-fill' : 'ri-user-3-line'; ?>"></i>
        <span>Akun</span>
    </a>

</div>