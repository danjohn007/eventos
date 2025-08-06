# Sistema de Códigos de Confirmación de Reservaciones

## Descripción
Este sistema proporciona códigos únicos de confirmación para cada reservación exitosa, permitiendo a los usuarios acceder a sus detalles de reservación de forma segura.

## Funcionalidades Implementadas

### 1. Generación Automática de Códigos
- **Ubicación**: `app/models/Reserva.php`
- **Función**: `generateConfirmationCode()`
- **Formato**: 8 caracteres alfanuméricos (0-9, A-Z)
- **Unicidad**: Verificación automática contra códigos existentes
- **Seguridad**: Generación con `random_int()` para máxima entropía

### 2. Página de Confirmación
- **Archivo**: `public/confirmacion.php`
- **Acceso**: GET/POST con parámetro `id` (ID de reservación)
- **Funcionalidades**:
  - Muestra código de confirmación prominente
  - Botón "Copiar Código" con fallback para navegadores antiguos
  - Detalles completos de la reservación
  - Instrucciones de uso
  - Información de seguridad
  - Manejo de errores robusto
  - Diseño responsivo
  - Soporte para impresión

### 3. Integración con Flujo Existente
- **Creación**: Códigos generados automáticamente al crear reservación
- **Enlace**: Botón en página de éxito redirige a confirmación
- **Base de datos**: Campo `codigo_confirmacion` añadido a tabla `reservaciones`

## Aspectos de Seguridad

### Validación de Entrada
- Filtrado de ID de reservación como entero
- Sanitización de parámetros GET/POST
- Verificación de existencia de reservación

### Protección de Datos
- No exposición de datos sensibles en URLs
- Códigos únicos e impredecibles
- Validación de permisos por ID de reservación

### Manejo de Errores
- Mensajes de error informativos pero seguros
- Redirección a página principal en caso de error
- Logs de errores para debugging

## Uso del Sistema

### Para Usuarios
1. Completar formulario de reservación
2. En página de éxito, hacer clic en "Ver Código de Confirmación"
3. Copiar y guardar el código único
4. Presentar código al momento del evento

### Para Administradores
- Los códigos se almacenan en la base de datos
- Consulta: `SELECT codigo_confirmacion FROM reservaciones WHERE id = ?`
- Validación: verificar código contra base de datos

### URLs de Acceso
- **Formato**: `/confirmacion.php?id=[ID_RESERVACION]`
- **Ejemplo**: `/confirmacion.php?id=2`

## Mantenimiento de Base de Datos

### Esquema Actualizado
```sql
ALTER TABLE reservaciones ADD COLUMN codigo_confirmacion VARCHAR(32);
```

### Consultas Útiles
```sql
-- Obtener reservación por código
SELECT * FROM reservaciones WHERE codigo_confirmacion = 'ABC12345';

-- Verificar unicidad de códigos
SELECT codigo_confirmacion, COUNT(*) FROM reservaciones 
GROUP BY codigo_confirmacion HAVING COUNT(*) > 1;

-- Reservaciones sin código (para migración)
SELECT * FROM reservaciones WHERE codigo_confirmacion IS NULL;
```

## Personalización

### Modificar Formato de Código
Editar función `generateConfirmationCode()` en `app/models/Reserva.php`:
- Cambiar longitud modificando el bucle
- Cambiar caracteres modificando `$characters`
- Añadir prefijos/sufijos según necesidades

### Estilos Visuales
Los estilos están definidos en `confirmacion.php`:
- `.confirmation-code`: estilo del código principal
- `.reservation-details`: detalles de reservación
- `.security-info`: información de seguridad
- Soporte completo para impresión con media queries

### Mensajes y Textos
Todos los textos están en español y pueden modificarse directamente en:
- `confirmacion.php`: interfaz de usuario
- `app/views/reserva_success.php`: botón de enlace
- Correos/notificaciones futuras

## Características Técnicas

### Compatibilidad
- PHP 7.4+
- Bootstrap 5.3
- Font Awesome 6.0
- Funciona con SQLite y MySQL
- Responsive design para móviles

### Performance
- Consultas optimizadas a base de datos
- Código JavaScript eficiente
- Carga asíncrona de recursos CDN
- Fallbacks para funcionalidad offline

### Accesibilidad
- Navegación con teclado
- Lectores de pantalla compatibles
- Contraste de colores adecuado
- Mensajes de error claros