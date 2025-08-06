<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Cámara de Comercio de Querétaro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-lock me-2"></i>
                            Panel de Administración
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-shield text-dark" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">Acceso Restringido</h5>
                        </div>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>Usuario
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required 
                                       autocomplete="username"
                                       autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key me-1"></i>Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Iniciar Sesión
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <small class="text-muted">
                            <a href="/eventos/public/" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>
                                Volver al formulario de reservación
                            </a>
                        </small>
                    </div>
                </div>
                
                <!-- Development note -->
                <div class="alert alert-info mt-3" id="dev-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Información de desarrollo:</h6>
                    <p class="mb-1"><strong>Usuario por defecto:</strong> admin</p>
                    <p class="mb-0"><strong>Contraseña por defecto:</strong> admin123</p>
                    <small class="text-muted">Esta información solo se muestra en modo desarrollo.</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hide dev info after 10 seconds or when form is submitted
        setTimeout(function() {
            const devInfo = document.getElementById('dev-info');
            if (devInfo) {
                devInfo.style.display = 'none';
            }
        }, 10000);
        
        // Form validation
        (function() {
            'use strict';
            
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                // Hide dev info when submitting
                const devInfo = document.getElementById('dev-info');
                if (devInfo) {
                    devInfo.style.display = 'none';
                }
                
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