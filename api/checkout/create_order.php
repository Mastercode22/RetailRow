<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../utils/Cart.php';

function create_order() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['message' => 'Only POST method is allowed.']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // 1. Validate incoming data
    $required_fields = ['name', 'email', 'phone', 'address', 'city', 'region', 'payment_method'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['message' => "Field '$field' is required."]);
            return;
        }
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid email format.']);
        return;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $db->beginTransaction();

        // 2. Get cart and items
        $cart_id = get_cart_id();
        if (!$cart_id) {
            throw new Exception("No active cart found.", 404);
        }

        $cart_query = "
            SELECT ci.product_id, ci.quantity, p.name, p.price, p.stock 
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = :cart_id AND p.is_active = 1
        ";
        $cart_stmt = $db->prepare($cart_query);
        $cart_stmt->bindParam(':cart_id', $cart_id);
        $cart_stmt->execute();
        $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($cart_items) === 0) {
            throw new Exception("Your cart is empty.", 400);
        }

        // 3. Server-side validation and calculation
        $subtotal = 0;
        $unavailable_items = [];
        
        foreach ($cart_items as $item) {
            // Check stock
            if ($item['stock'] < $item['quantity']) {
                $unavailable_items[] = "{$item['name']} (only {$item['stock']} left)";
            }
            // Recalculate subtotal based on DB prices
            $subtotal += $item['price'] * $item['quantity'];
        }

        if (!empty($unavailable_items)) {
            throw new Exception("Some items are no longer available: " . implode(', ', $unavailable_items), 409);
        }

        // Calculate totals (replace with your business logic)
        $tax = $subtotal * 0.10; // 10% example
        $shipping_fee = $subtotal > 100 ? 0 : 10; // Free shipping over 100
        $total_amount = $subtotal + $tax + $shipping_fee;
        
        // 4. Create the order
        $order_query = "
            INSERT INTO orders (user_id, session_id, customer_name, customer_email, customer_phone, 
                                shipping_address, shipping_city, shipping_region, delivery_notes,
                                subtotal, tax, shipping_fee, total_amount, payment_method, payment_status, status)
            VALUES (:user_id, :session_id, :customer_name, :customer_email, :customer_phone, 
                    :shipping_address, :shipping_city, :shipping_region, :delivery_notes,
                    :subtotal, :tax, :shipping_fee, :total_amount, :payment_method, 'pending', 'pending_payment')
        ";
        $order_stmt = $db->prepare($order_query);

        $session_id = session_id();

        $order_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $order_stmt->bindParam(':session_id', $session_id);
        $order_stmt->bindParam(':customer_name', $data['name']);
        $order_stmt->bindParam(':customer_email', $data['email']);
        $order_stmt->bindParam(':customer_phone', $data['phone']);
        $order_stmt->bindParam(':shipping_address', $data['address']);
        $order_stmt->bindParam(':shipping_city', $data['city']);
        $order_stmt->bindParam(':shipping_region', $data['region']);
        $order_stmt->bindParam(':delivery_notes', $data['delivery_notes']);
        $order_stmt->bindParam(':subtotal', $subtotal);
        $order_stmt->bindParam(':tax', $tax);
        $order_stmt->bindParam(':shipping_fee', $shipping_fee);
        $order_stmt->bindParam(':total_amount', $total_amount);
        $order_stmt->bindParam(':payment_method', $data['payment_method']);
        
        $order_stmt->execute();
        $order_id = $db->lastInsertId();

        // 5. Insert order items and update stock
        $order_item_query = "
            INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price)
            VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price)
        ";
        $order_item_stmt = $db->prepare($order_item_query);
        
        $update_stock_query = "UPDATE products SET stock = stock - :quantity WHERE id = :product_id";
        $update_stock_stmt = $db->prepare($update_stock_query);

        foreach ($cart_items as $item) {
            $order_item_stmt->bindParam(':order_id', $order_id);
            $order_item_stmt->bindParam(':product_id', $item['product_id']);
            $order_item_stmt->bindParam(':product_name', $item['name']);
            $order_item_stmt->bindParam(':quantity', $item['quantity']);
            $order_item_stmt->bindParam(':unit_price', $item['price']);
            $order_item_stmt->execute();
            
            $update_stock_stmt->bindParam(':quantity', $item['quantity']);
            $update_stock_stmt->bindParam(':product_id', $item['product_id']);
            $update_stock_stmt->execute();
        }

        // 6. Clean up cart
        $delete_cart_items_query = "DELETE FROM cart_items WHERE cart_id = :cart_id";
        $delete_items_stmt = $db->prepare($delete_cart_items_query);
        $delete_items_stmt->bindParam(':cart_id', $cart_id);
        $delete_items_stmt->execute();
        
        $delete_cart_query = "DELETE FROM carts WHERE id = :cart_id";
        $delete_cart_stmt = $db->prepare($delete_cart_query);
        $delete_cart_stmt->bindParam(':cart_id', $cart_id);
        $delete_cart_stmt->execute();
        
        unset($_SESSION['cart_id']);

        // 7. Commit transaction and return success
        $db->commit();
        
        http_response_code(201); // Created
        echo json_encode([
            'message' => 'Order created successfully.',
            'order_id' => $order_id
        ]);

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $code = is_int($e->getCode()) && $e->getCode() !== 0 ? $e->getCode() : 500;
        http_response_code($code);
        echo json_encode(['message' => $e->getMessage()]);
    }
}

create_order();
