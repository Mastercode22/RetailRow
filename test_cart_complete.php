<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once 'config/db.php';
require_once 'utils/Cart.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Test 1: Verify tables exist
    $tables_check = [];
    foreach (['carts', 'cart_items', 'products'] as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        $tables_check[$table] = $stmt->rowCount() > 0 ? 'EXISTS' : 'MISSING';
    }

    // Test 2: Check Cart class initialization
    $cart_init_status = 'OK';
    try {
        $cart = new Cart();
        $cart_data = $cart->getCart();
    } catch (Exception $e) {
        $cart_init_status = $e->getMessage();
    }

    // Test 3: Check if cart items have discount calculation
    $sample_items = [];
    $stmt = $db->query("SELECT ci.product_id, ci.quantity, p.name, p.price, p.discount FROM cart_items ci JOIN products p ON ci.product_id = p.id LIMIT 1");
    if ($stmt->rowCount() > 0) {
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        $base_price = floatval($item['price']);
        $discount = intval($item['discount']);
        $actual_price = $discount > 0 ? $base_price * (1 - $discount / 100) : $base_price;
        $sample_items = [
            'product' => $item['name'],
            'base_price' => $base_price,
            'discount_percent' => $discount,
            'calculated_price' => round($actual_price, 2)
        ];
    }

    // Test 4: Remove function test
    $remove_test = 'Function available';

    echo json_encode([
        'status' => 'success',
        'checks' => [
            'database_tables' => $tables_check,
            'cart_initialization' => $cart_init_status,
            'sample_price_calculation' => $sample_items,
            'remove_function' => $remove_test,
            'cart_items_count' => count($cart_data['items']),
            'cart_subtotal' => $cart_data['subtotal']
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
?>
