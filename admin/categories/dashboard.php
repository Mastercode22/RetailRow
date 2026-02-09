<?php
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
    <title>Dashboard - RetailRow Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php $page = 'dashboard'; include __DIR__ . '/includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-title"><strong>Dashboard</strong></div>
                <div class="admin-user">
                    <span>Welcome, <?php echo htmlspecialchars($user['name']); ?></span>
                    <form method="POST" action="../logout.php" style="display: inline; margin-left: 15px;">
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </header>
            <div class="admin-content">
                <h1>Welcome to RetailRow Admin</h1>
                <p style="margin-top: 10px; color: #666;">Select an option from the sidebar to manage your store.</p>
            </div>
        </main>
    </div>
</body>
</html>