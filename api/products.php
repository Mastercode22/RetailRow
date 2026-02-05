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
        $query = "SELECT COUNT(*) as count FROM products WHERE slug = :slug";
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
        try {
            $product_id = isset($_GET['id']) ? intval($_GET['id']) : null;

            if ($product_id) {
                $query = "SELECT 
                            p.id, 
                            p.name, 
                            p.slug,
                            p.description, 
                            p.price, 
                            p.old_price, 
                            p.discount, 
                            p.image, 
                            p.stock, 
                            p.is_featured, 
                            p.is_flash_sale, 
                            p.flash_end_time,
                            p.category_id,
                            c.name as category_name,
                            c.slug as category_slug
                          FROM products p
                          LEFT JOIN categories c ON p.category_id = c.id
                          WHERE p.id = :id AND p.is_active = 1";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $product_id);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    // Process product data
                    if (!empty($product['image'])) {
                        $imagePath = ltrim($product['image'], '/');
                        if (!preg_match('/^https?:\/\//', $imagePath)) {
                            $product['image'] = $imagePath;
                        }
                    } else {
                        $product['image'] = 'assets/images/products/placeholder.jpg';
                    }
                    $product['price'] = floatval($product['price']);
                    $product['old_price'] = $product['old_price'] ? floatval($product['old_price']) : null;
                    $product['discount'] = intval($product['discount']);
                    $product['stock'] = intval($product['stock']);
                    $product['is_featured'] = boolval($product['is_featured']);
                    $product['is_flash_sale'] = boolval($product['is_flash_sale']);
                    $product['in_stock'] = $product['stock'] > 0;

                    echo json_encode([
                        'success' => true,
                        'data' => $product
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Product not found'
                    ]);
                }
                exit;
            }

            // Get query parameters
            $type = isset($_GET['type']) ? $_GET['type'] : 'all';
            $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
            $featured = isset($_GET['featured']) ? filter_var($_GET['featured'], FILTER_VALIDATE_BOOLEAN) : null;
            $flash_sale = isset($_GET['flash_sale']) ? filter_var($_GET['flash_sale'], FILTER_VALIDATE_BOOLEAN) : null;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            $search = isset($_GET['search']) ? $_GET['search'] : null;

            // Base query
            $query = "SELECT 
                        p.id, 
                        p.name, 
                        p.slug,
                        p.description, 
                        p.price, 
                        p.old_price, 
                        p.discount, 
                        p.image, 
                        p.stock, 
                        p.is_featured, 
                        p.is_flash_sale, 
                        p.flash_end_time,
                        p.category_id,
                        c.name as category_name,
                        c.slug as category_slug
                      FROM products p
                      LEFT JOIN categories c ON p.category_id = c.id
                      WHERE p.is_active = 1";

            $params = [];

            // Filter by type
            switch ($type) {
                case 'category':
                    if ($category_id !== null) {
                        $query .= " AND p.category_id = :category_id";
                        $params[':category_id'] = $category_id;
                    }
                    break;
                
                case 'featured':
                    $query .= " AND p.is_featured = 1";
                    break;
                
                case 'flash_sale':
                    $query .= " AND p.is_flash_sale = 1 AND p.flash_end_time > NOW()";
                    break;
                
                case 'search':
                    if ($search !== null) {
                        $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
                        $params[':search'] = '%' . $search . '%';
                    }
                    break;
            }

            // Additional filters
            if ($featured !== null && $type !== 'featured') {
                $query .= " AND p.is_featured = :featured";
                $params[':featured'] = $featured ? 1 : 0;
            }

            if ($flash_sale !== null && $type !== 'flash_sale') {
                $query .= " AND p.is_flash_sale = :flash_sale";
                $params[':flash_sale'] = $flash_sale ? 1 : 0;
            }

            // Order by
            $query .= " ORDER BY p.created_at DESC";

            // Limit and offset
            if ($limit !== null) {
                $query .= " LIMIT :limit OFFSET :offset";
            }

            $stmt = $db->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Process product data
            foreach ($products as &$product) {
                // Ensure image path is properly formatted
                if (!empty($product['image'])) {
                    $imagePath = ltrim($product['image'], '/');
                    if (!preg_match('/^https?:\/\//', $imagePath)) {
                        $product['image'] = $imagePath;
                    }
                } else {
                    $product['image'] = 'assets/images/products/placeholder.jpg';
                }

                // Convert numeric values
                $product['price'] = floatval($product['price']);
                $product['old_price'] = $product['old_price'] ? floatval($product['old_price']) : null;
                $product['discount'] = intval($product['discount']);
                $product['stock'] = intval($product['stock']);
                $product['is_featured'] = boolval($product['is_featured']);
                $product['is_flash_sale'] = boolval($product['is_flash_sale']);
                
                // Calculate if product is in stock
                $product['in_stock'] = $product['stock'] > 0;
            }

            echo json_encode([
                'success' => true,
                'data' => $products,
                'count' => count($products)
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ]);
        }
        break;

    case 'POST':
        // Admin only - create product
        try {
            require_once __DIR__ . '/../config/auth.php';
            $auth = new Auth();
            $auth->requireAdmin();

            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['name']) || !isset($data['price'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Product name and price are required'
                ]);
                exit();
            }

            // Generate slug if not provided
            $slug = isset($data['slug']) && !empty($data['slug']) 
                ? generateSlug($data['slug'], $db) 
                : generateSlug($data['name'], $db);

            $query = "INSERT INTO products 
                      (category_id, name, slug, description, price, old_price, discount, 
                       image, stock, is_featured, is_flash_sale, flash_end_time, is_active,
                       meta_title, meta_description) 
                      VALUES 
                      (:category_id, :name, :slug, :description, :price, :old_price, :discount,
                       :image, :stock, :is_featured, :is_flash_sale, :flash_end_time, :is_active,
                       :meta_title, :meta_description)";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':category_id', $data['category_id'] ?? null);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':description', $data['description'] ?? null);
            $stmt->bindParam(':price', $data['price']);
            $stmt->bindParam(':old_price', $data['old_price'] ?? null);
            $stmt->bindParam(':discount', $data['discount'] ?? 0);
            $stmt->bindParam(':image', $data['image'] ?? null);
            $stmt->bindParam(':stock', $data['stock'] ?? 0);
            $stmt->bindParam(':is_featured', $data['is_featured'] ?? false);
            $stmt->bindParam(':is_flash_sale', $data['is_flash_sale'] ?? false);
            $stmt->bindParam(':flash_end_time', $data['flash_end_time'] ?? null);
            $stmt->bindParam(':is_active', $data['is_active'] ?? true);
            $stmt->bindParam(':meta_title', $data['meta_title'] ?? null);
            $stmt->bindParam(':meta_description', $data['meta_description'] ?? null);

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product created successfully',
                    'id' => $db->lastInsertId(),
                    'slug' => $slug
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create product'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error creating product',
                'error' => $e->getMessage()
            ]);
        }
        break;

    case 'PUT':
        // Admin only - update product
        try {
            require_once __DIR__ . '/../config/auth.php';
            $auth = new Auth();
            $auth->requireAdmin();

            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Product ID is required'
                ]);
                exit();
            }

            $updateFields = [];
            $params = [':id' => $data['id']];

            // Build dynamic update query
            $allowedFields = [
                'category_id', 'name', 'slug', 'description', 'price', 'old_price',
                'discount', 'image', 'stock', 'is_featured', 'is_flash_sale',
                'flash_end_time', 'is_active', 'meta_title', 'meta_description'
            ];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    if ($field === 'slug') {
                        // Ensure slug is unique
                        $slug = generateSlug($data[$field], $db, $data['id']);
                        $updateFields[] = "$field = :$field";
                        $params[":$field"] = $slug;
                    } else {
                        $updateFields[] = "$field = :$field";
                        $params[":$field"] = $data[$field];
                    }
                }
            }

            if (empty($updateFields)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'No fields to update'
                ]);
                exit();
            }

            $query = "UPDATE products SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $db->prepare($query);

            if ($stmt->execute($params)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update product'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ]);
        }
        break;

    case 'DELETE':
        // Admin only - delete product
        try {
            require_once __DIR__ . '/../config/auth.php';
            $auth = new Auth();
            $auth->requireAdmin();

            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['id'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Product ID is required'
                ]);
                exit();
            }

            // Hard delete
            $query = "DELETE FROM products WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $data['id']);

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete product'
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => $e->getMessage()
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
}
?>