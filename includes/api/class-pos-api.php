<?php
class Kresuber_POS_Api {
    public function __construct() {
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']); 
        add_action('wp_ajax_kresuber_process_order', [$this, 'process_order']); 
        add_action('wp_ajax_kresuber_get_product_categories', [$this, 'get_product_categories']);
        add_action('wp_ajax_nopriv_kresuber_get_product_categories', [$this, 'get_product_categories']);
        
        // NEW: Hook untuk mengambil riwayat pesanan
        add_action('wp_ajax_kresuber_get_orders_history', [$this, 'get_orders_history']);
    }

    // ... (Fungsi get_init, get_products, process_order, get_product_categories SAMA SEPERTI SEBELUMNYA - Biarkan tetap ada) ...
    // Pastikan Anda tidak menghapus fungsi process_order yang kita buat di langkah sebelumnya!

    public function get_init() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        wp_send_json_success(['tax_rate' => 10]);
    }

    public function get_products() {
        // ... (Biarkan kode get_products yang lama) ...
        // Agar hemat tempat, saya tidak tulis ulang, pastikan tetap ada.
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
                $query->the_post(); global $product;
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

    public function process_order() {
        // ... (Gunakan kode process_order dari jawaban sebelumnya) ...
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        $items = json_decode(stripslashes($_POST['items']), true);
        $table_no = sanitize_text_field($_POST['table_no']);
        
        if (empty($items)) { wp_send_json_error(['message' => 'Cart empty'], 400); return; }
        
        $order = wc_create_order();
        foreach ($items as $item) {
            $order->add_product(wc_get_product($item['id']), $item['qty']);
        }
        $order->update_meta_data('_kresuber_table_no', $table_no);
        $order->set_payment_method('cod');
        $order->calculate_totals();
        $order->update_status('completed', 'POS Order Table: ' . $table_no);
        
        wp_send_json_success(['message' => 'Success', 'order_id' => $order->get_id(), 'total' => $order->get_formatted_order_total()]);
    }

    public function get_product_categories() {
        // ... (Biarkan kode lama) ...
        $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
        $data = [];
        foreach ($cats as $c) { $data[] = ['id'=>$c->term_id, 'name'=>$c->name, 'slug'=>$c->slug]; }
        wp_send_json_success($data);
    }

    // FUNGSI BARU: Mengambil Riwayat Order Terakhir
    public function get_orders_history() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');

        // Ambil 10 order terakhir
        $orders = wc_get_orders([
            'limit' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $data = [];
        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->get_id(),
                'status' => $order->get_status(),
                'total' => $order->get_formatted_order_total(),
                'date' => wc_format_datetime($order->get_date_created()),
                'table' => $order->get_meta('_kresuber_table_no') ?: '-',
                'items_count' => $order->get_item_count()
            ];
        }

        wp_send_json_success($data);
    }
}