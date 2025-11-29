<?php
/**
 * Single Product Immersive UI with Modern Reviews
 * Location: templates/single-product-shell.php
 */
defined( 'ABSPATH' ) || exit;

$product_id = get_query_var('product_id');
$product = wc_get_product($product_id);

if (!$product) { wp_safe_redirect( home_url('/app') ); exit; }
$image = wp_get_attachment_image_url($product->get_image_id(), 'full') ?: 'https://placehold.co/600x800';

// Ambil Data Ulasan
$comments = get_comments([
    'post_id' => $product_id,
    'status' => 'approve',
    'type' => 'review'
]);
$avg_rating = $product->get_average_rating();
$review_count = $product->get_review_count();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?php echo $product->get_name(); ?></title>
    <?php wp_head(); ?>
    <style>
        /* CSS Inline Khusus Halaman Ini (Agar terisolasi dan prioritas tinggi) */
        body { background: #fff; padding-bottom: 90px; }
        
        /* Immersive Image (Tembus Status Bar) */
        .k-p-img-box { 
            width: 100%; height: 45vh; 
            background: #f0f0f0; position: relative; 
            margin-top: -60px; padding-top: 60px; /* Offset status bar */
        }
        .k-p-img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Floating Back Button */
        .k-btn-back-float { 
            position: absolute; top: 40px; left: 20px; z-index: 10; 
            width: 40px; height: 40px; background: rgba(255,255,255,0.9); 
            border-radius: 50%; display: flex; align-items: center; justify-content: center; 
            color: #1a1a1a; font-size: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
            text-decoration: none; 
        }
        
        /* Content Sheet (Rounded Top) */
        .k-p-info-sheet { 
            background: #fff; border-radius: 30px 30px 0 0; 
            padding: 30px 24px 100px; 
            position: relative; top: -30px; margin-bottom: -30px; 
            min-height: 60vh; 
        }
        
        .k-p-title { font-size: 26px; font-weight: 800; margin: 0 0 5px; color:#1a1a1a; }
        .k-p-price { font-size: 24px; font-weight: 800; color: #FF6B00; margin-bottom: 20px; }
        .k-p-desc { color: #666; line-height: 1.6; font-size: 15px; margin-bottom: 30px; }

        /* Review Section Styles */
        .k-review-head { display: flex; align-items: center; margin-bottom: 20px; }
        .k-rating-big { font-size: 40px; font-weight: 800; color: #1a1a1a; margin-right: 10px; }
        .k-rating-stars { color: #FFC107; font-size: 18px; }
        .k-rating-count { color: #888; font-size: 13px; margin-left: 5px; }

        .k-review-list { display: flex; flex-direction: column; gap: 15px; }
        .k-review-card { background: #FAFAFA; border-radius: 16px; padding: 16px; border: 1px solid #eee; }
        
        .k-rev-top { display: flex; align-items: center; margin-bottom: 8px; }
        .k-rev-avatar { width: 36px; height: 36px; border-radius: 50%; background: #FFCCBC; color: #D84315; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 12px; }
        .k-rev-info { flex-grow: 1; }
        .k-rev-name { font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0; }
        .k-rev-date { font-size: 11px; color: #999; }
        .k-rev-stars { color: #FFC107; font-size: 12px; }
        
        .k-rev-text { font-size: 13px; color: #444; line-height: 1.5; margin: 8px 0 0; }
        .k-rev-badge { display: inline-flex; align-items: center; background: #FFF3E0; color: #EF6C00; font-size: 11px; padding: 4px 10px; border-radius: 20px; margin-top: 10px; font-weight: 600; }
        .k-rev-badge i { margin-right: 4px; }

        /* Sticky Action Bar */
        .k-p-action { 
            position: fixed; bottom: 0; left: 0; width: 100%; 
            background: #fff; padding: 15px 20px; 
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05); 
            display: flex; gap: 15px; z-index: 100; box-sizing: border-box;
        }
        
        /* Desktop Center Fix */
        @media(min-width: 768px) { 
            .k-p-action { 
                max-width: 600px; left: 50%; transform: translateX(-50%); border-radius: 12px 12px 0 0; 
            } 
        }
    </style>
</head>
<body class="kresuber-app-mode">
    
    <div class="k-p-img-box">
        <a href="javascript:history.back()" class="k-btn-back-float"><i class="ri-arrow-left-s-line"></i></a>
        <img src="<?php echo esc_url($image); ?>" class="k-p-img" alt="<?php echo esc_attr($product->get_name()); ?>">
    </div>

    <div class="k-p-info-sheet">
        <h1 class="k-p-title"><?php echo $product->get_name(); ?></h1>
        <div class="k-p-price"><?php echo $product->get_price_html(); ?></div>
        
        <div style="height:1px; background:#f0f0f0; margin-bottom:20px;"></div>

        <h3 style="font-size:16px; font-weight:700; margin-bottom:10px;">Deskripsi</h3>
        <div class="k-p-desc"><?php echo apply_filters('the_content', $product->get_description()); ?></div>

        <div style="height:1px; background:#eee; margin:30px 0;"></div>

        <h3 style="font-size:16px; font-weight:700; margin-bottom:15px;">Ulasan Pelanggan</h3>
        
        <div class="k-review-head">
            <span class="k-rating-big"><?php echo number_format($avg_rating, 1); ?></span>
            <div>
                <div class="k-rating-stars">
                    <?php for($i=1; $i<=5; $i++) echo ($i <= round($avg_rating)) ? '<i class="ri-star-fill"></i>' : '<i class="ri-star-line" style="color:#ddd;"></i>'; ?>
                </div>
                <div class="k-rating-count">dari <?php echo $review_count; ?> ulasan</div>
            </div>
        </div>

        <div class="k-review-list">
            <?php if ( $comments ) : foreach ( $comments as $comment ) : 
                $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
                $emotion = get_comment_meta( $comment->comment_ID, 'k_emotion_badge', true ) ?: '👍 Mantap';
                $initial = strtoupper(substr($comment->comment_author, 0, 1));
            ?>
                <div class="k-review-card">
                    <div class="k-rev-top">
                        <div class="k-rev-avatar"><?php echo $initial; ?></div>
                        <div class="k-rev-info">
                            <h4 class="k-rev-name"><?php echo $comment->comment_author; ?></h4>
                            <span class="k-rev-date"><?php echo get_comment_date('d M Y', $comment); ?></span>
                        </div>
                        <div class="k-rev-stars">
                            <?php for($i=1; $i<=5; $i++) echo ($i <= $rating) ? '<i class="ri-star-fill"></i>' : '<i class="ri-star-fill" style="color:#ddd;"></i>'; ?>
                        </div>
                    </div>
                    <p class="k-rev-text"><?php echo $comment->comment_content; ?></p>
                    <div class="k-rev-badge"><i class="ri-emotion-happy-line"></i> <?php echo esc_html($emotion); ?></div>
                </div>
            <?php endforeach; else : ?>
                <p style="color:#888;">Belum ada ulasan untuk produk ini.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="k-p-action">
        <div class="k-qty-box" style="display:flex; align-items:center; background:#f5f5f5; border-radius:12px; padding:5px; height:50px;">
            <button class="k-qty-btn" style="width:40px; border:none; background:transparent; font-size:20px; font-weight:bold; cursor:pointer;" onclick="updQty(-1)">-</button>
            <span id="qty-val" style="width:30px; text-align:center; font-weight:700;">1</span>
            <button class="k-qty-btn" style="width:40px; border:none; background:transparent; font-size:20px; font-weight:bold; cursor:pointer;" onclick="updQty(1)">+</button>
        </div>
        <button class="k-btn-add" id="btn-add" onclick="addNow(<?php echo $product_id; ?>)" style="flex-grow:1; background:#FF6B00; color:white; height:50px; border-radius:14px; font-weight:800; font-size:16px; border:none; cursor:pointer; box-shadow:0 5px 15px rgba(255,107,0,0.3);">
            Tambah - <span id="price-total"><?php echo $product->get_price(); ?></span>
        </button>
    </div>

    <script>
        let qty = 1; 
        const price = <?php echo $product->get_price() ?: 0; ?>;
        
        function updQty(d) { 
            qty += d; if(qty<1) qty=1; 
            document.getElementById('qty-val').innerText = qty; 
            document.getElementById('price-total').innerText = (price * qty).toLocaleString(); 
        }
        
        function addNow(id) {
            const btn = document.getElementById('btn-add'); 
            btn.innerHTML = 'Menyimpan...';
            
            if(window.triggerAddWithQty) { 
                window.triggerAddWithQty(id, qty).then(() => { 
                    btn.innerHTML = 'Berhasil!'; 
                    setTimeout(() => window.location.href = '<?php echo home_url('/app'); ?>', 500); 
                }); 
            } else {
                // Fallback jika JS belum siap
                alert('Sedang memuat...');
            }
        }
    </script>

    <?php wp_footer(); ?>
</body>
</html>