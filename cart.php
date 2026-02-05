<?php
// Load dynamic settings
$settings = [];
try {
    $settingsResponse = file_get_contents('http://localhost/RetailRow/api/settings.php?keys=site_title,announcement_text,phone_number');
    if ($settingsResponse) {
        $settingsData = json_decode($settingsResponse, true);
        if ($settingsData && isset($settingsData['data'])) {
            $settings = $settingsData['data'];
        }
    }
} catch (Exception $e) {
    $settings = [
        'site_title' => 'RetailRow',
        'announcement_text' => 'FREE SHIPPING on orders over $50',
        'phone_number' => '+1-234-567-8900'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            margin-bottom: 50px;
        }

        .cart-items {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .cart-header {
            background: var(--jumia-orange);
            color: white;
            padding: 20px;
            font-size: 20px;
            font-weight: 700;
        }

        .cart-item {
            display: flex;
            padding: 20px;
            border-bottom: 1px solid var(--border-gray);
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 20px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: var(--jumia-dark);
            margin-bottom: 5px;
            font-size: 16px;
        }

        .item-price {
            color: var(--jumia-orange);
            font-weight: 700;
            font-size: 18px;
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid var(--border-gray);
            background: white;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-input {
            width: 50px;
            height: 30px;
            text-align: center;
            border: 1px solid var(--border-gray);
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }

        .remove-btn {
            color: #dc3545;
            cursor: pointer;
            font-size: 20px;
            padding: 5px;
        }

        .cart-summary {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            height: fit-content;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--jumia-dark);
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .summary-total {
            border-top: 2px solid var(--border-gray);
            padding-top: 15px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: 700;
            color: var(--jumia-dark);
        }

        .checkout-btn {
            background: var(--jumia-orange);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: background 200ms;
        }

        .checkout-btn:hover {
            background: #e67e0e;
        }

        .empty-cart {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .empty-cart h2 {
            color: var(--jumia-dark);
            margin-bottom: 20px;
        }

        .empty-cart p {
            color: var(--jumia-gray);
            font-size: 18px;
            margin-bottom: 30px;
        }

        .shop-now-btn {
            background: var(--jumia-orange);
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--jumia-orange);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .item-controls {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                <?php echo htmlspecialchars($settings['announcement_text'] ?? 'FREE SHIPPING on orders over $50'); ?>
            </div>
            <div class="announce-right">
                <span class="call-label">CALL TO ORDER</span>
                <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $settings['phone_number'] ?? '0302740642')); ?>" class="phone-number"><?php echo htmlspecialchars($settings['phone_number'] ?? '030 274 0642'); ?></a>
                <a href="#" class="shop-now-btn">SHOP NOW</a>
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
                <a href="#" class="utility-link">Customer Care</a>
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
                    <a href="/" class="logo-link">
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

                    <button class="nav-btn help-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                        </svg>
                        <span>Help</span>
                        <svg class="icon-arrow" viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M7 10l5 5 5-5z" />
                        </svg>
                    </button>

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
            <div id="cartContainer" class="cart-container">
                <!-- Cart Items will be loaded here -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-content">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-col">
                        <h4>RetailRow</h4>
                        <ul>
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="#">Careers</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="#">Sell on RetailRow</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Customer Service</h4>
                        <ul>
                            <li><a href="help.php">Help Center</a></li>
                            <li><a href="track-order.php">Track Your Order</a></li>
                            <li><a href="returns.php">Returns & Refunds</a></li>
                            <li><a href="size-guide.php">Size Guide</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Legal</h4>
                        <ul>
                            <li><a href="privacy.php">Privacy Policy</a></li>
                            <li><a href="terms.php">Terms of Service</a></li>
                            <li><a href="#">Cookie Policy</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>Payment Methods</h4>
                        <div class="payment-icons">
                            <img src="assets/images/payments/visa.png" alt="Visa">
                            <img src="assets/images/payments/mastercard.png" alt="Mastercard">
                            <img src="assets/images/payments/paypal.png" alt="PayPal">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; 2026 RetailRow. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Button -->
    <a href="#" id="whatsappBtn" class="whatsapp-float" aria-label="Contact us on WhatsApp">
        <svg viewBox="0 0 24 24" width="24" height="24">
            <path fill="#fff" d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
        </svg>
    </a>

    <!-- Back to Top -->
    <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>

    <script>
        let cart = [];
        let products = [];

        // Load products data for cart operations
        async function loadProducts() {
            try {
                const response = await fetch('http://localhost/RetailRow/api/products.php');
                const data = await response.json();
                if (data.success) {
                    products = data.data;
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        // Load cart from localStorage
        function loadCart() {
            cart = JSON.parse(localStorage.getItem('retailrow_cart') || '[]');
            updateCartCount();
            renderCart();
        }

        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem('retailrow_cart', JSON.stringify(cart));
            updateCartCount();
        }

        // Update cart count in header
        function updateCartCount() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cartCount').textContent = totalItems;
        }

        // Render cart items
        function renderCart() {
            const container = document.getElementById('cartContainer');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <h2>Your cart is empty</h2>
                        <p>Add some products to get started!</p>
                        <a href="index.php" class="shop-now-btn">Start Shopping</a>
                    </div>
                `;
                return;
            }

            let cartItemsHtml = `
                <div class="cart-items">
                    <div class="cart-header">Shopping Cart (${cart.length} items)</div>
            `;

            let subtotal = 0;

            cart.forEach((item, index) => {
                const product = products.find(p => p.id == item.id);
                if (!product) return;

                const itemTotal = product.price * item.quantity;
                subtotal += itemTotal;

                cartItemsHtml += `
                    <div class="cart-item">
                        <img src="${product.image || 'assets/images/placeholder.jpg'}" alt="${product.name}" class="item-image">
                        <div class="item-details">
                            <div class="item-name">${product.name}</div>
                            <div class="item-price">$${product.price.toFixed(2)}</div>
                        </div>
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="changeQuantity(${index}, -1)">-</button>
                                <input type="number" class="quantity-input" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)">
                                <button class="quantity-btn" onclick="changeQuantity(${index}, 1)">+</button>
                            </div>
                            <div class="item-price">$${(product.price * item.quantity).toFixed(2)}</div>
                            <span class="remove-btn" onclick="removeItem(${index})" title="Remove item">×</span>
                        </div>
                    </div>
                `;
            });

            cartItemsHtml += '</div>';

            const shipping = subtotal >= 50 ? 0 : 9.99;
            const total = subtotal + shipping;

            const summaryHtml = `
                <div class="cart-summary">
                    <div class="summary-title">Order Summary</div>
                    <div class="summary-row">
                        <span>Subtotal (${cart.length} items)</span>
                        <span>$${subtotal.toFixed(2)}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>${shipping === 0 ? 'FREE' : '$' + shipping.toFixed(2)}</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>$${total.toFixed(2)}</span>
                    </div>
                    <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    <a href="index.php" class="continue-shopping">← Continue Shopping</a>
                </div>
            `;

            container.innerHTML = cartItemsHtml + summaryHtml;
        }

        // Change quantity
        function changeQuantity(index, delta) {
            const newQuantity = cart[index].quantity + delta;
            if (newQuantity >= 1) {
                cart[index].quantity = newQuantity;
                saveCart();
                renderCart();
            }
        }

        // Update quantity directly
        function updateQuantity(index, newQuantity) {
            const qty = parseInt(newQuantity);
            if (qty >= 1) {
                cart[index].quantity = qty;
                saveCart();
                renderCart();
            }
        }

        // Remove item from cart
        function removeItem(index) {
            if (confirm('Remove this item from your cart?')) {
                cart.splice(index, 1);
                saveCart();
                renderCart();
            }
        }

        // Proceed to checkout
        function proceedToCheckout() {
            window.location.href = 'checkout.php';
        }

        // Initialize
        async function init() {
            await loadProducts();
            loadCart();
        }

        // Load cart when page loads
        init();
    </script>

    <script src="js/main.js"></script>
</body>
</html>