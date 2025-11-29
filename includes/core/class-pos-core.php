<?php
class Kresuber_POS_Core {
    public function __construct() {
        add_action('init', [$this, 'rewrites']);
        // Ganti template_redirect dengan template_include (Lebih kuat untuk override tema)
        add_filter('template_include', [$this, 'force_app_templates'], 99);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3);
        
        // Shortcodes
        add_shortcode('kresuber_pos_terminal', [$this, 'render_pos_terminal']);
        add_shortcode('kresuber_pos_app', [$this, 'render_pos_app']);

        new Kresuber_POS_Admin();
        new Kresuber_POS_Api();
    }

    public function rewrites() {
        add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
        add_rewrite_rule('^app/product/([^/]+)/?$', 'index.php?kresuber_endpoint=app_product&product_id=$matches[1]', 'top');
        add_rewrite_rule('^app/favorites/?$', 'index.php?kresuber_endpoint=app_favorites', 'top');
        add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
        
        add_rewrite_tag('%kresuber_endpoint%', '([^&]+)');
        add_rewrite_tag('%product_id%', '([^&]+)');
    }

    // FUNGSI UTAMA: Mencegat Tema WordPress
    public function force_app_templates($template) {
        // 1. Cek Halaman Custom Plugin (/app, /pos-terminal)
        $endpoint = get_query_var('kresuber_endpoint');
        
        if ($endpoint) {
            $files = [
                'pos' => 'app-shell.php',
                'app' => 'user-app-shell.php',
                'app_product' => 'single-product-shell.php',
                'app_favorites' => 'favorites-shell.php'
            ];

            if (isset($files[$endpoint])) {
                if ($endpoint === 'pos' && !current_user_can('edit_products')) {
                    auth_redirect();
                }
                // Return file path langsung (Bypass Theme)
                return KRESUBER_PATH . 'templates/' . $files[$endpoint];
            }
        }

        // 2. Cek Halaman WooCommerce Standar (Cart, Checkout, Account)
        // Kita paksa halaman ini menggunakan template "Shell" dari plugin kita
        if (function_exists('is_woocommerce')) {
            if (is_cart()) {
                return KRESUBER_PATH . 'templates/woocommerce/cart/cart.php';
            }
            if (is_checkout()) {
                // Bedakan antara Form Checkout input data dan Order Pay (Pembayaran)
                if(is_wc_endpoint_url('order-pay')) {
                    return KRESUBER_PATH . 'templates/woocommerce/checkout/form-pay.php';
                }
                return KRESUBER_PATH . 'templates/woocommerce/checkout/form-checkout.php';
            }
            if (is_account_page()) {
                // Gunakan wrapper khusus untuk akun agar full width
                return KRESUBER_PATH . 'templates/account-shell.php';
            }
        }

        return $template; // Kembalikan ke tema jika bukan halaman App
    }

    public function assets() {
        // Load assets HANYA di halaman App/WooCommerce yang relevan
        $is_target_page = get_query_var('kresuber_endpoint') || (function_exists('is_woocommerce') && (is_cart() || is_checkout() || is_account_page()));

        if (!$is_target_page) return;

        // CSS Global
        wp_enqueue_style('k-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
        wp_enqueue_style('k-pos-style', KRESUBER_URL . 'assets/css/pos-app.css', [], time()); 
        
        // JS Global
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

    // Shortcode Fallback (Jika user ingin manual insert)
    public function render_pos_terminal() { return 'Silakan akses /pos-terminal'; }
    public function render_pos_app() { return 'Silakan akses /app'; }
}