<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once 'config/db.php';
require_once 'utils/Cart.php';

echo json_encode([
    'message' => 'Testing cart functionality',
    'test_results' => []
], JSON_PRETTY_PRINT);

// Test 1: Check if carts table exists
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $result = [];
    
    // Check if carts table exists
    $stmt = $db->query("SHOW TABLES LIKE 'carts'");
    if ($stmt->rowCount() > 0) {
        $result['carts_table'] = '✅ Exists';
    } else {
        $result['carts_table'] = '❌ Missing';
    }
    
    // Check if cart_items table exists
    $stmt = $db->query("SHOW TABLES LIKE 'cart_items'");
    if ($stmt->rowCount() > 0) {
        $result['cart_items_table'] = '✅ Exists';
    } else {
        $result['cart_items_table'] = '❌ Missing';
    }
    
    // Test Cart class initialization
    try {
        $cart = new Cart();
        $result['cart_init'] = '✅ Cart initialized successfully';
        
        // Get cart data
        $cartData = $cart->getCart();
        $result['cart_data'] = '✅ Cart data retrieved: ' . count($cartData['items']) . ' items';
    } catch (Exception $e) {
        $result['cart_init'] = '❌ ' . $e->getMessage();
    }
    
    echo json_encode([
        'status' => 'success',
        'database' => 'Connected',
        'tests' => $result
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
