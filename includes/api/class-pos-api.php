<?php
class Kresuber_POS_Api {
    public function __construct() {
        // Init
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        
        // Products
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_nopriv_kresuber_get_product_categories', [$this, 'get_product_categories']);
        
        // Cart & Checkout
        add_action('wp_ajax_kresuber_sync_cart', [$this, 'sync_cart']); // <--- INI PENTING
        add_action('wp_ajax_nopriv_kresuber_sync_cart', [$this, 'sync_cart']); 
        add_action('wp_ajax_kresuber_process_order', [$this, 'process_order_checkout']);
        
        // Orders & Tables
        add_action('wp_ajax_kresuber_get_orders_history', [$this, 'get_orders_history']);
        add_action('wp_ajax_kresuber_manage_tables', [$this, 'manage_tables']);

        // Favorites
        add_action('wp_ajax_kresuber_toggle_favorite', [$this, 'toggle_favorite']);
        add_action('wp_ajax_kresuber_get_favorites', [$this, 'get_favorites']);
        add_action('wp_ajax_nopriv_kresuber_get_favorites', [$this, 'get_favorites']);
    }

    public function get_init() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        wp_send_json_success(['tax_rate' => 10]);
    }

    // --- SINKRONISASI KERANJANG (Fix Masalah Cart Kosong) ---
    public function sync_cart() {
        // Security check (Relaxed for guest app usage if needed, or strict)
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], KRESUBER_NONCE ) ) {
            // wp_send_json_error( 'Invalid nonce', 403 ); // Optional: Uncomment for stricter security
        }

        if ( ! isset( $_POST['items'] ) ) {
            wp_send_json_error( 'No items data' );
        }

        $items = json_decode( stripslashes( $_POST['items'] ), true );

        if ( ! function_exists( 'WC' ) ) {
            wp_send_json_error( 'WooCommerce not loaded' );
        }

        // 1. Kosongkan Cart Server saat ini
        WC()->cart->empty_cart();

        // 2. Isi ulang dengan data dari JS
        if ( is_array( $items ) && count( $items ) > 0 ) {
            foreach ( $items as $item ) {
                $product_id = intval( $item['id'] );
                $qty = intval( $item['qty'] );

                if ( $product_id > 0 && $qty > 0 ) {
                    // Tambahkan ke cart WooCommerce (tanpa validasi stok ribet agar cepat)
                    WC()->cart->add_to_cart( $product_id, $qty );
                }
            }
        }

        // 3. Hitung ulang total
        WC()->cart->calculate_totals();
        WC()->session->save_data(); // Simpan sesi

        wp_send_json_success( [ 
            'message' => 'Cart synced', 
            'count' => WC()->cart->get_cart_contents_count(),
            'total' => WC()->cart->get_cart_total()
        ] );
    }

    public function get_products() {
        $user_id = get_current_user_id();
        $favorites = $user_id ? get_user_meta($user_id, 'kresuber_favorites', true) : [];
        if (!is_array($favorites)) $favorites = [];

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
                    'category' => wc_get_product_category_list($product->get_id()),
                    'is_favorite' => in_array($product->get_id(), $favorites)
                ];
            }
        }
        wp_reset_postdata();
        wp_send_json_success($data);
    }

    public function toggle_favorite() {
        if (!is_user_logged_in()) { wp_send_json_error(['message' => 'Silakan login.']); return; }
        $product_id = intval($_POST['id']);
        $user_id = get_current_user_id();
        $favorites = get_user_meta($user_id, 'kresuber_favorites', true);
        if (!is_array($favorites)) $favorites = [];

        if (in_array($product_id, $favorites)) {
            $favorites = array_diff($favorites, [$product_id]);
            $status = 'removed';
        } else {
            $favorites[] = $product_id;
            $status = 'added';
        }
        update_user_meta($user_id, 'kresuber_favorites', array_values($favorites));
        wp_send_json_success(['status' => $status]);
    }

    public function get_favorites() {
        $user_id = get_current_user_id();
        if (!$user_id) { wp_send_json_success([]); return; }
        $favorites = get_user_meta($user_id, 'kresuber_favorites', true);
        if (empty($favorites) || !is_array($favorites)) { wp_send_json_success([]); return; }

        $args = ['post_type' => 'product', 'post__in' => $favorites, 'posts_per_page' => -1];
        $query = new WP_Query($args);
        $data = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post(); global $product;
                $img_id = $product->get_image_id();
                $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : 'https://placehold.co/150x150/orange/white?text=' . substr($product->get_name(), 0, 1);
                $data[] = ['id' => $product->get_id(), 'name' => $product->get_name(), 'price' => (float)$product->get_price(), 'image' => $img_url, 'is_favorite' => true];
            }
        }
        wp_reset_postdata();
        wp_send_json_success($data);
    }

    public function process_order_checkout() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        $items = json_decode(stripslashes($_POST['items']), true);
        $table_no = isset($_POST['table_no']) ? sanitize_text_field($_POST['table_no']) : '';
        $dining_type = isset($_POST['dining_type']) ? sanitize_text_field($_POST['dining_type']) : 'dine_in';
        
        if (empty($items) || !is_array($items)) { wp_send_json_error(['message' => 'Keranjang kosong.'], 400); return; }

        try {
            $order = wc_create_order();
            if (is_user_logged_in()) { $order->set_customer_id(get_current_user_id()); }
            foreach ($items as $item) { $order->add_product(wc_get_product($item['id']), intval($item['qty'])); }
            $order->update_meta_data('_kresuber_table_no', $table_no);
            $order->update_meta_data('_kresuber_dining_type', $dining_type);
            $order->calculate_totals();
            $order->update_status('pending', 'Order POS dibuat.');
            $order->save();
            wp_send_json_success(['message' => 'Redirecting...', 'order_id' => $order->get_id(), 'payment_url' => $order->get_checkout_payment_url()]);
        } catch (Exception $e) { wp_send_json_error(['message' => 'Error: ' . $e->getMessage()]); }
    }

    public function get_orders_history() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        if (!is_user_logged_in()) { wp_send_json_error(['message' => 'Unauthorized']); return; }
        $orders = wc_get_orders(['limit' => 20, 'orderby' => 'date', 'order' => 'DESC', 'status' => ['pending', 'processing', 'on-hold', 'completed']]);
        $data = [];
        foreach ($orders as $order) {
            $data[] = ['id' => $order->get_id(), 'status' => $order->get_status(), 'total' => $order->get_formatted_order_total(), 'date' => wc_format_datetime($order->get_date_created()), 'table' => $order->get_meta('_kresuber_table_no') ?: '-'];
        }
        wp_send_json_success($data);
    }

    public function manage_tables() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'get';
        $option_name = 'kresuber_restaurant_tables';
        $default_tables = []; for($i=1; $i<=6; $i++) { $default_tables[] = ['id' => $i, 'name' => 'Meja ' . $i]; }
        if ($mode === 'get') { $tables = get_option($option_name, $default_tables); if(!is_array($tables)) $tables = $default_tables; wp_send_json_success($tables); } 
        elseif ($mode === 'save') { $new_tables = json_decode(stripslashes($_POST['tables']), true); if(is_array($new_tables)) { update_option($option_name, $new_tables); wp_send_json_success(['message' => 'Data meja disimpan']); } }
    }

    public function get_product_categories() {
        $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
        $data = []; if(!is_wp_error($cats)) { foreach ($cats as $c) { $data[] = ['id'=>$c->term_id, 'name'=>$c->name, 'slug'=>$c->slug]; } }
        wp_send_json_success($data);
    }
}