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
        $type = $_GET['type'] ?? 'all';

        if ($type === 'featured') {
            // Get featured products
            $query = "SELECT p.id, p.name, p.description, p.price, p.old_price, p.discount, p.image, p.stock, c.name as category_name
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.is_featured = 1 AND p.is_active = 1
                     ORDER BY p.created_at DESC
                     LIMIT 12";
        } elseif ($type === 'flash_sale') {
            // Get flash sale products
            $query = "SELECT p.id, p.name, p.description, p.price, p.old_price, p.discount, p.image, p.stock, fs.discount as flash_discount, fs.end_time
                     FROM products p
                     INNER JOIN flash_sales fs ON p.id = fs.product_id
                     WHERE fs.is_active = 1 AND fs.end_time > NOW() AND p.is_active = 1
                     ORDER BY fs.end_time ASC";
        } elseif ($type === 'category' && isset($_GET['category_id'])) {
            // Get products by category
            $query = "SELECT p.id, p.name, p.description, p.price, p.old_price, p.discount, p.image, p.stock
                     FROM products p
                     WHERE p.category_id = :category_id AND p.is_active = 1
                     ORDER BY p.created_at DESC";
        } else {
            // Get all products
            $query = "SELECT p.id, p.name, p.description, p.price, p.old_price, p.discount, p.image, p.stock, c.name as category_name
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.is_active = 1
                     ORDER BY p.created_at DESC
                     LIMIT 20";
        }

        $stmt = $db->prepare($query);

        if ($type === 'category' && isset($_GET['category_id'])) {
            $stmt->bindParam(':category_id', $_GET['category_id']);
        }

        $stmt->execute();
        $products = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => $products
        ]);
        break;

    case 'POST':
        // Admin only - create product
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        $required_fields = ['name', 'price', 'category_id'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                exit();
            }
        }

        $query = "INSERT INTO products (category_id, name, description, price, old_price, discount, image, stock, is_featured, is_flash_sale)
                 VALUES (:category_id, :name, :description, :price, :old_price, :discount, :image, :stock, :is_featured, :is_flash_sale)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':old_price', $data['old_price'] ?? null);
        $stmt->bindParam(':discount', $data['discount'] ?? 0);
        $stmt->bindParam(':image', $data['image'] ?? null);
        $stmt->bindParam(':stock', $data['stock'] ?? 0);
        $stmt->bindParam(':is_featured', $data['is_featured'] ?? false);
        $stmt->bindParam(':is_flash_sale', $data['is_flash_sale'] ?? false);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Product created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create product']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>