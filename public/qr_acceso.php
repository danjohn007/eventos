<?php
/**
 * QR Access Code Generator
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 * 
 * Generates unique QR codes for event reservations
 */

// Start session for security
session_start();

// Error reporting for development (disable in production)
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

// Generate secure token function
function generateSecureToken($reservationId, $email, $timestamp = null) {
    if ($timestamp === null) {
        $timestamp = time();
    }
    
    // Create a unique string combining reservation data
    $data = $reservationId . '|' . $email . '|' . $timestamp;
    
    // Use a secret key (in production, this should be in environment variables)
    $secretKey = 'eventos_secret_key_2024'; // Change this in production!
    
    // Generate secure token using HMAC
    $token = hash_hmac('sha256', $data, $secretKey);
    
    // Encode the data and token together for verification
    $payload = base64_encode(json_encode([
        'id' => $reservationId,
        'email' => $email,
        'timestamp' => $timestamp,
        'token' => $token
    ]));
    
    return $payload;
}

// Verify token function (for future use)
function verifyToken($tokenPayload) {
    try {
        $data = json_decode(base64_decode($tokenPayload), true);
        if (!$data) return false;
        
        $secretKey = 'eventos_secret_key_2024'; // Same key as generation
        $expectedToken = hash_hmac('sha256', 
            $data['id'] . '|' . $data['email'] . '|' . $data['timestamp'], 
            $secretKey
        );
        
        return hash_equals($expectedToken, $data['token']);
    } catch (Exception $e) {
        return false;
    }
}

// Check if phpqrcode library exists
function checkQRLibrary() {
    $qrLibPath = __DIR__ . '/libs/phpqrcode/qrlib.php';
    return file_exists($qrLibPath);
}

// Generate QR code using phpqrcode library
function generateQRCode($data, $filename) {
    $qrLibPath = __DIR__ . '/libs/phpqrcode/qrlib.php';
    
    if (!file_exists($qrLibPath)) {
        throw new Exception('phpqrcode library not found. Please install it first.');
    }
    
    require_once $qrLibPath;
    
    // QR code parameters
    $errorCorrectionLevel = 'L'; // Low error correction
    $matrixPointSize = 6; // Size of each point
    
    // Generate QR code
    QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    
    return $filename;
}

// Get reservation data
$reservationId = isset($_GET['id']) ? (int)sanitizeInput($_GET['id']) : 0;
$email = isset($_GET['email']) ? sanitizeInput($_GET['email']) : '';

// Alternative: accept POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservationId = isset($_POST['id']) ? (int)sanitizeInput($_POST['id']) : 0;
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
}

$error = '';
$qrImagePath = '';
$accessUrl = '';
$token = '';

// Validate input
if ($reservationId <= 0) {
    $error = 'ID de reservación requerido.';
} elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Email válido requerido.';
} else {
    try {
        // Verify reservation exists (security check)
        $reservaModel = new Reserva();
        $reservation = $reservaModel->getByIdAndEmail($reservationId, $email);
        
        if (!$reservation) {
            $error = 'No se encontró una reservación con el ID y email proporcionados.';
        } else {
        $token = generateSecureToken($reservationId, $email);
        
        // Create access URL
        $baseUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'tusitio.com');
        $accessUrl = $baseUrl . '/acceso.php?token=' . urlencode($token);
        
        // Generate QR code if library is available
        if (checkQRLibrary()) {
            $qrDir = __DIR__ . '/temp/qr/';
            if (!is_dir($qrDir)) {
                mkdir($qrDir, 0755, true);
            }
            
            $filename = 'qr_' . $reservationId . '_' . time() . '.png';
            $qrImagePath = $qrDir . $filename;
            
            generateQRCode($accessUrl, $qrImagePath);
            
            // Set relative path for web display
            $qrImagePath = 'temp/qr/' . $filename;
        }
        
        } // End of reservation validation
        
    } catch (Exception $e) {
        $error = 'Error generando el código QR: ' . $e->getMessage();
    }
}

// Clean up old QR files (older than 1 hour)
function cleanupOldQRFiles() {
    $qrDir = __DIR__ . '/temp/qr/';
    if (is_dir($qrDir)) {
        $files = glob($qrDir . 'qr_*.png');
        $oneHourAgo = time() - 3600;
        
        foreach ($files as $file) {
            if (filemtime($file) < $oneHourAgo) {
                unlink($file);
            }
        }
    }
}

