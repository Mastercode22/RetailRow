<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing Cart System ===\n\n";

// Test 1: Session and Cart class
echo "1. Testing Cart class...\n";
session_start();
require_once 'utils/Cart.php';
require_once 'config/db.php';

$cart = new Cart();
$cart->add(1, 2); // Add product 1 with quantity 2

echo "   Session cart: " . json_encode($_SESSION['cart']) . "\n";

// Test 2: Get cart details from DB
echo "\n2. Testing getCartDetails()...\n";
$database = new Database();
$db = $database->getConnection();
$details = $cart->getCartDetails($db);

echo "   Items in cart: " . count($details['items']) . "\n";
echo "   Subtotal: " . $details['subtotal'] . "\n";

if (count($details['items']) > 0) {
    $item = $details['items'][0];
    echo "\n   First item:\n";
    echo "   - ID: " . $item['id'] . "\n";
    echo "   - Name: " . $item['name'] . "\n";
    echo "   - Price: " . $item['price'] . "\n";
    echo "   - Image: " . $item['image'] . "\n";
    echo "   - Description: " . substr($item['description'], 0, 50) . "...\n";
    echo "   - Quantity: " . $item['quantity'] . "\n";
}

// Test 3: Full JSON response
echo "\n3. Testing API response format...\n";
$apiResponse = json_encode(['success' => true, 'data' => $details]);
echo "   JSON Length: " . strlen($apiResponse) . "\n";
echo "   Valid JSON: " . (json_last_error() === JSON_ERROR_NONE ? "YES" : "NO") . "\n";

// Decode and verify
$decoded = json_decode($apiResponse, true);
echo "   Items count in decoded: " . count($decoded['data']['items']) . "\n";
?>
