<?php
class AppointmentManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function updateAppointmentStatus($appointmentId, $status, $doctorId) {
        $query = "UPDATE appointments SET status = :status WHERE id = :id AND doctor_id = :doctor_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $appointmentId);
        $stmt->bindParam(':doctor_id', $doctorId);
        return $stmt->execute();
    }
    
    public function getDoctorAppointments($doctorId, $status = null, $date = null) {
        $query = "SELECT a.*, u.first_name, u.last_name, u.phone, u.email,
                         pp.date_of_birth, pp.gender, pp.address
                  FROM appointments a 
                  JOIN users u ON a.patient_id = u.id 
                  LEFT JOIN patient_profiles pp ON u.id = pp.user_id
                  WHERE a.doctor_id = :doctor_id";
        
        $params = [':doctor_id' => $doctorId];
        
        if ($status) {
            $query .= " AND a.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date) {
            $query .= " AND a.appointment_date = :date";
            $params[':date'] = $date;
        }
        
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPatientHistory($patientId, $doctorId) {
        $query = "SELECT a.*, mn.diagnosis, mn.prescription, mn.notes as medical_notes
                  FROM appointments a 
                  LEFT JOIN medical_notes mn ON a.id = mn.appointment_id
                  WHERE a.patient_id = :patient_id AND a.doctor_id = :doctor_id
                  ORDER BY a.appointment_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllAppointments($status = null, $date = null) {
        $query = "SELECT a.*, 
                         p.first_name as patient_first, p.last_name as patient_last, 
                         p.phone as patient_phone, p.email as patient_email,
                         d.first_name as doctor_first, d.last_name as doctor_last,
                         prof.specialization,
                         dp.name as department_name
                  FROM appointments a 
                  JOIN users p ON a.patient_id = p.id 
                  JOIN users d ON a.doctor_id = d.id
                  LEFT JOIN doctor_profiles prof ON d.id = prof.user_id
                  LEFT JOIN departments dp ON prof.department_id = dp.id";
        
        $params = [];
        
        if ($status) {
            $query .= " WHERE a.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date) {
            $query .= ($status ? " AND" : " WHERE") . " a.appointment_date = :date";
            $params[':date'] = $date;
        }
        
        $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateAppointmentStatusAdmin($appointmentId, $status) {
        $query = "UPDATE appointments SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $appointmentId);
        return $stmt->execute();
    }
    
    public function getAppointmentStats() {
        $query = "SELECT 
                    COUNT(*) as total_appointments,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                  FROM appointments";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getMonthlyAppointments() {
        $query = "SELECT 
                    DATE_FORMAT(appointment_date, '%Y-%m') as month,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                  FROM appointments 
                  WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  GROUP BY DATE_FORMAT(appointment_date, '%Y-%m')
                  ORDER BY month";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
