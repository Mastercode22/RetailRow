<?php
/**
 * Orders API Endpoint
 * Fetches order history for the logged-in user
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Fetch orders with a count of items in each order
$query = "SELECT 
            o.id, o.total_amount, o.status, o.created_at,
            (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count
          FROM orders o 
          WHERE o.user_id = :user_id 
          ORDER BY o.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $orders]);