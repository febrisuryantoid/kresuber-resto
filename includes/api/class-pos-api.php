<?php
class Kresuber_POS_Api {
    public function __construct() {
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']); 
        
        // Checkout & Order Management
        add_action('wp_ajax_kresuber_process_order', [$this, 'process_order_checkout']); // Rename function biar jelas
        add_action('wp_ajax_kresuber_get_orders_history', [$this, 'get_orders_history']);
        
        // Data & Utilities
        add_action('wp_ajax_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_nopriv_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_kresuber_manage_tables', [$this, 'manage_tables']);
    }

    public function get_init() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        wp_send_json_success(['tax_rate' => 10]);
    }

    public function get_products() {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => [['taxonomy'=>'product_type', 'field'=>'slug', 'terms'=>['simple','variable']]]
        ];

        $query = new WP_Query($args);
        $data = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post(); 
                global $product;
                $img_id = $product->get_image_id();
                $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : 'https://placehold.co/150x150/orange/white?text=' . substr($product->get_name(), 0, 1);

                $data[] = [
                    'id' => $product->get_id(),
                    'name' => $product->get_name(),
                    'price' => (float)$product->get_price(),
                    'image' => $img_url,
                    'category' => wc_get_product_category_list($product->get_id())
                ];
            }
        }
        wp_reset_postdata();
        wp_send_json_success($data);
    }

    // --- LOGIKA UTAMA: Integrasi Checkout WooCommerce ---
    public function process_order_checkout() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');

        $items = json_decode(stripslashes($_POST['items']), true);
        $table_no = isset($_POST['table_no']) ? sanitize_text_field($_POST['table_no']) : '';
        $dining_type = isset($_POST['dining_type']) ? sanitize_text_field($_POST['dining_type']) : 'dine_in';
        
        if (empty($items) || !is_array($items)) {
            wp_send_json_error(['message' => 'Keranjang kosong.'], 400);
            return;
        }

        try {
            // 1. Buat Order Baru
            $order = wc_create_order();
            
            // 2. Set Customer ke User yang sedang login (Kasir/Staff) agar bisa akses halaman pembayaran
            if (is_user_logged_in()) {
                $order->set_customer_id(get_current_user_id());
            }

            // 3. Masukkan Produk
            foreach ($items as $item) {
                $order->add_product(wc_get_product($item['id']), intval($item['qty']));
            }

            // 4. Set Metadata
            $order->update_meta_data('_kresuber_table_no', $table_no);
            $order->update_meta_data('_kresuber_dining_type', $dining_type);
            
            // 5. Kalkulasi & Simpan
            $order->calculate_totals();
            $order->update_status('pending', 'Order POS dibuat. Menunggu pembayaran di kasir.'); // Status Pending Payment
            $order->save();

            // 6. Ambil URL Pembayaran WooCommerce
            // URL ini mengarah ke halaman Checkout standar tapi khusus untuk membayar order ini
            $payment_url = $order->get_checkout_payment_url();

            wp_send_json_success([
                'message' => 'Redirecting to payment...',
                'order_id' => $order->get_id(),
                'payment_url' => $payment_url
            ]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // --- Riwayat Order ---
    public function get_orders_history() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        
        // Cek login
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Unauthorized']); return;
        }

        $orders = wc_get_orders([
            'limit' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
            // Kita ambil semua status order
            'status' => ['pending', 'processing', 'on-hold', 'completed'] 
        ]);

        $data = [];
        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->get_id(),
                'status' => $order->get_status(),
                'total' => $order->get_formatted_order_total(),
                'date' => wc_format_datetime($order->get_date_created()),
                'table' => $order->get_meta('_kresuber_table_no') ?: '-',
            ];
        }

        wp_send_json_success($data);
    }

    // --- Manajemen Meja ---
    public function manage_tables() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'get';
        $option_name = 'kresuber_restaurant_tables';
        
        $default_tables = [];
        for($i=1; $i<=6; $i++) { $default_tables[] = ['id' => $i, 'name' => 'Meja ' . $i]; }

        if ($mode === 'get') {
            $tables = get_option($option_name, $default_tables);
            // Pastikan format array benar (fix jika data corrupt)
            if(!is_array($tables)) $tables = $default_tables;
            wp_send_json_success($tables);
        } elseif ($mode === 'save') {
            $new_tables = json_decode(stripslashes($_POST['tables']), true);
            if(is_array($new_tables)) {
                update_option($option_name, $new_tables);
                wp_send_json_success(['message' => 'Data meja disimpan']);
            } else {
                wp_send_json_error(['message' => 'Format data salah']);
            }
        }
    }

    // Helper
    public function get_product_categories() {
        $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
        $data = [];
        if(!is_wp_error($cats)) {
            foreach ($cats as $c) { $data[] = ['id'=>$c->term_id, 'name'=>$c->name, 'slug'=>$c->slug]; }
        }
        wp_send_json_success($data);
    }
}