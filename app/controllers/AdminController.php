<?php
/**
 * Admin Controller
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

class AdminController {
    private $adminModel;
    private $reservaModel;
    
    public function __construct() {
        $this->adminModel = new Admin();
        $this->reservaModel = new Reserva();
        
        // Initialize database tables if needed
        $this->adminModel->initializeTables();
        $this->adminModel->createInitialAdmin();
    }
    
    /**
     * Show login form
     */
    public function showLogin() {
        // Redirect if already logged in
        if ($this->adminModel->isLoggedIn()) {
            header('Location: ' . buildUrl('admin/dashboard'));
            exit;
        }
        
        $csrfToken = generateCSRFToken();
        $error = $_SESSION['login_error'] ?? '';
        unset($_SESSION['login_error']);
        
        include '../app/views/admin_login.php';
    }
    
    /**
     * Process login
     */
    public function login() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['login_error'] = 'Token de seguridad inválido.';
            header('Location: ' . buildUrl('admin'));
            exit;
        }
        
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Usuario y contraseña son requeridos.';
            header('Location: ' . buildUrl('admin'));
            exit;
        }
        
        $user = $this->adminModel->authenticate($username, $password);
        
        if ($user) {
            $this->adminModel->login($user);
            header('Location: ' . buildUrl('admin/dashboard'));
            exit;
        } else {
            $_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
            header('Location: ' . buildUrl('admin'));
            exit;
        }
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard() {
        // Check if logged in
        if (!$this->adminModel->isLoggedIn()) {
            header('Location: ' . buildUrl('admin'));
            exit;
        }
        
        // Get filters
        $filters = [
            'estatus' => sanitizeInput($_GET['estatus'] ?? ''),
            'fecha_desde' => sanitizeInput($_GET['fecha_desde'] ?? ''),
            'fecha_hasta' => sanitizeInput($_GET['fecha_hasta'] ?? ''),
        ];
        
        // Pagination
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 10;
        
        // Get reservations
        $reservaciones = $this->reservaModel->getAll($page, $limit, $filters);
        $totalReservaciones = $this->reservaModel->getCount($filters);
        $totalPages = ceil($totalReservaciones / $limit);
        
        $csrfToken = generateCSRFToken();
        $successMessage = $_SESSION['admin_success'] ?? '';
        $errorMessage = $_SESSION['admin_error'] ?? '';
        
        unset($_SESSION['admin_success']);
        unset($_SESSION['admin_error']);
        
        include '../app/views/admin_dashboard.php';
    }
    
    /**
     * Update reservation status
     */
    public function updateStatus() {
        // Check if logged in
        if (!$this->adminModel->isLoggedIn()) {
            header('Location: ' . buildUrl('admin'));
            exit;
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['admin_error'] = 'Token de seguridad inválido.';
            header('Location: ' . buildUrl('admin/dashboard'));
            exit;
        }
        
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        $status = sanitizeInput($_POST['status'] ?? '');
        
        $validStatuses = ['Pendiente', 'Confirmada', 'Cancelada'];
        
        if ($id && in_array($status, $validStatuses)) {
            if ($this->reservaModel->updateStatus($id, $status)) {
                $_SESSION['admin_success'] = 'Estatus actualizado exitosamente.';
            } else {
                $_SESSION['admin_error'] = 'Error al actualizar el estatus.';
            }
        } else {
            $_SESSION['admin_error'] = 'Datos inválidos.';
        }
        
        header('Location: ' . buildUrl('admin/dashboard'));
        exit;
    }
    
    /**
     * Delete reservation
     */
    public function deleteReservation() {
        // Check if logged in
        if (!$this->adminModel->isLoggedIn()) {
            header('Location: ' . buildUrl('admin'));
            exit;
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['admin_error'] = 'Token de seguridad inválido.';
            header('Location: ' . buildUrl('admin/dashboard'));
            exit;
        }
        
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        
        if ($id) {
            if ($this->reservaModel->delete($id)) {
                $_SESSION['admin_success'] = 'Reservación eliminada exitosamente.';
            } else {
                $_SESSION['admin_error'] = 'Error al eliminar la reservación.';
            }
        } else {
            $_SESSION['admin_error'] = 'ID de reservación inválido.';
        }
        
        header('Location: ' . buildUrl('admin/dashboard'));
        exit;
    }
    
    /**
     * Logout
     */
    public function logout() {
        $this->adminModel->logout();
        header('Location: ' . buildUrl('admin'));
        exit;
    }
}
?>