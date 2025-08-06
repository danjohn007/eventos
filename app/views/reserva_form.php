<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservación de Eventos - Cámara de Comercio de Querétaro</title>
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
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Reservación de Eventos
                        </h2>
                        <p class="mb-0 mt-2">Complete el formulario para registrar su reservación</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Errores encontrados:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_completo" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nombre Completo *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre_completo" 
                                           name="nombre_completo" 
                                           value="<?php echo htmlspecialchars($formData['nombre_completo'] ?? ''); ?>"
                                           required 
                                           maxlength="100">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Correo Electrónico *
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                                           required 
                                           maxlength="100">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Teléfono *
                                    </label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="telefono" 
                                           name="telefono" 
                                           value="<?php echo htmlspecialchars($formData['telefono'] ?? ''); ?>"
                                           required 
                                           pattern="[0-9\-\+\(\)\s]{10,}"
                                           title="Ingrese un teléfono válido (mínimo 10 dígitos)">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_evento" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>Fecha del Evento *
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="fecha_evento" 
                                           name="fecha_evento" 
                                           value="<?php echo htmlspecialchars($formData['fecha_evento'] ?? ''); ?>"
                                           required 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="numero_asistentes" class="form-label">
                                        <i class="fas fa-users me-1"></i>Número de Asistentes *
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="numero_asistentes" 
                                           name="numero_asistentes" 
                                           value="<?php echo htmlspecialchars($formData['numero_asistentes'] ?? ''); ?>"
                                           required 
                                           min="1" 
                                           max="1000">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_evento" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Tipo de Evento *
                                    </label>
                                    <select class="form-select" id="tipo_evento" name="tipo_evento" required>
                                        <option value="">Seleccione un tipo de evento</option>
                                        <option value="Conferencia" <?php echo ($formData['tipo_evento'] ?? '') === 'Conferencia' ? 'selected' : ''; ?>>Conferencia</option>
                                        <option value="Seminario" <?php echo ($formData['tipo_evento'] ?? '') === 'Seminario' ? 'selected' : ''; ?>>Seminario</option>
                                        <option value="Taller" <?php echo ($formData['tipo_evento'] ?? '') === 'Taller' ? 'selected' : ''; ?>>Taller</option>
                                        <option value="Networking" <?php echo ($formData['tipo_evento'] ?? '') === 'Networking' ? 'selected' : ''; ?>>Networking</option>
                                        <option value="Capacitación" <?php echo ($formData['tipo_evento'] ?? '') === 'Capacitación' ? 'selected' : ''; ?>>Capacitación</option>
                                        <option value="Reunión Empresarial" <?php echo ($formData['tipo_evento'] ?? '') === 'Reunión Empresarial' ? 'selected' : ''; ?>>Reunión Empresarial</option>
                                        <option value="Otro" <?php echo ($formData['tipo_evento'] ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Enviar Reservación
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-muted">
                        <small>
                            <i class="fas fa-shield-alt me-1"></i>
                            Sus datos están protegidos y serán utilizados únicamente para la gestión de su reservación.
                        </small>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="?route=admin" class="text-muted">
                        <i class="fas fa-cog me-1"></i>Panel de Administración
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set minimum date to tomorrow
        document.getElementById('fecha_evento').min = new Date(new Date().getTime() + 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        
        // Form validation enhancement
        (function() {
            'use strict';
            
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
</body>
</html>