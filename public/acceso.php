<?php
/**
 * Access Verification Page
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 * 
 * Verifies QR access tokens and grants event access
 */

// Start session
session_start();

// Error reporting for development
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

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

// Verify token function
function verifyToken($tokenPayload) {
    try {
        $data = json_decode(base64_decode($tokenPayload), true);
        if (!$data) return false;
        
        $secretKey = 'eventos_secret_key_2024'; // Same key as generation
        $expectedToken = hash_hmac('sha256', 
            $data['id'] . '|' . $data['email'] . '|' . $data['timestamp'], 
            $secretKey
        );
        
        // Verify token and check expiration (24 hours)
        $isValidToken = hash_equals($expectedToken, $data['token']);
        $isNotExpired = (time() - $data['timestamp']) < 86400; // 24 hours
        
        return $isValidToken && $isNotExpired ? $data : false;
    } catch (Exception $e) {
        return false;
    }
}

// Get token from URL
$token = isset($_GET['token']) ? sanitizeInput($_GET['token']) : '';
$accessGranted = false;
$reservationData = null;
$error = '';

if (empty($token)) {
    $error = 'Token de acceso requerido.';
} else {
    // Verify token
    $tokenData = verifyToken($token);
    
    if (!$tokenData) {
        $error = 'Token de acceso inválido o expirado.';
    } else {
        // Additional verification: check if reservation exists in database
        try {
            $reservaModel = new Reserva();
            $reservation = $reservaModel->getByIdAndEmail($tokenData['id'], $tokenData['email']);
            
            if (!$reservation) {
                $error = 'Reservación no encontrada en el sistema.';
            } elseif ($reservation['estatus'] !== 'Confirmada') {
                $error = 'La reservación no está confirmada. Estado actual: ' . $reservation['estatus'];
            } else {
                $accessGranted = true;
                $reservationData = $reservation;
            }
        } catch (Exception $e) {
            $error = 'Error verificando la reservación.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Acceso - Eventos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .status-granted {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        
        .status-denied {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        
        .access-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .reservation-details {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .reservation-details h3 {
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        
        .detail-value {
            color: #333;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎟️ Verificación de Acceso</h1>
            <p>Sistema de Control de Acceso a Eventos</p>
        </div>
        
        <?php if ($accessGranted): ?>
            <div class="status-granted">
                <div class="access-icon">✅</div>
                <h2>¡Acceso Autorizado!</h2>
                <p>Bienvenido(a) al evento. Tu reservación ha sido verificada exitosamente.</p>
            </div>
            
            <div class="reservation-details">
                <h3>Detalles de la Reservación</h3>
                
                <div class="detail-row">
                    <span class="detail-label">ID de Reservación:</span>
                    <span class="detail-value">#<?php echo $reservationData['id']; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Nombre:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($reservationData['nombre_completo']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($reservationData['email']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Teléfono:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($reservationData['telefono']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Fecha del Evento:</span>
                    <span class="detail-value"><?php echo date('d/m/Y', strtotime($reservationData['fecha_evento'])); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Número de Asistentes:</span>
                    <span class="detail-value"><?php echo $reservationData['numero_asistentes']; ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Tipo de Evento:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($reservationData['tipo_evento']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Estado:</span>
                    <span class="detail-value" style="color: #28a745; font-weight: bold;"><?php echo $reservationData['estatus']; ?></span>
                </div>
            </div>
            
            <p style="margin: 20px 0; color: #666;">
                <strong>Verificado el:</strong> <?php echo date('d/m/Y H:i:s'); ?>
            </p>
            
        <?php else: ?>
            <div class="status-denied">
                <div class="access-icon">❌</div>
                <h2>Acceso Denegado</h2>
                <p><strong>Error:</strong> <?php echo $error; ?></p>
                
                <?php if (strpos($error, 'expirado') !== false): ?>
                    <p style="margin-top: 15px;">
                        <small>Los códigos QR expiran después de 24 horas por seguridad. 
                        Solicita un nuevo código QR para acceder al evento.</small>
                    </p>
                <?php endif; ?>
            </div>
            
            <a href="qr_acceso.php" class="btn">Generar Nuevo Código QR</a>
        <?php endif; ?>
        
        <div class="footer">
            <p>Sistema de Reservación de Eventos</p>
            <p>Cámara de Comercio de Querétaro</p>
            <p><small>Verificación segura con token HMAC-SHA256</small></p>
        </div>
    </div>
    
    <?php if ($accessGranted): ?>
    <script>
        // Auto-refresh page after 5 minutes for security
        setTimeout(function() {
            window.location.reload();
        }, 300000); // 5 minutes
        
        // Prevent screenshots/copying for security
        document.addEventListener('keydown', function(e) {
            if (e.key === 'PrintScreen' || (e.ctrlKey && e.key === 's')) {
                e.preventDefault();
                alert('Por seguridad, no se permite capturar pantalla de esta página.');
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>