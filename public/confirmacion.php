<?php
/**
 * Confirmation Page - Display reservation confirmation code
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

// Start session
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Initialize variables
$reservation = null;
$error = null;
$reservationId = null;

// Get reservation ID from GET or POST
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $reservationId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
} elseif (isset($_POST['id']) && !empty($_POST['id'])) {
    $reservationId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
}

if ($reservationId) {
    try {
        $reservaModel = new Reserva();
        $reservation = $reservaModel->getById($reservationId);
        
        if (!$reservation) {
            $error = "No se encontró la reservación especificada.";
        } elseif (empty($reservation['codigo_confirmacion'])) {
            $error = "Esta reservación no tiene un código de confirmación asignado.";
        }
    } catch (Exception $e) {
        $error = "Error al consultar la reservación. Por favor, inténtelo de nuevo.";
        error_log("Error in confirmation page: " . $e->getMessage());
    }
} else {
    $error = "ID de reservación no válido.";
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
    <style>
        .confirmation-code {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 0.5rem;
            color: #0d6efd;
            background-color: #f8f9fa;
            border: 3px solid #0d6efd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            user-select: all;
            cursor: pointer;
        }
        .confirmation-code:hover {
            background-color: #e9ecef;
        }
        .copy-button {
            margin-top: 10px;
        }
        .reservation-details {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
        }
        .security-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-calendar-alt me-2"></i>
                Cámara de Comercio de Querétaro
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/">
                    <i class="fas fa-home me-1"></i>
                    Inicio
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if ($error): ?>
                    <!-- Error State -->
                    <div class="card shadow-lg border-danger">
                        <div class="card-header bg-danger text-white text-center">
                            <h2 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Error de Consulta
                            </h2>
                        </div>
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            
                            <h3 class="text-danger mb-4">No se pudo mostrar la confirmación</h3>
                            
                            <div class="alert alert-danger">
                                <h5 class="mb-2">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Detalles del error:
                                </h5>
                                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                            </div>
                            
                            <div class="mt-4">
                                <a href="/" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Volver al Inicio
                                </a>
                                <a href="mailto:eventos@camaraqueretaro.com" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-envelope me-2"></i>
                                    Contactar Soporte
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Success State -->
                    <div class="card shadow-lg">
                        <div class="card-header bg-success text-white text-center">
                            <h2 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Código de Confirmación
                            </h2>
                        </div>
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="fas fa-key text-success" style="font-size: 3rem;"></i>
                                <h3 class="text-success mt-3">¡Su reservación está confirmada!</h3>
                                <p class="lead">Código único de acceso:</p>
                            </div>
                            
                            <!-- Confirmation Code Display -->
                            <div class="confirmation-code" onclick="copyToClipboard('<?php echo $reservation['codigo_confirmacion']; ?>')" title="Clic para copiar">
                                <?php echo htmlspecialchars($reservation['codigo_confirmacion']); ?>
                            </div>
                            
                            <div class="text-center">
                                <button class="btn btn-outline-primary copy-button" onclick="copyToClipboard('<?php echo $reservation['codigo_confirmacion']; ?>')">
                                    <i class="fas fa-copy me-2"></i>
                                    Copiar Código
                                </button>
                            </div>
                            
                            <!-- Reservation Details -->
                            <div class="reservation-details">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Detalles de su reservación:
                                </h5>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong><i class="fas fa-user me-2"></i>Nombre:</strong><br>
                                        <?php echo htmlspecialchars($reservation['nombre_completo']); ?></p>
                                        
                                        <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong><br>
                                        <?php echo htmlspecialchars($reservation['email']); ?></p>
                                        
                                        <p><strong><i class="fas fa-phone me-2"></i>Teléfono:</strong><br>
                                        <?php echo htmlspecialchars($reservation['telefono']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong><i class="fas fa-calendar me-2"></i>Fecha del Evento:</strong><br>
                                        <?php echo date('d/m/Y', strtotime($reservation['fecha_evento'])); ?></p>
                                        
                                        <p><strong><i class="fas fa-users me-2"></i>Número de Asistentes:</strong><br>
                                        <?php echo htmlspecialchars($reservation['numero_asistentes']); ?></p>
                                        
                                        <p><strong><i class="fas fa-tag me-2"></i>Tipo de Evento:</strong><br>
                                        <?php echo htmlspecialchars($reservation['tipo_evento']); ?></p>
                                        
                                        <p><strong><i class="fas fa-check-circle me-2"></i>Estatus:</strong><br>
                                        <span class="badge bg-<?php echo $reservation['estatus'] === 'Confirmada' ? 'success' : ($reservation['estatus'] === 'Pendiente' ? 'warning' : 'danger'); ?>">
                                            <?php echo htmlspecialchars($reservation['estatus']); ?>
                                        </span></p>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <p><strong><i class="fas fa-clock me-2"></i>Fecha de Registro:</strong>
                                    <?php echo date('d/m/Y H:i', strtotime($reservation['fecha_creacion'])); ?></p>
                                </div>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb me-2"></i>Instrucciones importantes:</h6>
                                <ul class="mb-0">
                                    <li><strong>Guarde este código:</strong> Es único para su reservación</li>
                                    <li><strong>Presente este código:</strong> Al momento del evento para acceder</li>
                                    <li><strong>No comparta el código:</strong> Manténgalo seguro y confidencial</li>
                                    <li><strong>Contacto:</strong> Si tiene dudas, comuníquese con nosotros</li>
                                </ul>
                            </div>
                            
                            <!-- Security Information -->
                            <div class="security-info">
                                <h6><i class="fas fa-shield-alt me-2"></i>Información de Seguridad:</h6>
                                <p class="small mb-0">
                                    <strong>ID de Reservación:</strong> #<?php echo htmlspecialchars($reservation['id']); ?> |
                                    <strong>Código generado el:</strong> <?php echo date('d/m/Y H:i', strtotime($reservation['fecha_creacion'])); ?>
                                </p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="text-center mt-4">
                                <a href="/" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-plus me-2"></i>
                                    Nueva Reservación
                                </a>
                                <button class="btn btn-success btn-lg me-3" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>
                                    Imprimir
                                </button>
                                <a href="mailto:eventos@camaraqueretaro.com?subject=Consulta sobre reservación <?php echo htmlspecialchars($reservation['codigo_confirmacion']); ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-envelope me-2"></i>
                                    Contactar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy to clipboard function
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                // Use modern clipboard API
                navigator.clipboard.writeText(text).then(function() {
                    showCopyMessage('Código copiado al portapapeles');
                }, function(err) {
                    console.error('Error copying to clipboard: ', err);
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyTextToClipboard(text);
            }
        }
        
        function fallbackCopyTextToClipboard(text) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    showCopyMessage('Código copiado al portapapeles');
                } else {
                    showCopyMessage('No se pudo copiar. Seleccione manualmente el código.', 'warning');
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                showCopyMessage('Error al copiar. Seleccione manualmente el código.', 'danger');
            }
            
            document.body.removeChild(textArea);
        }
        
        function showCopyMessage(message, type = 'success') {
            // Create toast notification
            var toastHtml = `
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            // Create toast container if it doesn't exist
            var toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1055';
                document.body.appendChild(toastContainer);
            }
            
            // Add toast to container
            toastContainer.innerHTML = toastHtml;
            
            // Show toast
            var toastElement = toastContainer.querySelector('.toast');
            var toast = new bootstrap.Toast(toastElement, { delay: 3000 });
            toast.show();
        }
        
        // Print styles
        window.addEventListener('beforeprint', function() {
            document.title = 'Confirmación de Reservación - Código: <?php echo $reservation ? htmlspecialchars($reservation['codigo_confirmacion']) : ''; ?>';
        });
    </script>
    
    <style media="print">
        .btn, .navbar, .copy-button { display: none !important; }
        .confirmation-code { 
            border: 2px solid #000 !important; 
            background-color: #fff !important;
            color: #000 !important;
        }
        .card { 
            box-shadow: none !important; 
            border: 1px solid #000 !important;
        }
        .text-success, .text-primary { color: #000 !important; }
    </style>
</body>
</html>