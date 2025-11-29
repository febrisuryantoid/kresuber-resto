<?php
/**
 * Favorites Shell Template
 */

defined( 'ABSPATH' ) || exit;

// Load necessary assets (CSS, JS) from Kresuber_POS_Core::assets()

get_header(); // Or your custom header if needed

?>

<div id="k-app-container">
    <div class="k-header">
        <h1 class="k-title">Favorit Anda</h1>
    </div>
    <div class="k-content" style="padding: 20px;">
        <p>Halaman ini akan menampilkan produk favorit Anda.</p>
        <!-- Future: Implement dynamic favorite product listing -->
    </div>
    <?php include KRESUBER_PATH . 'templates/bottom-navbar.php'; // Include bottom navbar ?>
</div>

<?php
get_footer(); // Or your custom footer if needed
