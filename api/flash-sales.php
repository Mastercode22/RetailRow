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
        // Get active flash sales with product details
        $query = "SELECT fs.id, fs.title, fs.discount_percentage, fs.end_date, p.id as product_id, p.name, p.price, p.old_price, p.image, p.stock
                 FROM flash_sales fs
                 INNER JOIN products p ON fs.product_id = p.id
                 WHERE fs.is_active = 1 AND fs.end_date > NOW() AND p.is_active = 1
                 ORDER BY fs.end_date ASC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $flash_sales = $stmt->fetchAll();

        // Calculate time remaining for each flash sale
        foreach ($flash_sales as &$sale) {
            $end_time = strtotime($sale['end_date']);
            $current_time = time();
            $time_remaining = $end_time - $current_time;

            if ($time_remaining > 0) {
                $sale['hours_remaining'] = floor($time_remaining / 3600);
                $sale['minutes_remaining'] = floor(($time_remaining % 3600) / 60);
                $sale['seconds_remaining'] = $time_remaining % 60;
            } else {
                $sale['expired'] = true;
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $flash_sales
        ]);
        break;

    case 'POST':
        // Admin only - create flash sale
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        $required_fields = ['title', 'product_id', 'discount_percentage', 'end_date'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                exit();
            }
        }

        $query = "INSERT INTO flash_sales (title, product_id, discount_percentage, end_date, is_active)
                 VALUES (:title, :product_id, :discount_percentage, :end_date, :is_active)";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':product_id', $data['product_id']);
        $stmt->bindParam(':discount_percentage', $data['discount_percentage']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':is_active', $data['is_active'] ?? true);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Flash sale created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create flash sale']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>