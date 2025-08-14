<?php
class DoctorProfile {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function getDoctorProfile($userId) {
        $query = "SELECT u.*, dp.specialization, dp.bio, dp.availability, d.name as department_name
                  FROM users u 
                  LEFT JOIN doctor_profiles dp ON u.id = dp.user_id
                  LEFT JOIN departments d ON dp.department_id = d.id
                  WHERE u.id = :user_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateProfile($userId, $data) {
        // Update user table
        $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, 
                  phone = :phone WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':user_id', $userId);
        $result1 = $stmt->execute();
        
        // Update doctor profile
        $query = "UPDATE doctor_profiles SET specialization = :specialization, bio = :bio 
                  WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':specialization', $data['specialization']);
        $stmt->bindParam(':bio', $data['bio']);
        $stmt->bindParam(':user_id', $userId);
        $result2 = $stmt->execute();
        
        return $result1 && $result2;
    }
}
?>
