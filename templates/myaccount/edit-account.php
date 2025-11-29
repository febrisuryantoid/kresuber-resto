<?php
/**
 * Edit Account Full Page
 */
defined( 'ABSPATH' ) || exit;
$user = wp_get_current_user();
?>

<div style="padding: 20px; display:flex; align-items:center; border-bottom:1px solid #f0f0f0;">
    <a href="<?php echo wc_get_account_endpoint_url('dashboard'); ?>" style="font-size:24px; color:#333; margin-right:15px;"><i class="ri-arrow-left-s-line"></i></a>
    <h2 style="margin:0; font-size:18px; font-weight:800;">Edit Profil</h2>
</div>

<div class="k-edit-account-wrap" style="padding: 20px;">
    
    <form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <div class="k-form-group">
            <label class="k-form-label">Nama Depan</label>
            <input type="text" class="k-form-input" name="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
        </div>

        <div class="k-form-group">
            <label class="k-form-label">Nama Belakang</label>
            <input type="text" class="k-form-input" name="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
        </div>

        <div class="k-form-group">
            <label class="k-form-label">Nama Tampilan</label>
            <input type="text" class="k-form-input" name="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
        </div>

        <div class="k-form-group">
            <label class="k-form-label">Email</label>
            <input type="email" class="k-form-input" name="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
        </div>

        <fieldset style="border:none; padding:0; margin-top:30px;">
            <legend style="font-weight:800; font-size:16px; margin-bottom:15px; display:block;">Ganti Password (Opsional)</legend>
            
            <div class="k-form-group">
                <label class="k-form-label">Password Saat Ini</label>
                <input type="password" class="k-form-input" name="password_current" />
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Password Baru</label>
                <input type="password" class="k-form-input" name="password_1" />
            </div>
            <div class="k-form-group">
                <label class="k-form-label">Ulangi Password Baru</label>
                <input type="password" class="k-form-input" name="password_2" />
            </div>
        </fieldset>

        <?php do_action( 'woocommerce_edit_account_form' ); ?>

        <input type="hidden" name="action" value="save_account_details" />
        <input type="hidden" name="save-account-details-nonce" value="<?php echo wp_create_nonce( 'save_account_details' ); ?>" />
        
        <button type="submit" class="k-btn-save" name="save_account_details" value="Simpan Perubahan">Simpan Perubahan</button>

        <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
    </form>
</div>