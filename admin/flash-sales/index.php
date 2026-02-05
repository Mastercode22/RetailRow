<?php
require_once '../../config/auth.php';
require_once '../../config/db.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $flash_sale_id = intval($_GET['delete']);

    try {
        $stmt = $conn->prepare("DELETE FROM flash_sales WHERE id = ?");
        $stmt->execute([$flash_sale_id]);
        $success_message = "Flash sale deleted successfully!";
    } catch (Exception $e) {
        $error_message = "Error deleting flash sale: " . $e->getMessage();
    }
}

// Handle toggle active status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $flash_sale_id = intval($_GET['toggle']);

    try {
        $stmt = $conn->prepare("UPDATE flash_sales SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$flash_sale_id]);
        $success_message = "Flash sale status updated!";
    } catch (Exception $e) {
        $error_message = "Error updating flash sale: " . $e->getMessage();
    }
}

// Fetch all flash sales
try {
    $stmt = $conn->query("SELECT fs.*, p.name as product_name FROM flash_sales fs LEFT JOIN products p ON fs.product_id = p.id ORDER BY fs.created_at DESC");
    $flash_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $flash_sales = [];
    $error_message = "Error loading flash sales: " . $e->getMessage();
}

// Fetch products for dropdown
try {
    $stmt = $conn->query("SELECT id, name FROM products ORDER BY name ASC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flash Sales Management - RetailRow Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 250px;
            background: var(--jumia-dark);
            color: white;
            padding: 20px 0;
        }

        .admin-sidebar h2 {
            padding: 0 20px;
            margin-bottom: 30px;
            font-size: 18px;
        }

        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .admin-sidebar li {
            margin-bottom: 5px;
        }

        .admin-sidebar a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: background 200ms;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: rgba(255, 255, 255, 0.1);
        }

        .admin-main {
            flex: 1;
            padding: 30px;
            background: #f8f9fa;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .admin-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--jumia-dark);
            margin: 0;
        }

        .add-btn {
            background: var(--jumia-orange);
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background 200ms;
        }

        .add-btn:hover {
            background: #e67e0e;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .flash-sales-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: var(--jumia-dark);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
        }

        .flash-sale-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-gray);
        }

        .flash-sale-item:last-child {
            border-bottom: none;
        }

        .sale-info {
            flex: 1;
        }

        .sale-title {
            font-weight: 600;
            color: var(--jumia-dark);
            margin-bottom: 5px;
        }

        .sale-meta {
            color: var(--jumia-gray);
            font-size: 14px;
        }

        .sale-discount {
            background: var(--jumia-orange);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }

        .sale-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-expired {
            background: #fff3cd;
            color: #856404;
        }

        .sale-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 200ms;
        }

        .edit-btn {
            background: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background: #0056b3;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        .toggle-btn {
            background: #28a745;
            color: white;
        }

        .toggle-btn.inactive {
            background: #6c757d;
        }

        .toggle-btn:hover {
            opacity: 0.8;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .empty-state h3 {
            color: var(--jumia-dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--jumia-gray);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2>RetailRow Admin</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../products/">Products</a></li>
                <li><a href="../categories/">Categories</a></li>
                <li><a href="../banners/">Banners</a></li>
                <li><a href="index.php" class="active">Flash Sales</a></li>
                <li><a href="../settings/">Settings</a></li>
                <li><a href="../orders/">Orders</a></li>
                <li><a href="../users/">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Flash Sales Management</h1>
                <a href="add.php" class="add-btn">Add New Flash Sale</a>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (empty($flash_sales)): ?>
                <div class="empty-state">
                    <h3>No flash sales found</h3>
                    <p>Create flash sales to offer limited-time discounts on your products.</p>
                    <a href="add.php" class="add-btn">Create First Flash Sale</a>
                </div>
            <?php else: ?>
                <div class="flash-sales-table">
                    <div class="table-header">All Flash Sales (<?php echo count($flash_sales); ?>)</div>

                    <?php foreach ($flash_sales as $sale): ?>
                        <?php
                        $now = new DateTime();
                        $end_date = new DateTime($sale['end_date']);
                        $is_expired = $now > $end_date;
                        $status_class = $is_expired ? 'status-expired' : ($sale['is_active'] ? 'status-active' : 'status-inactive');
                        $status_text = $is_expired ? 'Expired' : ($sale['is_active'] ? 'Active' : 'Inactive');
                        ?>
                        <div class="flash-sale-item">
                            <div class="sale-info">
                                <div class="sale-title">
                                    <?php echo htmlspecialchars($sale['title']); ?>
                                    <span class="sale-discount">-<?php echo $sale['discount_percentage']; ?>%</span>
                                </div>
                                <div class="sale-meta">
                                    Product: <?php echo htmlspecialchars($sale['product_name'] ?? 'N/A'); ?> |
                                    Ends: <?php echo date('M j, Y H:i', strtotime($sale['end_date'])); ?> |
                                    <span class="sale-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </div>
                            </div>

                            <div class="sale-actions">
                                <a href="?toggle=<?php echo $sale['id']; ?>" class="action-btn toggle-btn <?php echo !$sale['is_active'] ? 'inactive' : ''; ?>">
                                    <?php echo $sale['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                </a>
                                <a href="edit.php?id=<?php echo $sale['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="?delete=<?php echo $sale['id']; ?>" class="action-btn delete-btn"
                                   onclick="return confirm('Are you sure you want to delete this flash sale?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>