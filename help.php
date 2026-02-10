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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ===========================
           Beautiful Help Center Design
           =========================== */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background: #f8f9fa;
        }

        /* Hero Section */
        .help-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5rem 0 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .help-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .help-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .help-hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .help-hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
        }

        .help-hero p {
            font-size: 1.25rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
        }

        /* Search Bar */
        .help-search {
            max-width: 650px;
            margin: 0 auto;
            position: relative;
            display: flex;
            gap: 0;
            background: white;
            border-radius: 50px;
            padding: 8px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .help-search-input {
            flex: 1;
            border: none;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            border-radius: 50px;
            outline: none;
            color: #2d3748;
        }

        .help-search-input::placeholder {
            color: #a0aec0;
        }

        .help-search-btn {
            background: linear-gradient(135deg, #f68b1e 0%, #ff9a3c 100%);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(246, 139, 30, 0.3);
        }

        .help-search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(246, 139, 30, 0.4);
        }

        /* Main Content */
        .main-content {
            padding: 4rem 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Quick Links Section */
        .quick-links {
            margin-bottom: 4rem;
        }

        .quick-links h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 3rem;
        }

        .faq-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .faq-category {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .faq-category::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s;
        }

        .faq-category:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.12);
        }

        .faq-category:hover::before {
            transform: scaleX(1);
        }

        .faq-category h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .faq-category ul {
            list-style: none;
        }

        .faq-category ul li {
            margin-bottom: 1rem;
        }

        .faq-category ul li a {
            color: #4a5568;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 0;
            transition: all 0.2s;
        }

        .faq-category ul li a::before {
            content: '→';
            color: #667eea;
            font-weight: bold;
            transition: transform 0.2s;
        }

        .faq-category ul li a:hover {
            color: #667eea;
            padding-left: 10px;
        }

        .faq-category ul li a:hover::before {
            transform: translateX(5px);
        }

        /* FAQ Section */
        .faq-section {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 4rem;
        }

        .faq-section h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #667eea;
        }

        .faq-item {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border-radius: 12px;
            border-left: 4px solid #667eea;
            transition: all 0.3s;
        }

        .faq-item:hover {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1);
            transform: translateX(5px);
        }

        .faq-item h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-item h3::before {
            content: 'Q';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .faq-item p {
            color: #4a5568;
            font-size: 1rem;
            line-height: 1.8;
            padding-left: 42px;
        }

        /* Contact Support */
        .contact-support {
            background: linear-gradient(135deg, #f68b1e 0%, #ff9a3c 100%);
            color: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 15px 50px rgba(246, 139, 30, 0.3);
            position: relative;
            overflow: hidden;
        }

        .contact-support::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .contact-support::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -15%;
            width: 350px;
            height: 350px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .contact-support h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .contact-support > p {
            font-size: 1.15rem;
            opacity: 0.95;
            margin-bottom: 3rem;
            position: relative;
            z-index: 1;
        }

        .support-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            position: relative;
            z-index: 1;
        }

        .support-option {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .support-option:hover {
            transform: translateY(-10px);
            background: white;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .support-option h3 {
            font-size: 1.5rem;
            color: #1a202c;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .support-option p {
            color: #4a5568;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .support-option p:first-of-type {
            font-weight: 700;
            color: #667eea;
            font-size: 1.15rem;
        }

        /* Stats Section */
        .help-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 4rem 0;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-label {
            color: #718096;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .help-hero h1 {
                font-size: 2rem;
            }

            .help-hero p {
                font-size: 1rem;
            }

            .help-search {
                flex-direction: column;
                border-radius: 16px;
            }

            .help-search-input,
            .help-search-btn {
                border-radius: 12px;
            }

            .faq-categories {
                grid-template-columns: 1fr;
            }

            .faq-section {
                padding: 2rem 1.5rem;
            }

            .contact-support {
                padding: 2rem 1.5rem;
            }

            .contact-support h2 {
                font-size: 1.75rem;
            }

            .support-options {
                grid-template-columns: 1fr;
            }
        }

        /* Scroll Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
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

    <!-- Hero Section -->
    <section class="help-hero">
        <div class="help-hero-content">
            <h1>How can we help you?</h1>
            <p>Search our knowledge base or browse categories below</p>
            
            <!-- Search Bar -->
            <div class="help-search">
                <input type="search" placeholder="Type your question here..." class="help-search-input" id="helpSearchInput">
                <button class="help-search-btn" onclick="searchHelp()">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Help Stats -->
            <div class="help-stats fade-in">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-question-circle" style="color: white;"></i>
                    </div>
                    <div class="stat-value">500+</div>
                    <div class="stat-label">Articles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users" style="color: white;"></i>
                    </div>
                    <div class="stat-value">10K+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock" style="color: white;"></i>
                    </div>
                    <div class="stat-value">&lt;2h</div>
                    <div class="stat-label">Response Time</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star" style="color: white;"></i>
                    </div>
                    <div class="stat-value">4.9/5</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>

            <!-- Quick Links -->
            <section class="quick-links fade-in">
                <h2>Browse by Category</h2>
                
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
            </section>

            <!-- FAQ Section -->
            <section class="faq-section fade-in">
                <h2>Frequently Asked Questions</h2>

                <div class="faq-item">
                    <h3 id="how-to-order">How do I place an order?</h3>
                    <p>Browse our products, add items to your cart, and proceed to checkout. You can pay using mobile money, card, or cash on delivery. Once your order is confirmed, you'll receive an SMS with your order details and tracking information.</p>
                </div>

                <div class="faq-item">
                    <h3 id="payment-methods">What payment methods do you accept?</h3>
                    <p>We accept MTN Mobile Money, Vodafone Cash, AirtelTigo Money, Visa, Mastercard, and cash on delivery. All online payments are processed through secure, encrypted channels to protect your financial information.</p>
                </div>

                <div class="faq-item">
                    <h3 id="delivery-time">How long does delivery take?</h3>
                    <p>Orders are typically delivered within 1-3 business days in Accra and 2-5 business days in other regions. Express delivery options are available for urgent orders at an additional fee.</p>
                </div>

                <div class="faq-item">
                    <h3 id="return-policy">What's your return policy?</h3>
                    <p>You can return items within 7 days of delivery if they're in original condition with tags attached. Some items may not be returnable for hygiene reasons (e.g., underwear, cosmetics). Refunds are processed within 5-7 business days.</p>
                </div>

                <div class="faq-item">
                    <h3 id="order-tracking">How can I track my order?</h3>
                    <p>You'll receive SMS updates about your order status at every stage. You can also contact our customer care team via phone or WhatsApp for real-time updates on your delivery.</p>
                </div>

                <div class="faq-item">
                    <h3 id="secure-payments">Are my payments secure?</h3>
                    <p>Yes, all payments are processed through secure, encrypted channels. We use industry-standard security measures including SSL encryption and PCI DSS compliance to protect your payment information.</p>
                </div>
            </section>

            <!-- Contact Support -->
            <section class="contact-support fade-in">
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
            </section>
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
        // Search functionality
        function searchHelp() {
            const query = document.getElementById('helpSearchInput').value.toLowerCase();
            if (query.trim() === '') return;
            
            const faqItems = document.querySelectorAll('.faq-item');
            let found = false;
            
            faqItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = 'block';
                    item.style.background = 'linear-gradient(135deg, #fff9e6 0%, #fff 100%)';
                    found = true;
                    
                    // Scroll to first match
                    if (!found) {
                        item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (!found) {
                alert('No results found. Please try different keywords or contact support.');
            }
        }

        // Enter key to search
        document.getElementById('helpSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchHelp();
            }
        });

        // Scroll animation
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });
    </script>
</body>
</html>