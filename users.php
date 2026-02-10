<?php
/**
 * Users API Endpoint
 * Manages user profile data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

session_start();

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($method === 'GET') {
    // Get current user profile
    $query = "SELECT id, name, email, phone, address, city, region, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} elseif ($method === 'PUT') {
    // Update user profile
    $data = json_decode(file_get_contents('php://input'), true);
    
    $updates = [];
    $params = [':id' => $user_id];
    $allowed = ['name', 'phone', 'address', 'city', 'region'];
    
    foreach ($allowed as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (!empty($updates)) {
        $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $db->prepare($query);
        if ($stmt->execute($params)) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }
    } else {
        echo json_encode(['success' => true, 'message' => 'No changes made']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}