<?php
/**
 * Navigation API Endpoint
 * Manages navigation menus and menu items
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGet($db);
            break;
        
        case 'POST':
            handlePost($db);
            break;
        
        case 'PUT':
            handlePut($db);
            break;
        
        case 'DELETE':
            handleDelete($db);
            break;
        
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleGet($db) {
    // Get menu by location
    if (isset($_GET['location'])) {
        $location = $_GET['location'];
        
        $query = "SELECT nm.*, 
                         (SELECT JSON_ARRAYAGG(
                             JSON_OBJECT(
                                 'id', mi.id,
                                 'label', mi.label,
                                 'url', mi.url,
                                 'page_id', mi.page_id,
                                 'parent_id', mi.parent_id,
                                 'position', mi.position,
                                 'target', mi.target
                             )
                         ) FROM menu_items mi 
                          WHERE mi.menu_id = nm.id AND mi.is_active = 1 
                          ORDER BY mi.position ASC) as items
                  FROM navigation_menus nm
                  WHERE nm.location = :location AND nm.is_active = 1
                  LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':location', $location);
        $stmt->execute();
        
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$menu) {
            echo json_encode(['success' => true, 'data' => null]);
            return;
        }
        
        // Decode items JSON
        $menu['items'] = json_decode($menu['items'] ?? '[]', true);
        
        echo json_encode(['success' => true, 'data' => $menu]);
        return;
    }
    
    // Get single menu by ID
    if (isset($_GET['id'])) {
        $query = "SELECT nm.*,
                         (SELECT JSON_ARRAYAGG(
                             JSON_OBJECT(
                                 'id', mi.id,
                                 'label', mi.label,
                                 'url', mi.url,
                                 'page_id', mi.page_id,
                                 'parent_id', mi.parent_id,
                                 'position', mi.position,
                                 'target', mi.target
                             )
                         ) FROM menu_items mi 
                          WHERE mi.menu_id = nm.id AND mi.is_active = 1 
                          ORDER BY mi.position ASC) as items
                  FROM navigation_menus nm
                  WHERE nm.id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$menu) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Menu not found']);
            return;
        }
        
        $menu['items'] = json_decode($menu['items'] ?? '[]', true);
        
        echo json_encode(['success' => true, 'data' => $menu]);
        return;
    }
    
    // Get all menus
    $query = "SELECT * FROM navigation_menus WHERE is_active = 1 ORDER BY name ASC";
    $stmt = $db->query($query);
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $menus]);
}

function handlePost($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name']) || !isset($data['location'])) {
        throw new Exception('Name and location are required');
    }
    
    $query = "INSERT INTO navigation_menus (name, location, is_active) 
              VALUES (:name, :location, :is_active)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':location', $data['location']);
    
    $is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Menu created successfully',
            'id' => $db->lastInsertId()
        ]);
    } else {
        throw new Exception('Failed to create menu');
    }
}

function handlePut($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('Menu ID is required');
    }
    
    $updates = [];
    $params = [':id' => $data['id']];
    
    $allowedFields = ['name', 'location', 'is_active'];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $query = "UPDATE navigation_menus SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu updated successfully']);
    } else {
        throw new Exception('Failed to update menu');
    }
}

function handleDelete($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    if (!isset($_GET['id'])) {
        throw new Exception('Menu ID is required');
    }
    
    $query = "DELETE FROM navigation_menus WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Menu deleted successfully']);
    } else {
        throw new Exception('Failed to delete menu');
    }
}
