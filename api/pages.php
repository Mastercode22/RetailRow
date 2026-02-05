<?php
/**
 * Pages API Endpoint
 * Manages dynamic pages (About, Contact, Privacy, etc.)
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
    // Single page by ID
    if (isset($_GET['id'])) {
        $query = "SELECT * FROM pages WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$page) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Page not found']);
            return;
        }
        
        echo json_encode(['success' => true, 'data' => $page]);
        return;
    }
    
    // Single page by slug
    if (isset($_GET['slug'])) {
        $query = "SELECT * FROM pages WHERE slug = :slug AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':slug', $_GET['slug']);
        $stmt->execute();
        
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$page) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Page not found']);
            return;
        }
        
        echo json_encode(['success' => true, 'data' => $page]);
        return;
    }
    
    // Footer pages
    if (isset($_GET['footer'])) {
        $query = "SELECT id, title, slug FROM pages WHERE show_in_footer = 1 AND is_active = 1 ORDER BY position ASC";
        $stmt = $db->query($query);
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $pages]);
        return;
    }
    
    // Header pages
    if (isset($_GET['header'])) {
        $query = "SELECT id, title, slug FROM pages WHERE show_in_header = 1 AND is_active = 1 ORDER BY position ASC";
        $stmt = $db->query($query);
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $pages]);
        return;
    }
    
    // All active pages
    $query = "SELECT id, title, slug, meta_title, meta_description, is_active, show_in_footer, show_in_header, position 
              FROM pages WHERE is_active = 1 ORDER BY position ASC";
    $stmt = $db->query($query);
    $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $pages]);
}

function handlePost($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['title']) || !isset($data['slug'])) {
        throw new Exception('Title and slug are required');
    }
    
    $query = "INSERT INTO pages (title, slug, content, meta_title, meta_description, is_active, show_in_footer, show_in_header, position)
              VALUES (:title, :slug, :content, :meta_title, :meta_description, :is_active, :show_in_footer, :show_in_header, :position)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':slug', $data['slug']);
    $stmt->bindParam(':content', $data['content'] ?? '');
    $stmt->bindParam(':meta_title', $data['meta_title'] ?? $data['title']);
    $stmt->bindParam(':meta_description', $data['meta_description'] ?? '');
    
    $is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $show_in_footer = isset($data['show_in_footer']) ? (bool)$data['show_in_footer'] : false;
    $show_in_header = isset($data['show_in_header']) ? (bool)$data['show_in_header'] : false;
    
    $stmt->bindParam(':is_active', $is_active, PDO::PARAM_BOOL);
    $stmt->bindParam(':show_in_footer', $show_in_footer, PDO::PARAM_BOOL);
    $stmt->bindParam(':show_in_header', $show_in_header, PDO::PARAM_BOOL);
    $stmt->bindParam(':position', $data['position'] ?? 0, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Page created successfully',
            'id' => $db->lastInsertId()
        ]);
    } else {
        throw new Exception('Failed to create page');
    }
}

function handlePut($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('Page ID is required');
    }
    
    $updates = [];
    $params = [':id' => $data['id']];
    
    $allowedFields = ['title', 'slug', 'content', 'meta_title', 'meta_description', 
                      'is_active', 'show_in_footer', 'show_in_header', 'position'];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        throw new Exception('No fields to update');
    }
    
    $query = "UPDATE pages SET " . implode(', ', $updates) . " WHERE id = :id";
    $stmt = $db->prepare($query);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Page updated successfully']);
    } else {
        throw new Exception('Failed to update page');
    }
}

function handleDelete($db) {
    require_once __DIR__ . '/../config/auth.php';
    $auth = new Auth();
    $auth->requireAdmin();
    
    if (!isset($_GET['id'])) {
        throw new Exception('Page ID is required');
    }
    
    $query = "UPDATE pages SET is_active = 0 WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Page deleted successfully']);
    } else {
        throw new Exception('Failed to delete page');
    }
}
