<?php
require_once 'config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$searchResults = [];

if (!empty($query)) {
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Search for products where the name matches the search term
        // We use prepared statements (:term) to prevent SQL injection
        $sql = "SELECT * FROM products WHERE name LIKE :term";
        $stmt = $db->prepare($sql);
        $term = "%" . $query . "%";
        $stmt->bindParam(':term', $term);
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Keep silent on errors or log them
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Results for "<?php echo htmlspecialchars($query); ?>" - RetailRow</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="main-nav">
            <div class="container nav-inner">
                <div class="nav-logo">
                    <button class="hamburger" id="hamburger" aria-label="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <a href="index.php" class="logo-link"><div class="logo-box">RetailRow</div></a>
                </div>
                <div class="nav-search">
                    <form class="search-form" action="search.php" method="GET">
                        <input type="search" id="searchInput" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search for products, brands and categories" autocomplete="off" required>
                        <button type="submit" class="search-btn">SEARCH</button>
                    </form>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>
                
                <!-- Right Nav Items -->
                <div class="nav-actions">
                    <button class="nav-btn account-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        <span>Account</span>
                        <svg class="icon-arrow" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 10l5 5 5-5z"/></svg>
                    </button>

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
            <section class="product-section">
                <h2 class="section-heading" style="margin-top: 20px; margin-bottom: 20px;">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
                
                <?php if (empty($searchResults)): ?>
                    <p>No products found matching "<?php echo htmlspecialchars($query); ?>".</p>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($searchResults as $product): ?>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="product-card-link">
                            <div class="product-card">
                                <div class="product-image">
                                    <?php $img = !empty($product['image']) ? $product['image'] : 'assets/images/placeholder.jpg'; ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-price">GH₵ <?php echo number_format($product['price'], 2); ?></div>
                                <?php if (!empty($product['old_price'])): ?>
                                    <div class="product-old-price">GH₵ <?php echo number_format($product['old_price'], 2); ?></div>
                                <?php endif; ?>
                            </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

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
            <button class="cart-checkout">CHECKOUT (GH₵ <span id="checkoutTotal">0.00</span>)</button>
        </div>
    </aside>

    <!-- Mobile Drawer -->
    <aside id="mobileDrawer" class="mobile-drawer" aria-hidden="true">
        <div class="drawer-header">
            <h3>Menu</h3>
            <button id="drawerClose" class="drawer-close" aria-label="Close menu">✕</button>
        </div>
        <div class="drawer-nav">
            <a href="index.php" class="drawer-link">
                <svg class="drawer-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Home
            </a>
            <a href="account.php" class="drawer-link">
                <svg class="drawer-icon" viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Account
            </a>
            <div id="categoryNavMobile"></div>
        </div>
    </aside>

    <div id="overlay" class="overlay" aria-hidden="true"></div>
    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>