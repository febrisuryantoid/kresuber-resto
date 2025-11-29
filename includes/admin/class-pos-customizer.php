<?php
/**
 * Class Kresuber_POS_Customizer
 * Menangani pengaturan tampilan via WordPress Customizer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kresuber_POS_Customizer {

    public function __construct() {
        add_action( 'customize_register', [ $this, 'register_settings' ] );
        add_action( 'wp_head', [ $this, 'output_custom_css' ], 100 ); // Prioritas tinggi agar menimpa CSS file
    }

    /**
     * 1. Mendaftarkan Panel dan Control
     */
    public function register_settings( $wp_customize ) {

        // A. PANEL UTAMA
        $wp_customize->add_panel( 'kresuber_pos_panel', [
            'title'       => __( '🎨 Kresuber POS App', 'kresuber-resto' ),
            'description' => 'Ubah tampilan Aplikasi POS Anda di sini.',
            'priority'    => 10, // Tampil di paling atas
        ] );

        // =====================================================================
        // SECTION 1: WARNA (COLORS)
        // =====================================================================
        $wp_customize->add_section( 'kresuber_colors_section', [
            'title' => 'Warna Aplikasi',
            'panel' => 'kresuber_pos_panel',
        ] );

        // Setting: Primary Color
        $wp_customize->add_setting( 'k_primary_color', [ 'default' => '#FF6B00', 'transport' => 'refresh' ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'k_primary_color', [
            'label'    => 'Warna Utama (Primary)',
            'section'  => 'kresuber_colors_section',
        ] ) );

        // Setting: Background App
        $wp_customize->add_setting( 'k_bg_color', [ 'default' => '#F8F9FD', 'transport' => 'refresh' ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'k_bg_color', [
            'label'    => 'Background Aplikasi',
            'section'  => 'kresuber_colors_section',
        ] ) );

        // Setting: Surface (Card) Color
        $wp_customize->add_setting( 'k_surface_color', [ 'default' => '#FFFFFF', 'transport' => 'refresh' ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'k_surface_color', [
            'label'    => 'Warna Kartu (Surface)',
            'section'  => 'kresuber_colors_section',
        ] ) );

        // =====================================================================
        // SECTION 2: LAYOUT & KOMPONEN
        // =====================================================================
        $wp_customize->add_section( 'kresuber_layout_section', [
            'title' => 'Layout & Komponen',
            'panel' => 'kresuber_pos_panel',
        ] );

        // Setting: Border Radius
        $wp_customize->add_setting( 'k_radius_md', [ 'default' => '12', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_radius_md', [
            'label'       => 'Kelengkungan Sudut (px)',
            'section'     => 'kresuber_layout_section',
            'type'        => 'number',
            'input_attrs' => [ 'min' => 0, 'max' => 30, 'step' => 1 ],
        ] );

        // Setting: Max Width Desktop
        $wp_customize->add_setting( 'k_desktop_width', [ 'default' => '600', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_desktop_width', [
            'label'       => 'Lebar Maksimal Desktop (px)',
            'description' => 'Biarkan 100% atau set pixel (cth: 600) untuk tampilan terpusat.',
            'section'     => 'kresuber_layout_section',
            'type'        => 'number',
        ] );
    }

    /**
     * 2. Output CSS ke Frontend (Override Style Bawaan)
     */
    public function output_custom_css() {
        // Hanya load di halaman App kita
        $endpoint = get_query_var('kresuber_endpoint');
        $is_wc = function_exists('is_woocommerce') && (is_cart() || is_checkout() || is_account_page());

        if ( ! $endpoint && ! $is_wc ) return;

        // Ambil value dari database
        $primary = get_theme_mod( 'k_primary_color', '#FF6B00' );
        $bg      = get_theme_mod( 'k_bg_color', '#F8F9FD' );
        $surface = get_theme_mod( 'k_surface_color', '#FFFFFF' );
        $radius  = get_theme_mod( 'k_radius_md', '12' );
        $width   = get_theme_mod( 'k_desktop_width', '600' );

        ?>
        <style type="text/css" id="kresuber-custom-css">
            :root {
                --k-primary: <?php echo esc_attr( $primary ); ?> !important;
                --k-bg: <?php echo esc_attr( $bg ); ?> !important;
                --k-surface: <?php echo esc_attr( $surface ); ?> !important;
                --k-radius-md: <?php echo esc_attr( $radius ); ?>px !important;
            }

            /* Override Desktop Width */
            @media (min-width: 768px) {
                #k-app-container, 
                .k-checkout-layout, 
                .k-account-shell-wrap, 
                .k-cart-wrap,
                .k-edit-account-wrap,
                .k-view-order-wrap,
                .k-bottom-navbar, 
                .k-bottom-bar, 
                .k-cart-summary, 
                .k-p-action {
                    max-width: <?php echo esc_attr( $width ); ?>px !important;
                }
            }
        </style>
        <?php
    }
}

// Inisialisasi Class
new Kresuber_POS_Customizer();