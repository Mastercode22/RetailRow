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
        // Get all active categories ordered by position
        $query = "SELECT id, name, icon FROM categories WHERE is_active = 1 ORDER BY position ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
        break;

    case 'POST':
        // Admin only - create category
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['name'])) {
            echo json_encode(['success' => false, 'message' => 'Category name is required']);
            exit();
        }

        $query = "INSERT INTO categories (name, icon, is_active, position) VALUES (:name, :icon, :is_active, :position)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':icon', $data['icon'] ?? null);
        $stmt->bindParam(':is_active', $data['is_active'] ?? true);
        $stmt->bindParam(':position', $data['position'] ?? 0);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>