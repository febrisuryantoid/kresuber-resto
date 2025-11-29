<?php
/**
 * Edit Account Full Page (Styled & Bilingual)
 */
defined( 'ABSPATH' ) || exit;
$user = wp_get_current_user();

// Shortcut Penerjemah
function _txt($id, $en) { return Kresuber_POS_Core::_k($id, $en); }
?>

<div style="padding: 20px; display:flex; align-items:center; border-bottom:1px solid #f0f0f0; background:#fff; position:sticky; top:0; z-index:50;">
    <a href="<?php echo wc_get_account_endpoint_url('dashboard'); ?>" style="font-size:24px; color:#333; margin-right:15px; text-decoration:none;"><i class="ri-arrow-left-s-line"></i></a>
    <h2 style="margin:0; font-size:18px; font-weight:800;"><?php echo _txt('Edit Profil', 'Edit Profile'); ?></h2>
</div>

<div class="k-edit-account-wrap">
    
    <form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

        <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

        <div class="k-form-group">
            <label class="k-form-label"><?php echo _txt('Nama Depan', 'First Name'); ?></label>
            <input type="text" class="k-form-input" name="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" placeholder="Ex: Budi" />
        </div>

        <div class="k-form-group">
            <label class="k-form-label"><?php echo _txt('Nama Belakang', 'Last Name'); ?></label>
            <input type="text" class="k-form-input" name="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" placeholder="Ex: Santoso" />
        </div>

        <div class="k-form-group">
            <label class="k-form-label"><?php echo _txt('Nama Tampilan', 'Display Name'); ?></label>
            <input type="text" class="k-form-input" name="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
            <small style="color:#999; display:block; margin-top:5px; font-size:12px;"><?php echo _txt('Nama ini akan terlihat di halaman akun dan ulasan.', 'This name will be visible in account section and reviews.'); ?></small>
        </div>

        <div class="k-form-group">
            <label class="k-form-label"><?php echo _txt('Email', 'Email Address'); ?></label>
            <input type="email" class="k-form-input" name="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
        </div>

        <fieldset class="k-fieldset">
            <legend><?php echo _txt('Ganti Password (Opsional)', 'Change Password (Optional)'); ?></legend>
            
            <div class="k-form-group">
                <label class="k-form-label"><?php echo _txt('Password Saat Ini', 'Current Password'); ?></label>
                <input type="password" class="k-form-input" name="password_current" placeholder="******" />
            </div>
            <div class="k-form-group">
                <label class="k-form-label"><?php echo _txt('Password Baru', 'New Password'); ?></label>
                <input type="password" class="k-form-input" name="password_1" placeholder="******" />
            </div>
            <div class="k-form-group">
                <label class="k-form-label"><?php echo _txt('Ulangi Password Baru', 'Confirm New Password'); ?></label>
                <input type="password" class="k-form-input" name="password_2" placeholder="******" />
            </div>
        </fieldset>

        <?php do_action( 'woocommerce_edit_account_form' ); ?>

        <input type="hidden" name="action" value="save_account_details" />
        <input type="hidden" name="save-account-details-nonce" value="<?php echo wp_create_nonce( 'save_account_details' ); ?>" />
        
        <button type="submit" class="k-btn-save" name="save_account_details" value="Simpan">
            <?php echo _txt('Simpan Perubahan', 'Save Changes'); ?>
        </button>

        <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
    </form>
</div>