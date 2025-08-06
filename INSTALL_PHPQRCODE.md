# Instrucciones de Instalación - Librería phpqrcode

## Instalación Automática (Recomendada)

1. Descarga la librería phpqrcode desde SourceForge:
   ```bash
   cd public/libs/
   wget https://sourceforge.net/projects/phpqrcode/files/phpqrcode-1.1.4.zip/download -O phpqrcode.zip
   unzip phpqrcode.zip
   mv phpqrcode-1.1.4 phpqrcode
   ```

2. Verifica que existe el archivo principal:
   ```bash
   ls -la public/libs/phpqrcode/qrlib.php
   ```

## Instalación Manual

1. Ve a https://sourceforge.net/projects/phpqrcode/
2. Descarga la versión más reciente (recomendada: phpqrcode-1.1.4)
3. Extrae el archivo ZIP
4. Copia la carpeta extraída a `public/libs/phpqrcode/`
5. Asegúrate de que el archivo `public/libs/phpqrcode/qrlib.php` existe

## Estructura de Archivos Requerida

```
public/
├── libs/
│   └── phpqrcode/
│       ├── qrlib.php          (archivo principal)
│       ├── qrconfig.php
│       ├── qrtools.php
│       └── ... (otros archivos)
├── temp/
│   └── qr/                    (directorio para QR temporales)
└── qr_acceso.php             (página principal)
```

## Permisos de Archivos

Asegúrate de que el directorio `public/temp/qr/` tenga permisos de escritura:

```bash
chmod 755 public/temp/qr/
```

## Verificación

Accede a `qr_acceso.php` en tu navegador. Si la instalación es correcta, no deberías ver el mensaje de advertencia sobre la librería faltante.

## Notas de Seguridad

- Los archivos QR temporales se eliminan automáticamente después de 1 hora
- El token generado utiliza HMAC-SHA256 para mayor seguridad
- Cambia la clave secreta en `qr_acceso.php` antes de usar en producción