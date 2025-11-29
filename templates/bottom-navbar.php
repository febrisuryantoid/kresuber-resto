<div class="k-bottom-navbar">
    <a href="<?php echo home_url('/app'); ?>" class="k-nav-item <?php echo (get_query_var('kresuber_endpoint') == 'app' || !get_query_var('kresuber_endpoint')) ? 'active' : ''; ?>">
        <i class="ri-home-4-line"></i>
        <span>Beranda</span>
    </a>
    <a href="<?php echo home_url('/app/favorites'); ?>" class="k-nav-item <?php echo (get_query_var('kresuber_endpoint') == 'app_favorites') ? 'active' : ''; ?>">
        <i class="ri-heart-line"></i>
        <span>Favorit</span>
    </a>
    <a href="<?php echo wc_get_account_endpoint_url( 'orders' ); ?>" class="k-nav-item <?php echo (is_wc_endpoint_url('orders')) ? 'active' : ''; ?>">
        <i class="ri-file-list-3-line"></i>
        <span>Pesanan</span>
    </a>
    <a href="<?php echo wc_get_account_endpoint_url( 'dashboard' ); ?>" class="k-nav-item <?php echo (is_account_page() && !is_wc_endpoint_url('orders')) ? 'active' : ''; ?>">
        <i class="ri-user-3-line"></i>
        <span>Akun</span>
    </a>
</div>