<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validation
if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Please provide name, email, and password']);
    exit;
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid email format']);
    exit;
}

if (strlen($data['password']) < 8) {
    http_response_code(400);
    echo json_encode(['message' => 'Password must be at least 8 characters']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Check if email exists
$check_query = "SELECT id FROM users WHERE email = :email LIMIT 1";
$check_stmt = $db->prepare($check_query);
$check_stmt->bindParam(':email', $data['email']);
$check_stmt->execute();

if ($check_stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(['message' => 'Email already registered']);
    exit;
}

// Create user
$query = "INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, 'customer', 'active')";
$stmt = $db->prepare($query);

$password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt->bindParam(':name', $data['name']);
$stmt->bindParam(':email', $data['email']);
$stmt->bindParam(':password', $password_hash);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(['message' => 'Registration successful. Please login.']);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Registration failed']);
}