<?php
class Kresuber_POS_Importer {

    public function import() {
        if (!class_exists('WooCommerce')) {
            return ['success' => false, 'message' => 'WooCommerce tidak aktif.'];
        }

        // 1. Reset Toko
        $this->reset_store();

        // 2. Ambil Data Demo
        $demo_data = $this->get_demo_data();
        $count = 0;

        foreach ($demo_data as $item) {
            // A. Buat Kategori
            $cat_id = $this->create_category($item['category']);

            // B. Buat Produk
            $product = new WC_Product_Simple();
            $product->set_name($item['name']);
            $product->set_slug(sanitize_title($item['name']));
            $product->set_regular_price($item['price']);
            $product->set_description($item['desc']);
            $product->set_short_description(substr($item['desc'], 0, 150) . '...');
            $product->set_status('publish');
            $product->set_catalog_visibility('visible');
            $product->set_stock_status('instock');
            $product->set_manage_stock(false);
            $product->set_reviews_allowed(true); // Aktifkan Review
            
            if ($cat_id) {
                $product->set_category_ids([$cat_id]);
            }

            $product_id = $product->save();

            if ($product_id) {
                // C. Tambahkan Ulasan Dummy
                $this->add_dummy_reviews($product_id);
                $count++;
            }
        }

        wp_cache_flush();
        wc_delete_product_transients();

        return [
            'success' => true, 
            'message' => "Selesai! $count produk demo dengan ulasan Indonesia telah dibuat."
        ];
    }

    private function add_dummy_reviews($product_id) {
        $names = [
            'Budi', 'Siti', 'Agus', 'Dewi', 'Rina', 'Bambang', 'Joko', 'Lestari', 'Putri', 'Eko', 
            'Sari', 'Wayan', 'Made', 'Dian', 'Rizky', 'Fajar', 'Indah', 'Bayu', 'Citra', 'Hendra',
            'Santoso', 'Wijaya', 'Kusuma', 'Pratama', 'Saputra', 'Hidayat', 'Wulandari', 'Permata'
        ];

        $comments_positive = [
            "Rasanya mantap banget! Bumbunya meresap sampai ke dalam.",
            "Porsinya pas dan mengenyangkan. Recommended!",
            "Enak parah, gak nyesel beli ini. Pasti bakal order lagi.",
            "Sesuai ekspektasi, rasanya otentik khas Indonesia.",
            "Pengiriman cepat, makanan masih hangat pas sampai.",
            "Teksturnya lembut, rasanya gurih. Cocok buat makan siang.",
            "Favorit keluarga nih, anak-anak juga pada suka.",
            "Harganya terjangkau tapi rasanya bintang lima.",
            "Pedasnya nampol! Buat pecinta pedas wajib coba.",
            "Packaging rapi dan bersih. Kualitas rasa terjaga."
        ];

        $emotions = ["😋 Lezat", "🔥 Pedas Mantap", "👍 Recommended", "❤️ Suka Banget", "📦 Porsi Besar"];

        // Acak jumlah review per produk (3 s.d 6 review)
        $review_count = rand(3, 6);

        for ($i = 0; $i < $review_count; $i++) {
            $name = $names[array_rand($names)] . ' ' . $names[array_rand($names)];
            $content = $comments_positive[array_rand($comments_positive)];
            $rating = rand(8, 10) > 8 ? 5 : 4; // Dominan bintang 5 (80%)
            $emotion = $emotions[array_rand($emotions)]; // Tambahan meta data unik

            $data = [
                'comment_post_ID' => $product_id,
                'comment_author' => $name,
                'comment_author_email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'comment_content' => $content,
                'comment_type' => 'review',
                'comment_date' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')), // Tanggal acak 1 bulan terakhir
                'comment_approved' => 1,
            ];

            $comment_id = wp_insert_comment($data);

            if ($comment_id) {
                update_comment_meta($comment_id, 'rating', $rating);
                update_comment_meta($comment_id, 'k_emotion_badge', $emotion); // Simpan badge emosi
            }
        }
    }

