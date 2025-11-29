<?php
class Kresuber_POS_Core {
    public function __construct() {
        add_action('init', [$this, 'rewrites']);
        add_filter('template_include', [$this, 'force_app_templates'], 999);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3);
        
        add_shortcode('kresuber_pos_terminal', [$this, 'render_pos_terminal']);
        add_shortcode('kresuber_pos_app', [$this, 'render_pos_app']);

        new Kresuber_POS_Admin();
        new Kresuber_POS_Api();
    }

    // --- HELPER BAHASA (INDONESIA DEFAULT) ---
    public static function get_lang() {
        return isset($_COOKIE['k_app_lang']) ? $_COOKIE['k_app_lang'] : 'id';
    }

    // Fungsi statis untuk dipanggil di template: Kresuber_POS_Core::_k('Indo', 'Eng')
    public static function _k($id_text, $en_text) {
        return self::get_lang() === 'en' ? $en_text : $id_text;
    }

    public function rewrites() {
        add_rewrite_rule('^app/cart/?$', 'index.php?kresuber_endpoint=app_cart', 'top');
        add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
        add_rewrite_rule('^app/product/([^/]+)/?$', 'index.php?kresuber_endpoint=app_product&product_id=$matches[1]', 'top');
        add_rewrite_rule('^app/favorites/?$', 'index.php?kresuber_endpoint=app_favorites', 'top');
        add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
        
        add_rewrite_tag('%kresuber_endpoint%', '([^&]+)');
        add_rewrite_tag('%product_id%', '([^&]+)');
    }

    public function force_app_templates($template) {
        $endpoint = get_query_var('kresuber_endpoint');
        
        if ($endpoint) {
            $files = [
                'pos'           => 'app-shell.php',
                'app'           => 'user-app-shell.php',
                'app_product'   => 'single-product-shell.php',
                'app_favorites' => 'favorites-shell.php',
                'app_cart'      => 'woocommerce/cart/cart.php'
            ];
            if (isset($files[$endpoint])) {
                if ($endpoint === 'pos' && !current_user_can('edit_products')) auth_redirect();
                return KRESUBER_PATH . 'templates/' . $files[$endpoint];
            }
        }

        if (function_exists('is_woocommerce')) {
            if (is_cart()) { wp_safe_redirect(home_url('/app/cart')); exit; }
            if (is_checkout()) {
                if(is_wc_endpoint_url('order-pay')) return KRESUBER_PATH . 'templates/woocommerce/checkout/form-pay.php';
                return KRESUBER_PATH . 'templates/woocommerce/checkout/form-checkout.php';
            }
            if (is_account_page()) return KRESUBER_PATH . 'templates/account-shell.php';
        }
        return $template;
    }

    public function assets() {
        $endpoint = get_query_var('kresuber_endpoint');
        $is_target = $endpoint || (function_exists('is_woocommerce') && (is_checkout() || is_account_page()));

        if (!$is_target) return;

        wp_enqueue_style('k-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
        wp_enqueue_style('k-pos-style', KRESUBER_URL . 'assets/css/pos-app.css', [], time()); 
        wp_enqueue_script('k-pos-app', KRESUBER_URL . 'assets/js/pos-app.js', ['jquery'], time(), true);
        
        wp_localize_script('k-pos-app', 'KRESUBER', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(KRESUBER_NONCE),
            'site_url' => site_url(),
            'current_lang' => self::get_lang() // Kirim status bahasa ke JS
        ]);
    }

    public function add_type_attribute($tag, $handle, $src) {
        if ('k-pos-app' !== $handle) return $tag;
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }

    public function render_pos_terminal() { return ''; }
    public function render_pos_app() { return ''; }
}