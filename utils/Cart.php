<?php
require_once __DIR__ . '/../config/db.php';

class Cart {
    private $conn;
    private $cartId;
    private $userId;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Handle User ID if logged in
        $this->userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Handle Cart ID
        if (!isset($_SESSION['cart_id'])) {
            $_SESSION['cart_id'] = uniqid('cart_', true);
        }
        $this->cartId = $_SESSION['cart_id'];
        
        // Ensure cart exists in DB
        $this->initializeCart();
    }

    private function initializeCart() {
        // Check if cart exists
        $query = "SELECT id FROM carts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $this->cartId);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $query = "INSERT INTO carts (id, user_id) VALUES (:id, :user_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $this->cartId);
            $stmt->bindValue(':user_id', $this->userId, $this->userId ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->execute();
        } else {
            // Update user_id if it was null and now we have a user
            if ($this->userId) {
                $query = "UPDATE carts SET user_id = :user_id WHERE id = :id AND user_id IS NULL";
                $stmt = $this->conn->prepare($query);
                $stmt->bindValue(':id', $this->cartId);
                $stmt->bindValue(':user_id', $this->userId, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    public function add($productId, $quantity = 1) {
        // Check if item exists in cart
        $query = "SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cart_id', $this->cartId);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $newQuantity = $row['quantity'] + $quantity;
            $this->update($productId, $newQuantity);
        } else {
            $query = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':cart_id', $this->cartId);
            $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public function update($productId, $quantity) {
        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }
        $query = "UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindValue(':cart_id', $this->cartId);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function remove($productId) {
        $query = "DELETE FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cart_id', $this->cartId);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getCart() {
        $query = "SELECT ci.product_id as id, ci.quantity, p.name, p.price, p.image, p.description, p.stock 
                  FROM cart_items ci 
                  JOIN products p ON ci.product_id = p.id 
                  WHERE ci.cart_id = :cart_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cart_id', $this->cartId);
        $stmt->execute();

        $items = [];
        $subtotal = 0;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['id'] = (int)$row['id'];
            $row['quantity'] = (int)$row['quantity'];
            $row['price'] = (float)$row['price'];
            $row['line_total'] = $row['price'] * $row['quantity'];
            $subtotal += $row['line_total'];
            $items[] = $row;
        }

        $tax = 0.00; 
        $shipping = 0.00; 
        $total = $subtotal + $tax + $shipping;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total
        ];
    }
}
?>