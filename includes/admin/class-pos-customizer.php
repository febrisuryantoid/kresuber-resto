<?php
/**
 * Class Kresuber_POS_Customizer
 * Advanced Customizer untuk Full App Styling
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Kresuber_POS_Customizer {

    public function __construct() {
        add_action( 'customize_register', [ $this, 'register_settings' ] );
        add_action( 'wp_head', [ $this, 'output_custom_css' ], 100 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'customize_controls_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * 1. Register Panel & Controls
     */
    public function register_settings( $wp_customize ) {

        // PANEL UTAMA (Priority 1 - Paling Atas)
        $wp_customize->add_panel( 'kresuber_pos_panel', [
            'title'       => __( 'Tampilan POS App', 'kresuber-resto' ),
            'description' => 'Pengaturan desain menyeluruh untuk Aplikasi POS.',
            'priority'    => 1,
        ] );

        // =====================================================================
        // [1] KATALOG & GRID PRODUK
        // =====================================================================
        $wp_customize->add_section( 'k_catalog_sec', [
            'title' => 'Katalog & Grid Produk',
            'panel' => 'kresuber_pos_panel',
        ] );

        // Responsive Columns (Setting Jumlah Produk Tiap Device)
        $this->add_number( $wp_customize, 'k_col_desk', 'Kolom Desktop', 'k_catalog_sec', 5, 1, 8 );
        $this->add_number( $wp_customize, 'k_col_tab', 'Kolom Tablet', 'k_catalog_sec', 3, 1, 5 );
        $this->add_number( $wp_customize, 'k_col_mob', 'Kolom Mobile', 'k_catalog_sec', 2, 1, 3 );

        // Gaps & Alignment
        $this->add_range( $wp_customize, 'k_grid_gap', 'Jarak Antar Produk (px)', 'k_catalog_sec', 16, 0, 50 );
        
        $wp_customize->add_setting( 'k_content_align', [ 'default' => 'left', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_content_align', [
            'label'    => 'Perataan Teks',
            'section'  => 'k_catalog_sec',
            'type'     => 'select',
            'choices'  => [
                'left'   => 'Rata Kiri',
                'center' => 'Rata Tengah',
            ]
        ] );

        // =====================================================================
        // [2] DESAIN KARTU PRODUK
        // =====================================================================
        $wp_customize->add_section( 'k_card_sec', [
            'title' => 'Desain Kartu Produk',
            'panel' => 'kresuber_pos_panel',
        ] );

        $this->add_range( $wp_customize, 'k_card_radius', 'Radius Kartu (px)', 'k_card_sec', 12, 0, 30 );
        $this->add_range( $wp_customize, 'k_card_shadow', 'Ketebalan Bayangan', 'k_card_sec', 0.05, 0, 0.5, 0.01 );
        $this->add_color( $wp_customize, 'k_card_bg', 'Warna Background Kartu', '#FFFFFF', 'k_card_sec' );

        // Toggle Elements
        $this->add_toggle( $wp_customize, 'k_show_img', 'Tampilkan Gambar Produk', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_title', 'Tampilkan Judul', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_price', 'Tampilkan Harga', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_add', 'Tampilkan Tombol Tambah', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_fav', 'Tampilkan Tombol Favorit', true, 'k_card_sec' );

        // =====================================================================
        // [3] HEADER & NAVIGASI
        // =====================================================================
        $wp_customize->add_section( 'k_header_sec', [
            'title' => 'Header & Navigasi',
            'panel' => 'kresuber_pos_panel',
        ] );

        // Search Box Style
        $this->add_color( $wp_customize, 'k_search_bg', 'Background Pencarian', '#F8F9FD', 'k_header_sec' );
        $this->add_color( $wp_customize, 'k_search_border', 'Border Pencarian', '#EEEEEE', 'k_header_sec' );
        $this->add_range( $wp_customize, 'k_search_radius', 'Radius Pencarian (px)', 'k_header_sec', 12, 0, 50 );

        // Cart Icon Style
        $this->add_color( $wp_customize, 'k_cart_icon_color', 'Warna Ikon Keranjang', '#1A1A1A', 'k_header_sec' );
        $this->add_range( $wp_customize, 'k_cart_icon_size', 'Ukuran Ikon (px)', 'k_header_sec', 24, 16, 40 );
        $this->add_color( $wp_customize, 'k_badge_bg', 'Warna Badge Hitungan', '#FF3D00', 'k_header_sec' );

        // =====================================================================
        // [4] WARNA GLOBAL
        // =====================================================================
        $wp_customize->add_section( 'k_colors_sec', [
            'title' => 'Warna Global',
            'panel' => 'kresuber_pos_panel',
        ] );

        $this->add_color( $wp_customize, 'k_primary_color', 'Warna Utama (Primary)', '#FF6B00', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_text_main', 'Warna Teks Utama', '#1A1A1A', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_text_sec', 'Warna Teks Sekunder', '#888888', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_border_color', 'Warna Garis', '#F0F0F0', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_app_bg', 'Background Luar (Desktop)', '#F0F2F5', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_content_bg', 'Background Konten Utama', '#FFFFFF', 'k_colors_sec' );

        // =====================================================================
        // [5] TIPOGRAFI & LAYOUT
        // =====================================================================
        $wp_customize->add_section( 'k_typo_sec', [
            'title' => 'Tipografi & Layout',
            'panel' => 'kresuber_pos_panel',
        ] );

        $wp_customize->add_setting( 'k_font_family', [ 'default' => 'Plus Jakarta Sans', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_font_family', [
            'label'    => 'Jenis Font',
            'section'  => 'k_typo_sec',
            'type'     => 'select',
            'choices'  => [
                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                'Inter'             => 'Inter',
                'Roboto'            => 'Roboto',
                'Poppins'           => 'Poppins',
            ]
        ] );

        $this->add_range( $wp_customize, 'k_base_size', 'Ukuran Font Dasar (px)', 'k_typo_sec', 14, 12, 18 );
        
        $wp_customize->add_setting( 'k_desktop_width', [ 'default' => '600px', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_desktop_width', [
            'label'       => 'Lebar Konten Desktop',
            'description' => 'Contoh: 600px, 1000px, 100%',
            'section'     => 'k_typo_sec',
            'type'        => 'text',
        ] );
    }

    /**
     * Helper Functions
     */
    private function add_color( $wp_customize, $id, $label, $default, $section ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'transport' => 'refresh' ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, [ 'label' => $label, 'section' => $section ] ) );
    }

    private function add_range( $wp_customize, $id, $label, $section, $default, $min, $max, $step = 1 ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'transport' => 'refresh' ] );
        $wp_customize->add_control( $id, [ 'label' => $label, 'section' => $section, 'type' => 'range', 'input_attrs' => [ 'min' => $min, 'max' => $max, 'step' => $step ] ] );
    }

    private function add_number( $wp_customize, $id, $label, $section, $default, $min, $max ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'transport' => 'refresh' ] );
        $wp_customize->add_control( $id, [ 'label' => $label, 'section' => $section, 'type' => 'number', 'input_attrs' => [ 'min' => $min, 'max' => $max ] ] );
    }

    private function add_toggle( $wp_customize, $id, $label, $default, $section ) {
        $wp_customize->add_setting( $id, [ 'default' => $default, 'transport' => 'refresh' ] );
        $wp_customize->add_control( $id, [ 'label' => $label, 'section' => $section, 'type' => 'checkbox' ] );
    }

    /**
     * 2. Load Fonts & Icons
     */
    public function enqueue_assets() {
        $font = get_theme_mod( 'k_font_family', 'Plus Jakarta Sans' );
        $url = 'https://fonts.googleapis.com/css2?family=' . str_replace(' ', '+', $font) . ':wght@400;500;600;700;800&display=swap';
        wp_enqueue_style( 'k-custom-font', $url );
        
        // Remix Icons
        wp_enqueue_style('k-icons', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
    }

    public function enqueue_admin_assets() {
        // Load icon di admin juga agar preview bagus (optional)
        wp_enqueue_style('k-icons-admin', 'https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
    }

    /**
     * 3. Output Dynamic CSS
     */
    public function output_custom_css() {
        // Cek halaman target
        $endpoint = get_query_var('kresuber_endpoint');
        $is_wc = function_exists('is_woocommerce') && (is_cart() || is_checkout() || is_account_page());
        if ( ! $endpoint && ! $is_wc ) return;

        // Get Values
        $primary    = get_theme_mod('k_primary_color', '#FF6B00');
        $bg_app     = get_theme_mod('k_app_bg', '#F0F2F5');
        $bg_cont    = get_theme_mod('k_content_bg', '#FFFFFF');
        $card_bg    = get_theme_mod('k_card_bg', '#FFFFFF');
        $text_main  = get_theme_mod('k_text_main', '#1A1A1A');
        $text_sec   = get_theme_mod('k_text_sec', '#888888');
        $border     = get_theme_mod('k_border_color', '#F0F0F0');
        
        $search_bg  = get_theme_mod('k_search_bg', '#F8F9FD');
        $search_bd  = get_theme_mod('k_search_border', '#EEEEEE');
        $search_rad = get_theme_mod('k_search_radius', 12);
        $cart_col   = get_theme_mod('k_cart_icon_color', '#1A1A1A');
        $cart_size  = get_theme_mod('k_cart_icon_size', 24);
        $badge_bg   = get_theme_mod('k_badge_bg', '#FF3D00');

        $col_d = get_theme_mod('k_col_desk', 5);
        $col_t = get_theme_mod('k_col_tab', 3);
        $col_m = get_theme_mod('k_col_mob', 2);
        $gap   = get_theme_mod('k_grid_gap', 16);
        $align = get_theme_mod('k_content_align', 'left');
        $width = get_theme_mod('k_desktop_width', '600px');
        
        $c_rad = get_theme_mod('k_card_radius', 12);
        $c_shd = get_theme_mod('k_card_shadow', 0.05);
        $v_title = get_theme_mod('k_show_title', true) ? 'block' : 'none';
        $v_price = get_theme_mod('k_show_price', true) ? 'block' : 'none';
        $v_add   = get_theme_mod('k_show_add', true) ? 'flex' : 'none';
        $v_fav   = get_theme_mod('k_show_fav', true) ? 'flex' : 'none';

        $font = get_theme_mod('k_font_family', 'Plus Jakarta Sans');
        $f_sz = get_theme_mod('k_base_size', 14);

        ?>
        <style id="kresuber-custom-css">
            :root {
                --k-primary: <?php echo esc_attr($primary); ?> !important;
                --k-bg: <?php echo esc_attr($bg_cont); ?> !important;
                --k-surface: <?php echo esc_attr($card_bg); ?> !important;
                --k-text-main: <?php echo esc_attr($text_main); ?> !important;
                --k-border: <?php echo esc_attr($border); ?> !important;
                --k-font: '<?php echo esc_attr($font); ?>', sans-serif !important;
                --k-radius-md: <?php echo esc_attr($c_rad); ?>px !important;
            }

            body, html { 
                font-family: var(--k-font); 
                font-size: <?php echo esc_attr($f_sz); ?>px;
                background-color: <?php echo esc_attr($bg_app); ?> !important;
            }

            /* --- HEADER & SEARCH --- */
            .app-search-input, #k-search {
                background-color: <?php echo esc_attr($search_bg); ?> !important;
                border-color: <?php echo esc_attr($search_bd); ?> !important;
                border-radius: <?php echo esc_attr($search_rad); ?>px !important;
            }
            .k-btn-cart i { 
                color: <?php echo esc_attr($cart_col); ?> !important; 
                font-size: <?php echo esc_attr($cart_size); ?>px !important;
            }
            .k-badge { background-color: <?php echo esc_attr($badge_bg); ?> !important; }

            /* --- GRID RESPONSIVE --- */
            .k-product-grid {
                grid-template-columns: repeat(<?php echo esc_attr($col_m); ?>, 1fr) !important;
                gap: <?php echo esc_attr($gap); ?>px !important;
            }
            @media (min-width: 600px) { .k-product-grid { grid-template-columns: repeat(<?php echo esc_attr($col_t); ?>, 1fr) !important; } }
            @media (min-width: 1024px) { .k-product-grid { grid-template-columns: repeat(<?php echo esc_attr($col_d); ?>, 1fr) !important; } }

            /* --- CARD STYLE & VISIBILITY --- */
            .k-card-prod {
                border-radius: <?php echo esc_attr($c_rad); ?>px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,<?php echo esc_attr($c_shd); ?>) !important;
                background-color: var(--k-surface) !important;
                border: 1px solid var(--k-border);
            }
            .k-card-title { 
                display: <?php echo $v_title; ?> !important; 
                text-align: <?php echo $align; ?> !important; 
                color: var(--k-text-main) !important;
            }
            .k-card-price { 
                display: <?php echo $v_price; ?> !important; 
                text-align: <?php echo $align; ?> !important; 
            }
            .k-btn-add-float { display: <?php echo $v_add; ?> !important; }
            .k-btn-fav { display: <?php echo $v_fav; ?> !important; }

            /* --- LAYOUT DESKTOP --- */
            @media (min-width: 768px) {
                #k-app-container, .k-checkout-layout, .k-account-shell-wrap, .k-cart-wrap, .k-edit-account-wrap, .k-view-order-wrap {
                    max-width: <?php echo esc_attr($width); ?> !important;
                    background-color: <?php echo esc_attr($bg_cont); ?> !important;
                }
                .k-bottom-navbar, .k-bottom-bar, .k-cart-summary, .k-p-action {
                    max-width: <?php echo esc_attr($width); ?> !important;
                }
            }
        </style>
        <?php
    }
}

new Kresuber_POS_Customizer();