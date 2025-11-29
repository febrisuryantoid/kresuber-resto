export class CartManager {
    constructor() { 
        this.items = []; 
        this.taxRate = 10; 
    }
    
    // Method Init (Optional)
    init(taxRate) { this.taxRate = taxRate || 10; }
    
    // ADD: Mendukung penambahan dengan Qty spesifik (Default 1)
    add(product, quantity = 1) {
        const existing = this.items.find(i => i.id === product.id);
        if (existing) {
            existing.qty += quantity;
        } else {
            // Pastikan struktur data aman
            this.items.push({ 
                id: product.id, 
                name: product.name || 'Produk',
                price: parseFloat(product.price) || 0,
                image: product.image || '',
                qty: quantity 
            });
        }
        
        // PENTING: Sync ke WooCommerce setiap kali cart berubah
        // Agar saat user pindah ke halaman /cart/, isinya sama
        this.syncWithWooCommerce(); 
        
        return this.getTotals();
    }
    
    // UPDATE: Ubah quantity (+/-)
    updateQty(id, delta) {
        const item = this.items.find(i => i.id === id);
        if (!item) return this.getTotals();
        
        item.qty += delta;
        if (item.qty <= 0) {
            this.items = this.items.filter(i => i.id !== id);
        }
        
        this.syncWithWooCommerce();
        return this.getTotals();
    }
    
    // REMOVE: Hapus item
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
        this.syncWithWooCommerce();
        return this.getTotals();
    }
    
    // CLEAR: Kosongkan keranjang
    clear() {
        this.items = [];
        this.syncWithWooCommerce();
        return this.getTotals();
    }
    
    // CALC: Hitung total
    getTotals() {
        const subtotal = this.items.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const tax = subtotal * (this.taxRate / 100);
        return { 
            items: this.items, 
            subtotal: subtotal, 
            tax: tax, 
            total: subtotal + tax 
        };
    }

    // SYNC: Kirim data cart ke server (PHP Session)
    syncWithWooCommerce() {
        if (typeof KRESUBER === 'undefined') return;

        // Gunakan Promise agar bisa di-await jika perlu
        return jQuery.post(KRESUBER.ajax_url, {
            action: 'kresuber_sync_cart',
            nonce: KRESUBER.nonce,
            items: JSON.stringify(this.items)
        });
    }

    // CHECKOUT: Proses order dan dapatkan Payment URL
    async checkout(tableNo, diningType) {
        if (this.items.length === 0) throw new Error("Keranjang kosong!");

        return new Promise((resolve, reject) => {
            jQuery.post(KRESUBER.ajax_url, {
                action: 'kresuber_process_order', // Pastikan nama action sama dengan di PHP
                nonce: KRESUBER.nonce,
                items: JSON.stringify(this.items),
                table_no: tableNo,
                dining_type: diningType
            })
            .done(response => {
                if (response.success) {
                    this.clear(); // Bersihkan cart lokal setelah sukses
                    resolve(response.data); // Return data (termasuk payment_url)
                } else {
                    reject(response.data.message || 'Terjadi kesalahan server.');
                }
            })
            .fail(() => {
                reject('Koneksi gagal.');
            });
        });
    }
}