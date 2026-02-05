<?php
// Load dynamic settings
$settings = [];
$categories = [];
try {
    $settingsResponse = file_get_contents('http://localhost/RetailRow/api/settings.php?keys=announcement_text,phone_number,site_title');
    if ($settingsResponse) {
        $settingsData = json_decode($settingsResponse, true);
        if ($settingsData && isset($settingsData['data'])) {
            $settings = $settingsData['data'];
        }
    }
    
    // Load categories
    $categoriesResponse = file_get_contents('http://localhost/RetailRow/api/categories.php');
    if ($categoriesResponse) {
        $categoriesData = json_decode($categoriesResponse, true);
        if ($categoriesData && isset($categoriesData['data'])) {
            $categories = $categoriesData['data'];
        }
    }
} catch (Exception $e) {
    // Fallback to defaults if API fails
    $settings = [
        'announcement_text' => 'If ibi love, igo show for your cart 💕',
        'phone_number' => '030 274 0642',
        'site_title' => 'RetailRow — Shop Quality Products at the Best Prices in Ghana'
    ];
    $categories = [];
}

// Category icons
$categoryIcons = [
    'Electronics' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M21 6h-7.59l3.29-3.29L16 2l-4 4-4-4-.71.71L10.59 6H3c-1.1 0-2 .89-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.11-.9-2-2-2zm0 14H3V8h18v12zM9 10v8l7-4z"/></svg>',
    'Fashion' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
];
$defaultIcon = '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product - <?php echo htmlspecialchars($settings['site_title'] ?? 'RetailRow'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/product.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                <?php echo htmlspecialchars($settings['announcement_text'] ?? 'If ibi love, igo show for your cart 💕'); ?>
            </div>
            <div class="announce-right">
                <span class="call-label">CALL TO ORDER</span>
                <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $settings['phone_number'] ?? '0302740642')); ?>" class="phone-number"><?php echo htmlspecialchars($settings['phone_number'] ?? '030 274 0642'); ?></a>
                <a href="index.php" class="shop-now-btn">SHOP NOW</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <!-- Top Utility Row -->
        <div class="header-utility">
            <div class="container utility-inner">
                <a href="#" class="utility-link">
                    <svg class="icon-sm" viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                    Sell on RetailRow
                </a>
                <div class="utility-divider"></div>
                <a href="#" class="utility-link">RetailRow Express</a>
                <div class="utility-divider"></div>
                <a href="contact.php" class="utility-link">Customer Care</a>
            </div>
        </div>

        <!-- Main Navigation Row -->
        <nav class="main-nav">
            <div class="container nav-inner">
                <!-- Logo -->
                <div class="nav-logo">
                    <button class="hamburger" id="hamburger" aria-label="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <a href="index.php" class="logo-link">
                        <div class="logo-box">RetailRow</div>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="nav-search">
                    <form class="search-form">
                        <input type="search" placeholder="Search for products, brands and categories" aria-label="Search products">
                        <button type="submit" class="search-btn">SEARCH</button>
                    </form>
                </div>

                <!-- Right Nav Items -->
                <div class="nav-actions">
                    <button class="nav-btn account-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                        <span>Account</span>
                        <svg class="icon-arrow" viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M7 10l5 5 5-5z" />
                        </svg>
                    </button>

                    <a href="help.php" class="nav-btn help-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                        </svg>
                        <span>Help</span>
                    </a>

                    <button id="cartToggle" class="nav-btn cart-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
                        </svg>
                        <span>Cart</span>
                        <span class="cart-count" id="cartCount">0</span>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div id="product-details-container">
                <!-- Skeleton Loader -->
                <div class="skeleton-loader">
                    <div class="skeleton-gallery"></div>
                    <div class="skeleton-info">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line short"></div>
                        <div class="skeleton-line medium"></div>
                        <div class="skeleton-line long"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-main">
            <div class="container footer-grid">
                <div class="footer-col">
                    <h4>About RetailRow</h4>
                    <p>RetailRow is your trusted partner for quality products at unbeatable prices in Ghana.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Policies</h4>
                    <ul>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="returns.php">Returns Policy</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <p>Phone: <?php echo htmlspecialchars($settings['phone_number'] ?? '030 274 0642'); ?></p>
                    <p>Email: support@retailrow.com</p>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                © RetailRow
            </div>
        </div>
    </footer>

    <!-- Cart Panel (Slide-in) -->
    <aside id="cartPanel" class="cart-panel" aria-hidden="true">
        <div class="cart-header">
            <h3>Cart Summary</h3>
            <button id="closeCart" aria-label="Close cart">✕</button>
        </div>
        <div class="cart-body" id="cartList">
            <p class="cart-empty">Your cart is empty</p>
        </div>
        <div class="cart-footer">
            <div class="cart-subtotal">Subtotal: GH₵ <span id="subtotal">0.00</span></div>
            <a href="checkout.php" class="cart-checkout" style="text-decoration: none;">CHECKOUT (GH₵ <span id="checkoutTotal">0.00</span>)</a>
        </div>
    </aside>

    <!-- Overlay -->
    <div id="overlay" class="overlay" aria-hidden="true"></div>

    <script src="js/product.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/api.js"></script>
</body>
</html>
