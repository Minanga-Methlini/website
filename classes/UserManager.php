<?php
class UserManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function getAllUsers($role = null) {
        $query = "SELECT u.*, 
                         dp.specialization, d.name as department_name,
                         pp.date_of_birth, pp.gender
                  FROM users u
                  LEFT JOIN doctor_profiles dp ON u.id = dp.user_id
                  LEFT JOIN departments d ON dp.department_id = d.id
                  LEFT JOIN patient_profiles pp ON u.id = pp.user_id";
        
        if ($role) {
            $query .= " WHERE u.role = :role";
        }
        
        $query .= " ORDER BY u.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function createUser($userData) {
        try {
            $this->db->beginTransaction();
            
            // Create user
            $query = "INSERT INTO users (username, email, password, role, first_name, last_name, phone) 
                      VALUES (:username, :email, :password, :role, :first_name, :last_name, :phone)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':username' => $userData['username'],
                ':email' => $userData['email'],
                ':password' => password_hash($userData['password'], PASSWORD_DEFAULT),
                ':role' => $userData['role'],
                ':first_name' => $userData['first_name'],
                ':last_name' => $userData['last_name'],
                ':phone' => $userData['phone']
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Create role-specific profile
            if ($userData['role'] === 'doctor') {
                $query = "INSERT INTO doctor_profiles (user_id, department_id, specialization, bio) 
                          VALUES (:user_id, :department_id, :specialization, :bio)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':department_id' => $userData['department_id'] ?? 1,
                    ':specialization' => $userData['specialization'] ?? '',
                    ':bio' => $userData['bio'] ?? ''
                ]);
            } elseif ($userData['role'] === 'patient') {
                $query = "INSERT INTO patient_profiles (user_id, date_of_birth, gender, address) 
                          VALUES (:user_id, :date_of_birth, :gender, :address)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':date_of_birth' => $userData['date_of_birth'] ?? null,
                    ':gender' => $userData['gender'] ?? null,
                    ':address' => $userData['address'] ?? ''
                ]);
            }
            
            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function updateUser($userId, $userData) {
        $query = "UPDATE users SET 
                  first_name = :first_name, 
                  last_name = :last_name, 
                  email = :email, 
                  phone = :phone 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':first_name' => $userData['first_name'],
            ':last_name' => $userData['last_name'],
            ':email' => $userData['email'],
            ':phone' => $userData['phone'],
            ':id' => $userId
        ]);
    }
    
    public function deleteUser($userId) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([':id' => $userId]);
    }
    
    public function getUserById($userId) {
        $query = "SELECT u.*, 
                         dp.specialization, dp.bio, d.name as department_name, dp.department_id,
                         pp.date_of_birth, pp.gender, pp.address
                  FROM users u
                  LEFT JOIN doctor_profiles dp ON u.id = dp.user_id
                  LEFT JOIN departments d ON dp.department_id = d.id
                  LEFT JOIN patient_profiles pp ON u.id = pp.user_id
                  WHERE u.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
