<?php
// Load dynamic settings
$settings = [];
$categories = [];
try {
    $settingsResponse = file_get_contents('http://localhost/RetailRow/api/settings.php?keys=announcement_text,phone_number,promo_text,flash_sale_timer,site_title');
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
        'promo_text' => 'Get up to 70% off on selected items',
        'flash_sale_timer' => '24:00:00',
        'site_title' => 'RetailRow — Shop Quality Products at the Best Prices in Ghana'
    ];
    $categories = [
        ['id' => 1, 'name' => 'Electronics'],
        ['id' => 2, 'name' => 'Fashion'],
        ['id' => 3, 'name' => 'Home & Garden']
    ];
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($settings['site_title'] ?? 'RetailRow — Shop Quality Products at the Best Prices in Ghana'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Dropdown styles for account menu */
        .nav-item-dropdown {
            position: relative;
        }
        .nav-item-dropdown .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            min-width: 180px;
            border-radius: 4px;
            padding: 8px 0;
            border: 1px solid #eee;
        }
        .nav-item-dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown-menu a.dropdown-item:hover {
            background-color: #f5f5f5;
        }
        .dropdown-menu .dropdown-item {
            white-space: nowrap;
        }
    </style>
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
                        <path fill="currentColor"
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
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
                    <form class="search-form" action="search.php" method="GET">
                        <input type="search" id="searchInput" name="q" placeholder="Search for products, brands and categories"
                            aria-label="Search products" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" autocomplete="off" required>
                        <button type="submit" class="search-btn">SEARCH</button>
                    </form>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>

                <!-- Right Nav Items -->
                <div class="nav-actions">
                    <div class="nav-item-dropdown" id="accountDropdownContainer">
                        <a href="account.php" class="nav-btn account-btn" id="accountLink">
                            <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor"
                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                            <span id="accountText">Account</span>
                            <svg class="icon-arrow" viewBox="0 0 24 24" width="16" height="16">
                                <path fill="currentColor" d="M7 10l5 5 5-5z" />
                            </svg>
                        </a>
                        <div class="dropdown-menu" id="accountDropdownMenu">
                            <a href="account.php" class="dropdown-item">Login</a>
                            <a href="account.php#register" class="dropdown-item">Register</a>
                            <a href="account.php#profile" class="dropdown-item">Profile</a>
                        </div>
                    </div>

                    <a href="help.php" class="nav-btn help-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor"
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z" />
                        </svg>
                        <span>Help</span>
                    </a>

                    <button id="cartToggle" class="nav-btn cart-btn">
                        <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor"
                                d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
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
            <!-- Hero Section (3-Column Grid) -->
            <section class="hero-section">
                <!-- Left: Category Sidebar -->
                <aside class="category-sidebar">
                    <nav class="category-nav" id="categoryNav">
                        <?php
                        // Define SVG icons for categories
                        $categoryIcons = [
                            'Electronics' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M21 6h-7.59l3.29-3.29L16 2l-4 4-4-4-.71.71L10.59 6H3c-1.1 0-2 .89-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.11-.9-2-2-2zm0 14H3V8h18v12zM9 10v8l7-4z"/>
                            </svg>',
                            'Fashion' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>',
                            'Home & Garden' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>',
                            'Sports' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.89l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z"/>
                            </svg>',
                            'Books' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                            </svg>',
                            'Beauty' => '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M9 11.75c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zm6 0c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-.29.02-.58.05-.86 2.36-1.05 4.23-2.98 5.21-5.37C11.07 8.33 14.05 10 17.42 10c.78 0 1.53-.09 2.25-.26.21.71.33 1.47.33 2.26 0 4.41-3.59 8-8 8z"/>
                            </svg>'
                        ];

                        $defaultIcon = '<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>';

                        foreach ($categories as $category) {
                            $icon = '';
                            if (!empty($category['icon'])) {
                                $iconPath = htmlspecialchars($category['icon']);
                                $icon = "<img src=\"{$iconPath}\" alt=\"" . htmlspecialchars($category['name']) . "\" class=\"cat-icon\">";
                            } else {
                                // Fallback to predefined SVG icons if no image is provided
                                $icon = $categoryIcons[$category['name']] ?? $defaultIcon;
                            }
                            echo "<a href=\"category.php?id={$category['id']}\" class=\"cat-link\">
                                {$icon}
                                <span>" . htmlspecialchars($category['name']) . "</span>
                            </a>";
                        }
                        ?>
                    </nav>
                </aside>

                <!-- Center: Hero <td>
                    <?php if (!empty($banner['image'])): ?>
                        <img src="../../<?php echo htmlspecialchars(['image']); ?>" alt="<?php echo htmlspecialchars(['title']); ?>" style="width: 200px; height: auto; border-radius: 4px;">
                    <?php else: ?>
                        <span style="color: #888;">No Image</span>
                    <?php endif; ?>
                </td>
                 -->
                <div class="hero-carousel">
                    <div class="carousel-container" id="carousel">
                        <div class="carousel-slides">
                            <!-- Placeholder slide - will be replaced by dynamic banners -->
                            <div class="carousel-slide slide-active">
                                <div class="slide-content slide-promo" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h2>Loading...</h2>
                                    <p class="promo-text">Please wait while we load our latest offers</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-dots" id="carouselDots">
                            <button class="active"></button>
                        </div>
                    </div>
                </div>

                <!-- Right: Service Cards -->
                <aside class="service-cards">
                    <div class="service-card">
                        <svg class="service-icon" viewBox="0 0 24 24" width="32" height="32">
                            <path fill="#f68b1e"
                                d="M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zm1.5-9H17V12h4.46L19.5 9.5zM6 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM20 8l3 4v5h-2c0 1.66-1.34 3-3 3s-3-1.34-3-3H9c0 1.66-1.34 3-3 3s-3-1.34-3-3H1V6c0-1.11.89-2 2-2h14v4h3zM3 6v9h.76c.55-.61 1.35-1 2.24-1 .89 0 1.69.39 2.24 1H15V6H3z" />
                        </svg>
                        <div class="service-text">
                            <h4>RETAILROW DELIVERY</h4>
                            <p>Send parcels easily</p>
                        </div>
                    </div>

                    <div class="service-card">
                        <svg class="service-icon" viewBox="0 0 24 24" width="32" height="32">
                            <path fill="#f68b1e"
                                d="M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zm1.5-9H17V12h4.46L19.5 9.5zM6 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM20 8l3 4v5h-2c0 1.66-1.34 3-3 3s-3-1.34-3-3H9c0 1.66-1.34 3-3 3s-3-1.34-3-3H1V6c0-1.11.89-2 2-2h14v4h3zM3 6v9h.76c.55-.61 1.35-1 2.24-1 .89 0 1.69.39 2.24 1H15V6H3z" />
                        </svg>
                        <div class="service-text">
                            <h4>RETAILROW DELIVERY</h4>
                            <p>Send parcels easily</p>
                        </div>
                    </div>
                    <div class="service-card">
                        <svg class="service-icon" viewBox="0 0 24 24" width="32" height="32">
                            <path fill="#f68b1e"
                                d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" />
                        </svg>
                        <div class="service-text">
                            <h4>SELL ON RETAILROW</h4>
                            <p>Make extra cash</p>
                        </div>
                    </div>
                    <div class="service-card">
                        <svg class="service-icon" viewBox="0 0 24 24" width="32" height="32">
                            <path fill="#f68b1e"
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                        <div class="service-text">
                            <h4>TRACK YOUR ORDER</h4>
                            <p>Track order in real time</p>
                        </div>
                    </div>
                </aside>
            </section>

            <!-- Flash Sales Section -->
            <section class="flash-sales">
                <div class="section-header">
                    <div class="section-title">
                        <svg class="icon-flash" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="#fff" d="M7 2v11h3v9l7-12h-4l4-8z" />
                        </svg>
                        <span>Flash Sales</span>
                    </div>
                    <div class="flash-timer">
                        <span class="timer-label">Time Left:</span>
                        <span id="flashTimer" class="timer-digits">00h : 15m : 37s</span>
                    </div>
                    <a href="#" class="see-all">SEE ALL <span>›</span></a>
                </div>

                <div class="flash-products-wrapper">
                    <button class="scroll-arrow left" aria-label="Scroll left">‹</button>
                    <div class="flash-products" id="flashScroll">
                        <a href="product.php?id=1" class="product-card-link">
                        <div class="product-card flash-product">
                            <div class="discount-badge">-25%</div>
                            <div class="product-image">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23ddd' width='150' height='150'/%3E%3C/svg%3E"
                                    alt="Product">
                            </div>
                            <h3 class="product-title">Gallon 2 Burner Electric Hotplate</h3>
                            <div class="product-price">GH₵ 118.24</div>
                            <div class="product-old-price">GH₵ 165.00</div>
                            <div class="stock-bar">
                                <div class="stock-progress" style="width: 45%"></div>
                            </div>
                            <div class="stock-text">8 items left</div>
                        </div>
                        </a>

                        <a href="product.php?id=2" class="product-card-link">
                        <div class="product-card flash-product">
                            <div class="discount-badge">-25%</div>
                            <div class="product-image">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Ccircle fill='%23f68b1e' cx='75' cy='75' r='50'/%3E%3C/svg%3E"
                                    alt="Product">
                            </div>
                            <h3 class="product-title">T-Face Size 7 Basketball</h3>
                            <div class="product-price">GH₵ 69.00</div>
                            <div class="product-old-price">GH₵ 92.00</div>
                            <div class="stock-bar">
                                <div class="stock-progress" style="width: 15%"></div>
                            </div>
                            <div class="stock-text">5 items left</div>
                        </div>
                        </a>

                        <a href="product.php?id=3" class="product-card-link">
                        <div class="product-card flash-product">
                            <div class="discount-badge">-46%</div>
                            <div class="product-image">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23e0e0e0' width='150' height='150'/%3E%3C/svg%3E"
                                    alt="Product">
                            </div>
                            <h3 class="product-title">Mens Hair Clippers Kit Professional</h3>
                            <div class="product-price">GH₵ 109.00</div>
                            <div class="product-old-price">GH₵ 203.00</div>
                            <div class="stock-bar">
                                <div class="stock-progress" style="width: 62%"></div>
                            </div>
                            <div class="stock-text">42 items left</div>
                        </div>
                        </a>

                        <a href="product.php?id=4" class="product-card-link">
                        <div class="product-card flash-product">
                            <div class="discount-badge">-15%</div>
                            <div class="product-image">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23333' width='150' height='150'/%3E%3C/svg%3E"
                                    alt="Product">
                            </div>
                            <h3 class="product-title">Nexus NASJ-X110 Double Door Refrigerator</h3>
                            <div class="product-price">GH₵ 1,235.00</div>
                            <div class="product-old-price">GH₵ 1,456.00</div>
                            <div class="stock-bar">
                                <div class="stock-progress" style="width: 30%"></div>
                            </div>
                            <div class="stock-text">10 items left</div>
                        </div>
                        </a>

                        <a href="product.php?id=5" class="product-card-link">
                        <div class="product-card flash-product">
                            <div class="discount-badge">-42%</div>
                            <div class="product-image">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23555' width='150' height='150'/%3E%3C/svg%3E"
                                    alt="Product">
                            </div>
                            <h3 class="product-title">20000mAh Small Size Universal Power Bank</h3>
                            <div class="product-price">GH₵ 63.00</div>
                            <div class="product-old-price">GH₵ 109.00</div>
                            <div class="stock-bar">
                                <div class="stock-progress" style="width: 80%"></div>
                            </div>
                            <div class="stock-text">498 items left</div>
                        </div>
                        </a>

                        <a href="product.php?id=6" class="product-card-link">
                        <div class="product-card flash-product">
                            <div class="discount-badge">-46%</div>
                            <div class="product-image">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23222' width='150' height='150'/%3E%3C/svg%3E"
                                    alt="Product">
                            </div>
                            <h3 class="product-title">I-socks Men Casual Sports Compression Socks</h3>
                            <div class="product-price">GH₵ 73.00 - GH₵ 83.70</div>
                            <div class="product-old-price">GH₵ 155.00</div>
                            <div class="stock-bar">
                                <div class="stock-progress" style="width: 18%"></div>
                            </div>
                            <div class="stock-text">18 items left</div>
                        </div>
                        </a>
                    </div>
                    <button class="scroll-arrow right" aria-label="Scroll right">›</button>
                </div>
            </section>

            <!-- Category Tiles -->
            <section class="category-tiles">
                <div class="tiles-grid">
                    <a href="#" class="tile-card tile-large">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%2341b06e' width='200' height='200'/%3E%3C/svg%3E"
                            alt="RetailRow Delivery">
                        <div class="tile-label">RetailRow Loves You</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%2367b7dc' width='200' height='200'/%3E%3C/svg%3E"
                            alt="Send Packages">
                        <div class="tile-label">Send Packages Securely</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%23f68b1e' width='200' height='200'/%3E%3C/svg%3E"
                            alt="Call to Order">
                        <div class="tile-label">Call to Order</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%23dc4f72' width='200' height='200'/%3E%3C/svg%3E"
                            alt="iDonboam Sales">
                        <div class="tile-label">iDonboam Sales</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Crect fill='%23e8f5e9' width='200' height='200'/%3E%3C/svg%3E"
                            alt="Blenders">
                        <div class="tile-label">Blenders</div>
                    </a>
                </div>
            </section>

            <!-- More Category Tiles (Second Row) -->
            <section class="category-tiles">
                <div class="tiles-grid tiles-grid-6">
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23fef3e8' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Kumasi Marketplace">
                        <div class="tile-label">Kumasi Marketplace</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23e3f2fd' width='150' height='150'/%3E%3C/svg%3E"
                            alt="New Arrivals">
                        <div class="tile-label">New Arrivals</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23fff9e6' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Groceries">
                        <div class="tile-label">Groceries</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23424242' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Fashion">
                        <div class="tile-label">Fashion</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23fce4ec' width='150' height='150'/%3E%3C/svg%3E"
                            alt="New this Week">
                        <div class="tile-label">New this Week</div>
                    </a>
                    <a href="#" class="tile-card tile-cta">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23e61601' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Sell on RetailRow">
                        <div class="tile-label">SELL ON</div>
                        <div class="tile-sublabel">RETAILROW</div>
                        <div class="tile-cta-text">and Earn Extra Cash</div>
                    </a>
                </div>
            </section>

            <!-- Third Row of Category Tiles -->
            <section class="category-tiles">
                <div class="tiles-grid tiles-grid-6">
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23212121' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Men Watches">
                        <div class="tile-label">Men Watches</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23263238' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Official Stores">
                        <div class="tile-label">Official Stores</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23e1f5fe' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Computing">
                        <div class="tile-label">Computing</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23f5f5f5' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Men Sneakers">
                        <div class="tile-label">Men Sneakers</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23e8eaf6' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Trending Now">
                        <div class="tile-label">Trending Now</div>
                    </a>
                    <a href="#" class="tile-card">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Crect fill='%23eeeeee' width='150' height='150'/%3E%3C/svg%3E"
                            alt="Appliances">
                        <div class="tile-label">Appliances</div>
                    </a>
                </div>
            </section>

            <!-- Top Selling Items -->
            <section class="product-section">
                <div class="section-title-bar">
                    <h2 class="section-heading">Top selling items</h2>
                    <a href="#" class="see-all-link">SEE ALL <span>›</span></a>
                </div>

                <div class="product-grid">
                    <div class="product-card">
                        <div class="discount-badge">-30%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23f5f5f5' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Inflatable Leisure Bean Bag Chair</h3>
                        <div class="product-price">GH₵ 220.00</div>
                        <div class="product-old-price">GH₵ 314.00</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-9%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%234a90e2' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Bedding Set (2 Pillow Cover + 1 Bed Sheet + 1 Duvet)</h3>
                        <div class="product-price">GH₵ 182.97</div>
                        <div class="product-old-price">GH₵ 199.97</div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23ffc0cb' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">AIDIALU Women's Sexy Lingerie Set - Black</h3>
                        <div class="product-price">GH₵ 104.00</div>
                        <div class="product-old-price">GH₵ 128.00</div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23333' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Universal 4G LTE Portable WiFi Router</h3>
                        <div class="product-price">GH₵ 165.00</div>
                        <div class="product-old-price"></div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23e0e0e0' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">2-pieces long-sleeved plaid shirt for men</h3>
                        <div class="product-price">GH₵ 175.00</div>
                        <div class="product-old-price">GH₵ 244.00</div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23fff' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">5-piece men's ice silk vest</h3>
                        <div class="product-price">GH₵ 208.76 - GH₵ 158.00</div>
                        <div class="product-old-price">GH₵ 284.84</div>
                    </div>
                </div>
            </section>

            <!-- Large Promo Banner -->
            <section class="promo-banner">
                <div class="banner-content">
                    <div class="banner-left">
                        <h2 class="banner-title">Black Love</h2>
                        <div class="banner-subtitle">RETAILROW LOVES YOU</div>
                        <div class="banner-badge">GET <span class="discount-large">-10%</span> RETAILROW LOVES YOU DEALS
                        </div>
                        <div class="banner-date">FEB 2ND | 12PM-3PM</div>
                        <div class="banner-terms">Min spend GH₵4000 | Capped at GH₵ 50</div>
                        <div class="banner-payments">
                            <span>ONLY</span>
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='25'%3E%3Crect fill='%231a1f71' width='40' height='25'/%3E%3C/svg%3E"
                                alt="VISA">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='25'%3E%3Crect fill='%23eb001b' width='40' height='25'/%3E%3C/svg%3E"
                                alt="Mastercard">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='25'%3E%3Crect fill='%23ffcb05' width='40' height='25'/%3E%3C/svg%3E"
                                alt="MTN">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='25'%3E%3Crect fill='%23e30613' width='40' height='25'/%3E%3C/svg%3E"
                                alt="Vodafone">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='25'%3E%3Crect fill='%23009ddc' width='40' height='25'/%3E%3C/svg%3E"
                                alt="AirtelTigo">
                        </div>
                    </div>
                    <div class="banner-right">
                        <div class="banner-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23dc143c' width='400' height='300'/%3E%3C/svg%3E"
                                alt="Promo visual">
                        </div>
                    </div>
                </div>
                <div class="banner-note">*T&C apply</div>
            </section>

            <!-- Deals You Don't Want to Miss -->
            <section class="product-section deals-section">
                <div class="section-title-bar">
                    <h2 class="section-heading">Deals You Don't Want to Miss | Up to 60% off</h2>
                    <a href="#" class="see-all-link">SEE ALL <span>›</span></a>
                </div>

                <div class="product-grid product-grid-large">
                    <div class="product-card">
                        <div class="discount-badge">-20%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%234a90e2' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Rich RFR / RSB-B 11/11L Gas Burner</h3>
                        <div class="product-price">GH₵ 2,266.00</div>
                        <div class="product-old-price">GH₵ 2,832.50</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-22%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23fff' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">NIVEA Black & White Invisible Antiperspirant Spray</h3>
                        <div class="product-price">GH₵ 56.00</div>
                        <div class="product-old-price">GH₵ 71.79</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-40%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%2341b06e' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">AMR ITALANG AMU-GZ401 Gas Cooker - 4 Burner</h3>
                        <div class="product-price">GH₵ 100.00</div>
                        <div class="product-old-price"></div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-26%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23e8f5e9' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Rich RE-LE4405 B Digital LED Indicator TV - 43"</h3>
                        <div class="product-price">GH₵ 2,928.00</div>
                        <div class="product-old-price">GH₵ 3,956.76</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-20%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23333' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Rich 1 Burner Gas Stove - Stainless Steel</h3>
                        <div class="product-price">GH₵ 105.88</div>
                        <div class="product-old-price">GH₵ 132.35</div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%234a90e2' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Samsung Galaxy A16 5-6 128GB/256GB Dual Sim</h3>
                        <div class="product-price">GH₵ 1,495.00</div>
                        <div class="product-old-price"></div>
                    </div>
                </div>

                <!-- Second Row -->
                <div class="product-grid product-grid-large">
                    <div class="product-card">
                        <div class="discount-badge">-30%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23e3f2fd' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Multi-functional Diaper Bag With USB</h3>
                        <div class="product-price">GH₵ 142.00</div>
                        <div class="product-old-price">GH₵ 203.00</div>
                    </div>

                    <div class="product-card">
                        <div class="product-badge hot">HOT</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23fff' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Rechargeable Fan - White</h3>
                        <div class="product-price">GH₵ 146.00</div>
                        <div class="product-old-price">GH₵ 291.00</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-38%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23212121' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Multiple In 1 Power Bank Wireless & Wall Charger</h3>
                        <div class="product-price">GH₵ 104.00</div>
                        <div class="product-old-price">GH₵ 167.20</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-20%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23333' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Oraimo PowerBank-OPB-P218D 20000mAh</h3>
                        <div class="product-price">GH₵ 180.00</div>
                        <div class="product-old-price">GH₵ 225.00</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-26%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23ff5722' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Hikers Digital Smartwatch - HD-G900 Ultra 2</h3>
                        <div class="product-price">GH₵ 94.00</div>
                        <div class="product-old-price">GH₵ 127.00</div>
                    </div>

                    <div class="product-card">
                        <div class="discount-badge">-33%</div>
                        <div class="product-image">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Crect fill='%23212121' width='180' height='180'/%3E%3C/svg%3E"
                                alt="Product">
                        </div>
                        <h3 class="product-title">Universal Hair Trimmer/clipper Set</h3>
                        <div class="product-price">GH₵ 71.99</div>
                        <div class="product-old-price">GH₵ 107.46</div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <!-- Newsletter -->
        <div class="newsletter-section">
            <div class="container newsletter-inner">
                <div class="newsletter-text">Get the latest deals</div>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" aria-label="Email address">
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>

        <!-- Footer Links -->
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
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='30'%3E%3Crect fill='%23ffcb05' width='50' height='30' rx='3'/%3E%3C/svg%3E"
                            alt="MTN Mobile Money">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='30'%3E%3Crect fill='%231a1f71' width='50' height='30' rx='3'/%3E%3C/svg%3E"
                            alt="VISA">
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

        <!-- Footer Bottom -->
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
            <button class="cart-checkout">CHECKOUT (GH₵ <span id="checkoutTotal">0.00</span>)</button>
        </div>
    </aside>

    <!-- Mobile Drawer -->
    <div class="mobile-drawer" id="mobileDrawer" aria-hidden="true">
        <div class="drawer-header">
            <h3>Menu</h3>
            <button id="drawerClose" class="drawer-close" aria-label="Close menu">✕</button>
        </div>
        <nav class="drawer-nav">
            <a href="#" class="drawer-link">
                <svg class="drawer-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path fill="currentColor"
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
                <span>My Account</span>
            </a>
            <a href="#" class="drawer-link">
                <svg class="drawer-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                </svg>
                <span>Help</span>
            </a>
        </nav>
        <nav class="category-nav" id="categoryNavMobile">
            <?php
            foreach ($categories as $category) {
                $icon = '';
                if (!empty($category['icon'])) {
                    $iconPath = htmlspecialchars($category['icon']);
                    $icon = "<img src=\"{$iconPath}\" alt=\"" . htmlspecialchars($category['name']) . "\" class=\"cat-icon\">";
                } else {
                    // Fallback to predefined SVG icons if no image is provided
                    $icon = $categoryIcons[$category['name']] ?? $defaultIcon;
                }
                echo "<a href=\"category.php?id={$category['id']}\" class=\"cat-link\">
                    {$icon}
                    <span>" . htmlspecialchars($category['name']) . "</span>
                </a>";
            }
            ?>
        </nav>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="overlay" aria-hidden="true"></div>

    <!-- WhatsApp Button -->
    <a href="#" id="whatsappBtn" class="whatsapp-float" aria-label="Contact us on WhatsApp">
        <svg viewBox="0 0 24 24" width="24" height="24">
            <path fill="#fff"
                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
        </svg>
    </a>

    <!-- Back to Top -->
    <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>

    <script>
        // This function updates the header UI based on authentication status.
        const updateAuthUI = (user) => {
            const accountText = document.getElementById('accountText');
            const accountDropdownMenu = document.getElementById('accountDropdownMenu');

            if (user) {
                // User is logged in
                if (accountText) {
                    // Display user's first name, fallback to 'Account'
                    accountText.textContent = user.name ? user.name.split(' ')[0] : 'Account';
                }
                if (accountDropdownMenu) {
                    accountDropdownMenu.innerHTML = `
                        <a href="account.php" class="dropdown-item">My Account</a>
                        <a href="account.php#profile" class="dropdown-item">Profile</a>
                        <a href="account.php#orders" class="dropdown-item">Orders</a>
                        <a href="#" onclick="auth.logout()" class="dropdown-item">Logout</a>
                    `;
                }
            }
            // If user is not logged in, the default HTML for the dropdown is already correct.
        };

        // Dynamic data loading
        document.addEventListener('DOMContentLoaded', async function() {
            const user = await auth.checkUser();
            updateAuthUI(user);

            loadBanners();
            loadFlashSales();
            loadFeaturedProducts();
        });

        // Load categories dynamically
        function loadCategories() {
            fetch('api/categories.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const categoryNav = document.getElementById('categoryNav');
                        if (categoryNav) {
                            // Define SVG icons for categories
                            const categoryIcons = {
                                'Electronics': `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path fill="currentColor" d="M21 6h-7.59l3.29-3.29L16 2l-4 4-4-4-.71.71L10.59 6H3c-1.1 0-2 .89-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.11-.9-2-2-2zm0 14H3V8h18v12zM9 10v8l7-4z"/>
                                </svg>`,
                                'Fashion': `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>`,
                                'Home & Garden': `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                </svg>`,
                                'Sports': `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path fill="currentColor" d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.89l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z"/>
                                </svg>`,
                                'Books': `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path fill="currentColor" d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                                </svg>`,
                                'Beauty': `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                    <path fill="currentColor" d="M9 11.75c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zm6 0c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-.29.02-.58.05-.86 2.36-1.05 4.23-2.98 5.21-5.37C11.07 8.33 14.05 10 17.42 10c.78 0 1.53-.09 2.25-.26.21.71.33 1.47.33 2.26 0 4.41-3.59 8-8 8z"/>
                                </svg>`
                            };

                            categoryNav.innerHTML = data.data.map(category => `
                                <a href="category.php?id=${category.id}" class="cat-link">
                                    ${categoryIcons[category.name] || `<svg class="cat-icon" viewBox="0 0 24 24" width="20" height="20">
                                        <path fill="currentColor" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>`}
                                    <span>${category.name}</span>
                                </a>
                            `).join('');
                        }
                    }
                })
                .catch(error => console.error('Error loading categories:', error));
        }

        // Load banners dynamically and initialize carousel
