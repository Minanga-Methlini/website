<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

// Check if user is logged in and is doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'trainer') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

$message = '';
$error = '';

// Check if required tables exist and create them if they don't
try {
    // Check if doctor_schedule table exists
    $check_table = $db->query("SHOW TABLES LIKE 'trainer_schedule'");
    if ($check_table->rowCount() == 0) {
        // Create doctor_schedule table
        $create_schedule_table = "
            CREATE TABLE `trainer_schedule` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `trainer_id` int(11) NOT NULL,
                `day_of_week` varchar(20) NOT NULL,
                `start_time` time DEFAULT NULL,
                `end_time` time DEFAULT NULL,
                `is_available` tinyint(1) DEFAULT 1,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_doctor_day` (`doctor_id`, `day_of_week`),
                KEY `idx_trainer_id` (`trainer_id`),
                KEY `idx_day_of_week` (`day_of_week`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        $db->exec($create_schedule_table);
    }

    // Check if doctor_breaks table exists
    $check_breaks = $db->query("SHOW TABLES LIKE 'trainer_breaks'");
    if ($check_breaks->rowCount() == 0) {
        // Create doctor_breaks table
        $create_breaks_table = "
            CREATE TABLE `trainer_breaks` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `trainer_id` int(11) NOT NULL,
                `break_date` date NOT NULL,
                `start_time` time NOT NULL,
                `end_time` time NOT NULL,
                `reason` varchar(255) DEFAULT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_trainer_id` (`trainer_id`),
                KEY `idx_break_date` (`break_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        $db->exec($create_breaks_table);
    }

    // Check if appointments table exists
    $check_appointments = $db->query("SHOW TABLES LIKE 'appointments'");
    if ($check_appointments->rowCount() == 0) {
        // Create appointments table
        $create_appointments_table = "
            CREATE TABLE `appointments` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `trainer_id` int(11) NOT NULL,
                `appointment_date` date NOT NULL,
                `appointment_time` time NOT NULL,
                `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
                `notes` text DEFAULT NULL,
                `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_user_id` (`user_id`),
                KEY `idx_trainer_id` (`trainer_id`),
                KEY `idx_appointment_date` (`appointment_date`),
                KEY `idx_status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        $db->exec($create_appointments_table);
    }

} catch (PDOException $e) {
    $error = "Database setup error: " . $e->getMessage();
}

// Handle schedule updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['update_schedule'])) {
            // Update weekly schedule
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            foreach ($days as $day) {
                $start_time = $_POST[$day . '_start'] ?? null;
                $end_time = $_POST[$day . '_end'] ?? null;
                $is_available = isset($_POST[$day . '_available']) ? 1 : 0;
                
                // Check if schedule exists for this day
                $check_query = "SELECT id FROM trainer_schedule WHERE trainer_id = :trainer_id AND day_of_week = :day";
                $check_stmt = $db->prepare($check_query);
                $check_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
                $check_stmt->bindParam(':day', $day);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    // Update existing schedule
                    $update_query = "UPDATE trainer_schedule SET start_time = :start_time, end_time = :end_time, is_available = :is_available WHERE trainer_id = :trainer_id AND day_of_week = :day";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(':start_time', $start_time);
                    $update_stmt->bindParam(':end_time', $end_time);
                    $update_stmt->bindParam(':is_available', $is_available);
                    $update_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
                    $update_stmt->bindParam(':day', $day);
                    $update_stmt->execute();
                } else {
                    // Insert new schedule
                    $insert_query = "INSERT INTO trainer_schedule (trainer_id, day_of_week, start_time, end_time, is_available) VALUES (:trainer_id, :day, :start_time, :end_time, :is_available)";
                    $insert_stmt = $db->prepare($insert_query);
                    $insert_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
                    $insert_stmt->bindParam(':day', $day);
                    $insert_stmt->bindParam(':start_time', $start_time);
                    $insert_stmt->bindParam(':end_time', $end_time);
                    $insert_stmt->bindParam(':is_available', $is_available);
                    $insert_stmt->execute();
                }
            }
            
            $message = 'Schedule updated successfully!';
        }
        
        if (isset($_POST['add_break'])) {
            $break_date = $_POST['break_date'];
            $break_start = $_POST['break_start'];
            $break_end = $_POST['break_end'];
            $break_reason = $_POST['break_reason'];
            
            $insert_break_query = "INSERT INTO trainer_breaks (trainer_id, break_date, start_time, end_time, reason) VALUES (:trainer_id, :break_date, :start_time, :end_time, :reason)";
            $insert_break_stmt = $db->prepare($insert_break_query);
            $insert_break_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
            $insert_break_stmt->bindParam(':break_date', $break_date);
            $insert_break_stmt->bindParam(':start_time', $break_start);
            $insert_break_stmt->bindParam(':end_time', $break_end);
            $insert_break_stmt->bindParam(':reason', $break_reason);
            
            if ($insert_break_stmt->execute()) {
                $message = 'Break/unavailable time added successfully!';
            } else {
                $error = 'Failed to add break time.';
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Get current schedule
$current_schedule = [];
$schedule_data = [];
try {
    $schedule_query = "SELECT * FROM trainer_schedule WHERE trainer_id = :trainer_id";
    $schedule_stmt = $db->prepare($schedule_query);
    $schedule_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
    $schedule_stmt->execute();
    $current_schedule = $schedule_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert to associative array for easier access
    foreach ($current_schedule as $schedule) {
        $schedule_data[$schedule['day_of_week']] = $schedule;
    }
} catch (PDOException $e) {
    $error = 'Error fetching schedule: ' . $e->getMessage();
}

// Get upcoming breaks
$upcoming_breaks = [];
try {
    $breaks_query = "SELECT * FROM trainer_breaks WHERE trainer_id = :trainer_id AND break_date >= CURDATE() ORDER BY break_date, start_time";
    $breaks_stmt = $db->prepare($breaks_query);
    $breaks_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
    $breaks_stmt->execute();
    $upcoming_breaks = $breaks_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If table doesn't exist, just set empty array
    $upcoming_breaks = [];
}

// Get today's appointments for quick overview
$today_appointments = [];
try {
    $today_query = "
        SELECT a.*, CONCAT(p.first_name, ' ', p.last_name) as user_name
        FROM appointments a 
        JOIN users p ON a.user_id = p.id 
        WHERE a.trainer_id = :trainer_id AND DATE(a.appointment_date) = CURDATE()
        ORDER BY a.appointment_time
    ";
    $today_stmt = $db->prepare($today_query);
    $today_stmt->bindParam(':trainer_id', $_SESSION['user_id']);
    $today_stmt->execute();
    $today_appointments = $today_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If table doesn't exist, just set empty array
    $today_appointments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - trainer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .doctor-bg {
            background: linear-gradient(135deg, 
                rgba(59, 130, 246, 0.1) 0%, 
                rgba(16, 185, 129, 0.1) 50%, 
                rgba(139, 92, 246, 0.1) 100%);
            min-height: 100vh;
        }

        .glass-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .schedule-day {
            transition: all 0.3s ease;
            border-radius: 1rem;
        }

        .schedule-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .schedule-day.available {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
            border: 2px solid #10b981;
        }

        .schedule-day.unavailable {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 2px solid #ef4444;
        }

        .time-slot {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transition: all 0.3s ease;
        }

        .time-slot:hover {
            background: rgba(59, 130, 246, 0.2);
            border-color: #3b82f6;
        }

        .appointment-slot {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.8), rgba(16, 185, 129, 0.9));
            color: white;
            border: none;
        }

        .break-slot {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.8), rgba(239, 68, 68, 0.9));
            color: white;
            border: none;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
            background: #ccc;
            border-radius: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch.active {
            background: #10b981;
        }

        .toggle-switch::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .toggle-switch.active::before {
            transform: translateX(30px);
        }
    </style>
</head>
<body class="doctor-bg">
    <div class="min-h-screen p-8">
        <!-- Header -->
        <div class="glass-card rounded-2xl p-6 mb-8 shadow-lg">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 mr-4">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">
                            Manage Schedule
                        </h1>
                        <p class="text-gray-600">Set your availability and manage your time</p>
                    </div>
                </div>
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold">
                    <i class="fas fa-clock mr-1"></i>
                    Today: <?php echo count($today_appointments); ?> appointments
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div class="glass-card rounded-xl p-4 mb-6 bg-green-50 border border-green-200 text-green-700">
                <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="glass-card rounded-xl p-4 mb-6 bg-red-50 border border-red-200 text-red-700">
                <i class="fas fa-exclamation-triangle mr-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-12 gap-8">
            <!-- Schedule Settings -->
            <div class="col-span-12 lg:col-span-8">
                <div class="glass-card rounded-2xl p-6 shadow-lg mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-calendar-week mr-3 text-blue-500"></i>
                        Weekly Schedule
                    </h3>

                    <form method="POST" class="space-y-4">
                        <?php
                        $days_full = [
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                            'sunday' => 'Sunday'
                        ];

                        foreach ($days_full as $day => $day_name):
                            $schedule = $schedule_data[$day] ?? null;
                            $is_available = $schedule ? $schedule['is_available'] : 0;
                            $start_time = $schedule ? $schedule['start_time'] : '09:00';
                            $end_time = $schedule ? $schedule['end_time'] : '17:00';
                        ?>
                            <div class="schedule-day p-4 <?php echo $is_available ? 'available' : 'unavailable'; ?>">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800"><?php echo $day_name; ?></h4>
                                    <div class="flex items-center">
                                        <span class="mr-2 text-sm text-gray-600">Available</span>
                                        <label class="toggle-switch <?php echo $is_available ? 'active' : ''; ?>">
                                            <input type="checkbox" name="<?php echo $day; ?>_available" 
                                                   <?php echo $is_available ? 'checked' : ''; ?> 
                                                   onchange="toggleDayAvailability(this, '<?php echo $day; ?>')"
                                                   style="display: none;">
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="<?php echo $day; ?>_times" class="<?php echo !$is_available ? 'hidden' : ''; ?>">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                            <input type="time" name="<?php echo $day; ?>_start" 
                                                   value="<?php echo $start_time; ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                            <input type="time" name="<?php echo $day; ?>_end" 
                                                   value="<?php echo $end_time; ?>"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="pt-4">
                            <button type="submit" name="update_schedule" 
                                    class="bg-gradient-to-r from-blue-500 to-green-500 text-white px-6 py-3 rounded-lg hover:from-blue-600 hover:to-green-600 transition duration-300 font-semibold">
                                <i class="fas fa-save mr-2"></i>Update Schedule
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Add Break/Unavailable Time -->
                <div class="glass-card rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-pause-circle mr-3 text-red-500"></i>
                        Add Break / Unavailable Time
                    </h3>

                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="break_date" required
                                   min="<?php echo date('Y-m-d'); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                            <input type="text" name="break_reason" placeholder="e.g., Lunch break, Personal time"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="break_start" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                            <input type="time" name="break_end" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" name="add_break" 
                                    class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-6 py-3 rounded-lg hover:from-red-600 hover:to-orange-600 transition duration-300 font-semibold">
                                <i class="fas fa-plus mr-2"></i>Add Break Time
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 lg:col-span-4">
                <!-- Today's Appointments -->
                <div class="glass-card rounded-2xl p-6 shadow-lg mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calendar-day mr-3 text-green-500"></i>
                        Today's Schedule
                    </h3>

                    <?php if (empty($today_appointments)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-calendar-check text-4xl mb-3"></i>
                            <p>No appointments today</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($today_appointments as $appointment): ?>
                                <div class="appointment-slot p-3 rounded-lg flex items-center justify-between">
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($appointment['user_name']); ?></div>
                                        <div class="text-sm opacity-90"><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm opacity-90"><?php echo ucfirst($appointment['status']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upcoming Breaks -->
                <div class="glass-card rounded-2xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-pause-circle mr-3 text-red-500"></i>
                        Upcoming Breaks
                    </h3>

                    <?php if (empty($upcoming_breaks)): ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clock text-4xl mb-3"></i>
                            <p>No breaks scheduled</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($upcoming_breaks as $break): ?>
                                <div class="break-slot p-3 rounded-lg">
                                    <div class="font-semibold"><?php echo htmlspecialchars($break['reason']); ?></div>
                                    <div class="text-sm opacity-90">
                                        <?php echo date('M d, Y', strtotime($break['break_date'])); ?>
                                    </div>
                                    <div class="text-sm opacity-90">
                                        <?php echo date('h:i A', strtotime($break['start_time'])); ?> - 
                                        <?php echo date('h:i A', strtotime($break['end_time'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDayAvailability(checkbox, day) {
            const timesDiv = document.getElementById(day + '_times');
            const scheduleDay = checkbox.closest('.schedule-day');
            const toggleSwitch = checkbox.closest('.toggle-switch');
            
            if (checkbox.checked) {
                timesDiv.classList.remove('hidden');
                scheduleDay.classList.remove('unavailable');
                scheduleDay.classList.add('available');
                toggleSwitch.classList.add('active');
            } else {
                timesDiv.classList.add('hidden');
                scheduleDay.classList.remove('available');
                scheduleDay.classList.add('unavailable');
                toggleSwitch.classList.remove('active');
            }
        }

        // Initialize toggle switches
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSwitches = document.querySelectorAll('.toggle