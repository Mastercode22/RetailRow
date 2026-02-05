<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Function to generate slug from name
function generateSlug($name, $db, $excludeId = null) {
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Check if slug exists and make it unique if needed
    $baseSlug = $slug;
    $counter = 1;
    
    while (true) {
        $query = "SELECT COUNT(*) as count FROM categories WHERE slug = :slug";
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':slug', $slug);
        if ($excludeId !== null) {
            $stmt->bindParam(':excludeId', $excludeId);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            break;
        }
        
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

switch ($method) {
    case 'GET':
        // Get all active categories ordered by position
        $query = "SELECT id, name, slug, icon, description FROM categories WHERE is_active = 1 ORDER BY position ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        // Process icon paths - ensure they have proper URL format
        foreach ($categories as &$category) {
            // If icon exists and doesn't start with http/https, prepend base path
            if (!empty($category['icon'])) {
                // Remove any leading slashes to avoid double slashes
                $iconPath = ltrim($category['icon'], '/');
                
                // If it's already a full URL, leave it as is
                if (!preg_match('/^https?:\/\//', $iconPath)) {
                    // Otherwise, ensure it's a proper relative path
                    $category['icon'] = $iconPath;
                }
            } else {
                // Set a default placeholder if no icon exists
                $category['icon'] = 'assets/images/icons/default-category.png';
            }
        }

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

        // Generate slug if not provided
        $slug = isset($data['slug']) && !empty($data['slug']) 
            ? generateSlug($data['slug'], $db) 
            : generateSlug($data['name'], $db);

        $query = "INSERT INTO categories (name, slug, icon, description, is_active, position) 
                  VALUES (:name, :slug, :icon, :description, :is_active, :position)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':icon', $data['icon'] ?? null);
        $stmt->bindParam(':description', $data['description'] ?? null);
        $stmt->bindParam(':is_active', $data['is_active'] ?? true);
        $stmt->bindParam(':position', $data['position'] ?? 0);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'id' => $db->lastInsertId(),
                'slug' => $slug
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create category']);
        }
        break;

    case 'PUT':
        // Admin only - update category
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'Category ID is required']);
            exit();
        }

        $updateFields = [];
        $params = [':id' => $data['id']];

        if (isset($data['name'])) {
            $updateFields[] = 'name = :name';
            $params[':name'] = $data['name'];
        }
        
        if (isset($data['slug'])) {
            // Ensure slug is unique
            $slug = generateSlug($data['slug'], $db, $data['id']);
            $updateFields[] = 'slug = :slug';
            $params[':slug'] = $slug;
        }
        
        if (isset($data['icon'])) {
            $updateFields[] = 'icon = :icon';
            $params[':icon'] = $data['icon'];
        }
        
        if (isset($data['description'])) {
            $updateFields[] = 'description = :description';
            $params[':description'] = $data['description'];
        }
        
        if (isset($data['is_active'])) {
            $updateFields[] = 'is_active = :is_active';
            $params[':is_active'] = $data['is_active'];
        }
        
        if (isset($data['position'])) {
            $updateFields[] = 'position = :position';
            $params[':position'] = $data['position'];
        }

        if (empty($updateFields)) {
            echo json_encode(['success' => false, 'message' => 'No fields to update']);
            exit();
        }

        $query = "UPDATE categories SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $stmt = $db->prepare($query);

        if ($stmt->execute($params)) {
            echo json_encode([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update category']);
        }
        break;

    case 'DELETE':
        // Admin only - delete category
        require_once __DIR__ . '/../config/auth.php';
        $auth = new Auth();
        $auth->requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'message' => 'Category ID is required']);
            exit();
        }

        $query = "DELETE FROM categories WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $data['id']);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete category']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>