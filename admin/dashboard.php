<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('admin');

$database = new Database();
$db = $database->getConnection();

// Get system statistics
$stats = [];

// Total users by role
$query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$stmt = $db->prepare($query);
$stmt->execute();
$user_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($user_stats as $stat) {
    $stats[$stat['role']] = $stat['count'];
}

// Total appointments
$query = "SELECT COUNT(*) as total FROM appointments";
$stmt = $db->prepare($query);
$stmt->execute();
$stats['total_appointments'] = $stmt->fetchColumn();

// Appointments by status
$query = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
$stmt = $db->prepare($query);
$stmt->execute();
$appointment_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent appointments
$query = "SELECT a.*, 
                 p.first_name as user_first, p.last_name as user_last,
                 t.first_name as trainer_first, t.last_name as trainer_last
          FROM appointments a
          JOIN users p ON a.user_id = p.id
          JOIN users t ON a.trainer_id = t.id
          ORDER BY a.created_at DESC
          LIMIT 10";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent users
$query = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Beyond Trust</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .medical-bg {
            background-image: 
                linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="medical" patternUnits="userSpaceOnUse" width="20" height="20"><circle cx="10" cy="10" r="1" fill="%23e0e7ff" opacity="0.3"/><path d="M8 10h4M10 8v4" stroke="%23e0e7ff" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23medical)"/></svg>');
            background-size: 40px 40px, cover;
        }
        
        .hero-pattern {
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.3) 0%, transparent 50%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .admin-bg {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23f3f4f6" opacity="0.1"><path d="M30 20h40v10H30zM20 35h60v5H20zM25 45h50v5H25zM30 55h40v5H30z"/><circle cx="15" cy="22" r="3"/><circle cx="15" cy="37" r="3"/><circle cx="15" cy="47" r="3"/><circle cx="15" cy="57" r="3"/></svg>');
            background-repeat: no-repeat;
            background-position: bottom right;
            background-size: 200px;
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .admin-icon-bg {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23f8fafc" opacity="0.05"><path d="M50 10c15 0 25 10 25 25s-10 25-25 25-25-10-25-25 10-25 25-25zm0 10c-8 0-15 7-15 15s7 15 15 15 15-7 15-15-7-15-15-15z"/><path d="M20 70c0-15 13-25 30-25s30 10 30 25v10H20V70z"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 150px;
        }
    </style>
</head>
<body class="min-h-screen py-8 relative" 
      style="background: linear-gradient(135deg, #ec6d2dff 0%, #cc12f1ff 50%, #09f1d3ff 100%); 
             background-size: cover;
             background-position: center;">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-sm shadow-lg border-b border-purple-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-purple-500 to-blue-600 p-2 rounded-xl">
                        <i class="fas fa-cog text-white text-2xl animate-spin" style="animation-duration: 8s;"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Admin</h1>
                        <p class="text-sm text-gray-500">Beyond Trust</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-purple-50 px-3 py-2 rounded-full">
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
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"><?php echo $stats['user'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full"></div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-green-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-user-md text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Trainers</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent"><?php echo $stats['trainer'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full"></div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-purple-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-purple-400 to-purple-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Appointments</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-800 bg-clip-text text-transparent"><?php echo $stats['total_appointments']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full"></div>
            </div>

            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-lg border border-orange-100 card-hover">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-orange-400 to-orange-600 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-user-shield text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Admins</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-orange-800 bg-clip-text text-transparent"><?php echo $stats['admin'] ?? 0; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full"></div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Appointments -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="hero-pattern p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Recent Appointments</h3>
                            <p class="text-gray-600 font-medium">Latest system activity</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 admin-bg">
                    <div class="space-y-4">
                        <?php foreach ($recent_appointments as $appointment): ?>
                            <div class="flex items-center justify-between p-5 bg-gradient-to-r from-white to-blue-50 border border-blue-100 rounded-xl shadow-sm hover:shadow-md transition-all card-hover">
                                <div>
                                    <p class="font-bold text-gray-800 text-lg">
                                        <?php echo $appointment['user_first'] . ' ' . $appointment['user_last']; ?>
                                    </p>
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-user-md mr-2"></i>
                                        with  <?php echo $appointment['trainer_first'] . ' ' . $appointment['trainer_last']; ?>
                                    </p>
                                    <p class="text-sm text-gray-500 flex items-center mt-1">
                                        <i class="fas fa-clock mr-2"></i>
                                        <?php echo date('M j, Y g:i A', strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time'])); ?>
                                    </p>
                                </div>
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
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-purple-100 overflow-hidden">
                <div class="hero-pattern p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-3 rounded-xl">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Recent Users</h3>
                            <p class="text-gray-600 font-medium">Newest registrations</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 admin-icon-bg">
                    <div class="space-y-4">
                        <?php foreach ($recent_users as $user): ?>
                            <div class="flex items-center justify-between p-5 bg-gradient-to-r from-white to-purple-50 border border-purple-100 rounded-xl shadow-sm hover:shadow-md transition-all card-hover">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-gradient-to-br from-gray-400 to-gray-600 p-3 rounded-xl shadow-md">
                                        <i class="fas fa-user text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 text-lg"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></p>
                                        <p class="text-sm text-gray-600 flex items-center">
                                            <i class="fas fa-envelope mr-2"></i>
                                            <?php echo $user['email']; ?>
                                        </p>
                                        <p class="text-sm text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-calendar-plus mr-2"></i>
                                            Joined <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    <?php 
                                    switch($user['role']) {
                                        case 'admin': echo 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300'; break;
                                        case 'doctor': echo 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300'; break;
                                        case 'patient': echo 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300'; break;
                                    }
                                    ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Management -->
        <div class="mt-8 bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
            <div class="hero-pattern p-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-cogs text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">System Management</h3>
                        <p class="text-gray-600 font-medium">Administrative controls</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-4 gap-4">
                    <a href="users.php" class="group bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl text-center hover:from-blue-100 hover:to-blue-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-blue-200">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">Manage Users</p>
                        <p class="text-xs text-gray-600 mt-1">user administration</p>
                    </a>
                    <a href="appointments.php" class="group bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl text-center hover:from-green-100 hover:to-green-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-green-200">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">Manage Appointments</p>
                        <p class="text-xs text-gray-600 mt-1">scheduling oversight</p>
                    </a>
                    <a href="departments.php" class="group bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl text-center hover:from-purple-100 hover:to-purple-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-purple-200">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">Manage Departments</p>
                        <p class="text-xs text-gray-600 mt-1">department structure</p>
                    </a>
                    <a href="reports.php" class="group bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl text-center hover:from-orange-100 hover:to-orange-200 transition-all transform hover:scale-105 shadow-md hover:shadow-lg border border-orange-200">
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        <p class="font-bold text-gray-800">View Reports</p>
                        <p class="text-xs text-gray-600 mt-1">analytics & insights</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Admin Animation -->
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 w-16 h-16 rounded-full flex items-center justify-center shadow-lg animate-bounce">
            <i class="fas fa-shield-alt text-white text-2xl"></i>
        </div>
    </div>
</body>
</html>