<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservación Exitosa - Cámara de Comercio de Querétaro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
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
                            ¡Reservación Exitosa!
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
                                Información de su reservación:
                            </h5>
                            
                            <?php if (!empty($reservationData)): ?>
                                <div class="row text-start">
                                    <div class="col-md-6">
                                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($reservationData['nombre_completo']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($reservationData['email']); ?></p>
                                        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($reservationData['telefono']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Fecha del Evento:</strong> <?php echo date('d/m/Y', strtotime($reservationData['fecha_evento'])); ?></p>
                                        <p><strong>Número de Asistentes:</strong> <?php echo htmlspecialchars($reservationData['numero_asistentes']); ?></p>
                                        <p><strong>Tipo de Evento:</strong> <?php echo htmlspecialchars($reservationData['tipo_evento']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($qrCode)): ?>
                        <div class="alert alert-primary text-center mt-4">
                            <h5 class="mb-3">
                                <i class="fas fa-qrcode me-2"></i>
                                Código QR de Acceso
                            </h5>
                            <p class="mb-3">Presente este código QR en el evento para validar su reservación:</p>
                            
                            <div class="qr-code-container mb-3">
                                <img src="<?php echo QRCodeHelper::generateReservationQRURL($qrCode, $reservationData, 200); ?>" 
                                     alt="Código QR de la reservación" 
                                     class="border border-2 border-dark rounded p-2 bg-white"
                                     style="max-width: 200px;">
                            </div>
                            
                            <div class="bg-light p-3 rounded">
                                <strong>Código de Reservación:</strong> 
                                <code class="fs-5 text-primary"><?php echo htmlspecialchars($qrCode); ?></code>
                            </div>
                            
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Guarde este código QR o tome una captura de pantalla para presentarlo en el evento.
                            </small>
                        </div>
                        <?php endif; ?>
                        
                        <p class="lead mb-4">
                            <?php echo htmlspecialchars($successMessage); ?>
                        </p>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-clock me-2"></i>Próximos pasos:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Su reservación tiene estatus: <strong>Pendiente</strong></li>
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
                            <?php if (!empty($qrCode)): ?>
                                Código de reservación: <strong><?php echo htmlspecialchars($qrCode); ?></strong>
                            <?php else: ?>
                                Número de referencia: <strong>REF-<?php echo date('Ymd') . '-' . substr(md5($reservationData['email'] ?? ''), 0, 6); ?></strong>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto redirect after 30 seconds
        setTimeout(function() {
            if (confirm('¿Desea realizar otra reservación?')) {
                window.location.href = '/';
            }
        }, 30000);
    </script>
</body>
</html>