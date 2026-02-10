<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['authenticated' => false, 'message' => 'Not logged in']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, name, email, role, avatar, phone, gender, dob, created_at FROM users WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        'authenticated' => true,
        'user' => $user
    ]);
} else {
    // Session exists but user deleted?
    session_destroy();
    http_response_code(401);
    echo json_encode(['authenticated' => false, 'message' => 'User not found']);
}