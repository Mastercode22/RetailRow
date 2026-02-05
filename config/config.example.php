<?php
/**
 * Environment Configuration
 * Copy this file to config.php and update with your settings
 */

// Environment mode: 'development', 'staging', or 'production'
define('ENVIRONMENT', 'development');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'retailrow');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'http://localhost/RetailRow');
define('API_URL', SITE_URL . '/api');
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('ENABLE_CSRF_PROTECTION', true);
define('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
define('MAX_UPLOAD_SIZE', 5242880); // 5MB

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
}

// CORS Settings (for API)
define('CORS_ALLOWED_ORIGINS', ['*']); // Change in production
define('CORS_ALLOWED_METHODS', ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']);
define('CORS_ALLOWED_HEADERS', ['Content-Type', 'Authorization']);

// Cache Settings
define('ENABLE_CACHE', ENVIRONMENT === 'production');
define('CACHE_DURATION', 3600); // 1 hour

// Email Configuration (for future use)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
define('FROM_EMAIL', 'noreply@retailrow.com');
define('FROM_NAME', 'RetailRow');

// Payment Gateway (for future use)
define('PAYMENT_MODE', 'sandbox'); // 'sandbox' or 'live'
define('PAYMENT_PUBLIC_KEY', '');
define('PAYMENT_SECRET_KEY', '');

// Feature Flags
define('ENABLE_REGISTRATION', false); // Customer registration
define('ENABLE_REVIEWS', false);      // Product reviews
define('ENABLE_WISHLIST', true);      // Wishlist feature
define('ENABLE_CART', true);          // Shopping cart
