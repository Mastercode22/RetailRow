<?php
/**
 * Database Configuration for RetailRow
 * This file handles the database connection using PDO
 */

class Database {
    private $host = "localhost";
    private $db_name = "retailrow";
    private $username = "root";  // Change this to your MySQL username
    private $password = "";      // Change this to your MySQL password
    private $conn;

    /**
     * Get database connection
     * @return PDO database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            die(json_encode([
                'success' => false,
                'message' => 'Database connection failed',
                'error' => $exception->getMessage()
            ]));
        }

        return $this->conn;
    }
}
?>