# eventos
Sistema de reservación de eventos 

## Funcionalidades

### Reservación de Eventos
- Formulario de reservación con validación completa
- Generación automática de código QR único para cada reservación
- Página de confirmación con código QR escaneable
- Validación de datos y protección CSRF

### Código QR de Acceso
Al confirmar una reservación, el sistema:
1. **Genera un código único** de 16 caracteres para la reservación
2. **Almacena el código** en la base de datos en el campo `codigo_qr`
3. **Muestra un código QR** en la página de confirmación que contiene:
   - Código único de la reservación
   - Información básica del evento
   - Timestamp de generación
4. **Permite validación** del código QR durante el evento

#### Flujo del Código QR:
```
Usuario completa formulario → Reservación guardada con código único → 
Página de éxito muestra QR code → Usuario presenta QR en el evento → 
Staff puede escanear y validar la reservación
```

El código QR contiene datos JSON con la estructura:
```json
{
  "codigo": "CODIGO_UNICO_16_CHARS",
  "tipo": "reservacion_evento", 
  "timestamp": 1234567890,
  "nombre": "Nombre del Usuario",
  "fecha_evento": "2025-12-15"
}
```

### Panel de Administración
- Gestión de reservaciones
- Actualización de estatus
- Filtros y paginación

## Instalación

1. Clonar el repositorio
2. Configurar la base de datos en `config/database.php`
3. Ejecutar el script de inicialización:
   ```bash
   php scripts/init_db.php
   ```
4. Servir la aplicación:
   ```bash
   php -S localhost:8000 -t public
   ```

## Estructura de Base de Datos

### Tabla: reservaciones
- `id` - ID único autoincremental
- `nombre_completo` - Nombre del usuario
- `email` - Correo electrónico
- `telefono` - Número de teléfono
- `fecha_evento` - Fecha del evento
- `numero_asistentes` - Cantidad de asistentes
- `tipo_evento` - Tipo de evento
- `codigo_qr` - **Código único de 16 caracteres para el QR** (NUEVO)
- `estatus` - Estado de la reservación (Pendiente/Confirmada/Cancelada)
- `comentarios` - Comentarios adicionales
- `fecha_creacion` - Fecha de creación
- `fecha_actualizacion` - Fecha de última actualización

## Credenciales por Defecto
- **Usuario:** admin
- **Contraseña:** admin123
