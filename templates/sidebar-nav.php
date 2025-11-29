<div class="pos-sidebar">
    <div class="pos-logo">
        <img src="<?php echo KRESUBER_URL . 'assets/images/kresuber-logo.png'; ?>" alt="Kresuber Resto" style="max-height: 50px;">
    </div>
    <nav class="pos-nav-menu">
        <ul>
            <li>
                <a href="#" class="nav-link active" data-nav="pos-main">
                    <i class="ri-dashboard-line"></i> POS Terminal
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-nav="table-management">
                    <i class="ri-table-line"></i> Table Management
                </a>
            </li>
            <li>
                <a href="#" class="nav-link" data-nav="orders-history">
                    <i class="ri-file-list-3-line"></i> Orders
                </a>
            </li>

            <li style="margin: 15px 20px; border-top: 1px solid #eee;"></li>

            <li>
                <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" target="_blank">
                    <i class="ri-restaurant-line"></i> Menu Management <i class="ri-external-link-line" style="font-size:12px; margin-left:auto;"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo admin_url('edit.php?post_type=shop_coupon'); ?>" target="_blank">
                    <i class="ri-coupon-4-line"></i> Discounts <i class="ri-external-link-line" style="font-size:12px; margin-left:auto;"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo admin_url('admin.php?page=wc-settings'); ?>" target="_blank">
                    <i class="ri-settings-4-line"></i> Settings <i class="ri-external-link-line" style="font-size:12px; margin-left:auto;"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>