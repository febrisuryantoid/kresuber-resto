<?php
class Kresuber_POS_Core {
    public function __construct() {
        add_action('init', [$this, 'rewrites']);
        add_action('template_redirect', [$this, 'load_template']);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3);
        
        // Shortcodes
        add_shortcode('kresuber_pos_terminal', [$this, 'render_pos_terminal']);
        add_shortcode('kresuber_pos_app', [$this, 'render_pos_app']);

        // --- FIX: MEMAKSA WOOCOMMERCE MENGGUNAKAN TEMPLATE DARI PLUGIN ---
        add_filter('woocommerce_locate_template', [$this, 'override_wc_templates'], 10, 3);

        new Kresuber_POS_Admin();
        new Kresuber_POS_Api();
    }

    // Fungsi Utama untuk Override Template WooCommerce
    public function override_wc_templates($template, $template_name, $template_path) {
        // Path ke folder template di dalam plugin ini
        $plugin_path = KRESUBER_PATH . 'templates/woocommerce/';
        
        // Cek apakah file template tersebut ada di folder plugin kita
        // Contoh: mengecek templates/woocommerce/checkout/form-pay.php
        $file = $plugin_path . $template_name;

        if (file_exists($file)) {
            return $file; // Gunakan file dari plugin
        }

        return $template; // Gunakan default (dari tema) jika tidak ada di plugin
    }

    public function rewrites() {
        add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
        add_rewrite_rule('^app/product/([^/]+)/?$', 'index.php?kresuber_endpoint=app_product&product_id=$matches[1]', 'top');
        add_rewrite_rule('^app/favorites/?$', 'index.php?kresuber_endpoint=app_favorites', 'top');
        add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
        add_rewrite_tag('%kresuber_endpoint%', '([^&]+)');
        add_rewrite_tag('%product_id%', '([^&]+)');
    }

    public function load_template() {
        global $post;
        if (is_object($post) && (has_shortcode($post->post_content, 'kresuber_pos_terminal') || has_shortcode($post->post_content, 'kresuber_pos_app'))) {
            return; 
        }

        $endpoint = get_query_var('kresuber_endpoint');
        if (!$endpoint) return;

        $templates = [
            'pos' => 'app-shell.php',
            'app' => 'user-app-shell.php',
            'app_favorites' => 'favorites-shell.php',
            'app_product' => 'single-product-shell.php',
        ];

        if (isset($templates[$endpoint])) {
            if ($endpoint === 'pos' && !current_user_can('edit_products')) {
                auth_redirect();
            }
            include KRESUBER_PATH . 'templates/' . $templates[$endpoint];
            exit;
        }
    }

    public function assets() {
        global $post;
        // Logic load assets lebih longgar agar CSS juga termuat di halaman Checkout WooCommerce
        $is_wc_page = function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url('order-pay'));
        $has_shortcode = is_object($post) && (has_shortcode($post->post_content, 'kresuber_pos_terminal') || has_shortcode($post->post_content, 'kresuber_pos_app'));
        $is_app_page = in_array(get_query_var('kresuber_endpoint'), ['app', 'app_favorites', 'app_orders', 'app_account']);

        if (!get_query_var('kresuber_endpoint') && !$is_wc_page && !$has_shortcode && !$is_app_page) return;

        // CSS
        wp_enqueue_style('k-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
        wp_enqueue_style('k-pos-style', KRESUBER_URL . 'assets/css/pos-app.css', [], KRESUBER_VERSION);
        
        // JS - Load as Module
        wp_enqueue_script('k-pos-app', KRESUBER_URL . 'assets/js/pos-app.js', ['jquery'], KRESUBER_VERSION, true);
        
        wp_localize_script('k-pos-app', 'KRESUBER', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(KRESUBER_NONCE),
            'site_url' => site_url()
        ]);
    }

    public function add_type_attribute($tag, $handle, $src) {
        if ('k-pos-app' !== $handle) return $tag;
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    public function render_pos_terminal() {
        if (!current_user_can('edit_products')) return '<p>You do not have permission to view this content.</p>';
        ob_start();
        include KRESUBER_PATH . 'templates/app-shell.php';
        return ob_get_clean();
    }

    public function render_pos_app() {
        ob_start();
        include KRESUBER_PATH . 'templates/user-app-shell.php';
        return ob_get_clean();
    }
}