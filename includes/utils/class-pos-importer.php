<?php
class Kresuber_POS_Importer {
    
    private $products = [
        ['name' => 'Nasi Goreng Spesial', 'desc' => 'Nasi goreng autentik dengan bumbu rahasia, disajikan dengan telur, ayam, dan kerupuk. Rasanya dijamin membuat ketagihan.'],
        ['name' => 'Sate Ayam Madura', 'desc' => 'Sate ayam empuk dengan bumbu kacang khas Madura yang kaya rasa, dibakar sempurna di atas arang.'],
        ['name' => 'Rendang Daging Sapi', 'desc' => 'Daging sapi empuk yang dimasak perlahan dengan santan dan rempah-rempah pilihan hingga bumbu meresap sempurna.'],
        ['name' => 'Gado-Gado Siram', 'desc' => 'Sayuran segar direbus, disajikan dengan lontong, tahu, tempe, dan saus kacang yang lezat dan sedikit pedas.'],
        ['name' => 'Soto Ayam Lamongan', 'desc' => 'Soto ayam bening dengan suwiran ayam, soun, tauge, dan taburan koya gurih yang khas.'],
        ['name' => 'Bakso Kuah Komplit', 'desc' => 'Bakso daging sapi kenyal dengan kuah kaldu gurih, dilengkapi mie, tahu, dan pangsit goreng.'],
        ['name' => 'Ikan Bakar Jimbaran', 'desc' => 'Ikan segar pilihan dibakar dengan bumbu khas Jimbaran, Bali. Disajikan dengan sambal matah.'],
        ['name' => 'Ayam Penyet Sambal Ijo', 'desc' => 'Ayam goreng empuk yang dipenyet dan disajikan dengan sambal cabai hijau pedas yang segar.'],
        ['name' => 'Rawon Surabaya', 'desc' => 'Sup daging berkuah hitam pekat dari kluwek, disajikan dengan tauge pendek dan sambal terasi.'],
        ['name' => 'Mie Aceh Kepiting', 'desc' => 'Mie kuning tebal dimasak dengan bumbu Aceh kaya rempah, disajikan dengan potongan kepiting segar.'],
        ['name' => 'Gudeg Jogja', 'desc' => 'Nangka muda dimasak dengan santan hingga empuk, disajikan dengan nasi, krecek, dan telur pindang.'],
        ['name' => 'Pempek Palembang', 'desc' => 'Pempek ikan tenggiri asli, disajikan dengan kuah cuko yang asam, manis, dan pedas.'],
        ['name' => 'Ketoprak Jakarta', 'desc' => 'Lontong, tahu, bihun, dan tauge disiram saus kacang dan diberi taburan bawang goreng.'],
        ['name' => 'Sop Buntut', 'desc' => 'Potongan buntut sapi empuk dalam kuah bening kaya rempah, disajikan dengan wortel dan kentang.'],
        ['name' => 'Bebek Goreng Crispy', 'desc' => 'Bebek yang diungkep bumbu lalu digoreng hingga kulitnya renyah dan dagingnya empuk. Nikmat!'],
        ['name' => 'Tahu Gejrot Cirebon', 'desc' => 'Tahu pong dipotong-potong lalu disiram kuah pedas asam dari cabai, bawang, dan air asam jawa.'],
        ['name' => 'Nasi Uduk Betawi', 'desc' => 'Nasi yang dimasak dengan santan dan rempah, disajikan dengan ayam goreng, tempe orek, dan sambal.'],
        ['name' => 'Lumpia Semarang', 'desc' => 'Lumpia renyah berisi rebung, telur, dan udang, disajikan dengan saus kental manis.'],
        ['name' => 'Es Cendol Elizabeth', 'desc' => 'Minuman segar berisi cendol hijau, santan, dan gula merah cair yang manis dan legit.'],
        ['name' => 'Karedok Sunda', 'desc' => 'Salad sayuran mentah seperti kacang panjang, tauge, dan kol, disiram dengan bumbu kacang.'],
        ['name' => 'Iga Bakar Madu', 'desc' => 'Iga sapi empuk yang dibakar dengan olesan madu dan bumbu rempah, menghasilkan rasa manis gurih.'],
        ['name' => 'Coto Makassar', 'desc' => 'Soto daging sapi kental khas Makassar, kaya rempah, biasa dinikmati dengan buras atau ketupat.'],
        ['name' => 'Pecel Lele Lamongan', 'desc' => 'Ikan lele goreng renyah disajikan dengan sambal terasi pedas, lalapan segar, dan nasi hangat.'],
        ['name' => 'Laksa Bogor', 'desc' => 'Kuah santan kuning kental dengan oncom, ketupat, tauge, dan bihun. Rasanya unik dan gurih.'],
        ['name' => 'Asinan Betawi', 'desc' => 'Campuran sayuran dan buah-buahan yang diasinkan, disiram kuah cuka pedas dengan taburan kacang.'],
        ['name' => 'Seblak Bandung', 'desc' => 'Kerupuk basah dimasak dengan bumbu pedas gurih, disajikan dengan telur, sayuran, dan ceker.'],
        ['name' => 'Kerak Telor', 'desc' => 'Omelet khas Betawi dari beras ketan dan telur, dibakar di atas arang dan ditaburi serundeng.'],
        ['name' => 'Es Pisang Ijo', 'desc' => 'Pisang yang dibalut adonan hijau, disajikan dengan bubur sumsum, sirup, dan es serut.'],
        ['name' => 'Martabak Manis Bangka', 'desc' => 'Martabak tebal dan lembut dengan berbagai pilihan topping seperti cokelat, keju, dan kacang.'],
        ['name' => 'Kue Cubit', 'desc' => 'Kue kecil setengah matang yang lumer di mulut, varian rasa green tea dan red velvet.'],
    ];

