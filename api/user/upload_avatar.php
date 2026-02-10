<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['avatar'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    exit;
}

$file = $_FILES['avatar'];
$user_id = $_SESSION['user_id'];
$upload_dir = __DIR__ . '/../../assets/uploads/';

// File validation
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'File upload error code: ' . $file['error']]);
    exit;
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF are allowed.']);
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File size exceeds 2MB limit.']);
    exit;
}

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$database = new Database();
$db = $database->getConnection();

// Get old avatar to delete it later
$old_avatar_query = "SELECT avatar FROM users WHERE id = :id";
$old_stmt = $db->prepare($old_avatar_query);
$old_stmt->bindParam(':id', $user_id);
$old_stmt->execute();
$old_avatar_filename = $old_stmt->fetchColumn();

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
$upload_path = $upload_dir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
    exit;
}

// Update user's avatar in database
$query = "UPDATE users SET avatar = :avatar WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':avatar', $filename);
$stmt->bindParam(':id', $user_id);

if ($stmt->execute()) {
    // Delete old avatar file if it exists and is not the default placeholder
    if ($old_avatar_filename && $old_avatar_filename !== 'default_avatar.png' && file_exists($upload_dir . $old_avatar_filename)) {
        unlink($upload_dir . $old_avatar_filename);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Avatar updated successfully.',
        'data' => ['filename' => $filename]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update database.']);
}
?>
