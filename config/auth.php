<?php
/**
 * Authentication Class for RetailRow
 * Handles user authentication and authorization
 */

require_once __DIR__ . '/db.php';

class Auth {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Require admin authentication
     * Returns 401 if not authenticated as admin
     */
    public function requireAdmin() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ]);
            exit();
        }

        return true;
    }

    /**
     * Require staff or admin authentication
     * Returns 401 if not authenticated
     */
    public function requireAuth() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Unauthorized. Please login.'
            ]);
            exit();
        }

        return true;
    }

    /**
     * Login user
     * @param string $email User email
     * @param string $password User password
     * @return array Response with success status
     */
    public function login($email, $password) {
        try {
            $query = "SELECT id, name, email, password, role, status FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Check if user is active
                if ($user['status'] !== 'active') {
                    return [
                        'success' => false,
                        'message' => 'Account is inactive. Please contact administrator.'
                    ];
                }

                // Start session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Login error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Logout user
     * @return array Response with success status
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy session
        session_unset();
        session_destroy();

        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    /**
     * Get current user info
     * @return array|null User info or null if not logged in
     */
    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ];
        }

        return null;
    }
}
?>