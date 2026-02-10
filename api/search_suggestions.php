<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Select id, name, price, and image (limit to 5 results for speed)
    $sql = "SELECT id, name, price, image FROM products WHERE name LIKE :term LIMIT 5";
    $stmt = $db->prepare($sql);
    $term = "%" . $query . "%";
    $stmt->bindParam(':term', $term);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $results]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>