// Clean up old files
cleanupOldQRFiles();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código QR de Acceso - Eventos</title>
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
        
        .header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .qr-section {
            margin: 30px 0;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 15px;
            border: 2px dashed #dee2e6;
        }
        
        .qr-code img {
            max-width: 300px;
            width: 100%;
            height: auto;
            border: 5px solid white;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .qr-placeholder {
            width: 300px;
            height: 300px;
            margin: 0 auto;
            background: #e9ecef;
            border: 2px dashed #adb5bd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #6c757d;
        }
        
        .qr-placeholder i {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        
        .access-url {
            margin: 20px 0;
            padding: 15px;
            background: #e7f3ff;
            border: 1px solid #b3d7ff;
            border-radius: 8px;
            word-break: break-all;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        .instructions {
            margin: 30px 0;
            text-align: left;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
        }
        
        .instructions h3 {
            color: #856404;
            margin-bottom: 15px;
        }
        
        .instructions ol {
            color: #856404;
            padding-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .form-section {
            margin: 30px 0;
            text-align: left;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
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
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .library-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .library-warning h4 {
            margin-bottom: 10px;
        }
        
        .library-warning code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .qr-placeholder {
                width: 250px;
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Código QR de Acceso</h1>
            <p>Genera tu código QR para acceder al evento</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!checkQRLibrary()): ?>
            <div class="library-warning">
                <h4>⚠️ Librería phpqrcode no encontrada</h4>
                <p>Para generar códigos QR, necesitas instalar la librería phpqrcode:</p>
                <ol>
                    <li>Descarga phpqrcode desde: <code>https://sourceforge.net/projects/phpqrcode/</code></li>
                    <li>Extrae el archivo en: <code>public/libs/phpqrcode/</code></li>
                    <li>Asegúrate de que exista el archivo: <code>public/libs/phpqrcode/qrlib.php</code></li>
                </ol>
                <p><strong>Mientras tanto, se mostrará la URL de acceso que puedes usar manualmente.</strong></p>
            </div>
        <?php endif; ?>
        
        <?php if (!$reservationId || !$email): ?>
            <form method="POST" class="form-section">
                <div class="form-group">
                    <label for="id">ID de Reservación:</label>
                    <input type="number" id="id" name="id" value="<?php echo $reservationId; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email de la Reservación:</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                
                <button type="submit" class="btn">Generar Código QR</button>
            </form>
        <?php endif; ?>
        
        <?php if ($token && !$error): ?>
            <div class="qr-section">
                <h2>Tu Código QR de Acceso</h2>
                
                <?php if ($qrImagePath && file_exists(__DIR__ . '/' . $qrImagePath)): ?>
                    <div class="qr-code">
                        <img src="<?php echo $qrImagePath; ?>" alt="Código QR de Acceso">
                    </div>
                <?php else: ?>
                    <div class="qr-placeholder">
                        <div style="font-size: 4rem;">📱</div>
                        <p>Código QR no disponible</p>
                        <p><small>Instala la librería phpqrcode</small></p>
                    </div>
                <?php endif; ?>
                
                <div class="access-url">
                    <strong>URL de Acceso:</strong><br>
                    <a href="<?php echo $accessUrl; ?>" target="_blank"><?php echo $accessUrl; ?></a>
                </div>
                
                <div class="instructions">
                    <h3>📋 Instrucciones de Uso:</h3>
                    <ol>
                        <li>Guarda este código QR en tu dispositivo móvil</li>
                        <li>Presenta el código QR en la entrada del evento</li>
                        <li>El personal escaneará tu código para verificar tu acceso</li>
                        <li>También puedes usar la URL directamente si no tienes el QR</li>
                    </ol>
                </div>
                
                <p><strong>Información de la Reservación:</strong></p>
                <p>ID: <?php echo $reservationId; ?> | Email: <?php echo $email; ?></p>
                
                <button onclick="window.print()" class="btn" style="margin-top: 20px;">
                    🖨️ Imprimir Código QR
                </button>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 0.9rem;">
            <p>Sistema de Reservación de Eventos</p>
            <p>Cámara de Comercio de Querétaro</p>
        </div>
    </div>
    
    <script>
        // Auto-cleanup: remove QR image from DOM after 10 minutes for security
        setTimeout(function() {
            const qrImages = document.querySelectorAll('.qr-code img');
            qrImages.forEach(img => {
                img.style.filter = 'blur(5px)';
                img.style.opacity = '0.5';
            });
        }, 600000); // 10 minutes
        
        // Print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                body { background: white !important; }
                .container { box-shadow: none !important; }
                .btn { display: none !important; }
                .library-warning { display: none !important; }
                .form-section { display: none !important; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>