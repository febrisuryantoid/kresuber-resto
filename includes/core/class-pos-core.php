<?php
class Kresuber_POS_Core {
    public function __construct() {
        add_action('init', [$this, 'rewrites']);
        add_filter('template_include', [$this, 'force_app_templates'], 999); // Prioritas tertinggi
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3);
        
        // Shortcodes
        add_shortcode('kresuber_pos_terminal', [$this, 'render_pos_terminal']);
        add_shortcode('kresuber_pos_app', [$this, 'render_pos_app']);

        new Kresuber_POS_Admin();
        new Kresuber_POS_Api();
    }

    public function rewrites() {
        // Tambahkan Endpoint Cart Baru
        add_rewrite_rule('^app/cart/?$', 'index.php?kresuber_endpoint=app_cart', 'top');
        
        add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
        add_rewrite_rule('^app/product/([^/]+)/?$', 'index.php?kresuber_endpoint=app_product&product_id=$matches[1]', 'top');
        add_rewrite_rule('^app/favorites/?$', 'index.php?kresuber_endpoint=app_favorites', 'top');
        add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
        
        add_rewrite_tag('%kresuber_endpoint%', '([^&]+)');
        add_rewrite_tag('%product_id%', '([^&]+)');
    }

    // FUNGSI UTAMA: Mencegat Tema WordPress
    public function force_app_templates($template) {
        $endpoint = get_query_var('kresuber_endpoint');
        
        // 1. Custom Endpoints Plugin
        if ($endpoint) {
            $files = [
                'pos'           => 'app-shell.php',
                'app'           => 'user-app-shell.php',
                'app_product'   => 'single-product-shell.php',
                'app_favorites' => 'favorites-shell.php',
                'app_cart'      => 'woocommerce/cart/cart.php' // Arahkan /app/cart ke sini
            ];

            if (isset($files[$endpoint])) {
                if ($endpoint === 'pos' && !current_user_can('edit_products')) {
                    auth_redirect();
                }
                // Paksa load file dari plugin
                return KRESUBER_PATH . 'templates/' . $files[$endpoint];
            }
        }

        // 2. Override Halaman WooCommerce Standar
        if (function_exists('is_woocommerce')) {
            // Redirect /cart/ asli ke /app/cart (Opsional, agar konsisten)
            if (is_cart()) {
                wp_safe_redirect(home_url('/app/cart'));
                exit;
            }
            
            if (is_checkout()) {
                if(is_wc_endpoint_url('order-pay')) {
                    return KRESUBER_PATH . 'templates/woocommerce/checkout/form-pay.php';
                }
                return KRESUBER_PATH . 'templates/woocommerce/checkout/form-checkout.php';
            }
            
            if (is_account_page()) {
                return KRESUBER_PATH . 'templates/account-shell.php';
            }
        }

        return $template;
    }

    public function assets() {
        // Load assets di halaman yang relevan
        $endpoint = get_query_var('kresuber_endpoint');
        $is_target_page = $endpoint || (function_exists('is_woocommerce') && (is_checkout() || is_account_page()));

        if (!$is_target_page) return;

        wp_enqueue_style('k-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
        
        // Gunakan time() agar cache CSS selalu fresh saat development
        wp_enqueue_style('k-pos-style', KRESUBER_URL . 'assets/css/pos-app.css', [], time()); 
        
        wp_enqueue_script('k-pos-app', KRESUBER_URL . 'assets/js/pos-app.js', ['jquery'], time(), true);
        
        wp_localize_script('k-pos-app', 'KRESUBER', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(KRESUBER_NONCE),
            'site_url' => site_url(),
            'cart_url' => home_url('/app/cart'), // Update URL Cart di JS
            'checkout_url' => wc_get_checkout_url()
        ]);
    }

    public function add_type_attribute($tag, $handle, $src) {
        if ('k-pos-app' !== $handle) return $tag;
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    public function render_pos_terminal() { return 'Silakan akses /pos-terminal'; }
    public function render_pos_app() { return 'Silakan akses /app'; }
}