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
    <title>Track Your Order - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Track your order in real-time
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
                <h1>Track Your Order</h1>
                <p>Enter your order number to track your package</p>
            </div>

            <div class="tracking-content">
                <div class="tracking-form-section">
                    <form class="tracking-form">
                        <div class="form-group">
                            <label for="orderNumber">Order Number *</label>
                            <input type="text" id="orderNumber" name="orderNumber" placeholder="Enter your order number" required>
                            <small class="help-text">Your order number was sent via SMS after purchase</small>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter phone number used for order" required>
                        </div>
                        <button type="submit" class="track-btn">Track Order</button>
                    </form>
                </div>

                <div class="tracking-info-section">
                    <h2>How to track your order</h2>
                    <div class="tracking-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h3>Order Confirmation</h3>
                                <p>You'll receive an SMS with your order number immediately after purchase.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h3>Processing</h3>
                                <p>Your order is being prepared for shipment (usually within 24 hours).</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h3>Shipped</h3>
                                <p>Your package is on its way. You'll receive tracking updates via SMS.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h3>Delivered</h3>
                                <p>Package delivered successfully. Enjoy your purchase!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Tracking Result (hidden by default) -->
                <div class="tracking-result" id="trackingResult" style="display: none;">
                    <h2>Order Status</h2>
                    <div class="order-details">
                        <div class="order-info">
                            <p><strong>Order #:</strong> <span id="resultOrderNumber"></span></p>
                            <p><strong>Status:</strong> <span id="resultStatus" class="status-processing">Processing</span></p>
                            <p><strong>Estimated Delivery:</strong> <span id="resultDeliveryDate"></span></p>
                        </div>
                        <div class="order-timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h4>Order Placed</h4>
                                    <p>January 30, 2026 at 2:30 PM</p>
                                </div>
                            </div>
                            <div class="timeline-item active">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h4>Processing</h4>
                                    <p>Your order is being prepared</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h4>Shipped</h4>
                                    <p>Package will be shipped soon</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h4>Delivered</h4>
                                    <p>Package delivered to customer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-help">
                    <h2>Need Help?</h2>
                    <p>Can't find your order or having issues tracking?</p>
                    <div class="help-options">
                        <div class="help-option">
                            <h3>📞 Call Customer Care</h3>
                            <p><?php echo htmlspecialchars($settings['phone_number']); ?></p>
                            <p>Mon - Sat: 8:00 AM - 8:00 PM</p>
                        </div>
                        <div class="help-option">
                            <h3>💬 WhatsApp Support</h3>
                            <p><?php echo htmlspecialchars($settings['phone_number']); ?></p>
                            <p>Quick responses for tracking issues</p>
                        </div>
                        <div class="help-option">
                            <h3>📧 Email Support</h3>
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
    <script>
        // Simple tracking simulation
        document.querySelector('.tracking-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const orderNumber = document.getElementById('orderNumber').value;
            const phone = document.getElementById('phone').value;

            if (orderNumber && phone) {
                // Show tracking result
                document.getElementById('resultOrderNumber').textContent = orderNumber;
                document.getElementById('resultDeliveryDate').textContent = 'February 2, 2026';
                document.getElementById('trackingResult').style.display = 'block';

                // Scroll to result
                document.getElementById('trackingResult').scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
</body>
</html></content>
<parameter name="filePath">c:\xampp\htdocs\RetailRow\track-order.php