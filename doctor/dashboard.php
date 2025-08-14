<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('trainer');

$database = new Database();
$db = $database->getConnection();

// Get today's appointments
$today = date('Y-m-d');
$query = "SELECT a.*, u.first_name, u.last_name, u.phone 
          FROM appointments a 
          JOIN users u ON a.user_id = u.id 
          WHERE a.trainer_id = :trainer_id AND a.appointment_date = :today 
          ORDER BY a.appointment_time";
$stmt = $db->prepare($query);
$stmt->bindParam(':trainer_id', $_SESSION['user_id']);
$stmt->bindParam(':today', $today);
$stmt->execute();
$today_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get appointment statistics
$query = "SELECT 
            COUNT(*) as total_appointments,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
            COUNT(CASE WHEN appointment_date >= CURDATE() THEN 1 END) as upcoming
          FROM appointments WHERE trainer_id = :trainer_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':trainer_id', $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>trainer Dashboard - Beyond Trust</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .medical-bg {
            background-image: 
                linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="medical" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="1" fill="%23dbeafe" opacity="0.3"/><path d="M8 10h4M10 8v4" stroke="%23dbeafe" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23medical)"/></svg>');
            background-size: 40px 40px, cover;
        }
        
        .hero-pattern {
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(120, 219, 226, 0.3) 0%, transparent 50%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .stethoscope-bg {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23f3f4f6" opacity="0.1"><path d="M30 20c0-5 5-10 10-10s10 5 10 10v20c0 5-5 10-10 10s-10-5-10-10V20zM60 20c0-5 5-10 10-10s10 5 10 10v20c0 5-5 10-10 10s-10-5-10-10V20z"/><path d="M40 50v10c0 10 10 20 20 20s20-10 20-20c0-5-5-10-10-10s-10 5-10 10"/></svg>');
            background-repeat: no-repeat;
            background-position: bottom right;
            background-size: 200px;
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="medical-bg min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-sm shadow-lg border-b border-blue-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-2 rounded-xl">
                        <i class="fas fa-user-md text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Trainer</h1>
                        <p class="text-sm text-gray-500">Beyond Trust</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-blue-50 px-3 py-2 rounded-full">
                        <div class="w-2 h-2 bg-green-500 rounded-full pulse-animation"></div>
                        <span class="text-gray-700 font-medium"> <?php echo $_SESSION['first_name']; ?></span>
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
            <!-- Today's Appointments -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="hero-pattern p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Today's Appointments</h3>
                            <p class="text-gray-600 font-medium"><?php echo date('F j, Y'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="p-6 stethoscope-bg">
                    <?php if (empty($today_appointments)): ?>
                        <div class="text-center py-12">
                            <div class="bg-gradient-to-br from-gray-100 to-gray-200 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                            </div>
                            <p class="text-gray-600 text-lg font-medium">No appointments scheduled for today</p>
                            <p class="text-gray-500 text-sm mt-2">Enjoy your free time!</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($today_appointments as $appointment): ?>
                                <div class="flex items-center justify-between p-5 bg-gradient-to-r from-white to-blue-50 border border-blue-100 rounded-xl shadow-sm hover:shadow-md transition-all card-hover">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-3 rounded-xl shadow-md">
                                            <i class="fas fa-user text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800 text-lg"><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-phone mr-1"></i>
                                                <?php echo $appointment['phone']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-gray-800"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $appointment['status'] === 'confirmed' ? 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300' : 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300'; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-purple-100 overflow-hidden">
                <div class="hero-pattern p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-3 rounded-xl">
                            <i class="fas fa-bolt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Quick Actions</h3>
                            <p class="text-gray-600 font-medium">Navigate your workspace</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <a href="appointments.php" class="group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl text-center hover:from-blue-100 hover:to-blue-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-blue-200">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-calendar text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">View All Appointments</p>
                            <p class="text-xs text-gray-600 mt-1">manage your schedule</p>
                        </a>
                        <a href="patients.php" class="group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl text-center hover:from-green-100 hover:to-green-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-green-200">
                            <div class="bg-gradient-to-br from-green-500 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">User Records</p>
                            <p class="text-xs text-gray-600 mt-1">access history</p>
                        </a>
                        <a href="schedule.php" class="group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl text-center hover:from-purple-100 hover:to-purple-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-purple-200">
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">Manage Schedule</p>
                            <p class="text-xs text-gray-600 mt-1">set availability</p>
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
    </div>

    <!-- Floating Medical Animation -->
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 w-16 h-16 rounded-full flex items-center justify-center shadow-lg animate-bounce">
            <i class="fas fa-heartbeat text-white text-2xl"></i>
        </div>
    </div>
</body>
</html>