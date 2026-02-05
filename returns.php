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
    <title>Returns & Refunds - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Hassle-free returns and refunds
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
                <h1>Returns & Refunds</h1>
                <p>Our return policy is designed to make shopping with us worry-free</p>
            </div>

            <div class="returns-content">
                <section class="returns-section">
                    <h2>Return Policy</h2>
                    <div class="policy-overview">
                        <div class="policy-item">
                            <div class="policy-icon">📅</div>
                            <div class="policy-details">
                                <h3>7-Day Return Window</h3>
                                <p>You can return items within 7 days of delivery for a full refund.</p>
                            </div>
                        </div>
                        <div class="policy-item">
                            <div class="policy-icon">📦</div>
                            <div class="policy-details">
                                <h3>Original Condition</h3>
                                <p>Items must be in original condition with all packaging and tags intact.</p>
                            </div>
                        </div>
                        <div class="policy-item">
                            <div class="policy-icon">🚫</div>
                            <div class="policy-details">
                                <h3>Non-Returnable Items</h3>
                                <p>Some items like underwear, cosmetics, and personalized products cannot be returned.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="returns-section">
                    <h2>How to Return an Item</h2>
                    <div class="return-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h3>Contact Us</h3>
                                <p>Call us at <?php echo htmlspecialchars($settings['phone_number']); ?> or email support@retailrow.com to initiate your return.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h3>Package the Item</h3>
                                <p>Pack the item securely in its original packaging with all accessories and documentation.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h3>Ship It Back</h3>
                                <p>We'll provide a return shipping label or pickup arrangement at no cost to you.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h3>Get Refunded</h3>
                                <p>Once we receive and inspect the item, we'll process your refund within 5-7 business days.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="returns-section">
                    <h2>Refund Process</h2>
                    <div class="refund-options">
                        <div class="refund-option">
                            <h3>Mobile Money Refund</h3>
                            <p>Refunds to MTN Mobile Money, Vodafone Cash, or AirtelTigo Money are processed instantly.</p>
                        </div>
                        <div class="refund-option">
                            <h3>Card Refund</h3>
                            <p>Card refunds may take 3-5 business days to appear on your statement.</p>
                        </div>
                        <div class="refund-option">
                            <h3>Store Credit</h3>
                            <p>You can choose to receive store credit for future purchases (processed immediately).</p>
                        </div>
                    </div>
                </section>

                <section class="returns-section">
                    <h2>Return Reasons</h2>
                    <div class="return-reasons">
                        <div class="reason">
                            <h4>Wrong Item</h4>
                            <p>If you received the wrong item, we'll arrange for a replacement or full refund.</p>
                        </div>
                        <div class="reason">
                            <h4>Damaged Item</h4>
                            <p>If your item arrived damaged, we'll process a full refund or replacement.</p>
                        </div>
                        <div class="reason">
                            <h4>Changed Mind</h4>
                            <p>If you're not satisfied with your purchase, you can return it within 7 days.</p>
                        </div>
                        <div class="reason">
                            <h4>Defective Product</h4>
                            <p>If the product is defective, we'll provide a replacement or full refund.</p>
                        </div>
                    </div>
                </section>

                <section class="returns-section">
                    <h2>Return Shipping</h2>
                    <p>We cover the cost of return shipping for most returns. If the return is due to our error (wrong item, damaged item), we'll arrange free pickup. For other returns, we provide a prepaid return label.</p>
                </section>

                <section class="returns-section">
                    <h2>Contact Us for Returns</h2>
                    <div class="contact-options">
                        <div class="contact-option">
                            <h3>📞 Phone</h3>
                            <p><?php echo htmlspecialchars($settings['phone_number']); ?></p>
                            <p>Mon - Sat: 8:00 AM - 8:00 PM</p>
                        </div>
                        <div class="contact-option">
                            <h3>💬 WhatsApp</h3>
                            <p><?php echo htmlspecialchars($settings['phone_number']); ?></p>
                            <p>Quick return assistance</p>
                        </div>
                        <div class="contact-option">
                            <h3>📧 Email</h3>
                            <p>returns@retailrow.com</p>
                            <p>For detailed return inquiries</p>
                        </div>
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
<parameter name="filePath">c:\xampp\htdocs\RetailRow\returns.php