<?php
require_once 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($id > 0) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Handle error
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Product Not Found'; ?> - RetailRow</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/product.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="main-nav">
            <div class="container nav-inner">
                <div class="nav-logo">
                    <a href="index.php" class="logo-link"><div class="logo-box">RetailRow</div></a>
                </div>
                <div class="nav-search">
                    <form class="search-form" action="search.php" method="GET">
                        <input type="search" id="searchInput" name="q" placeholder="Search products..." autocomplete="off" required>
                        <button type="submit" class="search-btn">SEARCH</button>
                    </form>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>
                <div class="nav-actions">
                    <button id="cartToggle" class="nav-btn cart-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        <span>Cart</span>
                        <span class="cart-count" id="cartCount">0</span>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if (!$product): ?>
                <div class="product-details-container" style="background: white; padding: 40px; text-align: center; margin-top: 20px; border-radius: 4px;">
                    <h2 style="font-size: 24px; margin-bottom: 16px;">Product Not Found</h2>
                    <p style="color: #75757a; margin-bottom: 24px;">The product you are looking for does not exist or has been removed.</p>
                    <a href="index.php" class="search-btn" style="display: inline-block; text-decoration: none; line-height: 40px;">Back to Home</a>
                </div>
            <?php else: ?>
            <div id="product-details-container">
                <!-- Product Gallery -->
                <div class="product-gallery">
                    <div class="main-image product-zoom-container">
                        <?php if(($product['discount'] ?? 0) > 0): ?>
                            <div class="discount-badge">-<?php echo $product['discount']; ?>%</div>
                        <?php endif; ?>
                        <img class="product-zoom-image" id="mainProductImage" src="<?php echo htmlspecialchars($product['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="thumbnails">
                        <div class="thumbnail active" onclick="document.getElementById('mainProductImage').src='<?php echo htmlspecialchars($product['image'] ?? 'assets/images/placeholder.jpg'); ?>'">
                            <img src="<?php echo htmlspecialchars($product['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="">
                        </div>
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="product-info">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="category"><?php echo htmlspecialchars($product['category_name'] ?? 'General'); ?></div>
                    
                    <div class="rating-row">
                        <div class="star-rating">
                            <span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star">★</span><span class="star" style="color: #DDD;">★</span>
                        </div>
                        <span class="rating-text"><span class="rating-count">4.5</span> (12 reviews)</span>
                    </div>
                    
                    <div class="price-section">
                        <span class="price">GH₵ <?php echo number_format($product['price'], 2); ?></span>
                        <?php if (!empty($product['old_price'])): ?>
                            <div>
                                <span class="old-price">GH₵ <?php echo number_format($product['old_price'], 2); ?></span>
                                <span class="savings-text">Save GH₵ <?php echo number_format($product['old_price'] - $product['price'], 2); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="stock <?php echo ($product['stock'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                        <?php echo ($product['stock'] > 0) ? 'In Stock' : 'Out of Stock'; ?>
                    </div>

                    <div class="description">
                        <?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available for this product.')); ?>
                    </div>

                    <div class="quantity-cta-section">
                        <label class="quantity-label">Quantity</label>
                        <div class="quantity-selector">
                            <button type="button" onclick="updateQty(-1)">−</button>
                            <input type="number" id="qtyInput" value="1" min="1" max="<?php echo max(1, $product['stock']); ?>">
                            <button type="button" onclick="updateQty(1)">+</button>
                        </div>
                        
                        <button id="add-to-cart" onclick="addToCart()">ADD TO CART</button>
                    </div>

                    <div class="trust-badges">
                        <div class="trust-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span>Genuine Products</span>
                        </div>
                        <div class="trust-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span>Secure Payment</span>
                        </div>
                        <div class="trust-badge">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span>Easy Returns</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details Tabs -->
            <div class="product-details-tabs">
                <div class="tabs-nav">
                    <button class="tab-btn active" data-tab="description">Description</button>
                    <button class="tab-btn" data-tab="specs">Specifications</button>
                    <button class="tab-btn" data-tab="reviews">Reviews</button>
                </div>
                <div class="tabs-container">
                    <div id="description" class="tab-content active">
                        <div class="detail-card">
                            <h3>Product Description</h3>
                            <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No detailed description available for this product.')); ?></p>
                        </div>
                    </div>
                    <div id="specs" class="tab-content">
                        <div class="detail-card">
                            <h3>Specifications</h3>
                            <table class="specs-table">
                                <tbody>
                                    <tr>
                                        <td>Brand</td>
                                        <td><?php echo htmlspecialchars($product['brand'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Weight</td>
                                        <td><?php echo htmlspecialchars($product['weight'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Dimensions</td>
                                        <td><?php echo htmlspecialchars($product['dimensions'] ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>SKU</td>
                                        <td><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="reviews" class="tab-content">
                        <div class="detail-card">
                            <h3>Customer Reviews</h3>
                            <p>There are no reviews for this product yet.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="newsletter-section">
            <div class="container newsletter-inner">
                <div class="newsletter-text">Get the latest deals</div>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" aria-label="Email address">
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-content">
            <div class="container footer-grid">
                <div class="footer-col">
                    <h4>About</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="terms.php">Terms & Conditions</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="size-guide.php">Size Guide</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Help</h4>
                    <ul>
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="returns.php">Returns</a></li>
                        <li><a href="track-order.php">Track Order</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Payments & Delivery</h4>
                    <div class="payment-icons">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='30'%3E%3Crect fill='%23ffcb05' width='50' height='30' rx='3'/%3E%3C/svg%3E" alt="MTN Mobile Money">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='30'%3E%3Crect fill='%231a1f71' width='50' height='30' rx='3'/%3E%3C/svg%3E" alt="VISA">
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Social & Apps</h4>
                    <ul>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">App Store</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                © RetailRow
            </div>
        </div>
    </footer>

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
            <button class="cart-checkout">CHECKOUT (GH₵ <span id="checkoutTotal">0.00</span>)</button>
        </div>
    </aside>
    <div id="overlay" class="overlay" aria-hidden="true"></div>

    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
    <script>
    function updateQty(change) {
        const input = document.getElementById('qtyInput');
        let newVal = parseInt(input.value) + change;
        if (newVal < 1) newVal = 1;
        // Optional: Check max stock
        input.value = newVal;
    }

    function addToCart() {
        const qty = parseInt(document.getElementById('qtyInput').value);
        cart.addItem({
            id: <?php echo $product['id']; ?>, 
            name: '<?php echo addslashes($product['name']); ?>', 
            price: <?php echo $product['price']; ?>, 
            image: '<?php echo addslashes($product['image'] ?? ''); ?>'
        }, qty);
        cart.openCart();
    }

    // Tab switching logic
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
            });
        });
    });
    </script>
</body>
</html>