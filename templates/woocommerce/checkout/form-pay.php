<?php
/**
 * Forced Mobile UI Checkout
 * Location: templates/woocommerce/checkout/form-pay.php
 */

defined( 'ABSPATH' ) || exit;

// Ambil data order
$order_id = $order->get_id();
$order_total = $order->get_formatted_order_total();
$table_no = $order->get_meta('_kresuber_table_no') ?: 'Unknown';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Checkout - Order #<?php echo $order_id; ?></title>
    <?php wp_head(); ?>
    
    <style>
        /* RESET & BASE STYLES (Memaksa Tampilan Mobile) */
        :root { --k-orange: #FF6B00; --k-bg: #F8F9FD; --k-text: #1C1C1E; --k-gray: #9FA1A6; }
        body, html { margin: 0; padding: 0; background-color: var(--k-bg) !important; font-family: 'Plus Jakarta Sans', sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important; -webkit-font-smoothing: antialiased; }
        
        /* Container Utama (Tengah Layar) */
        .k-checkout-layout { max-width: 480px; margin: 0 auto; background: var(--k-bg); min-height: 100vh; position: relative; padding-bottom: 120px; /* Ruang untuk tombol bawah */ }
        
        /* HEADER (Back Button & Title) */
        .k-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; background: transparent; position: sticky; top: 0; z-index: 10; backdrop-filter: blur(5px); }
        .k-btn-back { width: 40px; height: 40px; background: #fff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--k-text); text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.02); border: 1px solid #eee; transition: 0.2s; }
        .k-btn-back:active { transform: scale(0.95); }
        .k-page-title { font-size: 18px; font-weight: 700; color: var(--k-text); margin: 0; }
        .k-icon-bag { font-size: 24px; color: var(--k-text); }

        /* SECTION TITLES */
        .k-section { padding: 0 24px; margin-bottom: 24px; }
        .k-section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .k-sec-title { font-size: 16px; font-weight: 700; color: var(--k-text); margin: 0; }
        .k-link-add { color: var(--k-orange); font-size: 14px; font-weight: 600; text-decoration: none; }

        /* CARD STYLE (Putih, Rounded) */
        .k-card { background: #fff; border-radius: 20px; padding: 16px; display: flex; align-items: center; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        
        /* ADDRESS / TABLE CARD */
        .k-loc-icon { width: 48px; height: 48px; border-radius: 14px; background: #FFF0E6; display: flex; align-items: center; justify-content: center; color: var(--k-orange); font-size: 22px; flex-shrink: 0; margin-right: 16px; }
        .k-loc-info { flex-grow: 1; }
        .k-loc-label { display: block; font-size: 15px; font-weight: 700; color: var(--k-text); margin-bottom: 4px; }
        .k-loc-desc { font-size: 13px; color: var(--k-gray); margin: 0; line-height: 1.4; }
        .k-btn-edit { color: #4CAF50; font-size: 22px; margin-left: 10px; }

        /* PAYMENT METHOD LIST (CUSTOM RADIO) */
        ul.wc_payment_methods { list-style: none !important; padding: 0 !important; margin: 0 !important; }
        li.wc_payment_method { margin-bottom: 12px !important; position: relative; }
        
        /* Sembunyikan Radio Button Asli */
        li.wc_payment_method input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        
        /* Label Payment (Kartu) */
        li.wc_payment_method label {
            display: flex !important; align-items: center; justify-content: space-between;
            background: #fff; border-radius: 20px; padding: 16px 20px;
            cursor: pointer; transition: all 0.2s ease;
            border: 2px solid transparent; width: 100%; box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        /* Icon & Text Pembayaran */
        li.wc_payment_method label img { max-height: 24px; margin-right: 15px; order: -1; } /* Gambar di kiri */
        li.wc_payment_method label .payment_method_title { font-weight: 600; font-size: 15px; color: var(--k-text); flex-grow: 1; }

        /* LINGKARAN CHECK (Kanan) - State: Unchecked */
        li.wc_payment_method label::after {
            content: ''; width: 22px; height: 22px; border-radius: 50%;
            border: 2px solid #E0E0E0; display: block; flex-shrink: 0;
            transition: all 0.2s;
        }

        /* State: Checked (Warna Oranye + Centang) */
        li.wc_payment_method input[type="radio"]:checked + label { border-color: transparent; } /* Hapus border card jika desain bersih */
        li.wc_payment_method input[type="radio"]:checked + label::after {
            background-color: var(--k-orange); border-color: var(--k-orange);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='14' height='14' fill='white'%3E%3Cpath d='M10 15.172l9.192-9.193 1.415 1.414L10 18l-6.364-6.364 1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: center;
        }

        /* Deskripsi Pembayaran (Hidden secara default agar rapi) */
        .payment_box { display: none !important; } 

        /* BOTTOM FLOATING BUTTON */
        .k-bottom-bar {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: #fff; padding: 16px 24px 30px 24px;
            box-shadow: 0 -10px 40px rgba(0,0,0,0.05); z-index: 100;
            border-radius: 24px 24px 0 0;
            display: flex; justify-content: center;
        }
        
        /* Tombol Confirm (Besar & Oranye) */
        .k-btn-confirm {
            background: var(--k-orange); color: white; border: none;
            width: 100%; max-width: 440px; padding: 18px;
            font-size: 16px; font-weight: 700; border-radius: 16px;
            cursor: pointer; box-shadow: 0 10px 20px rgba(255, 107, 0, 0.25);
            transition: transform 0.2s; text-align: center;
        }
        .k-btn-confirm:active { transform: scale(0.98); }

        /* HIDE WOOCOMMERCE ELEMENTS */
        .woocommerce-error { background: #FFEBEE; color: #D32F2F; list-style: none; margin: 0 24px 20px; padding: 15px; border-radius: 12px; font-size: 13px; }
        .woocommerce-info { background: #E3F2FD; color: #1976D2; list-style: none; margin: 0 24px 20px; padding: 15px; border-radius: 12px; font-size: 13px; }
        
        /* Icon Font (Remix Icon) */
        @import url('https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css');
    </style>
</head>
<body>

<div class="k-checkout-layout">
    
    <div class="k-header">
        <a href="javascript:history.back()" class="k-btn-back"><i class="ri-arrow-left-s-line" style="font-size:24px;"></i></a>
        <h1 class="k-page-title">Checkout</h1>
        <i class="ri-shopping-bag-3-line k-icon-bag"></i>
    </div>

    <?php wc_print_notices(); ?>

    <form id="order_review" method="post">

        <div class="k-section">
            <div class="k-section-head">
                <h3 class="k-sec-title">Dining Location</h3>
                </div>
            <div class="k-card">
                <div class="k-loc-icon"><i class="ri-map-pin-user-fill"></i></div>
                <div class="k-loc-info">
                    <span class="k-loc-label">Meja: <?php echo esc_html($table_no); ?></span>
                    <p class="k-loc-desc">Order #<?php echo $order_id; ?> • <?php echo date_i18n('d M Y, H:i'); ?></p>
                </div>
                <div class="k-btn-edit"><i class="ri-checkbox-circle-fill"></i></div>
            </div>
        </div>

        <div class="k-section">
            <div class="k-section-head">
                <h3 class="k-sec-title">Payment Method</h3>
                <span class="k-link-add">Choose One</span>
            </div>

            <div id="payment" class="woocommerce-checkout-payment">
                <?php if ( $order->needs_payment() ) : ?>
                    <ul class="wc_payment_methods payment_methods methods">
                        <?php
                        if ( ! empty( $available_gateways ) ) {
                            foreach ( $available_gateways as $gateway ) {
                                wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
                            }
                        } else {
                            echo '<li class="woocommerce-notice">Tidak ada metode pembayaran tersedia.</li>';
                        }
                        ?>
                    </ul>
                <?php endif; ?>
                
                <div class="form-row" style="display:none;">
                    <input type="hidden" name="woocommerce_pay" value="1" />
                    <?php wc_get_template( 'checkout/terms.php' ); ?>
                    <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>
                    <?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); ?>
                    <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>
                    <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
                </div>
            </div>
        </div>

        <div class="k-bottom-bar">
            <button type="submit" class="k-btn-confirm" onclick="document.getElementById('place_order').click(); return false;">
                Confirm Payment - <?php echo $order_total; ?>
            </button>
        </div>

    </form>
</div>

<?php wp_footer(); ?>

<script>
    // JS Helper untuk memastikan styling radio button bekerja sempurna
    jQuery(document).ready(function($){
        // Klik pada seluruh area label/card akan memicu radio button
        $('li.wc_payment_method label').on('click', function(){
            // Hapus style dari semua, tambahkan ke yang aktif (Opsional, CSS :checked sudah menangani ini)
        });
    });
</script>

</body>
</html>