<?php
class Kresuber_POS_Core {
    public function __construct() {
        add_action('init', [$this, 'rewrites']);
        add_action('template_redirect', [$this, 'load_template']);
        add_action('wp_enqueue_scripts', [$this, 'assets']);
        add_filter('script_loader_tag', [$this, 'add_type_attribute'], 10, 3); // FIX JS MODULE
        
        new Kresuber_POS_Admin();
        new Kresuber_POS_Api();
    }

    public function rewrites() {
        add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
        add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
        add_rewrite_tag('%kresuber_endpoint%', '([^&]+)');
    }

    public function load_template() {
        $endpoint = get_query_var('kresuber_endpoint');
        if (!$endpoint) return;

        if ($endpoint === 'pos') {
            if (!current_user_can('edit_products')) auth_redirect();
            include KRESUBER_PATH . 'templates/app-shell.php';
            exit;
        }
        
        if ($endpoint === 'app') {
            include KRESUBER_PATH . 'templates/user-app-shell.php';
            exit;
        }
    }

    public function assets() {
        if (!get_query_var('kresuber_endpoint')) return;

        // CSS
        wp_enqueue_style('k-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
        wp_enqueue_style('k-pos-style', KRESUBER_URL . 'assets/css/pos-app.css', [], KRESUBER_VERSION);
        
        // JS - Load as Module
        wp_enqueue_script('k-pos-app', KRESUBER_URL . 'assets/js/pos-app.js', ['jquery'], KRESUBER_VERSION, true);
        
        // GLOBAL VARIABLE FIX
        wp_localize_script('k-pos-app', 'KRESUBER', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(KRESUBER_NONCE),
            'site_url' => site_url()
        ]);
    }

    // Fix for <script type="module">
    public function add_type_attribute($tag, $handle, $src) {
        if ('k-pos-app' !== $handle) return $tag;
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
}