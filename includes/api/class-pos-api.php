<?php
class Kresuber_POS_Api {
    public function __construct() {
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']); 
        
        // Fix & New Endpoints
        add_action('wp_ajax_kresuber_process_order', [$this, 'process_order']); // Endpoint baru untuk checkout
        add_action('wp_ajax_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_nopriv_kresuber_get_product_categories', [$this, 'get_product_categories']);
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

    // FUNGSI BARU: Memproses Pesanan menjadi WooCommerce Order
    public function process_order() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');

        // 1. Validasi Input
        $items_raw = isset($_POST['items']) ? $_POST['items'] : '[]';
        $items = json_decode(stripslashes($items_raw), true);
        $table_no = isset($_POST['table_no']) ? sanitize_text_field($_POST['table_no']) : '';
        $dining_type = isset($_POST['dining_type']) ? sanitize_text_field($_POST['dining_type']) : 'dine_in';
        $payment_method = 'cod'; // Default Cash/COD untuk POS

        if (empty($items) || !is_array($items)) {
            wp_send_json_error(['message' => 'Keranjang kosong.'], 400);
            return;
        }

        if (!class_exists('WooCommerce')) {
            wp_send_json_error(['message' => 'WooCommerce error.'], 500);
            return;
        }

        try {
            // 2. Buat Order Baru
            $order = wc_create_order();

            // 3. Masukkan Produk ke Order
            foreach ($items as $item) {
                $product_id = intval($item['id']);
                $qty = intval($item['qty']);
                
                if ($qty > 0) {
                    $order->add_product(wc_get_product($product_id), $qty);
                }
            }

            // 4. Set Metadata (Meja & Tipe Makan)
            $order->update_meta_data('_kresuber_table_no', $table_no);
            $order->update_meta_data('_kresuber_dining_type', $dining_type);
            $order->set_payment_method($payment_method);
            $order->set_payment_method_title('POS / Kasir');

            // 5. Kalkulasi Total & Simpan
            $order->calculate_totals();
            
            // Set status langsung 'completed' (Bayar di kasir dianggap lunas)
            // Atau ubah ke 'processing' jika ingin konfirmasi manual
            $order->update_status('completed', 'Order dibuat via POS Terminal. Meja: ' . $table_no);

            wp_send_json_success([
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->get_id(),
                'total' => $order->get_formatted_order_total()
            ]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Gagal membuat order: ' . $e->getMessage()]);
        }
    }

    public function get_product_categories() {
        $product_categories = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'fields'     => 'all',
        ]);

        $categories_data = [];
        if(!empty($product_categories) && !is_wp_error($product_categories)){
            foreach ($product_categories as $category) {
                $categories_data[] = [
                    'id'   => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }
        }

        wp_send_json_success($categories_data);
    }
}