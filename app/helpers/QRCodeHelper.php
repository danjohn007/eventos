<?php
/**
 * QR Code Helper
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

class QRCodeHelper {
    
    /**
     * Generate QR code URL using online service
     * Using qr-server.com as it's free and reliable
     */
    public static function generateQRCodeURL($data, $size = 200) {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}&format=png&ecc=M";
    }
    
    /**
     * Generate QR code data for reservation
     */
    public static function generateReservationQRData($codigoQr, $reservationData = null) {
        // Create a simple JSON structure with reservation info
        $qrData = [
            'codigo' => $codigoQr,
            'tipo' => 'reservacion_evento',
            'timestamp' => time()
        ];
        
        if ($reservationData) {
            $qrData['nombre'] = $reservationData['nombre_completo'] ?? '';
            $qrData['fecha_evento'] = $reservationData['fecha_evento'] ?? '';
        }
        
        return json_encode($qrData);
    }
    
    /**
     * Generate QR code image URL for reservation
     */
    public static function generateReservationQRURL($codigoQr, $reservationData = null, $size = 200) {
        $qrData = self::generateReservationQRData($codigoQr, $reservationData);
        return self::generateQRCodeURL($qrData, $size);
    }
}
?>