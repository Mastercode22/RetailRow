<?php
// Load dynamic settings
$settings = [];
try {
    $settingsResponse = @file_get_contents('http://localhost/RetailRow/api/settings.php?keys=announcement_text,phone_number,site_title');
    if ($settingsResponse) {
        $settingsData = json_decode($settingsResponse, true);
        if ($settingsData && isset($settingsData['data'])) {
            $settings = $settingsData['data'];
        }
    }
} catch (Exception $e) {
    $settings = [
        'announcement_text' => 'If ibi love, igo show for your cart 💕',
        'phone_number' => '030 274 0642',
        'site_title' => 'RetailRow'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - <?php echo htmlspecialchars($settings['site_title'] ?? 'RetailRow'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ===========================
           Modern Profile Page Design
           =========================== */
        
        /* Dropdown styles for account menu */
        .nav-item-dropdown { position: relative; }
        .nav-item-dropdown .dropdown-menu { 
            display: none; 
            position: absolute; 
            top: 100%; 
            right: 0; 
            background-color: #fff; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.15); 
            z-index: 1000; 
            min-width: 200px; 
            border-radius: 12px; 
            padding: 12px 0; 
            border: 1px solid #f0f0f0;
            margin-top: 8px;
        }
        .nav-item-dropdown:hover .dropdown-menu { display: block; }
        .dropdown-menu a.dropdown-item { 
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        .dropdown-menu a.dropdown-item:hover { 
            background: linear-gradient(90deg, rgba(246, 139, 30, 0.08) 0%, rgba(246, 139, 30, 0.03) 100%);
            color: #f68b1e;
        }
        .dropdown-menu .dropdown-item { white-space: nowrap; }

        /* Main Content */
        .main-content { 
            min-height: 60vh; 
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            padding-bottom: 4rem;
        }

        /* Account Layout - Modern Grid */
        .account-layout { 
            display: grid; 
            grid-template-columns: 280px 1fr; 
            gap: 2.5rem; 
            padding: 3rem 0;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Beautiful Sidebar */
        .account-sidebar { 
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            overflow: hidden;
            height: fit-content;
            position: sticky;
            top: 2rem;
            border: 1px solid rgba(0,0,0,0.05);
        }

        /* User Profile Section */
        .user-brief { 
            padding: 2.5rem 2rem;
            text-align: center;
            background: linear-gradient(135deg, #f68b1e 0%, #ff9a3c 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .user-brief::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .user-brief::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .avatar-upload {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 1.25rem;
            cursor: pointer;
            z-index: 1;
        }

        .avatar-upload img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,0.3);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }

        .avatar-upload:hover img {
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.05);
        }

        .avatar-upload .avatar-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.6);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.3s;
            backdrop-filter: blur(4px);
        }

        .avatar-upload:hover .avatar-overlay {
            opacity: 1;
        }

        .user-brief h4 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .user-brief .text-muted {
            color: rgba(255,255,255,0.85) !important;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        /* Sidebar Menu - Modern Design */
        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu a { 
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 1rem 1.5rem;
            color: #4a5568;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
            position: relative;
        }

        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            background: linear-gradient(90deg, #f68b1e 0%, transparent 100%);
            transition: all 0.3s;
        }

        .sidebar-menu a:hover {
            background: linear-gradient(90deg, rgba(246, 139, 30, 0.08) 0%, transparent 100%);
            color: #f68b1e;
        }

        .sidebar-menu a:hover::before {
            width: 4px;
            height: 100%;
        }

        .sidebar-menu a.active { 
            background: linear-gradient(90deg, rgba(246, 139, 30, 0.12) 0%, rgba(246, 139, 30, 0.03) 100%);
            border-left-color: #f68b1e;
            color: #f68b1e;
            font-weight: 600;
        }

        .sidebar-menu a i { 
            width: 22px;
            font-size: 1.1rem;
            text-align: center;
        }

        /* Content Area - Beautiful Cards */
        .account-content { 
            background: #fff;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            min-height: 600px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        /* Auth Container - Modern Login/Register */
        .auth-container { 
            max-width: 460px;
            margin: 4rem auto;
            padding: 3rem 2.5rem;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .auth-container h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .auth-container .subtitle {
            color: #718096;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .form-group { 
            margin-bottom: 1.75rem;
        }

        .form-group label { 
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
        }

        .form-control { 
            width: 100%;
            padding: 0.95rem 1.25rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #f68b1e;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(246, 139, 30, 0.08);
        }

        .btn-block { 
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #f68b1e 0%, #ff9a3c 100%);
            border: none;
            box-shadow: 0 4px 16px rgba(246, 139, 30, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(246, 139, 30, 0.4);
        }

        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mt-3 { margin-top: 1.5rem; }

        .auth-container a {
            color: #f68b1e;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .auth-container a:hover {
            color: #e67e0e;
            text-decoration: underline;
        }

        /* Order History - Premium Cards */
        .orders-list { 
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
            margin-top: 1.5rem;
        }

        .order-card { 
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            transition: all 0.3s;
        }

        .order-card:hover { 
            box-shadow: 0 12px 32px rgba(0,0,0,0.12);
            transform: translateY(-4px);
            border-color: #f68b1e;
        }

        .order-header { 
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.75rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }

        .order-number {
            font-size: 1.05rem;
            color: #2d3748;
        }

        .order-status { 
            text-transform: capitalize;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .status-pending-payment, .status-pending { 
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-processing { 
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .status-shipped { 
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .status-delivered { 
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .status-cancelled, .status-failed { 
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .order-body { 
            padding: 1.75rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .order-body p { 
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .order-body p strong {
            color: #2d3748;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-body p span {
            color: #4a5568;
            font-size: 1.05rem;
            font-weight: 600;
        }

        .order-footer { 
            padding: 1.25rem 1.75rem;
            text-align: right;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .btn-secondary { 
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-secondary:hover { 
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        /* Alert Messages */
        .alert {
            padding: 1.25rem 1.5rem;
            border-radius: 12px;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert i {
            font-size: 1.2rem;
        }

        /* Section Headers */
        .account-content h2 {
            font-size: 1.85rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .account-content .section-subtitle {
            color: #718096;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        /* Stats Cards (for profile overview) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border-color: #f68b1e;
        }

        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #f68b1e 0%, #ff9a3c 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.25rem;
        }

        .stat-card .stat-label {
            color: #718096;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .account-layout { 
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .account-sidebar { 
                position: static;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .account-content {
                padding: 1.75rem;
            }

            .auth-container {
                padding: 2rem 1.5rem;
                margin: 2rem auto;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .order-body {
                grid-template-columns: 1fr;
            }
        }

        .hidden { display: none !important; }

        /* Beautiful Loading State */
        #page-loader {
            text-align: center;
            padding: 6rem 2rem;
        }

        #page-loader i {
            color: #f68b1e;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
            margin: 2rem 0;
        }

        /* Profile Form Actions */
        .profile-form-actions {
            margin-top: 2.5rem;
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid #f0f0f0;
            padding-top: 2rem;
        }

        .profile-save-btn {
            background: linear-gradient(135deg, #f68b1e 0%, #ff9a3c 100%);
            color: white;
            border: none;
            padding: 14px 36px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(246, 139, 30, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        .profile-save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(246, 139, 30, 0.35);
            background: linear-gradient(135deg, #ff9a3c 0%, #f68b1e 100%);
        }

        .profile-save-btn:active {
            transform: translateY(0);
        }

        .profile-save-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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
                    <form class="search-form" action="search.php" method="GET">
                        <input type="search" id="searchInput" name="q" placeholder="Search for products, brands and categories" aria-label="Search products" autocomplete="off" required>
                        <button type="submit" class="search-btn">SEARCH</button>
                    </form>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>

                <!-- Right Nav Items -->
                <div class="nav-actions">
                    <div class="nav-item-dropdown" id="accountDropdownContainer">
                        <a href="account.php" class="nav-btn account-btn" id="accountLink">
                            <svg class="icon" viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                            </svg>
                            <span id="accountText">Account</span>
                            <svg class="icon-arrow" viewBox="0 0 24 24" width="16" height="16">
                                <path fill="currentColor" d="M7 10l5 5 5-5z" />
                            </svg>
                        </a>
                        <div class="dropdown-menu" id="accountDropdownMenu">
                            <a href="account.php" class="dropdown-item"><i class="fas fa-sign-in-alt"></i> Login</a>
                            <a href="account.php#register" class="dropdown-item"><i class="fas fa-user-plus"></i> Register</a>
                            <a href="profile.php" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                        </div>
                    </div>

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

    <main class="main-content">
        <div class="container">
            <!-- Loading State -->
            <div id="page-loader" style="text-align: center; padding: 4rem;">
                <i class="fas fa-spinner fa-spin fa-3x"></i>
            </div>

            <!-- Auth Forms (Login/Register) -->
            <div id="auth-section" class="hidden">
                <div class="auth-container" id="login-box">
                    <h2 class="text-center">Welcome Back!</h2>
                    <p class="text-center subtitle">Login to access your account</p>
                    <form id="login-form">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        <p class="mt-3 text-center">
                            Don't have an account? <a href="#" onclick="app.toggleAuth('register')">Create one now</a>
                        </p>
                    </form>
                </div>

                <div class="auth-container hidden" id="register-box">
                    <h2 class="text-center">Join RetailRow</h2>
                    <p class="text-center subtitle">Create your account to get started</p>
                    <form id="register-form">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Create a strong password" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> Create Account
                        </button>
                        <p class="mt-3 text-center">
                            Already have an account? <a href="#" onclick="app.toggleAuth('login')">Login here</a>
                        </p>
                    </form>
                </div>
            </div>

            <!-- User Dashboard -->
            <div id="dashboard-section" class="hidden">
                <div class="account-layout">
                    <aside class="account-sidebar">
                        <div class="user-brief">
                            <div class="avatar-upload">
                                <label for="avatar-input">
                                    <img src="assets/images/placeholder.jpg" id="sidebar-avatar" alt="Profile">
                                    <div class="avatar-overlay"><i class="fas fa-camera"></i></div>
                                </label>
                                <input type="file" id="avatar-input" accept="image/*" class="hidden">
                            </div>
                            <h4 id="sidebar-name">User Name</h4>
                            <p class="text-muted" id="sidebar-email">user@example.com</p>
                        </div>
                        <nav class="sidebar-menu">
                            <a href="#profile" class="active" onclick="account.loadSection('profile')">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="#orders" onclick="account.loadSection('orders')">
                                <i class="fas fa-box"></i> Orders
                            </a>
                            <a href="#addresses" onclick="account.loadSection('addresses')">
                                <i class="fas fa-map-marker-alt"></i> Addresses
                            </a>
                            <a href="#wishlist" onclick="account.loadSection('wishlist')">
                                <i class="fas fa-heart"></i> Wishlist
                            </a>
                            <a href="#security" onclick="account.loadSection('security')">
                                <i class="fas fa-shield-alt"></i> Security
                            </a>
                            <a href="#" onclick="auth.logout()">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </nav>
                    </aside>
                    
                    <main class="account-content" id="account-view">
                        <!-- Dynamic Content Loaded Here -->
                    </main>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
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
            <a href="account.php" class="drawer-link">
                <svg class="drawer-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
                <span>My Account</span>
            </a>
            <a href="help.php" class="drawer-link">
                <svg class="drawer-icon" viewBox="0 0 24 24" width="20" height="20">
                    <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                </svg>
                <span>Help</span>
            </a>
        </nav>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="overlay" aria-hidden="true"></div>

    <!-- Back to Top -->
    <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>

    <script>
        // Update header UI based on auth status
        const updateAuthUI = (user) => {
            const accountText = document.getElementById('accountText');
            const accountDropdownMenu = document.getElementById('accountDropdownMenu');

            if (user) {
                if (accountText) {
                    accountText.textContent = user.name ? user.name.split(' ')[0] : 'Account';
                }
                if (accountDropdownMenu) {
                    accountDropdownMenu.innerHTML = `
                        <a href="account.php" class="dropdown-item"><i class="fas fa-user-circle"></i> My Account</a>
                        <a href="profile.php" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                        <a href="account.php#orders" class="dropdown-item"><i class="fas fa-box"></i> Orders</a>
                        <a href="#" onclick="auth.logout()" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    `;
                }
            }
        };

        // Minimal 'app' object to provide toggleAuth function needed by auth.js and the page.
        const app = {
            toggleAuth: (form) => {
                const loginBox = document.getElementById('login-box');
                const registerBox = document.getElementById('register-box');
                if (form === 'register') {
                    loginBox.classList.add('hidden');
                    registerBox.classList.remove('hidden');
                } else { // 'login'
                    loginBox.classList.remove('hidden');
                    registerBox.classList.add('hidden');
                }
            }
        };
    </script>
    <script src="js/api.js"></script>
    <script src="js/auth.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cart.js"></script>
    <script src="js/account.js"></script>
    <script>
        // Ensure header updates when account page loads
        document.addEventListener('DOMContentLoaded', async () => {
            const user = await auth.checkUser();
            updateAuthUI(user);
        });
    </script>
</body>
</html>