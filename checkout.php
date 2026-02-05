<?php
// This file can be used to load shared configurations or helpers if needed.
// For now, it's minimal as most logic is handled via API and JS.
require_once __DIR__ . '/config/db.php'; // Ensures we can get session info etc. if needed

// We can potentially pre-load settings here if we want to avoid another API call
// For now, the header will fetch them as it does on other pages.
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - RetailRow</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>

<body class="checkout-body">

    <?php
        // A real implementation should use a shared header file, 
        // but based on the project structure, we replicate it.
    ?>
    <!-- Header -->
    <header class="main-header">
        <nav class="main-nav">
            <div class="container nav-inner">
                <div class="nav-logo">
                     <a href="/" class="logo-link">
                        <div class="logo-box">RetailRow</div>
                    </a>
                </div>
                <div class="nav-actions">
                    <span class="secure-checkout-label">Secure Checkout</span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div id="checkout-container" class="checkout-container">
            <!-- The checkout steps will be dynamically rendered here by checkout.js -->
            <div class="loading-checkout">
                <h2>Loading Checkout...</h2>
                <!-- You can add a spinner or skeleton loader here -->
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
       <div class="footer-bottom">
            <div class="container">
                © RetailRow
            </div>
        </div>
    </footer>

    <!-- The cart panel is included for consistency, though it won't be used on this page -->
    <aside id="cartPanel" class="cart-panel" aria-hidden="true"></aside>
    <div id="overlay" class="overlay" aria-hidden="true"></div>

    <script src="js/checkout.js"></script>

</body>
</html>
