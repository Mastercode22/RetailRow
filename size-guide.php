<?php
// Load dynamic settings
$settings = [];
try {
    $settingsResponse = file_get_contents('http://localhost/RetailRow/api/settings.php?keys=site_title');
    if ($settingsResponse) {
        $settingsData = json_decode($settingsResponse, true);
        if ($settingsData && isset($settingsData['data'])) {
            $settings = $settingsData['data'];
        }
    }
} catch (Exception $e) {
    $settings = [
        'site_title' => 'RetailRow — Shop Quality Products at the Best Prices in Ghana'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Size Guide - <?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Top Announcement Bar -->
    <div class="announcement-bar">
        <div class="container announce-inner">
            <div class="announce-left">
                Find your perfect fit with our size guide
            </div>
            <div class="announce-right">
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
                <h1>Size Guide</h1>
                <p>Find the perfect fit for your body type</p>
            </div>

            <div class="size-guide-content">
                <section class="size-section">
                    <h2>How to Measure Yourself</h2>
                    <div class="measurement-guide">
                        <div class="measurement-item">
                            <div class="measurement-image">
                                <img src="assets/images/size-guide/chest.png" alt="Chest measurement" style="width: 100px; height: 100px; object-fit: contain;">
                            </div>
                            <div class="measurement-details">
                                <h3>Chest</h3>
                                <p>Measure around the fullest part of your chest, keeping the tape horizontal.</p>
                            </div>
                        </div>
                        <div class="measurement-item">
                            <div class="measurement-image">
                                <img src="assets/images/size-guide/waist.png" alt="Waist measurement" style="width: 100px; height: 100px; object-fit: contain;">
                            </div>
                            <div class="measurement-details">
                                <h3>Waist</h3>
                                <p>Measure around your natural waistline, typically the narrowest part of your torso.</p>
                            </div>
                        </div>
                        <div class="measurement-item">
                            <div class="measurement-image">
                                <img src="assets/images/size-guide/hips.png" alt="Hips measurement" style="width: 100px; height: 100px; object-fit: contain;">
                            </div>
                            <div class="measurement-details">
                                <h3>Hips</h3>
                                <p>Measure around the widest part of your hips, keeping the tape horizontal.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="size-section">
                    <h2>Men's Size Chart</h2>
                    <div class="size-chart">
                        <table class="size-table">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Chest (inches)</th>
                                    <th>Waist (inches)</th>
                                    <th>Hips (inches)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>XS</td>
                                    <td>34-36</td>
                                    <td>28-30</td>
                                    <td>34-36</td>
                                </tr>
                                <tr>
                                    <td>S</td>
                                    <td>36-38</td>
                                    <td>30-32</td>
                                    <td>36-38</td>
                                </tr>
                                <tr>
                                    <td>M</td>
                                    <td>38-40</td>
                                    <td>32-34</td>
                                    <td>38-40</td>
                                </tr>
                                <tr>
                                    <td>L</td>
                                    <td>40-42</td>
                                    <td>34-36</td>
                                    <td>40-42</td>
                                </tr>
                                <tr>
                                    <td>XL</td>
                                    <td>42-44</td>
                                    <td>36-38</td>
                                    <td>42-44</td>
                                </tr>
                                <tr>
                                    <td>XXL</td>
                                    <td>44-46</td>
                                    <td>38-40</td>
                                    <td>44-46</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="size-section">
                    <h2>Women's Size Chart</h2>
                    <div class="size-chart">
                        <table class="size-table">
                            <thead>
                                <tr>
                                    <th>Size</th>
                                    <th>Chest (inches)</th>
                                    <th>Waist (inches)</th>
                                    <th>Hips (inches)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>XS</td>
                                    <td>32-34</td>
                                    <td>24-26</td>
                                    <td>34-36</td>
                                </tr>
                                <tr>
                                    <td>S</td>
                                    <td>34-36</td>
                                    <td>26-28</td>
                                    <td>36-38</td>
                                </tr>
                                <tr>
                                    <td>M</td>
                                    <td>36-38</td>
                                    <td>28-30</td>
                                    <td>38-40</td>
                                </tr>
                                <tr>
                                    <td>L</td>
                                    <td>38-40</td>
                                    <td>30-32</td>
                                    <td>40-42</td>
                                </tr>
                                <tr>
                                    <td>XL</td>
                                    <td>40-42</td>
                                    <td>32-34</td>
                                    <td>42-44</td>
                                </tr>
                                <tr>
                                    <td>XXL</td>
                                    <td>42-44</td>
                                    <td>34-36</td>
                                    <td>44-46</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="size-section">
                    <h2>Shoe Size Conversion</h2>
                    <div class="size-chart">
                        <table class="size-table">
                            <thead>
                                <tr>
                                    <th>US Size</th>
                                    <th>UK Size</th>
                                    <th>EU Size</th>
                                    <th>Foot Length (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>6</td>
                                    <td>5</td>
                                    <td>39</td>
                                    <td>24.5</td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>6</td>
                                    <td>40</td>
                                    <td>25.5</td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>7</td>
                                    <td>41</td>
                                    <td>26.5</td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>8</td>
                                    <td>42</td>
                                    <td>27.5</td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>9</td>
                                    <td>43</td>
                                    <td>28.5</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>10</td>
                                    <td>44</td>
                                    <td>29.5</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="size-section">
                    <h2>Fitting Tips</h2>
                    <div class="fitting-tips">
                        <div class="tip">
                            <h3>General Tips</h3>
                            <ul>
                                <li>Take measurements while wearing form-fitting clothing</li>
                                <li>Measure yourself in front of a mirror for accuracy</li>
                                <li>When in doubt, size up for a more comfortable fit</li>
                                <li>Consider the fabric - some materials stretch more than others</li>
                            </ul>
                        </div>
                        <div class="tip">
                            <h3>Clothing Care</h3>
                            <ul>
                                <li>Check care labels for washing instructions</li>
                                <li>Some fabrics may shrink after first wash</li>
                                <li>Air dry when possible to maintain shape</li>
                                <li>Iron on reverse side to protect prints</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="size-section">
                    <h2>Still Need Help?</h2>
                    <p>Our customer care team is here to help you find the perfect size.</p>
                    <div class="help-options">
                        <div class="help-option">
                            <h3>📞 Call Us</h3>
                            <p>030 274 0642</p>
                            <p>Mon - Sat: 8:00 AM - 8:00 PM</p>
                        </div>
                        <div class="help-option">
                            <h3>💬 WhatsApp</h3>
                            <p>030 274 0642</p>
                            <p>Get personalized sizing advice</p>
                        </div>
                        <div class="help-option">
                            <h3>📧 Email</h3>
                            <p>sizing@retailrow.com</p>
                            <p>Send your measurements for recommendations</p>
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
<parameter name="filePath">c:\xampp\htdocs\RetailRow\size-guide.php