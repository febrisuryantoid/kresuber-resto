<?php
/**
 * Custom Mobile Dashboard UI (Fixed)
 */
defined( 'ABSPATH' ) || exit;
$user = wp_get_current_user();
?>

<div class="k-profile-header">
    <div style="display:flex; justify-content:center;">
        <?php echo get_avatar( $user->ID, 96, '', '', ['class' => 'k-profile-avatar'] ); ?>
    </div>
    <h2 class="k-profile-name"><?php echo esc_html( $user->display_name ); ?></h2>
    <p class="k-profile-email"><?php echo esc_html( $user->user_email ); ?></p>
</div>

<div class="k-menu-grid">
    <a href="<?php echo wc_get_account_endpoint_url( 'orders' ); ?>" class="k-menu-item">
        <div class="k-menu-icon"><i class="ri-file-list-3-line"></i></div>
        <div class="k-menu-label">Riwayat Pesanan</div>
        <i class="ri-arrow-right-s-line" style="color:#ccc;"></i>
    </a>

    <a href="<?php echo home_url('/app/favorites'); ?>" class="k-menu-item">
        <div class="k-menu-icon"><i class="ri-heart-line"></i></div>
        <div class="k-menu-label">Favorit Saya</div>
        <i class="ri-arrow-right-s-line" style="color:#ccc;"></i>
    </a>

    <a href="<?php echo wc_get_account_endpoint_url( 'edit-account' ); ?>" class="k-menu-item">
        <div class="k-menu-icon"><i class="ri-user-settings-line"></i></div>
        <div class="k-menu-label">Edit Profil</div>
        <i class="ri-arrow-right-s-line" style="color:#ccc;"></i>
    </a>

    <a href="<?php echo wp_logout_url( home_url('/app') ); ?>" class="k-menu-item" style="border-color:#ffebee;">
        <div class="k-menu-icon" style="background:#ffebee; color:red;"><i class="ri-logout-box-r-line"></i></div>
        <div class="k-menu-label" style="color:red;">Keluar</div>
    </a>
</div>