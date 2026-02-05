<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/auth.php';

$auth = new Auth();
$auth->requireAdmin();

$user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RetailRow</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-header {
            background: white;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-gray);
            margin-bottom: 30px;
        }

        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--jumia-orange);
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: var(--jumia-red);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 200ms;
        }

        .logout-btn:hover {
            background: #d01400;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 15px;
            background: var(--jumia-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--jumia-dark);
        }

        .card-description {
            color: var(--jumia-gray);
            margin-bottom: 20px;
        }

        .card-link {
            display: inline-block;
            background: var(--jumia-orange);
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background 200ms;
        }

        .card-link:hover {
            background: #e67e0e;
        }

        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--jumia-orange);
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--jumia-gray);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="admin-nav">
                <div class="admin-logo">RetailRow Admin</div>
                <div class="admin-user">
                    <span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span>
                    <form method="POST" action="logout.php" style="display: inline;">
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 style="margin-bottom: 30px; color: var(--jumia-dark);">Dashboard</h1>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-number" id="total-products">0</div>
                <div class="stat-label">Total Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="total-categories">0</div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="active-banners">0</div>
                <div class="stat-label">Active Banners</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="flash-sales">0</div>
                <div class="stat-label">Flash Sales</div>
            </div>
        </div>

        <!-- Management Cards -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon">📦</div>
                <h3 class="card-title">Products</h3>
                <p class="card-description">Manage your product catalog, prices, and inventory</p>
                <a href="products/" class="card-link">Manage Products</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">📂</div>
                <h3 class="card-title">Categories</h3>
                <p class="card-description">Organize products into categories and subcategories</p>
                <a href="categories/" class="card-link">Manage Categories</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">🖼️</div>
                <h3 class="card-title">Banners</h3>
                <p class="card-description">Control hero carousel banners and promotional images</p>
                <a href="banners/" class="card-link">Manage Banners</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">⚡</div>
                <h3 class="card-title">Flash Sales</h3>
                <p class="card-description">Create and manage limited-time discount offers</p>
                <a href="flash-sales/" class="card-link">Manage Flash Sales</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">⚙️</div>
                <h3 class="card-title">Settings</h3>
                <p class="card-description">Configure site settings, announcements, and preferences</p>
                <a href="settings/" class="card-link">Site Settings</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">📊</div>
                <h3 class="card-title">Orders</h3>
                <p class="card-description">View and manage customer orders (future feature)</p>
                <a href="orders/" class="card-link">View Orders</a>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard stats
        async function loadStats() {
            try {
                const [productsRes, categoriesRes, bannersRes, flashSalesRes] = await Promise.all([
                    fetch('../api/products.php'),
                    fetch('../api/categories.php'),
                    fetch('../api/banners.php'),
                    fetch('../api/flash-sales.php')
                ]);

                const products = await productsRes.json();
                const categories = await categoriesRes.json();
                const banners = await bannersRes.json();
                const flashSales = await flashSalesRes.json();

                document.getElementById('total-products').textContent = products.data ? products.data.length : 0;
                document.getElementById('total-categories').textContent = categories.data ? categories.data.length : 0;
                document.getElementById('active-banners').textContent = banners.data ? banners.data.length : 0;
                document.getElementById('flash-sales').textContent = flashSales.data ? flashSales.data.length : 0;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        loadStats();
    </script>
</body>
</html>