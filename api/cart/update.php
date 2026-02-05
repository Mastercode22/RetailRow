<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../../utils/Cart.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputData = file_get_contents('php://input');
        $data = json_decode($inputData, true);

        if (isset($data['product_id']) && isset($data['quantity'])) {
            $cart = new Cart();
            $cart->update($data['product_id'], $data['quantity']);
            
            echo json_encode(['success' => true, 'message' => 'Cart updated.']);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Product ID and quantity are required.'
            ]);
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}