<?php
class Kresuber_POS_Api {
    public function __construct() {
        add_action('wp_ajax_kresuber_get_init_data', [$this, 'get_init']);
        add_action('wp_ajax_kresuber_get_products', [$this, 'get_products']);
        add_action('wp_ajax_nopriv_kresuber_get_products', [$this, 'get_products']); // Allow App User to see products
        add_action('wp_ajax_kresuber_sync_cart', [$this, 'sync_cart']);
        add_action('wp_ajax_nopriv_kresuber_sync_cart', [$this, 'sync_cart']);
    }

    public function get_init() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');
        wp_send_json_success(['tax_rate' => 10]);
    }

    public function get_products() {
        // Relaxed nonce check for public App, Strict for POS logic if needed
        // check_ajax_referer(KRESUBER_NONCE, 'nonce'); 

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
                
                // Fallback image if empty
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

    public function sync_cart() {
        check_ajax_referer(KRESUBER_NONCE, 'nonce');

        if (!isset($_POST['items']) || !is_array($_POST['items'])) {
            wp_send_json_error('Invalid cart data.', 400);
        }

        if (!function_exists('WC')) {
            wp_send_json_error('WooCommerce is not active.', 500);
            return;
        }

        $items = json_decode(stripslashes($_POST['items']), true);
        
        WC()->cart->empty_cart();

        foreach ($items as $item) {
            $product_id = intval($item['id']);
            $quantity = intval($item['qty']);
            WC()->cart->add_to_cart($product_id, $quantity);
        }

        wp_send_json_success(['message' => 'Cart synced successfully.']);
    }
}