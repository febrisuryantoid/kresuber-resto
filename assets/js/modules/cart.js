export class CartManager {
    constructor() { this.items = []; this.taxRate = 10; }
    init(taxRate) { this.taxRate = taxRate || 10; }
    
    add(product) {
        const existing = this.items.find(i => i.id === product.id);
        if (existing) existing.qty++;
        else this.items.push({ ...product, qty: 1 });
        return this.getTotals();
    }
    
    updateQty(id, delta) {
        const item = this.items.find(i => i.id === id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) this.items = this.items.filter(i => i.id !== id);
        return this.getTotals();
    }
    
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
        return this.getTotals();
    }
    
    clear() {
        this.items = [];
        return this.getTotals();
    }
    
    getTotals() {
        const subtotal = this.items.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const tax = subtotal * (this.taxRate / 100);
        return { items: this.items, subtotal, tax, total: subtotal + tax };
    }

    // FUNGSI CHECKOUT
    async checkout(tableNo, diningType) {
        if (this.items.length === 0) {
            throw new Error("Keranjang kosong!");
        }

        return new Promise((resolve, reject) => {
            jQuery.post(KRESUBER.ajax_url, {
                action: 'kresuber_process_order', // Sesuai endpoint API baru
                nonce: KRESUBER.nonce,
                items: JSON.stringify(this.items),
                table_no: tableNo,
                dining_type: diningType
            })
            .done(response => {
                if (response.success) {
                    this.clear(); // Bersihkan keranjang
                    // Resolve dengan SELURUH data (termasuk payment_url)
                    resolve(response.data); 
                } else {
                    reject(response.data.message || 'Terjadi kesalahan server.');
                }
            })
            .fail(err => {
                reject('Koneksi gagal.');
            });
        });
    }
}