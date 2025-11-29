<?php
class Kresuber_POS_Admin {
    public function __construct() { add_action('admin_menu', [$this, 'menu']); }
    public function menu() { add_menu_page('Kresuber', 'Kresuber Resto', 'manage_options', 'kresuber-pos', [$this, 'page'], 'dashicons-store', 56); }
    public function page() { 
        ?>
        <div class="wrap">
            <h1 style="margin-bottom:20px;">Kresuber Resto — Dashboard</h1>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <div style="background: white; padding: 30px; border-radius: 12px; border-left: 5px solid #FF6B00; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h2 style="margin-top:0;">🖥️ POS Terminal (Kasir)</h2>
                    <p>Akses halaman kasir untuk staf restoran.</p>
                    <a href="<?php echo home_url('/pos-terminal'); ?>" target="_blank" class="button button-primary" style="background: #FF6B00; border-color: #FF6B00; padding: 5px 20px;">Buka POS</a>
                </div>

                <div style="background: white; padding: 30px; border-radius: 12px; border-left: 5px solid #7CB342; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h2 style="margin-top:0;">📱 User App (Pelanggan)</h2>
                    <p>Tampilan menu digital untuk pelanggan (Self-Order).</p>
                    <a href="<?php echo home_url('/app'); ?>" target="_blank" class="button button-primary" style="background: #7CB342; border-color: #7CB342; padding: 5px 20px;">Buka App</a>
                </div>
            </div>
            
            <div style="margin-top:20px; padding:15px; background:#fff; border-radius:8px;">
                <p><strong>Status:</strong> <span style="color:green">● Aktif</span> | <strong>Versi:</strong> <?php echo KRESUBER_VERSION; ?></p>
                <p style="color:#666; font-size:12px;">Jika halaman error 404, silakan <a href="<?php echo admin_url('options-permalink.php'); ?>">Simpan Permalinks</a>.</p>
            </div>
        </div>
        <?php
    }
}