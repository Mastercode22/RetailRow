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
    <title>Help Center - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Need help? Find answers to common questions
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
                <h1>Help Center</h1>
                <p>Find answers to your questions</p>
            </div>

            <div class="help-content">
                <!-- Search Bar -->
                <div class="help-search">
                    <input type="search" placeholder="Search for help..." class="help-search-input">
                    <button class="help-search-btn">Search</button>
                </div>

                <!-- FAQ Categories -->
                <div class="faq-categories">
                    <div class="faq-category">
                        <h3>🛒 Shopping</h3>
                        <ul>
                            <li><a href="#how-to-order">How to place an order</a></li>
                            <li><a href="#payment-methods">Payment methods</a></li>
                            <li><a href="#order-tracking">Track your order</a></li>
                            <li><a href="#change-order">Change or cancel order</a></li>
                        </ul>
                    </div>
                    <div class="faq-category">
                        <h3>🚚 Delivery</h3>
                        <ul>
                            <li><a href="#delivery-time">Delivery timeframes</a></li>
                            <li><a href="#delivery-fees">Delivery fees</a></li>
                            <li><a href="#delivery-areas">Delivery areas</a></li>
                            <li><a href="#failed-delivery">Failed delivery</a></li>
                        </ul>
                    </div>
                    <div class="faq-category">
                        <h3>↩️ Returns</h3>
                        <ul>
                            <li><a href="#return-policy">Return policy</a></li>
                            <li><a href="#how-to-return">How to return items</a></li>
                            <li><a href="#refund-process">Refund process</a></li>
                            <li><a href="#damaged-items">Damaged items</a></li>
                        </ul>
                    </div>
                    <div class="faq-category">
                        <h3>💳 Payments</h3>
                        <ul>
                            <li><a href="#secure-payments">Secure payments</a></li>
                            <li><a href="#payment-issues">Payment issues</a></li>
                            <li><a href="#failed-payments">Failed payments</a></li>
                            <li><a href="#refund-credits">Refund credits</a></li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="faq-section">
                    <h2>Frequently Asked Questions</h2>

                    <div class="faq-item">
                        <h3 id="how-to-order">How do I place an order?</h3>
                        <p>Browse our products, add items to your cart, and proceed to checkout. You can pay using mobile money, card, or cash on delivery.</p>
                    </div>

                    <div class="faq-item">
                        <h3 id="payment-methods">What payment methods do you accept?</h3>
                        <p>We accept MTN Mobile Money, Vodafone Cash, AirtelTigo Money, Visa, Mastercard, and cash on delivery.</p>
                    </div>

                    <div class="faq-item">
                        <h3 id="delivery-time">How long does delivery take?</h3>
                        <p>Orders are typically delivered within 1-3 business days in Accra and 2-5 business days in other regions.</p>
                    </div>

                    <div class="faq-item">
                        <h3 id="return-policy">What's your return policy?</h3>
                        <p>You can return items within 7 days of delivery if they're in original condition. Some items may not be returnable for hygiene reasons.</p>
                    </div>

                    <div class="faq-item">
                        <h3 id="order-tracking">How can I track my order?</h3>
                        <p>You'll receive SMS updates about your order status. You can also contact our customer care team for updates.</p>
                    </div>

                    <div class="faq-item">
                        <h3 id="secure-payments">Are my payments secure?</h3>
                        <p>Yes, all payments are processed through secure, encrypted channels. We use industry-standard security measures.</p>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="contact-support">
                    <h2>Still need help?</h2>
                    <p>Can't find what you're looking for? Our customer care team is here to help.</p>
                    <div class="support-options">
                        <div class="support-option">
                            <h3>📞 Call Us</h3>
                            <p><?php echo htmlspecialchars($settings['phone_number']); ?></p>
                            <p>Mon - Sat: 8:00 AM - 8:00 PM</p>
                        </div>
                        <div class="support-option">
                            <h3>💬 WhatsApp</h3>
                            <p><?php echo htmlspecialchars($settings['phone_number']); ?></p>
                            <p>Quick responses for urgent inquiries</p>
                        </div>
                        <div class="support-option">
                            <h3>📧 Email</h3>
                            <p>support@retailrow.com</p>
                            <p>We'll respond within 24 hours</p>
                        </div>
                    </div>
                </div>
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
<parameter name="filePath">c:\xampp\htdocs\RetailRow\help.php