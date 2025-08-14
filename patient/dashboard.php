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
    <style>
        .medical-bg {
            background-image: 
                linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="medical" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="1" fill="%23dcfce7" opacity="0.3"/><path d="M8 10h4M10 8v4" stroke="%23dcfce7" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23medical)"/></svg>');
            background-size: 40px 40px, cover;
        }
        
        .hero-pattern {
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(34, 197, 94, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(168, 85, 247, 0.3) 0%, transparent 50%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .health-bg {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23f0fdf4" opacity="0.1"><path d="M50 20c-8 0-15 7-15 15v10c0 8 7 15 15 15s15-7 15-15V35c0-8-7-15-15-15z"/><circle cx="50" cy="70" r="10"/><path d="M35 65h30v10H35z"/></svg>');
            background-repeat: no-repeat;
            background-position: bottom right;
            background-size: 200px;
        }

        .doctor-pattern {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23eff6ff" opacity="0.1"><circle cx="50" cy="30" r="12"/><path d="M30 50c0-10 9-18 20-18s20 8 20 18v20H30V50z"/><path d="M40 25h20v5H40zM42 30h16v3H42z"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 150px;
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .heartbeat {
            animation: heartbeat 2s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 50%, 100% { transform: scale(1); }
            25%, 75% { transform: scale(1.1); }
        }
    </style>
</head>
<body class="medical-bg min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-sm shadow-lg border-b border-green-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-green-500 to-blue-600 p-2 rounded-xl">
                        <i class="fas fa-user text-white text-2xl heartbeat"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">User Dashboard</h1>
                        <p class="text-sm text-gray-500">Beyond Trust Management System</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-green-50 px-3 py-2 rounded-full">
                        <div class="w-2 h-2 bg-green-500 rounded-full pulse-animation"></div>
                        <span class="text-gray-700 font-medium"><?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition-all transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-blue-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Appointments</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"><?php echo $stats['total_appointments']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full"></div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-green-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent"><?php echo $stats['completed']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full"></div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-yellow-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent"><?php echo $stats['pending']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full"></div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-purple-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-purple-400 to-purple-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Upcoming</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent"><?php echo $stats['upcoming']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full"></div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Appointments -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-green-100 overflow-hidden">
                <div class="hero-pattern p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-green-500 to-blue-600 p-3 rounded-xl">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">Recent Appointments</h3>
                            <p class="text-gray-600 font-medium">Your history</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 health-bg">
                    <?php if (empty($recent_appointments)): ?>
                        <div class="text-center py-12">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-600 text-lg font-medium mb-4">No appointments yet</p>
                            <p class="text-gray-500 text-sm mb-4">Start your journey today!</p>
                            <a href="book-appointment.php" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-full hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">
                                <i class="fas fa-calendar-plus mr-2"></i>Book Your First Appointment
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_appointments as $appointment): ?>
                                <div class="flex items-center justify-between p-5 bg-gradient-to-r from-white to-green-50 border border-green-100 rounded-xl shadow-sm hover:shadow-md transition-all card-hover">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gradient-to-br from-green-400 to-green-600 p-3 rounded-xl shadow-md">
                                            <i class="fas fa-user-md text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-lg">Dr. <?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-stethoscope mr-2"></i>
                                                <?php echo $appointment['specialization']; ?>
                                            </p>
                                            <p class="text-sm text-gray-500 flex items-center mt-1">
                                                <i class="fas fa-building mr-2"></i>
                                                <?php echo $appointment['department_name']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-gray-800"><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            <?php 
                                            switch($appointment['status']) {
                                                case 'confirmed': echo 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300'; break;
                                                case 'completed': echo 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300'; break;
                                                case 'cancelled': echo 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300'; break;
                                                default: echo 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300';
                                            }
                                            ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Trainers -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="hero-pattern p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Available Trainers</h3>
                            <p class="text-gray-600 font-medium">Find your healthcare provider</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 doctor-pattern">
                    <div class="grid grid-cols-1 gap-4">
                        <?php foreach ($trainers as $trainer): ?>
                            <div class="flex items-center justify-between p-5 bg-gradient-to-r from-white to-blue-50 border border-blue-100 rounded-xl shadow-sm hover:shadow-md transition-all card-hover">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-3 rounded-xl shadow-md">
                                        <i class="fas fa-user-md text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-lg"> <?php echo $trainer['first_name'] . ' ' . $trainer['last_name']; ?></p>
                                        <p class="text-sm text-gray-600 flex items-center">
                                            <i class="fas fa-stethoscope mr-2"></i>
                                            <?php echo $trainer['specialization']; ?>
                                        </p>
                                        <p class="text-sm text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-building mr-2"></i>
                                            <?php echo $trainer['department_name']; ?>
                                        </p>
                                    </div>
                                </div>
                                <a href="book-appointment.php?doctor_id=<?php echo $traine['id']; ?>" 
                                   class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-full text-sm hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-calendar-plus mr-1"></i>Book
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-green-100 overflow-hidden">
            <div class="hero-pattern p-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-teal-500 to-green-600 p-3 rounded-xl">
                        <i class="fas fa-bolt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-teal-600 to-green-600 bg-clip-text text-transparent">Quick Actions</h3>
                        <p class="text-gray-600 font-medium">Manage your healthcare</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-4 gap-4">
                    <a href="book-appointment.php" class="group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl text-center hover:from-blue-100 hover:to-blue-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-blue-200">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-calendar-plus text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">Book Appointment</p>
                        <p class="text-xs text-gray-600 mt-1">schedule your visit</p>
                    </a>
                    <a href="appointment.php" class="group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl text-center hover:from-green-100 hover:to-green-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-green-200">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-history text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">Appointment History</p>
                        <p class="text-xs text-gray-600 mt-1">view past visits</p>
                    </a>
                    <a href="doctors.php" class="group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl text-center hover:from-purple-100 hover:to-purple-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-purple-200">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                    
                        <p class="font-bold text-gray-800">Find Trainers</p>
                        <p class="text-xs text-gray-600 mt-1">browse specialists</p>
                    </a>
                    <a href="profile.php" class="group bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl text-center hover:from-orange-100 hover:to-orange-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-orange-200">
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-cog text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">Profile Settings</p>
                        <p class="text-xs text-gray-600 mt-1">update your info</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Health Animation -->
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="bg-gradient-to-r from-green-500 to-teal-600 w-16 h-16 rounded-full flex items-center justify-center shadow-lg animate-bounce">
            <i class="fas fa-heart text-white text-2xl heartbeat"></i>
        </div>
    </div>
</body>
</html>