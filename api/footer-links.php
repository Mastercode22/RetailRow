<?php
/**
 * Footer Links API Endpoint
 * Manages footer link groups and links
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
    // Get all footer link groups with their links
    $query = "SELECT flg.id, flg.title, flg.position,
                     (SELECT JSON_ARRAYAGG(
                         JSON_OBJECT(
                             'id', fl.id,
                             'label', fl.label,
                             'url', fl.url,
                             'page_id', fl.page_id,
                             'position', fl.position
                         )
                     ) FROM footer_links fl 
                      WHERE fl.group_id = flg.id AND fl.is_active = 1 
                      ORDER BY fl.position ASC) as links
              FROM footer_link_groups flg
              WHERE flg.is_active = 1
              ORDER BY flg.position ASC";
    
    $stmt = $db->query($query);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Decode links JSON
    foreach ($groups as &$group) {
        $group['links'] = json_decode($group['links'] ?? '[]', true);
    }
    
    echo json_encode(['success' => true, 'data' => $groups]);
}

function handlePost($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Create link group
    if (isset($data['type']) && $data['type'] === 'group') {
        if (!isset($data['title'])) {
            throw new Exception('Title is required');
        }
        
        $query = "INSERT INTO footer_link_groups (title, position, is_active) 
                  VALUES (:title, :position, :is_active)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':position', $data['position'] ?? 0, PDO::PARAM_INT);
        
        $is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
        $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Footer group created successfully',
                'id' => $db->lastInsertId()
            ]);
        } else {
            throw new Exception('Failed to create footer group');
        }
        return;
    }
    
    // Create link
    if (!isset($data['group_id']) || !isset($data['label']) || !isset($data['url'])) {
        throw new Exception('Group ID, label, and URL are required');
    }
    
    $query = "INSERT INTO footer_links (group_id, label, url, page_id, position, is_active) 
              VALUES (:group_id, :label, :url, :page_id, :position, :is_active)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':group_id', $data['group_id'], PDO::PARAM_INT);
    $stmt->bindParam(':label', $data['label']);
    $stmt->bindParam(':url', $data['url']);
    $stmt->bindParam(':page_id', $data['page_id'] ?? null, PDO::PARAM_INT);
    $stmt->bindParam(':position', $data['position'] ?? 0, PDO::PARAM_INT);
    
    $is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Footer link created successfully',
            'id' => $db->lastInsertId()
        ]);
    } else {
        throw new Exception('Failed to create footer link');
    }
}

function handlePut($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || !isset($data['type'])) {
        throw new Exception('ID and type are required');
    }
    
    if ($data['type'] === 'group') {
        // Update group
        $updates = [];
        $params = [':id' => $data['id']];
        
        $allowedFields = ['title', 'position', 'is_active'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            throw new Exception('No fields to update');
        }
        
        $query = "UPDATE footer_link_groups SET " . implode(', ', $updates) . " WHERE id = :id";
    } else {
        // Update link
        $updates = [];
        $params = [':id' => $data['id']];
        
        $allowedFields = ['group_id', 'label', 'url', 'page_id', 'position', 'is_active'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            throw new Exception('No fields to update');
        }
        
        $query = "UPDATE footer_links SET " . implode(', ', $updates) . " WHERE id = :id";
    }
    
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Updated successfully']);
    } else {
        throw new Exception('Failed to update');
    }
}

function handleDelete($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    if (!isset($_GET['id']) || !isset($_GET['type'])) {
        throw new Exception('ID and type are required');
    }
    
    if ($_GET['type'] === 'group') {
        $query = "DELETE FROM footer_link_groups WHERE id = :id";
    } else {
        $query = "DELETE FROM footer_links WHERE id = :id";
    }
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Deleted successfully']);
    } else {
        throw new Exception('Failed to delete');
    }
}
