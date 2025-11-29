<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal</title>
    <?php wp_head(); ?>
    <style>
        /* Simple styling for new views */
        .pos-view-section { display: none; }
        .pos-view-section.active { display: block; }
        
        /* Table Grid Style */
        .table-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; }
        .table-card { background: white; padding: 20px; border-radius: 12px; text-align: center; border: 2px solid #eee; cursor: pointer; transition: 0.2s; }
        .table-card:hover { border-color: var(--k-primary); }
        .table-card.selected { background: var(--k-primary); color: white; border-color: var(--k-primary); }

        /* Order List Style */
        .order-history-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        .order-history-table th, .order-history-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        .order-history-table th { background: #f8f9fd; font-weight: 600; }
    </style>
</head>
<body id="pos-app-wrapper" class="kresuber-app-mode">
    <?php include KRESUBER_PATH . 'templates/sidebar-nav.php'; ?>
    
    <main class="pos-main-content">
        
        <div id="view-pos-main" class="pos-view-section active">
            <div class="pos-catalog-header">
                <div class="pos-search-bar">
                    <i class="ri-search-line"></i>
                    <input type="text" id="k-search" placeholder="Cari menu...">
                </div>
                <select class="pos-category-dropdown">
                    <option value="all">Semua Kategori</option>
                </select>
                <button onclick="location.reload()" style="border:none; background:white; padding:10px; border-radius:8px; cursor:pointer;"><i class="ri-refresh-line"></i></button>
            </div>
            
            <div class="pos-category-tabs">
                </div>

            <div id="k-grid" class="pos-product-grid">
                <div style="grid-column:1/-1; text-align:center; padding:50px;">
                    <i class="ri-loader-4-line ri-spin" style="font-size:30px; color:var(--k-primary);"></i>
                </div>
            </div>
        </div>

        <div id="view-table-management" class="pos-view-section">
            <h2 style="margin-bottom: 20px;">Manajemen Meja</h2>
            <p style="color:#666; margin-bottom:20px;">Pilih meja untuk melihat status atau menetapkan pesanan.</p>
            <div id="k-table-grid" class="table-grid">
                </div>
        </div>

        <div id="view-orders-history" class="pos-view-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0;">Riwayat Pesanan</h2>
                <button id="btn-refresh-orders" class="k-btn-primary" style="padding: 8px 15px; font-size:14px;">Refresh</button>
            </div>
            <div style="overflow-x:auto;">
                <table class="order-history-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Tanggal</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="k-orders-list-body">
                        <tr><td colspan="5" style="text-align:center;">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <?php include KRESUBER_PATH . 'templates/views/view-cart.php'; ?>

    <?php wp_footer(); ?>
</body>
</html>