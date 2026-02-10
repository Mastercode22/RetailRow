<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Email and password required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, name, email, password, role, avatar, status FROM users WHERE email = :email LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $data['email']);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($data['password'], $user['password'])) {
        // Password correct
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        // Remove password from response
        unset($user['password']);
        
        echo json_encode([
            'authenticated' => true,
            'message' => 'Login successful',
            'user' => $user
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid credentials']);
    }
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid credentials']);
}