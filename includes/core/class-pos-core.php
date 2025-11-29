<?php
class Kresuber_POS_Core {
    public function __construct() {
        add_action('init', [$this, 'rewrites']);
        add_action('template_redirect', [$this, 'load_template']);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3);
        
        // INTERCEPT WOOCOMMERCE TEMPLATES (Cart, Checkout, Account)
        add_filter('woocommerce_locate_template', [$this, 'override_wc_templates'], 10, 3);

        new Kresuber_POS_Admin();
        new Kresuber_POS_Api();
    }

    public function rewrites() {
        // Endpoint khusus App
        add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
        add_rewrite_rule('^app/product/([^/]+)/?$', 'index.php?kresuber_endpoint=app_product&product_id=$matches[1]', 'top');
        add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
        
        add_rewrite_tag('%kresuber_endpoint%', '([^&]+)');
        add_rewrite_tag('%product_id%', '([^&]+)');
    }

    public function load_template() {
        $endpoint = get_query_var('kresuber_endpoint');
        if (!$endpoint) return;

        $templates = [
            'pos' => 'app-shell.php',
            'app' => 'user-app-shell.php',
            'app_product' => 'single-product-shell.php', // FILE BARU UNTUK DETAIL
        ];

        if (isset($templates[$endpoint])) {
            if ($endpoint === 'pos' && !current_user_can('edit_products')) {
                auth_redirect();
            }
            include KRESUBER_PATH . 'templates/' . $templates[$endpoint];
            exit;
        }
    }

    // Fungsi "Paksa" Template WooCommerce menggunakan file dari Plugin
    public function override_wc_templates($template, $template_name, $template_path) {
        // Cari file di folder templates/woocommerce/ plugin ini
        $plugin_path = KRESUBER_PATH . 'templates/woocommerce/';
        $file = $plugin_path . $template_name;

        if (file_exists($file)) {
            return $file;
        }
        return $template;
    }

    public function assets() {
        // Load assets di halaman plugin ATAU halaman WooCommerce standar
        $is_wc_page = function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url());
        $is_app_page = get_query_var('kresuber_endpoint');

        if (!$is_app_page && !$is_wc_page) return;

        // CSS
        wp_enqueue_style('k-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
        wp_enqueue_style('k-pos-style', KRESUBER_URL . 'assets/css/pos-app.css', [], time()); // Use time() for dev cache busting
        
        // JS
        wp_enqueue_script('k-pos-app', KRESUBER_URL . 'assets/js/pos-app.js', ['jquery'], time(), true);
        
        wp_localize_script('k-pos-app', 'KRESUBER', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(KRESUBER_NONCE),
            'site_url' => site_url(),
            'cart_url' => wc_get_cart_url(),
            'checkout_url' => wc_get_checkout_url()
        ]);
    }

    public function add_type_attribute($tag, $handle, $src) {
        if ('k-pos-app' !== $handle) return $tag;
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
}