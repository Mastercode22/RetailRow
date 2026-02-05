<?php
require_once '../../config/auth.php';
require_once '../../config/db.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $banner_id = intval($_GET['delete']);

    try {
        // Get banner data for image deletion
        $stmt = $conn->prepare("SELECT image FROM banners WHERE id = ?");
        $stmt->execute([$banner_id]);
        $banner = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($banner) {
            // Delete the image file if it exists
            if ($banner['image'] && file_exists('../../' . $banner['image'])) {
                unlink('../../' . $banner['image']);
            }

            // Delete from database
            $stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
            $stmt->execute([$banner_id]);

            $success_message = "Banner deleted successfully!";
        }
    } catch (Exception $e) {
        $error_message = "Error deleting banner: " . $e->getMessage();
    }
}

// Handle toggle visibility
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $banner_id = intval($_GET['toggle']);

    try {
        $stmt = $conn->prepare("UPDATE banners SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$banner_id]);
        $success_message = "Banner visibility updated!";
    } catch (Exception $e) {
        $error_message = "Error updating banner: " . $e->getMessage();
    }
}

// Fetch all banners
try {
    $stmt = $conn->query("SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $banners = [];
    $error_message = "Error loading banners: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Management - RetailRow Admin</title>
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

        .banners-table {
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

        .banner-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .banner-item:last-child {
            border-bottom: none;
        }

        .banner-image {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 20px;
        }

        .banner-info {
            flex: 1;
        }

        .banner-title {
            font-weight: 600;
            color: var(--jumia-dark);
            margin-bottom: 5px;
        }

        .banner-meta {
            color: var(--jumia-gray);
            font-size: 14px;
        }

        .banner-actions {
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
                <li><a href="index.php" class="active">Banners</a></li>
                <li><a href="../flash-sales/">Flash Sales</a></li>
                <li><a href="../settings/">Settings</a></li>
                <li><a href="../orders/">Orders</a></li>
                <li><a href="../users/">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Banner Management</h1>
                <a href="add.php" class="add-btn">Add New Banner</a>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (empty($banners)): ?>
                <div class="empty-state">
                    <h3>No banners found</h3>
                    <p>Create your first banner to showcase promotions and announcements.</p>
                    <a href="add.php" class="add-btn">Add First Banner</a>
                </div>
            <?php else: ?>
                <div class="banners-table">
                    <div class="table-header">All Banners (<?php echo count($banners); ?>)</div>

                    <?php foreach ($banners as $banner): ?>
                        <div class="banner-item">
                            <img src="../../<?php echo htmlspecialchars($banner['image']); ?>"
                                 alt="<?php echo htmlspecialchars($banner['title']); ?>" class="banner-image">

                            <div class="banner-info">
                                <div class="banner-title"><?php echo htmlspecialchars($banner['title']); ?></div>
                                <div class="banner-meta">
                                    Sort Order: <?php echo $banner['sort_order']; ?> |
                                    Status: <span style="color: <?php echo $banner['is_active'] ? '#28a745' : '#6c757d'; ?>">
                                        <?php echo $banner['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span> |
                                    Created: <?php echo date('M j, Y', strtotime($banner['created_at'])); ?>
                                </div>
                            </div>

                            <div class="banner-actions">
                                <a href="?toggle=<?php echo $banner['id']; ?>" class="action-btn toggle-btn <?php echo !$banner['is_active'] ? 'inactive' : ''; ?>">
                                    <?php echo $banner['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                </a>
                                <a href="edit.php?id=<?php echo $banner['id']; ?>" class="action-btn edit-btn">Edit</a>
                                <a href="?delete=<?php echo $banner['id']; ?>" class="action-btn delete-btn"
                                   onclick="return confirm('Are you sure you want to delete this banner?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>