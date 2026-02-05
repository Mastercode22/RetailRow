<?php
require_once '../../config/auth.php';
require_once '../../config/db.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

$errors = [];
$success_message = '';

// Fetch current settings
$settings = [];
try {
    $stmt = $conn->query("SELECT setting_key, setting_value FROM settings");
    $settingsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($settingsData as $setting) {
        $settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    $errors[] = "Error loading settings: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSettings = [
        'site_title' => trim($_POST['site_title'] ?? ''),
        'announcement_text' => trim($_POST['announcement_text'] ?? ''),
        'phone_number' => trim($_POST['phone_number'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'facebook_url' => trim($_POST['facebook_url'] ?? ''),
        'twitter_url' => trim($_POST['twitter_url'] ?? ''),
        'instagram_url' => trim($_POST['instagram_url'] ?? ''),
        'free_shipping_threshold' => floatval($_POST['free_shipping_threshold'] ?? 50),
        'currency' => trim($_POST['currency'] ?? 'USD'),
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0'
    ];

    // Validation
    if (empty($newSettings['site_title'])) {
        $errors[] = "Site title is required.";
    }

    if (!filter_var($newSettings['email'], FILTER_VALIDATE_EMAIL) && !empty($newSettings['email'])) {
        $errors[] = "Invalid email address.";
    }

    // Save settings if no errors
    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            foreach ($newSettings as $key => $value) {
                $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }

            $conn->commit();
            $success_message = "Settings updated successfully!";
            $settings = $newSettings;
        } catch (Exception $e) {
            $conn->rollBack();
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
    <title>Settings - RetailRow Admin</title>
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

        .settings-container {
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

        .settings-section {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .settings-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--jumia-dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--jumia-orange);
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

        .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            min-height: 80px;
            resize: vertical;
        }

        .form-textarea:focus {
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
                <li><a href="../flash-sales/">Flash Sales</a></li>
                <li><a href="index.php" class="active">Settings</a></li>
                <li><a href="../orders/">Orders</a></li>
                <li><a href="../users/">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Global Settings</h1>
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

            <div class="settings-container">
                <form method="POST">
                    <!-- General Settings -->
                    <div class="settings-section">
                        <h2 class="section-title">General Settings</h2>

                        <div class="form-group">
                            <label class="form-label" for="site_title">Site Title *</label>
                            <input type="text" id="site_title" name="site_title" class="form-input"
                                   value="<?php echo htmlspecialchars($settings['site_title'] ?? 'RetailRow'); ?>" required>
                            <div class="help-text">The main title of your website</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="announcement_text">Announcement Text</label>
                            <input type="text" id="announcement_text" name="announcement_text" class="form-input"
                                   value="<?php echo htmlspecialchars($settings['announcement_text'] ?? 'FREE SHIPPING on orders over $50'); ?>">
                            <div class="help-text">Text displayed in the top announcement bar</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="phone_number">Phone Number</label>
                                <input type="text" id="phone_number" name="phone_number" class="form-input"
                                       value="<?php echo htmlspecialchars($settings['phone_number'] ?? '030 274 0642'); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input"
                                       value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="address">Business Address</label>
                            <textarea id="address" name="address" class="form-textarea"><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="settings-section">
                        <h2 class="section-title">Social Media Links</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="facebook_url">Facebook URL</label>
                                <input type="url" id="facebook_url" name="facebook_url" class="form-input"
                                       value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>"
                                       placeholder="https://facebook.com/yourpage">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="twitter_url">Twitter URL</label>
                                <input type="url" id="twitter_url" name="twitter_url" class="form-input"
                                       value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>"
                                       placeholder="https://twitter.com/yourhandle">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="instagram_url">Instagram URL</label>
                            <input type="url" id="instagram_url" name="instagram_url" class="form-input"
                                   value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>"
                                   placeholder="https://instagram.com/youraccount">
                        </div>
                    </div>

                    <!-- E-commerce Settings -->
                    <div class="settings-section">
                        <h2 class="section-title">E-commerce Settings</h2>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="free_shipping_threshold">Free Shipping Threshold ($)</label>
                                <input type="number" id="free_shipping_threshold" name="free_shipping_threshold" class="form-input"
                                       value="<?php echo htmlspecialchars($settings['free_shipping_threshold'] ?? '50'); ?>" min="0" step="0.01">
                                <div class="help-text">Minimum order amount for free shipping</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="currency">Currency</label>
                                <select id="currency" name="currency" class="form-input">
                                    <option value="USD" <?php echo ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                    <option value="EUR" <?php echo ($settings['currency'] ?? 'USD') === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                    <option value="GBP" <?php echo ($settings['currency'] ?? 'USD') === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                    <option value="PKR" <?php echo ($settings['currency'] ?? 'USD') === 'PKR' ? 'selected' : ''; ?>>PKR (₨)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="settings-section">
                        <h2 class="section-title">System Settings</h2>

                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode"
                                       <?php echo ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                <label for="maintenance_mode" style="margin: 0; font-weight: 600; color: var(--jumia-dark);">
                                    Maintenance Mode
                                </label>
                            </div>
                            <div class="help-text">When enabled, the website will show a maintenance page to visitors</div>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Save Settings</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>