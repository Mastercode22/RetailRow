<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, name, email, role, avatar, status FROM users WHERE id = :id AND status = 'active' LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => $user
        ]);
    } else {
        // User not found or inactive, destroy session
        session_unset();
        session_destroy();
        echo json_encode([
            'success' => true,
            'authenticated' => false
        ]);
    }
} else {
    echo json_encode([
        'success' => true,
        'authenticated' => false
    ]);
}