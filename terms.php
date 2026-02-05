<?php
// Load dynamic settings
$settings = [];
try {
    $settingsResponse = file_get_contents('http://localhost/RetailRow/api/settings.php?keys=site_title');
    if ($settingsResponse) {
        $settingsData = json_decode($settingsResponse, true);
        if ($settingsData && isset($settingsData['data'])) {
            $settings = $settingsData['data'];
        }
    }
} catch (Exception $e) {
    $settings = [
        'site_title' => 'RetailRow — Shop Quality Products at the Best Prices in Ghana'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Please read our terms carefully
            </div>
            <div class="announce-right">
                <a href="index.php" class="shop-now-btn">SHOP NOW</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <nav class="main-nav">
            <div class="container nav-inner">
                <div class="nav-logo">
                    <a href="index.php" class="logo-link">
                        <div class="logo-box">RetailRow</div>
                    </a>
                </div>
                <div class="nav-actions">
                    <a href="index.php" class="nav-btn">Home</a>
                    <a href="cart.php" class="nav-btn cart-btn">
                        <span>Cart</span>
                        <span class="cart-count" id="cartCount">0</span>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Terms of Service</h1>
                <p>Last updated: January 30, 2026</p>
            </div>

            <div class="policy-content">
                <section class="policy-section">
                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing and using RetailRow, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>
                </section>

                <section class="policy-section">
                    <h2>2. Use License</h2>
                    <p>Permission is granted to temporarily access the materials (information or software) on RetailRow for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                    <ul>
                        <li>Modify or copy the materials</li>
                        <li>Use the materials for any commercial purpose or for any public display</li>
                        <li>Attempt to decompile or reverse engineer any software contained on RetailRow</li>
                        <li>Remove any copyright or other proprietary notations from the materials</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>3. Products and Pricing</h2>
                    <p>All products are subject to availability. Prices are subject to change without notice. We reserve the right to modify or discontinue products without prior notice. We strive to display accurate price information, but errors may occur.</p>
                </section>

                <section class="policy-section">
                    <h2>4. Orders and Payment</h2>
                    <p>By placing an order, you agree to pay the total amount specified. Payment must be made at the time of order placement. We accept various payment methods including mobile money and card payments. All payments are processed securely.</p>
                </section>

                <section class="policy-section">
                    <h2>5. Shipping and Delivery</h2>
                    <p>We will make reasonable efforts to deliver products within the estimated timeframe. However, delivery dates are estimates only. We are not liable for delays caused by factors beyond our control.</p>
                </section>

                <section class="policy-section">
                    <h2>6. Returns and Refunds</h2>
                    <p>Items may be returned within 7 days of delivery if they are in original condition and packaging. Custom or personalized items may not be returnable. Refunds will be processed within 5-7 business days after receipt of returned items.</p>
                </section>

                <section class="policy-section">
                    <h2>7. User Accounts</h2>
                    <p>When you create an account with us, you must provide accurate and complete information. You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
                </section>

                <section class="policy-section">
                    <h2>8. Prohibited Uses</h2>
                    <p>You may not use our products for any illegal or unauthorized purpose. You must not transmit any worms or viruses or any code of a destructive nature.</p>
                </section>

                <section class="policy-section">
                    <h2>9. Limitation of Liability</h2>
                    <p>In no event shall RetailRow or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on RetailRow.</p>
                </section>

                <section class="policy-section">
                    <h2>10. Governing Law</h2>
                    <p>These terms and conditions are governed by and construed in accordance with the laws of Ghana, and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.</p>
                </section>

                <section class="policy-section">
                    <h2>11. Changes to Terms</h2>
                    <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will try to provide at least 30 days notice prior to any new terms taking effect.</p>
                </section>

                <section class="policy-section">
                    <h2>12. Contact Information</h2>
                    <p>If you have any questions about these Terms of Service, please contact us:</p>
                    <div class="contact-info">
                        <p><strong>Email:</strong> legal@retailrow.com</p>
                        <p><strong>Phone:</strong> 030 274 0642</p>
                        <p><strong>Address:</strong> Accra, Ghana</p>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>RetailRow</h3>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="careers.php">Careers</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="#">Sell on RetailRow</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Customer Care</h3>
                    <ul>
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="track-order.php">Track Your Order</a></li>
                        <li><a href="returns.php">Returns & Refunds</a></li>
                        <li><a href="size-guide.php">Size Guide</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Legal</h3>
                    <ul>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="cookies.php">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html></content>
<parameter name="filePath">c:\xampp\htdocs\RetailRow\terms.php