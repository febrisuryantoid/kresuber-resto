export class CartManager {
    constructor() { this.items = []; this.taxRate = 10; }
    init(taxRate) { this.taxRate = taxRate || 10; }
    
    add(product) {
        const existing = this.items.find(i => i.id === product.id);
        if (existing) existing.qty++;
        else this.items.push({ ...product, qty: 1 });
        this.syncWithWooCommerce();
        return this.getTotals();
    }
    
    updateQty(id, delta) {
        const item = this.items.find(i => i.id === id);
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) this.items = this.items.filter(i => i.id !== id);
        this.syncWithWooCommerce();
        return this.getTotals();
    }
    
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
        this.syncWithWooCommerce();
        return this.getTotals();
    }
    
    getTotals() {
        const subtotal = this.items.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const tax = subtotal * (this.taxRate / 100);
        return { items: this.items, subtotal, tax, total: subtotal + tax };
    }

    syncWithWooCommerce() {
        if (typeof KRESUBER === 'undefined') return;
        jQuery.post(KRESUBER.ajax_url, {
            action: 'kresuber_sync_cart',
            nonce: KRESUBER.nonce,
            items: JSON.stringify(this.items)
        }).done(res => {
            if(!res.success) console.error("Cart sync failed.");
        }).fail(() => console.error("Cart sync request failed."));
    }
}