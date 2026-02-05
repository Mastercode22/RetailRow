<?php
$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : 'N/A';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed - RetailRow</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkout.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        .confirmation-icon {
            color: #28a745;
            font-size: 5rem;
        }
        .confirmation-title {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 20px;
            color: #333;
        }
        .confirmation-text {
            font-size: 1.1rem;
            color: #555;
            margin-top: 15px;
        }
        .order-id {
            display: inline-block;
            background-color: #f2f2f2;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 20px;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="main-header">
        <nav class="main-nav">
            <div class="container nav-inner">
                <div class="nav-logo">
                     <a href="/" class="logo-link">
                        <div class="logo-box">RetailRow</div>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="confirmation-container">
            <div class="confirmation-icon">✔</div>
            <h1 class="confirmation-title">Thank You For Your Order!</h1>
            <p class="confirmation-text">
                Your order has been placed successfully. You will receive an email confirmation shortly.
            </p>
            <p class="confirmation-text">Your Order ID is:</p>
            <div class="order-id"><?php echo $order_id; ?></div>
            <div style="margin-top: 40px;">
                <a href="/" class="step-btn step-btn-primary">Continue Shopping</a>
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

</body>
</html>
