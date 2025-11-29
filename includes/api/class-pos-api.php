<?php
class Kresuber_POS_Api {
    public function __construct() {
        // Init Data
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        
        // Produk & Kategori
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_nopriv_kresuber_get_product_categories', [$this, 'get_product_categories']);
        
        // Checkout & Order Management
        add_action('wp_ajax_kresuber_process_order', [$this, 'process_order_checkout']);
        add_action('wp_ajax_kresuber_get_orders_history', [$this, 'get_orders_history']);
        
        // Table Management
        add_action('wp_ajax_kresuber_manage_tables', [$this, 'manage_tables']);

        // Favorites Feature
        add_action('wp_ajax_kresuber_toggle_favorite', [$this, 'toggle_favorite']);
        add_action('wp_ajax_kresuber_get_favorites', [$this, 'get_favorites']);
        add_action('wp_ajax_nopriv_kresuber_get_favorites', [$this, 'get_favorites']); // Allow guest (opsional, usually logged in)
    }

    public function get_init() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        wp_send_json_success(['tax_rate' => 10]);
    }

    public function get_products() {
        // Ambil daftar ID favorit user
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
                    'is_favorite' => in_array($product->get_id(), $favorites) // Tandai jika favorit
                ];
            }
        }
        wp_reset_postdata();
        wp_send_json_success($data);
    }

    // Toggle Favorit: Simpan atau Hapus
    public function toggle_favorite() {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Silakan login untuk menyimpan favorit.']);
            return;
        }
        
        $product_id = intval($_POST['id']);
        $user_id = get_current_user_id();
        $favorites = get_user_meta($user_id, 'kresuber_favorites', true);
        if (!is_array($favorites)) $favorites = [];

        if (in_array($product_id, $favorites)) {
            // Hapus
            $favorites = array_diff($favorites, [$product_id]);
            $status = 'removed';
        } else {
            // Tambah
            $favorites[] = $product_id;
            $status = 'added';
        }

        update_user_meta($user_id, 'kresuber_favorites', array_values($favorites));
        wp_send_json_success(['status' => $status]);
    }

    // Ambil Data Favorit
    public function get_favorites() {
        $user_id = get_current_user_id();
        if (!$user_id) { wp_send_json_success([]); return; }

        $favorites = get_user_meta($user_id, 'kresuber_favorites', true);
        if (empty($favorites) || !is_array($favorites)) {
            wp_send_json_success([]); 
            return;
        }

        $args = [
            'post_type' => 'product',
            'post__in' => $favorites,
            'posts_per_page' => -1
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
                    'is_favorite' => true
                ];
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
        
        if (empty($items) || !is_array($items)) {
            wp_send_json_error(['message' => 'Keranjang kosong.'], 400);
            return;
        }

        try {
            $order = wc_create_order();
            if (is_user_logged_in()) {
                $order->set_customer_id(get_current_user_id());
            }

            foreach ($items as $item) {
                $order->add_product(wc_get_product($item['id']), intval($item['qty']));
            }

            $order->update_meta_data('_kresuber_table_no', $table_no);
            $order->update_meta_data('_kresuber_dining_type', $dining_type);
            
            $order->calculate_totals();
            $order->update_status('pending', 'Order POS dibuat. Menunggu pembayaran.');
            $order->save();

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

    public function get_orders_history() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Unauthorized']); return;
        }

        $orders = wc_get_orders([
            'limit' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
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

    public function manage_tables() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'get';
        $option_name = 'kresuber_restaurant_tables';
        
        $default_tables = [];
        for($i=1; $i<=6; $i++) { $default_tables[] = ['id' => $i, 'name' => 'Meja ' . $i]; }

        if ($mode === 'get') {
            $tables = get_option($option_name, $default_tables);
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

    public function get_product_categories() {
        $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
        $data = [];
        if(!is_wp_error($cats)) {
            foreach ($cats as $c) { $data[] = ['id'=>$c->term_id, 'name'=>$c->name, 'slug'=>$c->slug]; }
        }
        wp_send_json_success($data);
    }
}