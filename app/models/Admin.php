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
            $sql = "UPDATE admin_users SET ultimo_acceso = NOW() WHERE id = :id";
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
                
                $sql = "INSERT INTO admin_users (username, password, nombre_completo, activo, fecha_creacion) 
                        VALUES (:username, :password, :nombre_completo, 1, NOW())";
                $stmt = $this->db->prepare($sql);
                
                return $stmt->execute([
                    ':username' => $defaultUsername,
                    ':password' => $defaultPassword,
                    ':nombre_completo' => 'Administrador'
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
            // Create admin_users table
            $sql = "CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                nombre_completo VARCHAR(100) NOT NULL,
                activo TINYINT(1) DEFAULT 1,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ultimo_acceso TIMESTAMP NULL
            )";
            $this->db->exec($sql);
            
            // Create reservaciones table
            $sql = "CREATE TABLE IF NOT EXISTS reservaciones (
                id INT AUTO_INCREMENT PRIMARY KEY,
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
            $this->db->exec($sql);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error initializing tables: " . $e->getMessage());
            return false;
        }
    }
}
?>