<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="/RetailRow/admin/dashboard.php" class="sidebar-logo">RetailRow Admin</a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/RetailRow/admin/dashboard.php" class="<?php echo (isset($page) && $page === 'dashboard') ? 'active' : ''; ?>">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="/RetailRow/admin/products/index.php" class="<?php echo (isset($page) && $page === 'products') ? 'active' : ''; ?>">
                    Products
                </a>
            </li>
            <li>
                <a href="/RetailRow/admin/categories/index.php" class="<?php echo (isset($page) && $page === 'categories') ? 'active' : ''; ?>">
                    Categories
                </a>
            </li>
        </ul>
    </nav>
</aside>