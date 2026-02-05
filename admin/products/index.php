<?php
require_once __DIR__ . '/../../config/auth.php';

$auth = new Auth();
$auth->requireAdmin();

$user = $auth->getCurrentUser();

// Get all products with category info
require_once __DIR__ . '/../../config/db.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - RetailRow Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .add-btn {
            background: var(--jumia-orange);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .add-btn:hover {
            background: #e67e0e;
        }

        .products-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background: var(--jumia-light-gray);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--jumia-dark);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-gray);
        }

        th {
            background: var(--jumia-light-gray);
            font-weight: 600;
            color: var(--jumia-dark);
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
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

        .action-btn {
            background: var(--jumia-orange);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            margin-right: 5px;
        }

        .action-btn:hover {
            background: #e67e0e;
        }

        .delete-btn {
            background: var(--jumia-red);
        }

        .delete-btn:hover {
            background: #d01400;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--jumia-orange);
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
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
                    <form method="POST" action="../logout.php" style="display: inline;">
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <a href="../dashboard.php" class="back-link">← Back to Dashboard</a>

        <div class="page-header">
            <h1>Manage Products</h1>
            <a href="add.php" class="add-btn">Add New Product</a>
        </div>

        <div class="products-table">
            <div class="table-header">Products (<?php echo count($products); ?>)</div>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="../../<?php echo htmlspecialchars($product['image']); ?>" alt="Product" class="product-image">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: var(--border-gray); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--jumia-gray);">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $product['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $product['id']; ?>" class="action-btn">Edit</a>
                            <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="action-btn delete-btn">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                try {
                    const response = await fetch(`../../api/products.php`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('Product deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error deleting product');
                    console.error(error);
                }
            }
        }
    </script>
</body>
</html>