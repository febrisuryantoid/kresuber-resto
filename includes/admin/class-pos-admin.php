<?php
class Kresuber_POS_Admin {
    public function __construct() { 
        add_action('admin_menu', [$this, 'menu']); 
        add_action('wp_ajax_kresuber_import_demo', [$this, 'import_demo_products']);
    }
    
    public function menu() { add_menu_page('Kresuber', 'Kresuber Resto', 'manage_options', 'kresuber-pos', [$this, 'page'], 'dashicons-store', 56); }
    
    public function page() { 
        ?>
        <div class="wrap">
            <h1 style="margin-bottom:20px;">Kresuber Resto — Dashboard</h1>
            
            <div id="kresuber-admin-notices"></div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom: 20px;">
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

            <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 20px;">
                <h2 style="margin-top:0;">🚀 Penggunaan Shortcode</h2>
                <p>Gunakan shortcode berikut untuk menampilkan aplikasi di halaman mana pun:</p>
                <ul>
                    <li>Terminal Kasir: <code>[kresuber_pos_terminal]</code></li>
                    <li>Aplikasi Pelanggan: <code>[kresuber_pos_app]</code></li>
                </ul>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 12px;">
                <h2 style="margin-top:0;">📦 Produk Demo</h2>
                <p>Impor 30 produk demo kuliner Indonesia untuk pengujian. Tindakan ini tidak dapat diurungkan.</p>
                <button id="import-demo-btn" class="button">Impor Produk Demo</button>
            </div>
            
            <div style="margin-top:20px; padding:15px; background:#fff; border-radius:8px;">
                <p><strong>Status:</strong> <span style="color:green">● Aktif</span> | <strong>Versi:</strong> <?php echo KRESUBER_VERSION; ?></p>
                <p style="color:#666; font-size:12px;">Jika halaman error 404, silakan <a href="<?php echo admin_url('options-permalink.php'); ?>">Simpan Permalinks</a>.</p>
            </div>
        </div>
        
        <script>
            jQuery(document).ready(function($) {
                $('#import-demo-btn').on('click', function() {
                    $(this).prop('disabled', true).text('Mengimpor...');
                    $.post(ajaxurl, { action: 'kresuber_import_demo', nonce: '<?php echo wp_create_nonce(KRESUBER_NONCE); ?>' }, function(res) {
                        let notice = '<div class="notice notice-success is-dismissible"><p>' + res.data.message + '</p></div>';
                        if (!res.success) {
                            notice = '<div class="notice notice-error is-dismissible"><p>' + res.data.message + '</p></div>';
                        }
                        $('#kresuber-admin-notices').html(notice);
                        $('#import-demo-btn').prop('disabled', false).text('Impor Produk Demo');
                    });
                });
            });
        </script>
        <?php
    }

    public function import_demo_products() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
            return;
        }

        require_once KRESUBER_PATH . 'includes/utils/class-pos-importer.php';
        $importer = new Kresuber_POS_Importer();
        $result = $importer->import();

        if ($result['success']) {
            wp_send_json_success(['message' => $result['message']]);
        } else {
            wp_send_json_error(['message' => $result['message']]);
        }
    }
}