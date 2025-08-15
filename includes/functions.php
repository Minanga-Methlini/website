<?php
require_once __DIR__ . '/../config/database.php';

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function getUserById($id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function gettrainerProfile($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT dp.*, d.name as department_name 
              FROM trainer_profiles dp 
              JOIN departments d ON dp.department_id = d.id 
              WHERE dp.user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getuserProfile($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM user_profiles WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllAppointments($trainer_id = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT a.*, 
                     p.first_name as user_first, p.last_name as user_last, p.phone as user_phone,
                     d.first_name as trainer_first, d.last_name as trainer_last,
                     dept.name as department_name
              FROM appointments a
              JOIN users p ON a._id = p.id
              JOIN users d ON a.trainer_id = d.id
              LEFT JOIN trainer_profiles dp ON d.id = dp.user_id
              LEFT JOIN departments dept ON dp.department_id = dept.id";
    
    if ($trainer_id) {
        $query .= " WHERE a.trainer_id = :trainer_id";
    }
    
    $query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    
    $stmt = $db->prepare($query);
    if ($trainer_id) {
        $stmt->bindParam(':trainer_id', $trainer_id);
    }
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserByTrainer($trainer_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT DISTINCT u.*, pp.date_of_birth, pp.gender, pp.address, pp.emergency_contact,
                     COUNT(a.id) as total_appointments,
                     MAX(a.appointment_date) as last_visit
              FROM users u
              JOIN appointments a ON u.id = a.user_id
              LEFT JOIN user_profiles pp ON u.id = pp.user_id
              WHERE a.trainer_id = :trainer_id AND u.role = 'user'
              GROUP BY u.id
              ORDER BY last_visit DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':trainer_id', $trainer_id);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateAppointmentStatus($appointment_id, $status) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "UPDATE appointments SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $appointment_id);
    
    return $stmt->execute();
}

function getAllUsers($role = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT u.*, 
                     CASE 
                         WHEN u.role = 'trainer' THEN dept.name
                         ELSE NULL 
                     END as department_name
              FROM users u
              LEFT JOIN trainer_profiles dp ON u.id = dp.user_id
              LEFT JOIN departments dept ON dp.department_id = dept.id";
    
    if ($role) {
        $query .= " WHERE u.role = :role";
    }
    
    $query .= " ORDER BY u.created_at DESC";
    
    $stmt = $db->prepare($query);
    if ($role) {
        $stmt->bindParam(':role', $role);
    }
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createUser($username, $email, $password, $role, $first_name, $last_name, $phone = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $hashed_password = hashPassword($password);
    
    $query = "INSERT INTO users (username, email, password, role, first_name, last_name, phone) 
              VALUES (:username, :email, :password, :role, :first_name, :last_name, :phone)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':phone', $phone);
    
    if ($stmt->execute()) {
        return $db->lastInsertId();
    }
    return false;
}

function createrTrainerProfile($user_id, $department_id, $specialization = null, $bio = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO trainer_profiles (user_id, department_id, specialization, bio) 
              VALUES (:user_id, :department_id, :specialization, :bio)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':department_id', $department_id);
    $stmt->bindParam(':specialization', $specialization);
    $stmt->bindParam(':bio', $bio);
    
    return $stmt->execute();
}

function createUserProfile($user_id, $date_of_birth = null, $gender = null, $address = null, $emergency_contact = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO user_profiles (user_id, date_of_birth, gender, address, emergency_contact) 
              VALUES (:user_id, :date_of_birth, :gender, :address, :emergency_contact)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':emergency_contact', $emergency_contact);
    
    return $stmt->execute();
}

function deleteUser($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $user_id);
    
    return $stmt->execute();
}

function getAllDepartments() {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT d.*, COUNT(dp.id) as trainer_count 
              FROM departments d 
              LEFT JOIN trainer_profiles dp ON d.id = dp.department_id 
              GROUP BY d.id 
              ORDER BY d.name";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createDepartment($name, $description = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO departments (name, description) VALUES (:name, :description)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    
    return $stmt->execute();
}

function updateDepartment($id, $name, $description = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "UPDATE departments SET name = :name, description = :description WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':id', $id);
    
    return $stmt->execute();
}

function deleteDepartment($id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "DELETE FROM departments WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    return $stmt->execute();
}

function addNote($appointment_id, $trainer_id, $diagnosis = null, $discription = null, $notes = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO notes (appointment_id, trainer_id, diagnosis, discription, notes) 
              VALUES (:appointment_id, :trainer_id, :diagnosis, :discription, :notes)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->bindParam(':trainer_id', $trainer_id);
    $stmt->bindParam(':diagnosis', $diagnosis);
    $stmt->bindParam(':discription', $discription);
    $stmt->bindParam(':notes', $notes);
    
    return $stmt->execute();
}

function getNotes($appointment_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT mn.*, u.first_name, u.last_name 
              FROM notes mn 
              JOIN users u ON mn.trainer_id = u.id 
              WHERE mn.appointment_id = :appointment_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':appointment_id', $appointment_id);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAvailableTrainer($department_id = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT u.*, dp.specialization, d.name as department_name 
              FROM users u 
              JOIN trainer_profiles dp ON u.id = dp.user_id 
              JOIN departments d ON dp.department_id = d.id 
              WHERE u.role = 'trainer'";
    
    if ($department_id) {
        $query .= " AND dp.department_id = :department_id";
    }
    
    $query .= " ORDER BY u.first_name, u.last_name";
    
    $stmt = $db->prepare($query);
    if ($department_id) {
        $stmt->bindParam(':department_id', $department_id);
    }
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function bookAppointment($user_id, $trainer_id, $appointment_date, $appointment_time, $notes = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO appointments (user_id, trainer_id, appointment_date, appointment_time, notes) 
              VALUES (:user_id, :trainer_id, :appointment_date, :appointment_time, :notes)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':trainer_id', $trainer_id);
    $stmt->bindParam(':appointment_date', $appointment_date);
    $stmt->bindParam(':appointment_time', $appointment_time);
    $stmt->bindParam(':notes', $notes);
    
    return $stmt->execute();
}
?>
