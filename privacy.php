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
    <title>Privacy Policy - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Your privacy is important to us
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
                <h1>Privacy Policy</h1>
                <p>Last updated: January 30, 2026</p>
            </div>

            <div class="policy-content">
                <section class="policy-section">
                    <h2>1. Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support. This may include:</p>
                    <ul>
                        <li>Name and contact information</li>
                        <li>Billing and shipping addresses</li>
                        <li>Payment information</li>
                        <li>Purchase history</li>
                        <li>Communications with us</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>2. How We Use Your Information</h2>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Process and fulfill your orders</li>
                        <li>Provide customer service</li>
                        <li>Send you important updates about your orders</li>
                        <li>Improve our products and services</li>
                        <li>Send marketing communications (with your consent)</li>
                        <li>Prevent fraud and maintain security</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>3. Information Sharing</h2>
                    <p>We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:</p>
                    <ul>
                        <li>With service providers who help us operate our business</li>
                        <li>To comply with legal obligations</li>
                        <li>To protect our rights and prevent fraud</li>
                        <li>With your explicit consent</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>4. Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. This includes encryption of sensitive data and regular security assessments.</p>
                </section>

                <section class="policy-section">
                    <h2>5. Cookies and Tracking</h2>
                    <p>We use cookies and similar technologies to enhance your browsing experience, analyze site traffic, and personalize content. You can control cookie settings through your browser preferences.</p>
                </section>

                <section class="policy-section">
                    <h2>6. Your Rights</h2>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access the personal information we hold about you</li>
                        <li>Correct inaccurate information</li>
                        <li>Request deletion of your information</li>
                        <li>Opt out of marketing communications</li>
                        <li>Data portability</li>
                    </ul>
                </section>

                <section class="policy-section">
                    <h2>7. Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us:</p>
                    <div class="contact-info">
                        <p><strong>Email:</strong> privacy@retailrow.com</p>
                        <p><strong>Phone:</strong> 030 274 0642</p>
                        <p><strong>Address:</strong> Accra, Ghana</p>
                    </div>
                </section>

                <section class="policy-section">
                    <h2>8. Changes to This Policy</h2>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>
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
<parameter name="filePath">c:\xampp\htdocs\RetailRow\privacy.php