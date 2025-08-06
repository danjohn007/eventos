<?php
/**
 * Admin Model
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Authenticate admin user
     */
    public function authenticate($username, $password) {
        try {
            $sql = "SELECT id, username, password FROM admin_users WHERE username = :username AND activo = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                return $user;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Error authenticating admin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        try {
            $sql = "UPDATE admin_users SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
    }
    
    /**
     * Login user
     */
    public function login($user) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_login_time'] = time();
    }
    
    /**
     * Logout user
     */
    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_login_time']);
        unset($_SESSION['csrf_token']);
        session_destroy();
    }
    
    /**
     * Create initial admin user (for setup)
     */
    public function createInitialAdmin() {
        try {
            // Check if admin table exists and has users
            $sql = "SELECT COUNT(*) as count FROM admin_users";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                // Create default admin user
                $defaultUsername = 'admin';
                $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
                $defaultEmail = 'admin@ejemplo.com';
                
                $sql = "INSERT INTO admin_users (username, password, email, activo) 
                        VALUES (:username, :password, :email, :activo)";
                $stmt = $this->db->prepare($sql);
                
                return $stmt->execute([
                    ':username' => $defaultUsername,
                    ':password' => $defaultPassword,
                    ':email' => $defaultEmail,
                    ':activo' => 1
                ]);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error creating initial admin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Initialize database tables if they don't exist
     */
    public function initializeTables() {
        try {
            // Check if using SQLite or MySQL
            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            // First, try to update existing table schema if needed
            $this->updateTableSchema();
            
            if ($driver === 'sqlite') {
                // SQLite syntax
                $sql = "CREATE TABLE IF NOT EXISTS admin_users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    activo INTEGER DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
            } else {
                // MySQL syntax
                $sql = "CREATE TABLE IF NOT EXISTS admin_users (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    activo TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
            }
            $this->db->exec($sql);
            
            // Create reservaciones table
            if ($driver === 'sqlite') {
                // SQLite syntax
                $sql = "CREATE TABLE IF NOT EXISTS reservaciones (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    nombre_completo VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    telefono VARCHAR(20) NOT NULL,
                    fecha_evento DATE NOT NULL,
                    numero_asistentes INTEGER NOT NULL,
                    tipo_evento VARCHAR(50) NOT NULL,
                    estatus VARCHAR(20) DEFAULT 'Pendiente' CHECK (estatus IN ('Pendiente', 'Confirmada', 'Cancelada')),
                    comentarios TEXT NULL,
                    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
            } else {
                // MySQL syntax
                $sql = "CREATE TABLE IF NOT EXISTS reservaciones (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    nombre_completo VARCHAR(100) NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    telefono VARCHAR(20) NOT NULL,
                    fecha_evento DATE NOT NULL,
                    numero_asistentes INT NOT NULL,
                    tipo_evento VARCHAR(50) NOT NULL,
                    estatus ENUM('Pendiente', 'Confirmada', 'Cancelada') DEFAULT 'Pendiente',
                    comentarios TEXT NULL,
                    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
            }
            $this->db->exec($sql);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error initializing tables: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update table schema if needed (for existing installations)
     */
    private function updateTableSchema() {
        try {
            // Check if admin_users table exists
            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            if ($driver === 'sqlite') {
                $checkTable = "SELECT name FROM sqlite_master WHERE type='table' AND name='admin_users'";
            } else {
                $checkTable = "SHOW TABLES LIKE 'admin_users'";
            }
            
            $stmt = $this->db->prepare($checkTable);
            $stmt->execute();
            $tableExists = $stmt->fetch();
            
            if ($tableExists) {
                // Table exists, check if we need to add email column
                if ($driver === 'sqlite') {
                    $checkColumn = "PRAGMA table_info(admin_users)";
                    $stmt = $this->db->prepare($checkColumn);
                    $stmt->execute();
                    $columns = $stmt->fetchAll();
                    
                    $hasEmail = false;
                    foreach ($columns as $column) {
                        if ($column['name'] === 'email') {
                            $hasEmail = true;
                            break;
                        }
                    }
                    
                    if (!$hasEmail) {
                        // Add email column
                        $this->db->exec("ALTER TABLE admin_users ADD COLUMN email VARCHAR(100) DEFAULT 'admin@ejemplo.com'");
                        // Update existing records to have a default email
                        $this->db->exec("UPDATE admin_users SET email = 'admin@ejemplo.com' WHERE email IS NULL OR email = ''");
                    }
                } else {
                    // MySQL
                    $checkColumn = "SHOW COLUMNS FROM admin_users LIKE 'email'";
                    $stmt = $this->db->prepare($checkColumn);
                    $stmt->execute();
                    $hasEmail = $stmt->fetch();
                    
                    if (!$hasEmail) {
                        // Add email column
                        $this->db->exec("ALTER TABLE admin_users ADD COLUMN email VARCHAR(100) NOT NULL DEFAULT 'admin@ejemplo.com'");
                    }
                }
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error updating table schema: " . $e->getMessage());
            return false;
        }
    }
}
?>