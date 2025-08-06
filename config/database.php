<?php
/**
 * Database Configuration
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Database configuration
    private $host = 'localhost';
    private $database = 'fix360_eventos';
    private $username = 'fix360_eventos';
    private $password = 'Danjohn007';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            // For development/testing, use SQLite if MySQL is not available
            if ($this->isInDevelopment()) {
                $dbPath = __DIR__ . '/../storage/database.sqlite';
                $this->ensureStorageDirectory();
                $dsn = "sqlite:{$dbPath}";
                $this->connection = new PDO($dsn);
            } else {
                // Production MySQL connection
                $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
                $this->connection = new PDO($dsn, $this->username, $this->password);
            }
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            foreach ($options as $option => $value) {
                $this->connection->setAttribute($option, $value);
            }
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    private function isInDevelopment() {
        return !isset($_SERVER['HTTP_HOST']) || 
               strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
               strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
               strpos($_SERVER['HTTP_HOST'], 'fix360.app') === false;
    }
    
    private function ensureStorageDirectory() {
        $storageDir = __DIR__ . '/../storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }
}
?>