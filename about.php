<?php
// Load dynamic settings
$settings = [];
try {
    $settingsResponse = file_get_contents('http://localhost/RetailRow/api/settings.php?keys=site_title,phone_number');
    if ($settingsResponse) {
        $settingsData = json_decode($settingsResponse, true);
        if ($settingsData && isset($settingsData['data'])) {
            $settings = $settingsData['data'];
        }
    }
} catch (Exception $e) {
    $settings = [
        'site_title' => 'RetailRow — Shop Quality Products at the Best Prices in Ghana',
        'phone_number' => '030 274 0642'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Welcome to RetailRow - Your Trusted Online Shopping Destination
            </div>
            <div class="announce-right">
                <span class="call-label">CALL TO ORDER</span>
                <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $settings['phone_number'])); ?>" class="phone-number"><?php echo htmlspecialchars($settings['phone_number']); ?></a>
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
                <h1>About RetailRow</h1>
                <p>Your trusted online shopping destination in Ghana</p>
            </div>

            <div class="about-content">
                <section class="about-section">
                    <h2>Who We Are</h2>
                    <p>RetailRow is Ghana's leading online marketplace, connecting customers with quality products at the best prices. We are committed to providing an exceptional shopping experience with fast delivery, secure payments, and excellent customer service.</p>
                </section>

                <section class="about-section">
                    <h2>Our Mission</h2>
                    <p>To make quality products accessible to every Ghanaian household by providing a seamless online shopping experience with competitive prices and reliable delivery services.</p>
                </section>

                <section class="about-section">
                    <h2>Why Choose RetailRow?</h2>
                    <div class="features-grid">
                        <div class="feature-card">
                            <h3>Fast Delivery</h3>
                            <p>Quick and reliable delivery across Ghana with our extensive logistics network.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Quality Products</h3>
                            <p>We source only the best products from trusted manufacturers and suppliers.</p>
                        </div>
                        <div class="feature-card">
                            <h3>Secure Payments</h3>
                            <p>Safe and secure payment options including mobile money and card payments.</p>
                        </div>
                        <div class="feature-card">
                            <h3>24/7 Support</h3>
                            <p>Our customer care team is always ready to assist you with any questions.</p>
                        </div>
                    </div>
                </section>

                <section class="about-section">
                    <h2>Contact Information</h2>
                    <div class="contact-info">
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($settings['phone_number']); ?></p>
                        <p><strong>Email:</strong> support@retailrow.com</p>
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
<parameter name="filePath">c:\xampp\htdocs\RetailRow\about.php