    private function reset_store() {
        // Hapus Produk
        $products = get_posts(['post_type' => 'product', 'numberposts' => -1, 'fields' => 'ids']);
        foreach ($products as $id) wp_delete_post($id, true);

        // Hapus Kategori
        $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'fields' => 'ids']);
        if (!is_wp_error($cats)) foreach ($cats as $id) wp_delete_term($id, 'product_cat');
    }

    private function create_category($name) {
        $term = term_exists($name, 'product_cat');
        if ($term) return is_array($term) ? $term['term_id'] : $term;
        $new = wp_insert_term($name, 'product_cat');
        return !is_wp_error($new) ? $new['term_id'] : false;
    }

    private function get_demo_data() {
        $desc = "Hidangan spesial yang diolah dengan bahan-bahan pilihan berkualitas tinggi. Resep warisan nusantara yang menyajikan cita rasa otentik dalam setiap suapan. Cocok dinikmati kapan saja bersama orang terkasih.";
        
        return [
            ['name' => 'Nasi Goreng Kampung', 'category' => 'Rice', 'price' => 35000, 'desc' => "Nasi goreng tradisional dengan bumbu terasi, disajikan dengan telur mata sapi, ayam suwir, dan kerupuk. " . $desc],
            ['name' => 'Sate Ayam Madura', 'category' => 'Chicken', 'price' => 30000, 'desc' => "Sate ayam empuk dengan bumbu kacang kental khas Madura dan kecap manis. " . $desc],
            ['name' => 'Rendang Sapi Padang', 'category' => 'Beef', 'price' => 45000, 'desc' => "Daging sapi pilihan dimasak perlahan dengan santan dan rempah-rempah hingga meresap sempurna. " . $desc],
            ['name' => 'Soto Betawi Asli', 'category' => 'Soup', 'price' => 38000, 'desc' => "Kuah santan susu yang gurih dengan potongan daging dan jeroan sapi, ditambah emping renyah. " . $desc],
            ['name' => 'Gado-Gado Siram', 'category' => 'Vegetables', 'price' => 25000, 'desc' => "Sayuran segar rebus disiram saus kacang medok, tahu, tempe, dan kerupuk. " . $desc],
            ['name' => 'Mie Goreng Jawa', 'category' => 'Noodles', 'price' => 32000, 'desc' => "Mie telor dimasak dengan anglo arang, memberikan aroma smokey yang khas dan lezat. " . $desc],
            ['name' => 'Ayam Bakar Taliwang', 'category' => 'Chicken', 'price' => 50000, 'desc' => "Ayam kampung muda dibakar dengan bumbu pedas khas Lombok yang menggugah selera. " . $desc],
            ['name' => 'Gurame Asam Manis', 'category' => 'Seafood', 'price' => 85000, 'desc' => "Ikan gurame goreng tepung disiram saus asam manis dengan potongan nanas segar. " . $desc],
            ['name' => 'Es Teler Sultan', 'category' => 'Beverages', 'price' => 25000, 'desc' => "Campuran alpukat, kelapa muda, nangka, dan susu kental manis yang menyegarkan dahaga. " . $desc],
            ['name' => 'Martabak Manis Keju', 'category' => 'Dessert', 'price' => 45000, 'desc' => "Martabak tebal lembut dengan topping keju parut melimpah dan susu kental manis. " . $desc],
            ['name' => 'Pempek Kapal Selam', 'category' => 'Snacks', 'price' => 22000, 'desc' => "Pempek ikan tenggiri isi telur besar, disajikan dengan cuko pedas asam yang segar. " . $desc],
            ['name' => 'Rawon Setan Surabaya', 'category' => 'Soup', 'price' => 40000, 'desc' => "Sup daging sapi kuah hitam kluwek yang gurih, disajikan dengan tauge dan telur asin. " . $desc],
            ['name' => 'Iga Penyet Sambal Ijo', 'category' => 'Beef', 'price' => 55000, 'desc' => "Iga sapi goreng empuk dipenyet dengan sambal cabai hijau yang pedas dan segar. " . $desc],
            ['name' => 'Kwetiau Sapi Pontianak', 'category' => 'Noodles', 'price' => 38000, 'desc' => "Kwetiau beras dimasak dengan daging sapi dan sayuran, rasa gurih asin yang pas. " . $desc],
            ['name' => 'Udang Saus Padang', 'category' => 'Seafood', 'price' => 65000, 'desc' => "Udang segar dimasak dengan saus padang yang pedas, manis, dan gurih kental. " . $desc],
            ['name' => 'Es Pisang Ijo', 'category' => 'Dessert', 'price' => 18000, 'desc' => "Pisang rajaibalut adonan hijau pandan, disajikan dengan bubur sumsum lembut. " . $desc],
            ['name' => 'Kopi Tubruk Gayo', 'category' => 'Beverages', 'price' => 15000, 'desc' => "Kopi hitam asli Aceh Gayo dengan aroma kuat dan rasa yang nendang. " . $desc],
            ['name' => 'Tahu Gejrot Cirebon', 'category' => 'Snacks', 'price' => 12000, 'desc' => "Tahu pong goreng disiram kuah gula merah pedas asam bawang. " . $desc],
            ['name' => 'Bebek Goreng Crispy', 'category' => 'Chicken', 'price' => 42000, 'desc' => "Bebek ungkep bumbu rempah digoreng garing, disajikan dengan sambal korek. " . $desc],
            ['name' => 'Sop Buntut Spesial', 'category' => 'Soup', 'price' => 75000, 'desc' => "Potongan buntut sapi empuk dalam kuah kaldu bening yang kaya rempah pala dan cengkeh. " . $desc],
            ['name' => 'Nasi Liwet Solo', 'category' => 'Rice', 'price' => 30000, 'desc' => "Nasi gurih santan disajikan dengan suwiran ayam, sayur labu siam, dan areh. " . $desc],
            ['name' => 'Coto Makassar', 'category' => 'Soup', 'price' => 35000, 'desc' => "Soto daging kental khas Makassar dengan bumbu kacang tanah yang gurih. " . $desc],
            ['name' => 'Bakso Urat Jumbo', 'category' => 'Soup', 'price' => 28000, 'desc' => "Bakso daging sapi asli dengan tekstur urat yang kenyal dan kuah kaldu bening. " . $desc],
            ['name' => 'Siomay Bandung', 'category' => 'Snacks', 'price' => 20000, 'desc' => "Siomay ikan tenggiri dikukus, disajikan dengan bumbu kacang, kecap, dan jeruk limau. " . $desc],
            ['name' => 'Es Cendol Dawet', 'category' => 'Beverages', 'price' => 15000, 'desc' => "Minuman segar santan gula merah dengan butiran cendol tepung beras yang kenyal. " . $desc],
            ['name' => 'Kerak Telor Betawi', 'category' => 'Snacks', 'price' => 25000, 'desc' => "Omelet beras ketan dan telur bebek dengan serundeng kelapa sangrai yang gurih. " . $desc],
            ['name' => 'Ayam Betutu Bali', 'category' => 'Chicken', 'price' => 55000, 'desc' => "Ayam utuh dimasak dengan bumbu base genep khas Bali yang kaya rempah pedas. " . $desc],
            ['name' => 'Lontong Sayur Medan', 'category' => 'Rice', 'price' => 28000, 'desc' => "Lontong dengan kuah santan tauco, udang, telur balado, dan kerupuk merah. " . $desc],
            ['name' => 'Kepiting Lada Hitam', 'category' => 'Seafood', 'price' => 120000, 'desc' => "Kepiting segar dimasak dengan saus lada hitam yang pedas hangat dan gurih. " . $desc],
            ['name' => 'Klepon Gula Merah', 'category' => 'Dessert', 'price' => 10000, 'desc' => "Kue bola ketan hijau isi gula merah cair dan taburan kelapa parut. " . $desc]
        ];
    }
}