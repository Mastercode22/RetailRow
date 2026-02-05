<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->requireAdmin();

$user = $auth->getCurrentUser();

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';
$category = null;

// Get category ID from URL
$category_id = intval($_GET['id'] ?? 0);

if (!$category_id) {
    header('Location: index.php');
    exit();
}

// Fetch category data
$query = "SELECT * FROM categories WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $category_id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = intval($_POST['position'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Handle icon upload
    $icon_path = $category['icon']; // Keep old icon if no new one is uploaded
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../assets/uploads/icons/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Delete old icon if it exists
        if ($icon_path && file_exists(__DIR__ . '/../../' . $icon_path)) {
            unlink(__DIR__ . '/../../' . $icon_path);
        }

        $file_extension = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
        $file_name = 'category_' . uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['icon']['tmp_name'], $target_path)) {
            $icon_path = 'assets/uploads/icons/' . $file_name;
        }
    }

    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        $query = "UPDATE categories SET name = :name, icon = :icon, is_active = :is_active, position = :position WHERE id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':icon', $icon_path);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':id', $category_id);

        if ($stmt->execute()) {
            header('Location: index.php?success=2'); // 2 for update
            exit();
        } else {
            $error = 'Failed to update category';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category - RetailRow Admin</title>
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--jumia-dark);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--jumia-dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-gray);
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--jumia-orange);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .submit-btn {
            background: var(--jumia-orange);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 200ms;
        }

        .submit-btn:hover {
            background: #e67e0e;
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

        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .current-icon {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border: 1px solid var(--border-gray);
            border-radius: 4px;
            margin-bottom: 10px;
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
        <a href="index.php" class="back-link">← Back to Categories</a>

        <div class="form-container">
            <h1 class="form-title">Edit Category</h1>

            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($category['name']); ?>">
                </div>

                <div class="form-group">
                    <label for="icon">Category Icon</label>
                    <?php if ($category['icon']): ?>
                        <p>Current Icon:</p>
                        <img src="../../<?php echo htmlspecialchars($category['icon']); ?>" alt="Current Icon" class="current-icon">
                    <?php endif; ?>
                    <input type="file" id="icon" name="icon" accept="image/*">
                    <small style="color: var(--jumia-gray); font-size: 12px;">Recommended: 40x40px PNG or SVG. Uploading a new icon will replace the current one.</small>
                </div>

                <div class="form-group">
                    <label for="position">Display Position</label>
                    <input type="number" id="position" name="position" min="0" value="<?php echo $category['position']; ?>">
                    <small style="color: var(--jumia-gray); font-size: 12px;">Lower numbers appear first</small>
                </div>

                <div class="form-group">
                    <label>Options</label>
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo $category['is_active'] ? 'checked' : ''; ?>>
                        <label for="is_active">Active (visible on site)</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
