<?php
/**
 * Main Router and Entry Point
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

// Start session
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration
require_once '../config/database.php';

// Include models
require_once '../app/models/Reserva.php';
require_once '../app/models/Admin.php';

// Include controllers
require_once '../app/controllers/ReservaController.php';
require_once '../app/controllers/AdminController.php';

// Security helper function
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// CSRF token helper
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// URL helper for relative redirects
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = dirname($scriptName);
    
    // Remove 'public' from path if present
    if (basename($basePath) === 'public') {
        $basePath = dirname($basePath);
    }
    
    // Ensure no double slashes and proper trailing
    $basePath = '/' . trim($basePath, '/');
    if ($basePath === '/') {
        $basePath = '';
    }
    
    return $basePath;
}

function buildUrl($route = '') {
    $baseUrl = getBaseUrl();
    $route = ltrim($route, '/');
    
    if (empty($route)) {
        return $baseUrl . '/';
    }
    
    return $baseUrl . '/?route=' . $route;
}

// Simple router
$request = $_GET['route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Remove leading slash
$request = ltrim($request, '/');

// Route handling
switch ($request) {
    case '':
    case 'reserva':
        $controller = new ReservaController();
        if ($method === 'GET') {
            $controller->showForm();
        } elseif ($method === 'POST') {
            $controller->submitReservation();
        }
        break;
        
    case 'reserva/success':
        $controller = new ReservaController();
        $controller->showSuccess();
        break;
        
    case 'admin':
        $controller = new AdminController();
        if ($method === 'GET') {
            $controller->showLogin();
        } elseif ($method === 'POST') {
            $controller->login();
        }
        break;
        
    case 'admin/dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;
        
    case 'admin/logout':
        $controller = new AdminController();
        $controller->logout();
        break;
        
    case 'admin/update-status':
        $controller = new AdminController();
        if ($method === 'POST') {
            $controller->updateStatus();
        }
        break;
        
    case 'admin/delete':
        $controller = new AdminController();
        if ($method === 'POST') {
            $controller->deleteReservation();
        }
        break;
        
    default:
        http_response_code(404);
        echo "404 - Página no encontrada";
        break;
}
?>