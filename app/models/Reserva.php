<?php
/**
 * Reserva Model
 * Sistema de Reservación de Eventos - Cámara de Comercio de Querétaro
 */

class Reserva {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create a new reservation
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO reservaciones (nombre_completo, email, telefono, fecha_evento, numero_asistentes, tipo_evento, estatus, fecha_creacion) 
                    VALUES (:nombre_completo, :email, :telefono, :fecha_evento, :numero_asistentes, :tipo_evento, 'Pendiente', CURRENT_TIMESTAMP)";
            
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':nombre_completo' => $data['nombre_completo'],
                ':email' => $data['email'],
                ':telefono' => $data['telefono'],
                ':fecha_evento' => $data['fecha_evento'],
                ':numero_asistentes' => $data['numero_asistentes'],
                ':tipo_evento' => $data['tipo_evento']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating reservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all reservations with pagination
     */
    public function getAll($page = 1, $limit = 10, $filters = []) {
        try {
            $offset = ($page - 1) * $limit;
            
            $sql = "SELECT * FROM reservaciones WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['estatus'])) {
                $sql .= " AND estatus = :estatus";
                $params[':estatus'] = $filters['estatus'];
            }
            
            if (!empty($filters['fecha_desde'])) {
                $sql .= " AND fecha_evento >= :fecha_desde";
                $params[':fecha_desde'] = $filters['fecha_desde'];
            }
            
            if (!empty($filters['fecha_hasta'])) {
                $sql .= " AND fecha_evento <= :fecha_hasta";
                $params[':fecha_hasta'] = $filters['fecha_hasta'];
            }
            
            $sql .= " ORDER BY fecha_creacion DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind limit and offset separately as integers
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            // Bind other parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting reservations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total count for pagination
     */
    public function getCount($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM reservaciones WHERE 1=1";
            $params = [];
            
            // Apply same filters as getAll
            if (!empty($filters['estatus'])) {
                $sql .= " AND estatus = :estatus";
                $params[':estatus'] = $filters['estatus'];
            }
            
            if (!empty($filters['fecha_desde'])) {
                $sql .= " AND fecha_evento >= :fecha_desde";
                $params[':fecha_desde'] = $filters['fecha_desde'];
            }
            
            if (!empty($filters['fecha_hasta'])) {
                $sql .= " AND fecha_evento <= :fecha_hasta";
                $params[':fecha_hasta'] = $filters['fecha_hasta'];
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $result = $stmt->fetch();
            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update reservation status
     */
    public function updateStatus($id, $status) {
        try {
            $sql = "UPDATE reservaciones SET estatus = :estatus WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                ':estatus' => $status,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete reservation
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM reservaciones WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting reservation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate reservation data
     */
    public function validate($data) {
        $errors = [];
        
        if (empty($data['nombre_completo']) || strlen($data['nombre_completo']) < 2) {
            $errors[] = "El nombre completo es requerido y debe tener al menos 2 caracteres.";
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Un email válido es requerido.";
        }
        
        if (empty($data['telefono']) || !preg_match('/^[0-9\-\+\(\)\s]{10,}$/', $data['telefono'])) {
            $errors[] = "Un teléfono válido es requerido (mínimo 10 dígitos).";
        }
        
        if (empty($data['fecha_evento'])) {
            $errors[] = "La fecha del evento es requerida.";
        } else {
            $eventDate = new DateTime($data['fecha_evento']);
            $today = new DateTime();
            if ($eventDate <= $today) {
                $errors[] = "La fecha del evento debe ser futura.";
            }
        }
        
        if (empty($data['numero_asistentes']) || !is_numeric($data['numero_asistentes']) || $data['numero_asistentes'] < 1) {
            $errors[] = "El número de asistentes debe ser un número mayor a 0.";
        }
        
        if (empty($data['tipo_evento'])) {
            $errors[] = "Debe seleccionar un tipo de evento.";
        }
        
        return $errors;
    }
}
?>