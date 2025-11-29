<?php
class Kresuber_POS_Api {
    public function __construct() {
        // ... hook yang sudah ada sebelumnya biarkan ...
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']); 
        add_action('wp_ajax_kresuber_process_order', [$this, 'process_order']); 
        add_action('wp_ajax_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_nopriv_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_kresuber_get_orders_history', [$this, 'get_orders_history']);
        
        // NEW: Hook Manajemen Meja
        add_action('wp_ajax_kresuber_manage_tables', [$this, 'manage_tables']);
    }

    // ... (Fungsi get_init, get_products, process_order, get_orders_history TETAP ADA, JANGAN DIHAPUS) ...

    // FUNGSI BARU: CRUD Meja
    public function manage_tables() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        
        $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'get';
        $option_name = 'kresuber_restaurant_tables';
        
        // Default data jika belum ada
        $default_tables = [];
        for($i=1; $i<=10; $i++) { $default_tables[] = ['id' => $i, 'name' => 'Meja ' . $i]; }

        $current_tables = get_option($option_name, $default_tables);

        if ($mode === 'get') {
            wp_send_json_success($current_tables);
        } 
        
        elseif ($mode === 'save') {
            if (!isset($_POST['tables'])) {
                wp_send_json_error(['message' => 'Data tidak valid']); 
                return;
            }
            // Simpan array meja baru (sanitize array)
            $new_tables = json_decode(stripslashes($_POST['tables']), true);
            if (update_option($option_name, $new_tables)) {
                wp_send_json_success(['message' => 'Data meja diperbarui', 'tables' => $new_tables]);
            } else {
                // Jika data sama, update_option return false, tapi kita anggap sukses
                wp_send_json_success(['message' => 'Data meja tersimpan (tidak ada perubahan)', 'tables' => $new_tables]);
            }
        }
    }
}