    public function import() {
        if (!class_exists('WooCommerce')) {
            return ['success' => false, 'message' => 'WooCommerce is not active.'];
        }

        $count = 0;
        foreach ($this->products as $index => $p) {
            // Check if product exists by SKU to prevent duplicates
            $sku = 'KRSBR-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $existing_product = wc_get_product_id_by_sku($sku);

            if ($existing_product) {
                continue;
            }

            $product = new WC_Product_Simple();
            
            // Set properties based on WooCommerce CSV Schema guidance
            $product->set_name($p['name']);
            $product->set_sku($sku); // SKU
            $product->set_regular_price(rand(15000, 50000)); // Regular price
            $product->set_description($p['desc']); // Description
            $product->set_short_description(substr($p['desc'], 0, 150)); // Short description
            $product->set_status('publish'); // Published = 1
            $product->set_catalog_visibility('visible'); // Visibility in catalog = visible
            $product->set_stock_status('instock'); // In stock? = 1
            $product->set_reviews_allowed(true); // Reviews allowed? = 1
            $product->set_image_id($this->get_placeholder_image()); // Images

            $product_id = $product->save();
            
            if ($product_id && !is_wp_error($product_id)) {
                $this->add_reviews($product_id);
                $count++;
            }
        }
        // Clear caches after import
        wp_cache_flush();
        wc_delete_product_transients();

        if ($count > 0) {
            return ['success' => true, 'message' => "$count produk demo baru berhasil diimpor."];
        } else {
            return ['success' => true, 'message' => "Produk demo sudah ada. Tidak ada produk baru yang diimpor."];
        }
    }
    
    private function add_reviews($product_id) {
        $review_count = rand(3, 7);
        $comments = [
            'Enak banget, bumbunya pas!',
            'Porsinya banyak, harganya oke.',
            'Pelayanan cepat, tempatnya bersih.',
            'Rasa otentik, jadi inget kampung halaman.',
            'Sambelnya mantap, pedesnya nampol!',
            'Recommended! Pasti balik lagi.',
            'Minumannya juga seger-seger.'
        ];

        for ($i = 0; $i < $review_count; $i++) {
            $commentdata = [
                'comment_post_ID' => $product_id,
                'comment_author' => 'Pelanggan ' . rand(1, 100),
                'comment_author_email' => 'customer' . rand(1, 100) . '@example.com',
                'comment_content' => $comments[array_rand($comments)],
                'comment_type' => 'review',
                'comment_parent' => 0,
                'user_id' => 0,
                'comment_approved' => 1,
            ];
            $comment_id = wp_insert_comment($commentdata);
            
            if($comment_id) {
                // Rating (4 or 5 stars)
                add_comment_meta($comment_id, 'rating', rand(4, 5), true);
            }
        }
    }

    private function get_placeholder_image() {
        // This function could be expanded to download a real image.
        // For now, we return null so WooCommerce uses its default placeholder.
        return null;
    }
}
