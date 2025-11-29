<?php
/*
Plugin Name: Kresuber Resto
Plugin URI: https://resto.kresuber.co.id/
Description: Shortcodes for POS/App, Demo Product Importer, Responsive Product Grid, Product Detail Page, Dynamic Bottom Navbar, WooCommerce Account Integration. FIXES: Improved Product Import & UI Customizer.
Version: 1.1.8
Author: Kresuber Digital
Author URI: https://resto.kresuber.co.id/
Text Domain: kresuber-resto
*/
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'KRESUBER_VERSION', '1.1.8' );
define( 'KRESUBER_PATH', plugin_dir_path( __FILE__ ) );
define( 'KRESUBER_URL', plugin_dir_url( __FILE__ ) );
define( 'KRESUBER_NONCE', 'kresuber_pos_secure' );

// Autoloader
spl_autoload_register(function ($class) {
    if (strpos($class, 'Kresuber_POS_') !== false) {
        $parts = explode('_', strtolower(str_replace('Kresuber_POS_', '', $class)));
        $filename = 'class-pos-' . $parts[0] . '.php';
        $paths = [
            KRESUBER_PATH . 'includes/core/' . $filename,
            KRESUBER_PATH . 'includes/api/' . $filename,
            KRESUBER_PATH . 'includes/admin/' . $filename,
            KRESUBER_PATH . 'includes/utils/' . $filename
        ];
        foreach($paths as $path) { if (file_exists($path)) { require_once $path; return; } }
    }
});

// Activation Hook (Crucial for /app url)
register_activation_hook(__FILE__, function() {
    add_rewrite_rule('^pos-terminal/?$', 'index.php?kresuber_endpoint=pos', 'top');
    add_rewrite_rule('^app/?$', 'index.php?kresuber_endpoint=app', 'top');
    flush_rewrite_rules();
});

// LOAD CUSTOMIZER (Fitur Pengaturan Tampilan)
// Memastikan file class customizer dimuat agar menu "Kresuber POS App" muncul di Appearance > Customize
if ( file_exists( KRESUBER_PATH . 'includes/admin/class-pos-customizer.php' ) ) {
    require_once KRESUBER_PATH . 'includes/admin/class-pos-customizer.php';
}

add_action('plugins_loaded', function() { new Kresuber_POS_Core(); });