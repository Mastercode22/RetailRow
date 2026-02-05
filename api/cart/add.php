<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../../utils/Cart.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputData = file_get_contents('php://input');
        $data = json_decode($inputData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg()
            ]);
            exit;
        }

        if (isset($data['product_id']) && isset($data['quantity'])) {
            $cart = new Cart();
            $cart->add($data['product_id'], $data['quantity']);
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Product added to cart.']);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Product ID and quantity are required.',
                'received' => $data
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