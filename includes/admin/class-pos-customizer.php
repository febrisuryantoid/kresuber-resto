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
        // [1] WARNA GLOBAL & TIPOGRAFI
        // =====================================================================
        $wp_customize->add_section( 'k_colors_sec', [
            'title' => 'Warna & Tipografi Global',
            'panel' => 'kresuber_pos_panel',
        ] );
        
        // WARNA GLOBAL
        $this->add_color( $wp_customize, 'k_primary_color', 'Warna Utama (Primary)', '#FF6B00', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_text_main', 'Warna Teks Utama', '#1A1A1A', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_text_sec', 'Warna Teks Sekunder', '#888888', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_border_color', 'Warna Garis', '#F0F0F0', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_app_bg', 'Background Luar (Desktop)', '#F0F2F5', 'k_colors_sec' );
        $this->add_color( $wp_customize, 'k_content_bg', 'Background Konten Utama', '#FFFFFF', 'k_colors_sec' );
        $this->add_range( $wp_customize, 'k_base_size', 'Ukuran Font Dasar (px)', 'k_colors_sec', 14, 12, 18 ); // +1

        // TIPOGRAFI & LAYOUT
        $wp_customize->add_setting( 'k_font_family', [ 'default' => 'Plus Jakarta Sans', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_font_family', [
            'label'    => 'Jenis Font',
            'section'  => 'k_colors_sec',
            'type'     => 'select',
            'choices'  => [
                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                'Inter'             => 'Inter',
                'Roboto'            => 'Roboto',
                'Poppins'           => 'Poppins',
            ]
        ] );
        
        $wp_customize->add_setting( 'k_desktop_width', [ 'default' => '600px', 'transport' => 'refresh' ] );
        $wp_customize->add_control( 'k_desktop_width', [
            'label'       => 'Lebar Konten Desktop',
            'description' => 'Contoh: 600px, 1000px, 100%',
            'section'     => 'k_colors_sec',
            'type'        => 'text',
        ] );

        // =====================================================================
        // [2] KATALOG & GRID PRODUK
        // =====================================================================
        $wp_customize->add_section( 'k_catalog_sec', [
            'title' => 'Katalog & Grid Produk',
            'panel' => 'kresuber_pos_panel',
        ] );

        // Responsive Columns
        $this->add_number( $wp_customize, 'k_col_desk', 'Kolom Desktop', 'k_catalog_sec', 5, 1, 8 );
        $this->add_number( $wp_customize, 'k_col_tab', 'Kolom Tablet', 'k_catalog_sec', 3, 1, 5 );
        $this->add_number( $wp_customize, 'k_col_mob', 'Kolom Mobile', 'k_catalog_sec', 2, 1, 3 );

        // Gaps & Alignment
        $this->add_range( $wp_customize, 'k_grid_gap', 'Jarak Antar Produk (px)', 'k_catalog_sec', 16, 0, 50 );
        $this->add_range( $wp_customize, 'k_card_padding', 'Padding Kartu (px)', 'k_catalog_sec', 10, 5, 30 ); // +1
        
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
        $this->add_range( $wp_customize, 'k_catalog_padding', 'Padding Katalog (px)', 'k_catalog_sec', 20, 0, 40 ); // +1

        // =====================================================================
        // [3] DESAIN KARTU PRODUK
        // =====================================================================
        $wp_customize->add_section( 'k_card_sec', [
            'title' => 'Desain Kartu Produk',
            'panel' => 'kresuber_pos_panel',
        ] );

        $this->add_range( $wp_customize, 'k_card_radius', 'Radius Kartu (px)', 'k_card_sec', 12, 0, 30 );
        $this->add_range( $wp_customize, 'k_card_shadow', 'Ketebalan Bayangan', 'k_card_sec', 0.05, 0, 0.5, 0.01 );
        $this->add_color( $wp_customize, 'k_card_bg', 'Warna Background Kartu', '#FFFFFF', 'k_card_sec' );
        $this->add_range( $wp_customize, 'k_card_img_radius', 'Radius Gambar Produk (px)', 'k_card_sec', 8, 0, 20 ); // +1

        // Toggle Elements
        $this->add_toggle( $wp_customize, 'k_show_img', 'Tampilkan Gambar Produk', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_title', 'Tampilkan Judul', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_price', 'Tampilkan Harga', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_add', 'Tampilkan Tombol Tambah', true, 'k_card_sec' );
        $this->add_toggle( $wp_customize, 'k_show_fav', 'Tampilkan Tombol Favorit', true, 'k_card_sec' );
        $this->add_range( $wp_customize, 'k_title_lines', 'Batasan Baris Judul', 'k_card_sec', 2, 1, 3 ); // +1
        
        // =====================================================================
        // [4] HEADER & SEARCH
        // =====================================================================
        $wp_customize->add_section( 'k_header_sec', [
            'title' => 'Header & Pencarian',
            'panel' => 'kresuber_pos_panel',
        ] );

        // Search Box Style (Affected selectors: .app-search-wrap, .app-search-input, .pos-search-bar)
        $this->add_color( $wp_customize, 'k_search_bg', 'Background Pencarian', '#F8F9FD', 'k_header_sec' );
        $this->add_color( $wp_customize, 'k_search_border', 'Border Pencarian', '#EEEEEE', 'k_header_sec' );
        $this->add_range( $wp_customize, 'k_search_radius', 'Radius Pencarian (px)', 'k_header_sec', 12, 0, 50 );

        // Cart Icon Style
        $this->add_color( $wp_customize, 'k_cart_icon_color', 'Warna Ikon Keranjang', '#1A1A1A', 'k_header_sec' );
        $this->add_range( $wp_customize, 'k_cart_icon_size', 'Ukuran Ikon (px)', 'k_header_sec', 24, 16, 40 );
        $this->add_color( $wp_customize, 'k_badge_bg', 'Warna Badge Hitungan', '#FF3D00', 'k_header_sec' );
        $this->add_range( $wp_customize, 'k_header_padding', 'Padding Header (px)', 'k_header_sec', 15, 10, 30 ); // +1
        $this->add_color( $wp_customize, 'k_header_bg', 'Background Header', '#FFFFFF', 'k_header_sec' ); // +1
        
        // =====================================================================
        // [5] ACCOUNT & PROFILE
        // =====================================================================
        $wp_customize->add_section( 'k_account_sec', [
            'title' => 'Akun & Profil',
            'panel' => 'kresuber_pos_panel',
        ] );

        $this->add_color( $wp_customize, 'k_profile_header_color', 'Warna Utama Header Profil', '#FF6B00', 'k_account_sec' );
        $this->add_range( $wp_customize, 'k_profile_radius', 'Radius Header Bawah (px)', 'k_account_sec', 30, 0, 50 );
        $this->add_range( $wp_customize, 'k_profile_avatar_size', 'Ukuran Avatar (px)', 'k_account_sec', 90, 50, 120 );
        $this->add_color( $wp_customize, 'k_profile_icon_bg', 'Background Ikon Menu', '#FFF0E6', 'k_account_sec' );
        $this->add_color( $wp_customize, 'k_profile_menu_bg', 'Background Menu Item', '#FFFFFF', 'k_account_sec' ); // +1
        $this->add_range( $wp_customize, 'k_profile_menu_radius', 'Radius Menu Item (px)', 'k_account_sec', 16, 0, 30 ); // +1
        $this->add_color( $wp_customize, 'k_lang_active_bg', 'BG Switcher Bahasa Aktif', '#FFFFFF', 'k_account_sec' ); // +1

        // =====================================================================
        // [6] FOOTER NAVIGASI MOBILE
        // =====================================================================
        $wp_customize->add_section( 'k_nav_sec', [
            'title' => 'Navigasi Footer (Mobile)',
            'panel' => 'kresuber_pos_panel',
        ] );

        $this->add_color( $wp_customize, 'k_nav_bg', 'Background Navigasi', '#FFFFFF', 'k_nav_sec' );
        $this->add_color( $wp_customize, 'k_nav_inactive_color', 'Warna Ikon Non-Aktif', '#AAAAAA', 'k_nav_sec' );
        $this->add_color( $wp_customize, 'k_nav_active_color', 'Warna Ikon Aktif', '#FF6B00', 'k_nav_sec' ); // FIX: Ikon Aktif
        $this->add_range( $wp_customize, 'k_nav_icon_size', 'Ukuran Ikon (px)', 'k_nav_sec', 24, 18, 30 );
        $this->add_range( $wp_customize, 'k_nav_padding_y', 'Padding Vertikal (px)', 'k_nav_sec', 10, 5, 20 ); // +1
        
        // =====================================================================
        // [7] FORMULIR & TOMBOL (BARU)
        // =====================================================================
        $wp_customize->add_section( 'k_form_sec', [
            'title' => 'Formulir & Tombol',
            'panel' => 'kresuber_pos_panel',
        ] );
        
        $this->add_range( $wp_customize, 'k_btn_radius', 'Radius Tombol Utama (px)', 'k_form_sec', 14, 0, 50 ); // k-btn-confirm, k-btn-save
        $this->add_color( $wp_customize, 'k_btn_text_color', 'Warna Teks Tombol Utama', '#FFFFFF', 'k_form_sec' ); // +1
        $this->add_range( $wp_customize, 'k_input_radius', 'Radius Input Formulir (px)', 'k_form_sec', 12, 0, 50 ); // +1
        $this->add_color( $wp_customize, 'k_input_bg', 'Background Input', '#fcfcfc', 'k_form_sec' ); // +1
        $this->add_color( $wp_customize, 'k_input_border_focus', 'Border Input Fokus', '#FF6B00', 'k_form_sec' ); // +1
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
     * Helper: Mencerahkan warna hex untuk gradient
     */
    private static function lighten_color($hex, $percent) {
        // Ensure hex is clean
        $hex = ltrim($hex, '#');

        // Convert to RGB
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        // Calculate new RGB values
        $r = max(0, min(255, $r + 255 * ($percent / 100)));
        $g = max(0, min(255, $g + 255 * ($percent / 100)));
        $b = max(0, min(255, $b + 255 * ($percent / 100)));

        // Convert back to hex
        return '#' . str_pad(dechex(round($r)), 2, '0', STR_PAD_LEFT) .
                     str_pad(dechex(round($g)), 2, '0', STR_PAD_LEFT) .
                     str_pad(dechex(round($b)), 2, '0', STR_PAD_LEFT);
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

        // Get Values Global
        $primary    = get_theme_mod('k_primary_color', '#FF6B00');
        $text_main  = get_theme_mod('k_text_main', '#1A1A1A');
        $text_sec   = get_theme_mod('k_text_sec', '#888888');
        $border     = get_theme_mod('k_border_color', '#F0F0F0');
        $bg_app     = get_theme_mod('k_app_bg', '#F0F2F5');
        $bg_cont    = get_theme_mod('k_content_bg', '#FFFFFF');
        $font       = get_theme_mod('k_font_family', 'Plus Jakarta Sans');
        $f_sz       = get_theme_mod('k_base_size', 14);
        $width      = get_theme_mod('k_desktop_width', '600px');
        
        // Get Values Search & Header
        $search_bg      = get_theme_mod('k_search_bg', '#F8F9FD');
        $search_bd      = get_theme_mod('k_search_border', '#EEEEEE');
        $search_rad     = get_theme_mod('k_search_radius', 12);
        $cart_col       = get_theme_mod('k_cart_icon_color', '#1A1A1A');
        $cart_size      = get_theme_mod('k_cart_icon_size', 24);
        $badge_bg       = get_theme_mod('k_badge_bg', '#FF3D00');
        $header_pad     = get_theme_mod('k_header_padding', 15);
        $header_bg      = get_theme_mod('k_header_bg', '#FFFFFF');

        // Get Values Grid & Card
        $col_d      = get_theme_mod('k_col_desk', 5);
        $col_t      = get_theme_mod('k_col_tab', 3);
        $col_m      = get_theme_mod('k_col_mob', 2);
        $gap        = get_theme_mod('k_grid_gap', 16);
        $align      = get_theme_mod('k_content_align', 'left');
        $c_rad      = get_theme_mod('k_card_radius', 12);
        $c_shd      = get_theme_mod('k_card_shadow', 0.05);
        $c_bg       = get_theme_mod('k_card_bg', '#FFFFFF');
        $c_img_rad  = get_theme_mod('k_card_img_radius', 8);
        $c_padding  = get_theme_mod('k_card_padding', 10);
        $c_title_lines = get_theme_mod('k_title_lines', 2);
        $catalog_pad= get_theme_mod('k_catalog_padding', 20);

        // Get Values Account & Nav
        $prof_color     = get_theme_mod('k_profile_header_color', '#FF6B00');
        $prof_radius    = get_theme_mod('k_profile_radius', 30);
        $prof_avatar    = get_theme_mod('k_profile_avatar_size', 90);
        $prof_icon_bg   = get_theme_mod('k_profile_icon_bg', '#FFF0E6');
        $prof_menu_bg   = get_theme_mod('k_profile_menu_bg', '#FFFFFF');
        $prof_menu_rad  = get_theme_mod('k_profile_menu_radius', 16);
        $lang_active_bg = get_theme_mod('k_lang_active_bg', '#FFFFFF');
        $nav_bg         = get_theme_mod('k_nav_bg', '#FFFFFF');
        $nav_inactive   = get_theme_mod('k_nav_inactive_color', '#AAAAAA');
        $nav_active     = get_theme_mod('k_nav_active_color', '#FF6B00');
        $nav_icon_size  = get_theme_mod('k_nav_icon_size', 24);
        $nav_padding_y  = get_theme_mod('k_nav_padding_y', 10);

        // Get Values Form & Button
        $btn_rad        = get_theme_mod('k_btn_radius', 14);
        $btn_text_col   = get_theme_mod('k_btn_text_color', '#FFFFFF');
        $input_rad      = get_theme_mod('k_input_radius', 12);
        $input_bg       = get_theme_mod('k_input_bg', '#fcfcfc');
        $input_border_focus = get_theme_mod('k_input_border_focus', '#FF6B00');

        ?>
        <style id="kresuber-custom-css">
            :root {
                --k-primary: <?php echo esc_attr($primary); ?> !important;
                --k-bg: <?php echo esc_attr($bg_cont); ?> !important;
                --k-surface: <?php echo esc_attr($c_bg); ?> !important;
                --k-text-main: <?php echo esc_attr($text_main); ?> !important;
                --k-border: <?php echo esc_attr($border); ?> !important;
                --k-font: '<?php echo esc_attr($font); ?>', sans-serif !important;
                --k-radius-md: <?php echo esc_attr($c_rad); ?>px !important;
            }

            body, html { 
                font-family: var(--k-font); 
                font-size: <?php echo esc_attr($f_sz); ?>px;
                background-color: <?php echo esc_attr($bg_app); ?> !important;
                color: var(--k-text-main);
            }
            h1, h2, h3, h4 { color: var(--k-text-main); }


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

            /* --- HEADER & SEARCH --- */
            .k-header { 
                padding: <?php echo esc_attr($header_pad); ?>px 20px !important;
                background: <?php echo esc_attr($header_bg); ?> !important;
            }
            .app-search-input, .pos-search-bar input {
                background-color: <?php echo esc_attr($search_bg); ?> !important;
                border-color: <?php echo esc_attr($search_bd); ?> !important;
                border-radius: <?php echo esc_attr($search_rad); ?>px !important;
            }
            .k-btn-cart i { 
                color: <?php echo esc_attr($cart_col); ?> !important; 
                font-size: <?php echo esc_attr($cart_size); ?>px !important;
            }
            .k-badge { background-color: <?php echo esc_attr($badge_bg); ?> !important; }

            /* --- GRID & CATALOG --- */
            .k-product-grid {
                padding: <?php echo esc_attr($catalog_pad); ?>px !important;
                /* Menggunakan auto-fill dengan fallback minmax untuk konsistensi */
                grid-template-columns: repeat(auto-fill, minmax(calc(100% / <?php echo esc_attr($col_m); ?> - <?php echo esc_attr($gap); ?>px), 1fr)) !important; 
                gap: <?php echo esc_attr($gap); ?>px !important;
            }
            @media (max-width: 480px) { .k-product-grid { grid-template-columns: repeat(<?php echo esc_attr($col_m); ?>, 1fr) !important; } }
            @media (min-width: 600px) { .k-product-grid { grid-template-columns: repeat(<?php echo esc_attr($col_t); ?>, 1fr) !important; } }
            @media (min-width: 1024px) { .k-product-grid { grid-template-columns: repeat(<?php echo esc_attr($col_d); ?>, 1fr) !important; } }

            /* --- CARD STYLE & VISIBILITY --- */
            .k-card-prod {
                border-radius: <?php echo esc_attr($c_rad); ?>px !important;
                box-shadow: 0 4px 15px rgba(0,0,0,<?php echo esc_attr($c_shd); ?>) !important;
                background-color: var(--k-surface) !important;
                border: 1px solid var(--k-border);
                padding: <?php echo esc_attr($c_padding); ?>px !important;
            }
            .k-card-img {
                border-radius: <?php echo esc_attr($c_img_rad); ?>px !important;
            }
            .k-card-title { 
                display: <?php echo (get_theme_mod('k_show_title', true) ? 'block' : 'none'); ?> !important; 
                text-align: <?php echo $align; ?> !important; 
                color: var(--k-text-main) !important;
                -webkit-line-clamp: <?php echo esc_attr($c_title_lines); ?> !important;
                line-clamp: <?php echo esc_attr($c_title_lines); ?> !important;
                height: auto !important; /* Biarkan tinggi diatur oleh line-clamp */
            }
            .k-card-price { 
                display: <?php echo (get_theme_mod('k_show_price', true) ? 'block' : 'none'); ?> !important; 
                text-align: <?php echo $align; ?> !important; 
            }
            .k-btn-add-float { display: <?php echo (get_theme_mod('k_show_add', true) ? 'flex' : 'none'); ?> !important; }
            .k-btn-fav { display: <?php echo (get_theme_mod('k_show_fav', true) ? 'flex' : 'none'); ?> !important; }
            
            /* --- ACCOUNT & PROFILE --- */
            .k-profile-header { 
                background: linear-gradient(135deg, <?php echo esc_attr($prof_color); ?> 0%, <?php echo esc_attr(self::lighten_color($prof_color, 15)); ?> 100%) !important; 
                border-radius: 0 0 <?php echo esc_attr($prof_radius); ?>px <?php echo esc_attr($prof_radius); ?>px !important; 
            }
            .k-profile-avatar { 
                width: <?php echo esc_attr($prof_avatar); ?>px !important; 
                height: <?php echo esc_attr($prof_avatar); ?>px !important; 
            }
            .k-menu-icon { 
                background: <?php echo esc_attr($prof_icon_bg); ?> !important; 
            }
            .k-menu-item {
                 background: <?php echo esc_attr($prof_menu_bg); ?> !important;
                 border-radius: <?php echo esc_attr($prof_menu_rad); ?>px !important; 
            }
            .k-lang-opt.active {
                background: <?php echo esc_attr($lang_active_bg); ?> !important;
            }

            /* --- BOTTOM NAVIGASI MOBILE --- */
            .k-bottom-navbar {
                background: <?php echo esc_attr($nav_bg); ?> !important;
                padding-top: <?php echo esc_attr($nav_padding_y); ?>px !important;
                padding-bottom: <?php echo esc_attr($nav_padding_y); ?>px !important;
            }
            .k-nav-item {
                color: <?php echo esc_attr($nav_inactive); ?> !important;
            }
            .k-nav-item.active {
                 color: <?php echo esc_attr($nav_active); ?> !important;
            }
            .k-nav-item i {
                font-size: <?php echo esc_attr($nav_icon_size); ?>px !important;
            }

            /* --- FORMULIR & TOMBOL --- */
            .k-btn-confirm, .k-btn-save, .pos-bill-actions .btn-action.btn-primary {
                border-radius: <?php echo esc_attr($btn_rad); ?>px !important;
                color: <?php echo esc_attr($btn_text_col); ?> !important;
            }
            .k-form-input, .pos-category-dropdown {
                border-radius: <?php echo esc_attr($input_rad); ?>px !important;
                background: <?php echo esc_attr($input_bg); ?> !important;
            }
            .k-form-input:focus, .pos-category-dropdown:focus {
                border-color: <?php echo esc_attr($input_border_focus); ?> !important;
            }
        </style>
        <?php
    }
}

new Kresuber_POS_Customizer();