// Load banners dynamically and initialize carousel
async function loadBanners() {
    try {
        const response = await fetch('api/banners.php');
        const data = await response.json();

        if (data.success && data.data && data.data.length > 0) {
            const carouselSlides = document.querySelector('.carousel-slides');
            const dotsContainer = document.getElementById('carouselDots');
            
            if (carouselSlides) {
                // Create slides with an explicit image wrapper
                carouselSlides.innerHTML = data.data.map((banner, index) => `
                    <div class="carousel-slide ${index === 0 ? 'slide-active' : ''}">
                        <div class="slide-image">
                            <img src="${banner.image}" alt="${banner.title || 'Special Offer'}">
                        </div>
                        <div class="slide-content">
                            <h2 class="slide-title">${banner.title || 'Good sale'}</h2>
                            ${banner.subtitle ? `<p class="promo-text">${banner.subtitle}</p>` : ''}
                            <button class="promo-btn" onclick="window.location.href='${banner.link || '#'}'">
                                ${banner.button_text || 'SHOP NOW'}
                            </button>
                        </div>
                    </div>
                `).join('');

                // Update dots
                if (dotsContainer) {
                    dotsContainer.innerHTML = data.data.map((_, index) => 
                        `<button class="${index === 0 ? 'active' : ''}"></button>`
                    ).join('');
                }

                initializeCarousel();
            }
        }
    } catch (error) {
        console.error('Error loading banners:', error);
    }
}

        // Initialize carousel functionality
        function initializeCarousel() {
            const slidesEl = document.querySelector('.carousel-slides');
            const slides = [...document.querySelectorAll('.carousel-slide')];
            const dotsEl = document.getElementById('carouselDots');
            let idx = 0;
            const autoplay = 5000;

            if (slides.length === 0 || !dotsEl) return;

            // Set up dot click handlers
            const dots = dotsEl.querySelectorAll('button');
            dots.forEach((dot, i) => {
                dot.addEventListener('click', () => goTo(i));
            });

            function goTo(i) {
                idx = i;
                update();
                resetTimer();
            }

            function update() {
                if (slidesEl) {
                    slidesEl.style.transform = `translateX(${-idx * 100}%)`;
                }
                // Update active dot
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === idx);
                });
            }

            let timer = setInterval(() => { 
                idx = (idx + 1) % slides.length; 
                update();
            }, autoplay);

            function resetTimer() {
                clearInterval(timer);
                timer = setInterval(() => { 
                    idx = (idx + 1) % slides.length; 
                    update();
                }, autoplay);
            }

            // Pause on hover
            const carouselEl = document.querySelector('.hero-carousel');
            if (carouselEl) {
                carouselEl.addEventListener('mouseenter', () => clearInterval(timer));
                carouselEl.addEventListener('mouseleave', () => {
                    timer = setInterval(() => { 
                        idx = (idx + 1) % slides.length; 
                        update();
                    }, autoplay);
                });
            }
        }

        // Load flash sales dynamically
        async function loadFlashSales() {
            try {
                const response = await fetch('api/flash-sales.php');
                const data = await response.json();

                if (data.success && data.data && data.data.length > 0) {
                    const flashProducts = document.querySelector('.flash-products');
                    if (flashProducts) {
                        flashProducts.innerHTML = data.data.slice(0, 6).map(product => `
                            <a href="product.php?id=${product.id}" class="product-card-link">
                                <div class="product-card flash-product">
                                    <div class="discount-badge">-${product.discount_percentage}%</div>
                                    <div class="product-image">
                                        <img src="${product.image || 'assets/images/placeholder.jpg'}" alt="${product.name}">
                                    </div>
                                    <h3 class="product-title">${product.name}</h3>
                                    <div class="product-price">GH₵${(product.price * (1 - product.discount_percentage / 100)).toFixed(2)}</div>
                                    <div class="product-old-price">GH₵${product.price}</div>
                                    <div class="stock-bar">
                                        <div class="stock-progress" style="width: ${Math.min((product.stock / 100) * 100, 100)}%"></div>
                                    </div>
                                    <div class="stock-text">${product.stock} left</div>
                                </div>
                            </a>
                        `).join('');

                        // Start timer if we have flash sales
                        if (data.data.length > 0 && data.data[0].end_date) {
                            startFlashSaleTimer(data.data[0].end_date);
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading flash sales:', error);
            }
        }

        function startFlashSaleTimer(endDate) {
            const timerElement = document.getElementById('flashTimer');
            if (!timerElement) return;

            const endTime = new Date(endDate).getTime();

            const timerInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    timerElement.textContent = "EXPIRED";
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerElement.textContent = `${String(hours).padStart(2, '0')}h : ${String(minutes).padStart(2, '0')}m : ${String(seconds).padStart(2, '0')}s`;
            }, 1000);
        }

        // Load featured products dynamically
        async function loadFeaturedProducts() {
            try {
                const response = await fetch('api/products.php?type=featured');
                const data = await response.json();

                if (data.success && data.data) {
                    const productGrid = document.querySelector('.product-grid');
                    if (productGrid) {
                        productGrid.innerHTML = data.data.slice(0, 6).map(product => `
                            <a href="product.php?id=${product.id}" class="product-card-link">
                                <div class="product-card">
                                    ${product.discount > 0 ? `<div class="discount-badge">-${product.discount}%</div>` : ''}
                                    <div class="product-image">
                                        <img src="${product.image || 'assets/images/placeholder.jpg'}" alt="${product.name}">
                                    </div>
                                    <h3 class="product-title">${product.name}</h3>
                                    <div class="product-price">GH₵${product.price}</div>
                                    ${product.old_price ? `<div class="product-old-price">GH₵${product.old_price}</div>` : ''}
                                </div>
                            </a>
                        `).join('');
                    }
                }
            } catch (error) {
                console.error('Error loading featured products:', error);
            }
        }
    </script>

    <script src="js/api.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
</body>

</html>