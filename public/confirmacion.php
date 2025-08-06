<?php
/**
 * Confirmation Page
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 * 
 * This page displays the confirmation details for a successful reservation
 * It receives the reservation ID via GET parameter and displays the reservation data
 */

// Start session
session_start();

// Include configuration and models
require_once '../config/database.php';
require_once '../app/models/Reserva.php';

// Security helper function
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Get and validate reservation ID from URL parameter
$reservation_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;

if (!$reservation_id) {
    // Invalid or missing ID, redirect to home page
    header('Location: /');
    exit;
}

// Get reservation data
$reservaModel = new Reserva();
$reservationData = $reservaModel->getById($reservation_id);

if (!$reservationData) {
    // Reservation not found, redirect to home page
    header('Location: /');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reservación - Cámara de Comercio de Querétaro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-calendar-alt me-2"></i>
                Cámara de Comercio de Querétaro
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-success text-white text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            ¡Reservación Confirmada!
                        </h2>
                    </div>
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h3 class="text-success mb-4">¡Gracias por su reservación!</h3>
                        
                        <div class="alert alert-success">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Detalles de su reservación:
                            </h5>
                            
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p><strong>ID de Reservación:</strong> #<?php echo htmlspecialchars($reservationData['id']); ?></p>
                                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($reservationData['nombre_completo']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($reservationData['email']); ?></p>
                                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($reservationData['telefono']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha del Evento:</strong> <?php echo date('d/m/Y', strtotime($reservationData['fecha_evento'])); ?></p>
                                    <p><strong>Número de Asistentes:</strong> <?php echo htmlspecialchars($reservationData['numero_asistentes']); ?></p>
                                    <p><strong>Tipo de Evento:</strong> <?php echo htmlspecialchars($reservationData['tipo_evento']); ?></p>
                                    <p><strong>Estatus:</strong> <span class="badge bg-warning"><?php echo htmlspecialchars($reservationData['estatus']); ?></span></p>
                                </div>
                            </div>
                        </div>
                        
                        <p class="lead mb-4">
                            Su reservación ha sido registrada exitosamente. En breve nos pondremos en contacto con usted para confirmar los detalles.
                        </p>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-clock me-2"></i>Próximos pasos:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Su reservación tiene estatus: <strong><?php echo htmlspecialchars($reservationData['estatus']); ?></strong></li>
                                <li><i class="fas fa-envelope text-primary me-2"></i>Recibirá un email de confirmación en breve</li>
                                <li><i class="fas fa-phone text-info me-2"></i>Nuestro equipo se pondrá en contacto con usted para confirmar los detalles</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="/" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-plus me-2"></i>
                                Nueva Reservación
                            </a>
                            <a href="mailto:eventos@camaraqueretaro.com" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-envelope me-2"></i>
                                Contactar
                            </a>
                        </div>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Guarde esta información para sus registros. 
                            Número de referencia: <strong>REF-<?php echo date('Ymd', strtotime($reservationData['fecha_creacion'])) . '-' . str_pad($reservationData['id'], 6, '0', STR_PAD_LEFT); ?></strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Print button functionality
        function printConfirmation() {
            window.print();
        }
        
        // Add print button after page loads
        document.addEventListener('DOMContentLoaded', function() {
            const buttonContainer = document.querySelector('.mt-4');
            const printButton = document.createElement('a');
            printButton.href = '#';
            printButton.className = 'btn btn-outline-secondary btn-lg ms-2';
            printButton.innerHTML = '<i class="fas fa-print me-2"></i>Imprimir';
            printButton.onclick = function(e) {
                e.preventDefault();
                printConfirmation();
            };
            buttonContainer.appendChild(printButton);
        });
    </script>
</body>
</html>