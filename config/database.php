<?php
/**
 * Database Connection Class
 * MIQT System
 */

require_once __DIR__ . '/config.php';

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    public $conn;

    /**
     * Connect to database
     */
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbname,
                $this->user,
                $this->pass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4")
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            die();
        }

        return $this->conn;
    }

    /**
     * Execute a query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            echo "Query Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Get single record
     */
    public function single($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : false;
    }

    /**
     * Get multiple records
     */
    public function resultSet($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : false;
    }

    /**
     * Get row count
     */
    public function rowCount($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : 0;
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Begin Transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit Transaction
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Rollback Transaction
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
}

// Create global database instance
$database = new Database();
$db = $database->connect();
?>
