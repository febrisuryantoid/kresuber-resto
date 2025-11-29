<?php
/**
 * Custom View: POS Cart Panel
 * Used by templates/app-shell.php
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="pos-cart-panel">
    <div class="pos-cart-header">
        <h2>Order #<span id="k-order-id">20</span></h2>
        <div class="pos-cart-dining-options">
            <select id="k-select-table">
                <option value="">Pilih Meja</option>
                <option value="1">Meja 1</option>
                <option value="2">Meja 2</option>
                <option value="3">Meja 3</option>
            </select>
            <select id="k-select-dining-type">
                <option value="dine_in">Dine In</option>
                <option value="take_away">Take Away</option>
            </select>
        </div>
    </div>

    <div id="k-cart-list" class="pos-cart-list">
        <!-- Cart items will be rendered here by JavaScript -->
        <div style="text-align: center; padding: 20px; color: #888;">Keranjang kosong. Tambahkan produk!</div>
    </div>

    <div class="pos-cart-bill">
        <div class="pos-bill-row">
            <span>Subtotal</span>
            <span id="k-subtotal">Rp 0</span>
        </div>
        <div class="pos-bill-row">
            <span>Pajak (10%)</span>
            <span id="k-tax">Rp 0</span>
        </div>
        <div class="pos-bill-row total">
            <span>Total</span>
            <span id="k-total">Rp 0</span>
        </div>

        <div class="pos-bill-actions">
            <button class="btn-action btn-kot-print">KOT & Print</button>
            <button class="btn-action btn-draft">Draft</button>
            <button class="btn-action btn-bill-payment btn-primary">Bill & Payment</button>
            <button class="btn-action btn-bill-print btn-success">Bill & Print</button>
        </div>
    </div>
</div>
