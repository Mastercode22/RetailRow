<?php
require_once __DIR__ . '/../../config/auth.php';

$auth = new Auth();
$auth->requireAdmin();

$user = $auth->getCurrentUser();

// Get all categories
require_once __DIR__ . '/../../config/db.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM categories ORDER BY position ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - RetailRow Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
.message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
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

        .categories-table {
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

        .category-icon {
            width: 40px;
            height: 40px;
            object-fit: contain;
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
            <h1>Manage Categories</h1>
            <a href="add.php" class="add-btn">Add New Category</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="message success" style="margin-bottom: 20px;">
                <?php
                if ($_GET['success'] == 1) echo 'Category added successfully!';
                if ($_GET['success'] == 2) echo 'Category updated successfully!';
                ?>
            </div>
        <?php endif; ?>

        <div class="categories-table">
            <div class="table-header">Categories (<?php echo count($categories); ?>)</div>
            <table>
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <?php if ($category['icon']): ?>
                                <img src="../../<?php echo htmlspecialchars($category['icon']); ?>" alt="Icon" class="category-icon">
                            <?php else: ?>
                                <div style="width: 40px; height: 40px; background: var(--border-gray); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--jumia-gray);">No Icon</div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo $category['position']; ?></td>
                        <td>
                            <span class="status-badge <?php echo $category['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $category['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $category['id']; ?>" class="action-btn">Edit</a>
                            <button onclick="deleteCategory(<?php echo $category['id']; ?>)" class="action-btn delete-btn">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category? This will affect all products in this category.')) {
                try {
                    const response = await fetch(`../../api/categories.php`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ id: id })
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('Category deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Error deleting category');
                    console.error(error);
                }
            }
        }
    </script>
</body>
</html>