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
        $keys = $_GET['keys'] ?? null;

        if ($keys) {
            // Get specific settings
            $keys_array = explode(',', $keys);
            $placeholders = str_repeat('?,', count($keys_array) - 1) . '?';

            $query = "SELECT key_name, value FROM settings WHERE key_name IN ($placeholders)";
            $stmt = $db->prepare($query);
            $stmt->execute($keys_array);
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } else {
            // Get all settings
            $query = "SELECT key_name, value FROM settings";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        }

        echo json_encode([
            'success' => true,
            'data' => $settings
        ]);
        break;

    case 'POST':
        // Admin only - update settings
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        $updated = 0;
        foreach ($data as $key => $value) {
            $query = "INSERT INTO settings (key_name, value) VALUES (:key, :value)
                     ON DUPLICATE KEY UPDATE value = :value";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);

            if ($stmt->execute()) {
                $updated++;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "$updated settings updated successfully"
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>