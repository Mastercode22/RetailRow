<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $allowed_fields = ['name', 'phone', 'gender', 'dob'];
    $updates = [];
    $params = [];
    
    foreach ($allowed_fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        echo json_encode(['message' => 'No changes provided']);
        exit;
    }
    
    $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':id', $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Profile updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Update failed']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get full profile including addresses
    $query = "SELECT id, name, email, phone, gender, dob, avatar, created_at FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get addresses
    $addr_query = "SELECT * FROM addresses WHERE user_id = :uid";
    $addr_stmt = $db->prepare($addr_query);
    $addr_stmt->bindParam(':uid', $user_id);
    $addr_stmt->execute();
    $user['addresses'] = $addr_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($user);
}