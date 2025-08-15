<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('user');

$database = new Database();
$db = $database->getConnection();

// Get patient's appointments
$query = "SELECT a.*, u.first_name, u.last_name, dp.specialization, d.name as department_name
          FROM appointments a 
          JOIN users u ON a.trainer_id = u.id 
          JOIN trainer_profiles dp ON u.id = dp.user_id
          JOIN departments d ON dp.department_id = d.id
          WHERE a.user_id = :user_id 
          ORDER BY a.appointment_date DESC, a.appointment_time DESC
          LIMIT 5";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get appointment statistics
$query = "SELECT 
            COUNT(*) as total_appointments,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN appointment_date >= CURDATE() THEN 1 END) as upcoming
          FROM appointments WHERE user_id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get available trainers
$query = "SELECT u.id, u.first_name, u.last_name, dp.specialization, d.name as department_name
          FROM users u 
          JOIN trainer_profiles dp ON u.id = dp.user_id
          JOIN departments d ON dp.department_id = d.id
          WHERE u.role = 'trainer' 
          LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Beyond Trust</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        .stat-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .appointment-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        
        .completed {
            border-left-color: #10b981;
        }
        
        .confirmed {
            border-left-color: #3b82f6;
        }
        
        .pending {
            border-left-color: #f59e0b;
        }
        
        .cancelled {
            border-left-color: #ef4444;
        }
        
        .quick-action-card {
            transition: all 0.3s ease;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .floating-heart {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        .heartbeat {
            animation: heartbeat 1.5s ease-in-out infinite;
        }
        
        @keyframes heartbeat {
            0%, 50%, 100% { transform: scale(1); }
            25%, 75% { transform: scale(1.1); }
        }
    </style>
</head>
<body class="min-h-screen py-8 relative p-6" 
      style="background: linear-gradient(135deg, #ec6d2dff 0%, #cc12f1ff 50%, #09f1d3ff 100%); 
             background-size: cover;
             background-position: center;">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-2 rounded-lg">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 font-['Montserrat']">Beyond Trust</h1>
                        <p class="text-xs text-gray-500">User Dashboard</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-indigo-50 px-4 py-2 rounded-full">
                        <div class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></div>
                        <span class="text-gray-700 font-medium"><?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition-all shadow-sm">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Banner -->
        <div class="dashboard-header text-white rounded-xl shadow-lg mb-8 p-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-purple-600/20"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-bold mb-2 font-['Montserrat']">Welcome back, <?php echo $_SESSION['first_name']; ?>!</h2>
                <p class="text-indigo-100 max-w-2xl">Manage your fitness journey, track appointments, and connect with your trainers.</p>
                <div class="mt-6 flex gap-4">
                    <a href="book-appointment.php" class="bg-white text-indigo-600 px-6 py-2 rounded-full font-medium hover:bg-indigo-50 transition-colors">
                        <i class="fas fa-calendar-plus mr-2"></i>Book Appointment
                    </a>
                    <a href="profile.php" class="bg-indigo-700 text-white px-6 py-2 rounded-full font-medium hover:bg-indigo-800 transition-colors">
                        <i class="fas fa-user-cog mr-2"></i>Profile Settings
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Appointments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_appointments']; ?></p>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed']; ?></p>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Pending</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending']; ?></p>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Upcoming</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['upcoming']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Appointments -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 font-['Montserrat'] flex items-center gap-3">
                            <div class="bg-indigo-600 p-2 rounded-lg">
                                <i class="fas fa-calendar-day text-white text-lg"></i>
                            </div>
                            Recent Appointments
                        </h3>
                        <a href="appointments.php" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($recent_appointments)): ?>
                        <div class="text-center py-12">
                            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium mb-2">No appointments yet</p>
                            <p class="text-gray-500 text-sm mb-4">Get started by booking your first session</p>
                            <a href="book-appointment.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-full inline-block hover:from-indigo-700 hover:to-purple-700 transition-colors">
                                <i class="fas fa-calendar-plus mr-2"></i>Book Now
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_appointments as $appointment): ?>
                                <div class="appointment-card bg-white border border-gray-100 rounded-lg p-5 <?php echo $appointment['status']; ?>">
                                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <div class="bg-indigo-100 p-3 rounded-lg">
                                                <i class="fas fa-user-md text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900"><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></h4>
                                                <p class="text-indigo-600 text-sm font-medium"><?php echo $appointment['specialization']; ?></p>
                                                <p class="text-gray-500 text-xs mt-1 flex items-center">
                                                    <i class="fas fa-building mr-1"></i>
                                                    <?php echo $appointment['department_name']; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900"><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></p>
                                            <p class="text-gray-600 text-sm"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                            <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full 
                                                <?php 
                                                switch($appointment['status']) {
                                                    case 'completed': echo 'bg-green-100 text-green-800'; break;
                                                    case 'confirmed': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                                    default: echo 'bg-yellow-100 text-yellow-800';
                                                }
                                                ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Trainers -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 font-['Montserrat'] flex items-center gap-3">
                            <div class="bg-indigo-600 p-2 rounded-lg">
                                <i class="fas fa-user-md text-white text-lg"></i>
                            </div>
                            Available Trainers
                        </h3>
                        <a href="trainers.php" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4">
                        <?php foreach ($trainers as $trainer): ?>
                            <div class="appointment-card bg-white border border-gray-100 rounded-lg p-5">
                                <div class="flex flex-col sm:flex-row justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <div class="bg-indigo-100 p-3 rounded-lg">
                                            <i class="fas fa-user-md text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900"><?php echo $trainer['first_name'] . ' ' . $trainer['last_name']; ?></h4>
                                            <p class="text-indigo-600 text-sm font-medium"><?php echo $trainer['specialization']; ?></p>
                                            <p class="text-gray-500 text-xs mt-1 flex items-center">
                                                <i class="fas fa-building mr-1"></i>
                                                <?php echo $trainer['department_name']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <a href="book-appointment.php?trainer_id=<?php echo $trainer['id']; ?>" 
                                           class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-full text-sm hover:from-indigo-700 hover:to-purple-700 transition-colors">
                                            <i class="fas fa-calendar-plus mr-1"></i>Book
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden position-relative">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 font-['Montserrat'] flex items-center gap-3">
                    <div class="bg-indigo-600 p-2 rounded-lg">
                        <i class="fas fa-bolt text-white text-lg"></i>
                    </div>
                    Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="book-appointment.php" class="quick-action-card bg-white border border-gray-100 rounded-lg p-6 text-center hover:shadow-md">
                        <div class="bg-indigo-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-plus text-indigo-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Book Appointment</h4>
                        <p class="text-gray-500 text-sm">Schedule a new session</p>
                    </a>
                    <a href="appointments.php" class="quick-action-card bg-white border border-gray-100 rounded-lg p-6 text-center hover:shadow-md">
                        <div class="bg-indigo-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-indigo-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Appointments</h4>
                        <p class="text-gray-500 text-sm">View your schedule</p>
                    </a>
                    <a href="profile.php" class="quick-action-card bg-white border border-gray-100 rounded-lg p-6 text-center hover:shadow-md">
                        <div class="bg-indigo-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-cog text-indigo-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Profile</h4>
                        <p class="text-gray-500 text-sm">Update your information</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Health Indicator -->
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 w-14 h-14 rounded-full flex items-center justify-center shadow-lg floating-heart">
            <i class="fas fa-heart text-white text-xl heartbeat"></i>
        </div>
    </div>
</body>
</html>