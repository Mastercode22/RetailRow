<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all active banners ordered by sort_order
        $query = "SELECT id, title, image, link, sort_order FROM banners WHERE is_active = 1 ORDER BY sort_order ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $banners = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => $banners
        ]);
        break;

    case 'POST':
        // Admin only - create banner
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        $query = "INSERT INTO banners (title, image, link, sort_order, is_active)
                 VALUES (:title, :image, :link, :sort_order, :is_active)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $data['title'] ?? null);
        $stmt->bindParam(':image', $data['image'] ?? null);
        $stmt->bindParam(':link', $data['link'] ?? null);
        $stmt->bindParam(':sort_order', $data['sort_order'] ?? 0);
        $stmt->bindParam(':is_active', $data['is_active'] ?? true);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Banner created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create banner']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>