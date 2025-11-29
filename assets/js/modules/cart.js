export class CartManager {
    constructor() { 
        this.items = []; 
        this.taxRate = 10; 
    }
    
    init(taxRate) { this.taxRate = taxRate || 10; }
    
    // ADD: Tambah Item & Sync
    add(product, quantity = 1) {
        const existing = this.items.find(i => i.id === product.id);
        if (existing) {
            existing.qty += quantity;
        } else {
            this.items.push({ 
                id: product.id, 
                name: product.name || 'Produk',
                price: parseFloat(product.price) || 0,
                image: product.image || '',
                qty: quantity 
            });
        }
        
        // PENTING: Kirim data ke server agar halaman /cart/ sinkron
        this.syncWithWooCommerce(); 
        
        return this.getTotals();
    }
    
    // UPDATE QTY
    updateQty(id, delta) {
        const item = this.items.find(i => i.id === id);
        if (!item) return this.getTotals();
        
        item.qty += delta;
        if (item.qty <= 0) {
            this.items = this.items.filter(i => i.id !== id);
        }
        
        this.syncWithWooCommerce(); // Sync lagi
        return this.getTotals();
    }
    
    // REMOVE ITEM
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
        this.syncWithWooCommerce(); // Sync lagi
        return this.getTotals();
    }
    
    // CLEAR CART
    clear() {
        this.items = [];
        this.syncWithWooCommerce(); // Sync lagi (kosongkan server)
        return this.getTotals();
    }
    
    getTotals() {
        const subtotal = this.items.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const tax = subtotal * (this.taxRate / 100);
        return { items: this.items, subtotal, tax, total: subtotal + tax };
    }

    // FUNGSI SINKRONISASI KE SERVER
    syncWithWooCommerce() {
        if (typeof KRESUBER === 'undefined') return;

        // Kita gunakan jQuery post sederhana tanpa blocking UI
        jQuery.post(KRESUBER.ajax_url, {
            action: 'kresuber_sync_cart',
            nonce: KRESUBER.nonce,
            items: JSON.stringify(this.items)
        }).done(function(res) {
            if(res.success) {
                console.log("Cart synced:", res.data.count);
            }
        });
    }

    // CHECKOUT PROSES
    async checkout(tableNo, diningType) {
        if (this.items.length === 0) throw new Error("Keranjang kosong!");

        return new Promise((resolve, reject) => {
            jQuery.post(KRESUBER.ajax_url, {
                action: 'kresuber_process_order',
                nonce: KRESUBER.nonce,
                items: JSON.stringify(this.items),
                table_no: tableNo,
                dining_type: diningType
            })
            .done(response => {
                if (response.success) {
                    this.clear(); // Bersihkan cart lokal
                    resolve(response.data);
                } else {
                    reject(response.data.message || 'Error server.');
                }
            })
            .fail(() => {
                reject('Koneksi gagal.');
            });
        });
    }
}