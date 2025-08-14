<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('patient');

$database = new Database();
$db = $database->getConnection();

$success = '';
$error = '';

// Get all doctors
$query = "SELECT u.id, u.first_name, u.last_name, dp.specialization, d.name as department_name
          FROM users u 
          JOIN doctor_profiles dp ON u.id = dp.user_id
          JOIN departments d ON dp.department_id = d.id
          WHERE u.role = 'doctor'";
$stmt = $db->prepare($query);
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctor_id = sanitizeInput($_POST['doctor_id']);
    $appointment_date = sanitizeInput($_POST['appointment_date']);
    $appointment_time = sanitizeInput($_POST['appointment_time']);
    $notes = sanitizeInput($_POST['notes']);
    
    if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = 'Please fill in all required fields';
    } else {
        // Check if appointment slot is available
        $query = "SELECT * FROM appointments 
                  WHERE doctor_id = :doctor_id 
                  AND appointment_date = :appointment_date 
                  AND appointment_time = :appointment_time
                  AND status != 'cancelled'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':doctor_id', $doctor_id);
        $stmt->bindParam(':appointment_date', $appointment_date);
        $stmt->bindParam(':appointment_time', $appointment_time);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = 'This time slot is already booked. Please choose another time.';
        } else {
            // Book the appointment
            $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, notes, status) 
                      VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :notes, 'pending')";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':patient_id', $_SESSION['user_id']);
            $stmt->bindParam(':doctor_id', $doctor_id);
            $stmt->bindParam(':appointment_date', $appointment_date);
            $stmt->bindParam(':appointment_time', $appointment_time);
            $stmt->bindParam(':notes', $notes);
            
            if ($stmt->execute()) {
                $success = 'Appointment booked successfully! You will receive a confirmation soon.';
            } else {
                $error = 'Failed to book appointment. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Medicare System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <i class="fas fa-calendar-plus text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">Book Appointment</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200 transition flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                    <a href="../includes/logout.php" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Schedule Your Appointment</h2>
            <p class="text-gray-600">Choose your preferred doctor and time slot</p>
        </div>

        <div class="bg-white rounded-2xl card-shadow overflow-hidden">
            <!-- Alert Messages -->
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 m-6 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                        <div>
                            <p class="text-red-800 font-medium"><?php echo $error; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-4 m-6 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                        <div>
                            <p class="text-green-800 font-medium"><?php echo $success; ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="p-8">
                <form method="POST" class="space-y-6">
                    <!-- Doctor Selection -->
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <label class="flex items-center text-gray-700 text-sm font-semibold mb-3">
                            <i class="fas fa-user-md text-blue-500 mr-2"></i>
                            Select Doctor
                        </label>
                        <select name="doctor_id" required 
                                class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 bg-white">
                            <option value="">Choose your preferred doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo $doctor['id']; ?>">
                                    Dr. <?php echo $doctor['first_name'] . ' ' . $doctor['last_name']; ?> - 
                                    <?php echo $doctor['specialization']; ?> (<?php echo $doctor['department_name']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date and Time Selection -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-xl">
                            <label class="flex items-center text-gray-700 text-sm font-semibold mb-3">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                Appointment Date
                            </label>
                            <input type="date" name="appointment_date" required 
                                   min="<?php echo date('Y-m-d'); ?>"
                                   class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 bg-white">
                        </div>

                        <div class="bg-gray-50 p-6 rounded-xl">
                            <label class="flex items-center text-gray-700 text-sm font-semibold mb-3">
                                <i class="fas fa-clock text-blue-500 mr-2"></i>
                                Appointment Time
                            </label>
                            <select name="appointment_time" required 
                                    class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 bg-white">
                                <option value="">Select preferred time</option>
                                <optgroup label="Morning">
                                    <option value="09:00:00">9:00 AM</option>
                                    <option value="09:30:00">9:30 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="10:30:00">10:30 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="11:30:00">11:30 AM</option>
                                </optgroup>
                                <optgroup label="Afternoon">
                                    <option value="14:00:00">2:00 PM</option>
                                    <option value="14:30:00">2:30 PM</option>
                                    <option value="15:00:00">3:00 PM</option>
                                    <option value="15:30:00">3:30 PM</option>
                                    <option value="16:00:00">4:00 PM</option>
                                    <option value="16:30:00">4:30 PM</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <label class="flex items-center text-gray-700 text-sm font-semibold mb-3">
                            <i class="fas fa-notes-medical text-blue-500 mr-2"></i>
                            Additional Notes <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <textarea name="notes" rows="4" 
                                  class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 bg-white resize-none"
                                  placeholder="Please describe your symptoms, reason for visit, or any specific concerns..."></textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button type="submit" 
                                class="btn-primary text-white px-8 py-4 rounded-xl font-semibold flex items-center justify-center flex-1">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Book Appointment
                        </button>
                        <a href="dashboard.php" 
                           class="bg-gray-100 text-gray-700 px-8 py-4 rounded-xl font-semibold hover:bg-gray-200 transition flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-8 bg-blue-50 rounded-2xl p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-blue-800 mb-2">Important Information</h3>
                    <ul class="text-blue-700 space-y-1 text-sm">
                        <li>• Please arrive 15 minutes before your scheduled appointment</li>
                        <li>• Bring your insurance card and a valid ID</li>
                        <li>• You will receive a confirmation email once your appointment is approved</li>
                        <li>• For urgent medical issues, please visit the emergency department</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>