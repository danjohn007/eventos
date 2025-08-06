<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Cámara de Comercio de Querétaro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-cogs me-2"></i>
                Panel de Administración
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>
                    <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="?route=admin/logout">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($successMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">Total</h5>
                                <h3><?php echo $totalReservaciones; ?></h3>
                            </div>
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">Pendientes</h5>
                                <h3><?php 
                                    $pendientes = array_filter($reservaciones, function($r) { return $r['estatus'] === 'Pendiente'; });
                                    echo count($pendientes);
                                ?></h3>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">Confirmadas</h5>
                                <h3><?php 
                                    $confirmadas = array_filter($reservaciones, function($r) { return $r['estatus'] === 'Confirmada'; });
                                    echo count($confirmadas);
                                ?></h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="card-title">Canceladas</h5>
                                <h3><?php 
                                    $canceladas = array_filter($reservaciones, function($r) { return $r['estatus'] === 'Cancelada'; });
                                    echo count($canceladas);
                                ?></h3>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtros de Búsqueda
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="?route=admin/dashboard">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="estatus" class="form-label">Estatus</label>
                            <select class="form-select" id="estatus" name="estatus">
                                <option value="">Todos</option>
                                <option value="Pendiente" <?php echo ($filters['estatus'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Confirmada" <?php echo ($filters['estatus'] === 'Confirmada') ? 'selected' : ''; ?>>Confirmada</option>
                                <option value="Cancelada" <?php echo ($filters['estatus'] === 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?php echo htmlspecialchars($filters['fecha_desde']); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?php echo htmlspecialchars($filters['fecha_hasta']); ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>
                                Filtrar
                            </button>
                            <a href="?route=admin/dashboard" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reservations Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Reservaciones (<?php echo $totalReservaciones; ?> total)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($reservaciones)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay reservaciones que mostrar</h5>
                        <p class="text-muted">Las reservaciones aparecerán aquí cuando los usuarios las registren.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Fecha Evento</th>
                                    <th>Asistentes</th>
                                    <th>Tipo</th>
                                    <th>Estatus</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservaciones as $reservacion): ?>
                                    <tr>
                                        <td><?php echo $reservacion['id']; ?></td>
                                        <td><?php echo htmlspecialchars($reservacion['nombre_completo']); ?></td>
                                        <td><?php echo htmlspecialchars($reservacion['email']); ?></td>
                                        <td><?php echo htmlspecialchars($reservacion['telefono']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($reservacion['fecha_evento'])); ?></td>
                                        <td><?php echo $reservacion['numero_asistentes']; ?></td>
                                        <td><?php echo htmlspecialchars($reservacion['tipo_evento']); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo match($reservacion['estatus']) {
                                                    'Pendiente' => 'bg-warning text-dark',
                                                    'Confirmada' => 'bg-success',
                                                    'Cancelada' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            ?>">
                                                <?php echo $reservacion['estatus']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($reservacion['fecha_creacion'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Status Update Dropdown -->
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><h6 class="dropdown-header">Cambiar Estatus</h6></li>
                                                        <li>
                                                            <form method="POST" action="?route=admin/update-status" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $reservacion['id']; ?>">
                                                                <input type="hidden" name="status" value="Pendiente">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-clock text-warning me-2"></i>Pendiente
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="?route=admin/update-status" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $reservacion['id']; ?>">
                                                                <input type="hidden" name="status" value="Confirmada">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-check-circle text-success me-2"></i>Confirmada
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="?route=admin/update-status" class="d-inline">
                                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                                <input type="hidden" name="id" value="<?php echo $reservacion['id']; ?>">
                                                                <input type="hidden" name="status" value="Cancelada">
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-times-circle text-danger me-2"></i>Cancelada
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                                
                                                <!-- Delete Button -->
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?php echo $reservacion['id']; ?>, '<?php echo addslashes(htmlspecialchars($reservacion['nombre_completo'])); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?route=admin/dashboard&page=<?php echo $page - 1; ?>&<?php echo http_build_query($filters); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?route=admin/dashboard&page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?route=admin/dashboard&page=<?php echo $page + 1; ?>&<?php echo http_build_query($filters); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea eliminar la reservación de <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" action="?route=admin/delete" class="d-inline" id="deleteForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteUserName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 300000);
    </script>
</body>
</html>