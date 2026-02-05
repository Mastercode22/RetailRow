<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../utils/Cart.php';

    $cart = new Cart();
    $cartData = $cart->getCart();

    echo json_encode([
        'success' => true,
        'data' => $cartData
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}