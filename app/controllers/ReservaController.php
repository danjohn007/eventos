<?php
/**
 * Reserva Controller
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

class ReservaController {
    private $reservaModel;
    
    public function __construct() {
        $this->reservaModel = new Reserva();
    }
    
    /**
     * Show reservation form
     */
    public function showForm() {
        $csrfToken = generateCSRFToken();
        $errors = $_SESSION['form_errors'] ?? [];
        $formData = $_SESSION['form_data'] ?? [];
        
        // Clear session data after displaying
        unset($_SESSION['form_errors']);
        unset($_SESSION['form_data']);
        
        include '../app/views/reserva_form.php';
    }
    
    /**
     * Submit reservation
     */
    public function submitReservation() {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $_SESSION['form_errors'] = ['Token de seguridad inválido. Por favor, inténtelo de nuevo.'];
            header('Location: /eventos/public/');
            exit;
        }
        
        // Sanitize input data
        $data = [
            'nombre_completo' => sanitizeInput($_POST['nombre_completo'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'telefono' => sanitizeInput($_POST['telefono'] ?? ''),
            'fecha_evento' => sanitizeInput($_POST['fecha_evento'] ?? ''),
            'numero_asistentes' => filter_var($_POST['numero_asistentes'] ?? 0, FILTER_VALIDATE_INT),
            'tipo_evento' => sanitizeInput($_POST['tipo_evento'] ?? '')
        ];
        
        // Validate data
        $errors = $this->reservaModel->validate($data);
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
            header('Location: /eventos/public/');
            exit;
        }
        
        // Save reservation
        if ($this->reservaModel->create($data)) {
            $_SESSION['success_message'] = 'Su reservación ha sido registrada exitosamente. En breve nos pondremos en contacto con usted.';
            $_SESSION['reservation_data'] = $data;
            header('Location: /eventos/public/?route=reserva/success');
            exit;
        } else {
            $_SESSION['form_errors'] = ['Error al procesar la reservación. Por favor, inténtelo de nuevo.'];
            $_SESSION['form_data'] = $data;
            header('Location: /eventos/public/');
            exit;
        }
    }
    
    /**
     * Show success page
     */
    public function showSuccess() {
        $successMessage = $_SESSION['success_message'] ?? '';
        $reservationData = $_SESSION['reservation_data'] ?? [];
        
        if (empty($successMessage)) {
            header('Location: /eventos/public/');
            exit;
        }
        
        // Clear session data after displaying
        unset($_SESSION['success_message']);
        unset($_SESSION['reservation_data']);
        
        include '../app/views/reserva_success.php';
    }
}
?>