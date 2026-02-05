<?php
require_once '../../config/auth.php';
require_once '../../config/db.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $button_text = trim($_POST['button_text'] ?? 'SHOP NOW');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Validation
    if (empty($title)) {
        $errors[] = "Banner title is required.";
    }

    if (empty($_FILES['image']['name'])) {
        $errors[] = "Banner image is required.";
    }

    // Handle image upload
    $image_path = '';
    if (!empty($_FILES['image']['name']) && empty($errors)) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = "Image size too large. Maximum size is 5MB.";
        } else {
            $upload_dir = '../../assets/images/banners/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = 'banner_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'assets/images/banners/' . $file_name;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Save to database if no errors
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO banners (title, subtitle, image, link, button_text, sort_order, is_active, created_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $subtitle, $image_path, $link, $button_text, $sort_order, $is_active]);

            $success_message = "Banner added successfully!";
            // Reset form
            $title = $subtitle = $link = '';
            $button_text = 'SHOP NOW';
            $sort_order = 0;
            $is_active = 1;
        } catch (Exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Banner - RetailRow Admin</title>
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

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background 200ms;
        }

        .back-btn:hover {
            background: #5a6268;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 30px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--jumia-dark);
            margin-bottom: 5px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 200ms;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--jumia-orange);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .submit-btn {
            background: var(--jumia-orange);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 200ms;
        }

        .submit-btn:hover {
            background: #e67e0e;
        }

        .image-preview {
            margin-top: 10px;
            max-width: 300px;
            display: none;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                <h1 class="admin-title">Add New Banner</h1>
                <a href="index.php" class="back-btn">← Back to Banners</a>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label" for="title">Banner Title *</label>
                        <input type="text" id="title" name="title" class="form-input"
                               value="<?php echo htmlspecialchars($title ?? ''); ?>" required
                               placeholder="e.g., FLASH SALE, NEW ARRIVALS">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="subtitle">Subtitle</label>
                        <input type="text" id="subtitle" name="subtitle" class="form-input"
                               value="<?php echo htmlspecialchars($subtitle ?? ''); ?>"
                               placeholder="e.g., Up to 60% OFF, Limited Time Only">
                        <small style="color: var(--jumia-gray); font-size: 14px;">
                            Optional - appears below the title
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image">Banner Image *</label>
                        <input type="file" id="image" name="image" class="form-input" accept="image/*" required>
                        <small style="color: var(--jumia-gray); font-size: 14px;">
                            Recommended size: 1200x400px. Max 5MB (JPG, PNG, GIF, WebP)
                        </small>
                        <div class="image-preview" id="imagePreview"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="link">Link URL (optional)</label>
                        <input type="url" id="link" name="link" class="form-input"
                               value="<?php echo htmlspecialchars($link ?? ''); ?>"
                               placeholder="https://example.com">
                        <small style="color: var(--jumia-gray); font-size: 14px;">
                            Where should the banner link to when clicked?
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="button_text">Button Text</label>
                        <input type="text" id="button_text" name="button_text" class="form-input"
                               value="<?php echo htmlspecialchars($button_text ?? 'SHOP NOW'); ?>"
                               placeholder="SHOP NOW">
                        <small style="color: var(--jumia-gray); font-size: 14px;">
                            Text to display on the call-to-action button
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-input"
                               value="<?php echo htmlspecialchars($sort_order ?? 0); ?>" min="0">
                        <small style="color: var(--jumia-gray); font-size: 14px;">
                            Lower numbers appear first. Leave as 0 for auto-ordering.
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active"
                                   <?php echo ($is_active ?? 1) ? 'checked' : ''; ?>>
                            <label for="is_active" style="margin: 0; font-weight: 600; color: var(--jumia-dark);">
                                Active (visible on website)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Add Banner</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>