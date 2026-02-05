<?php
require_once '../../config/auth.php';
require_once '../../config/db.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success_message = '';

// Fetch products for dropdown
try {
    $stmt = $conn->query("SELECT id, name, price FROM products ORDER BY name ASC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
    $errors[] = "Error loading products: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $product_id = intval($_POST['product_id'] ?? 0);
    $discount_percentage = intval($_POST['discount_percentage'] ?? 0);
    $end_date = trim($_POST['end_date'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Validation
    if (empty($title)) {
        $errors[] = "Flash sale title is required.";
    }

    if ($product_id <= 0) {
        $errors[] = "Please select a product.";
    }

    if ($discount_percentage <= 0 || $discount_percentage > 90) {
        $errors[] = "Discount percentage must be between 1 and 90.";
    }

    if (empty($end_date)) {
        $errors[] = "End date is required.";
    } else {
        $endDateTime = new DateTime($end_date);
        $now = new DateTime();
        if ($endDateTime <= $now) {
            $errors[] = "End date must be in the future.";
        }
    }

    // Check if product already has an active flash sale
    if ($product_id > 0 && empty($errors)) {
        try {
            $stmt = $conn->prepare("SELECT id FROM flash_sales WHERE product_id = ? AND is_active = 1 AND end_date > NOW()");
            $stmt->execute([$product_id]);
            if ($stmt->fetch()) {
                $errors[] = "This product already has an active flash sale.";
            }
        } catch (Exception $e) {
            $errors[] = "Error checking existing flash sales: " . $e->getMessage();
        }
    }

    // Save to database if no errors
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO flash_sales (title, product_id, discount_percentage, end_date, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $product_id, $discount_percentage, $end_date, $is_active]);

            $success_message = "Flash sale added successfully!";
            // Reset form
            $title = '';
            $product_id = 0;
            $discount_percentage = 0;
            $end_date = '';
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
    <title>Add Flash Sale - RetailRow Admin</title>
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            background: white;
        }

        .form-select:focus {
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

        .help-text {
            color: var(--jumia-gray);
            font-size: 14px;
            margin-top: 5px;
        }

        .product-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            display: none;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-price {
            font-weight: 600;
            color: var(--jumia-dark);
        }

        .discounted-price {
            color: var(--jumia-orange);
            font-weight: 700;
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
                <h1 class="admin-title">Add New Flash Sale</h1>
                <a href="index.php" class="back-btn">← Back to Flash Sales</a>
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
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="title">Flash Sale Title *</label>
                        <input type="text" id="title" name="title" class="form-input"
                               value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                        <div class="help-text">A descriptive title for this flash sale</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="product_id">Select Product *</label>
                            <select id="product_id" name="product_id" class="form-select" required>
                                <option value="">Choose a product...</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>"
                                            data-price="<?php echo $product['price']; ?>"
                                            <?php echo ($product_id ?? 0) == $product['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['name']); ?> - $<?php echo number_format($product['price'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="discount_percentage">Discount Percentage *</label>
                            <input type="number" id="discount_percentage" name="discount_percentage" class="form-input"
                                   value="<?php echo htmlspecialchars($discount_percentage ?? 0); ?>" min="1" max="90" required>
                            <div class="help-text">Percentage discount (1-90%)</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="end_date">End Date & Time *</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-input"
                               value="<?php echo htmlspecialchars($end_date ?? ''); ?>" required>
                        <div class="help-text">When this flash sale will automatically end</div>
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

                    <div id="productPreview" class="product-preview">
                        <div class="product-info">
                            <div>
                                <strong>Selected Product:</strong> <span id="selectedProductName">None</span>
                            </div>
                            <div>
                                <span class="product-price">Original: $<span id="originalPrice">0.00</span></span>
                                <span class="discounted-price">Discounted: $<span id="discountedPrice">0.00</span></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Create Flash Sale</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        function updateProductPreview() {
            const select = document.getElementById('product_id');
            const discountInput = document.getElementById('discount_percentage');
            const preview = document.getElementById('productPreview');
            const productName = document.getElementById('selectedProductName');
            const originalPrice = document.getElementById('originalPrice');
            const discountedPrice = document.getElementById('discountedPrice');

            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption.value) {
                const price = parseFloat(selectedOption.getAttribute('data-price'));
                const discount = parseInt(discountInput.value) || 0;
                const discounted = price * (1 - discount / 100);

                productName.textContent = selectedOption.text.split(' - $')[0];
                originalPrice.textContent = price.toFixed(2);
                discountedPrice.textContent = discounted.toFixed(2);
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        document.getElementById('product_id').addEventListener('change', updateProductPreview);
        document.getElementById('discount_percentage').addEventListener('input', updateProductPreview);

        // Initialize preview on page load
        updateProductPreview();
    </script>
</body>
</html>