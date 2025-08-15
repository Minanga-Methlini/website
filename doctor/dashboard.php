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
    <title>Trainer Dashboard - Wellness Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #6bd6e1;
            --primary-light: #a8e6e6;
            --primary-dark: #4a9ba8;
            --secondary: #ffb3ab;
            --accent: #ff8b94;
            --text: #2d3748;
            --text-light: #718096;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --success: #48bb78;
            --warning: #ed8936;
            --error: #f56565;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(107, 214, 225, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 179, 171, 0.1) 0%, transparent 20%);
        }

        .card-hover {
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            @apply px-3 py-1 rounded-full text-sm font-medium;
        }

        .status-pending {
            background: rgba(255, 179, 171, 0.2);
            color: #d46b6b;
            border: 1px solid rgba(255, 179, 171, 0.4);
        }

        .status-confirmed {
            background: rgba(107, 214, 225, 0.2);
            color: #2b6cb0;
            border: 1px solid rgba(107, 214, 225, 0.4);
        }

        .status-completed {
            background: rgba(72, 187, 120, 0.2);
            color: #22543d;
            border: 1px solid rgba(72, 187, 120, 0.4);
        }

        .status-cancelled {
            background: rgba(245, 101, 101, 0.2);
            color: #9b2c2c;
            border: 1px solid rgba(245, 101, 101, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            @apply text-white px-6 py-2 rounded-lg font-medium transition-all hover:shadow-lg;
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            @apply text-white px-6 py-2 rounded-lg font-medium transition-all hover:shadow-lg;
        }

        .nav-gradient {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
        }

        .avatar-initials {
            @apply w-10 h-10 rounded-full flex items-center justify-center text-white font-bold;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .wave-pattern {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%236bd6e1" fill-opacity="0.1" d="M0,256L48,261.3C96,267,192,277,288,245.3C384,213,480,139,576,128C672,117,768,171,864,197.3C960,224,1056,224,1152,208C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: 100% 50px;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Navigation -->
    <nav class="nav-gradient text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-white p-2 rounded-xl shadow-md">
                        <i class="fas fa-heartbeat text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Wellness Center</h1>
                        <p class="text-sm text-white/80">Trainer Dashboard</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 bg-white/20 px-4 py-2 rounded-full">
                        <div class="w-2 h-2 bg-green-300 rounded-full pulse"></div>
                        <span class="font-medium"><?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-full transition-all shadow-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-blue-50 card-hover">
                <div class="flex items-center">
                    <div class="bg-primary p-3 rounded-xl text-white">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Appointments</p>
                        <p class="text-3xl font-bold text-primary-dark"><?php echo $stats['total_appointments']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-primary to-primary-dark rounded-full"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-green-50 card-hover">
                <div class="flex items-center">
                    <div class="bg-green-400 p-3 rounded-xl text-white">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $stats['completed']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-green-400 to-green-600 rounded-full"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-50 card-hover">
                <div class="flex items-center">
                    <div class="bg-secondary p-3 rounded-xl text-white">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold text-accent"><?php echo $stats['pending']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-secondary to-accent rounded-full"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-purple-50 card-hover">
                <div class="flex items-center">
                    <div class="bg-purple-400 p-3 rounded-xl text-white">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Upcoming</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $stats['upcoming']; ?></p>
                    </div>
                </div>
                <div class="mt-4 h-1 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full"></div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Today's Appointments -->
            <div class="bg-white rounded-2xl shadow-sm border border-blue-50 overflow-hidden wave-pattern">
                <div class="bg-primary/10 p-6 border-b border-blue-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-primary p-3 rounded-xl text-white">
                            <i class="fas fa-calendar-day text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-primary-dark">Today's Appointments</h3>
                            <p class="text-gray-600 font-medium"><?php echo date('F j, Y'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($today_appointments)): ?>
                        <div class="text-center py-12">
                            <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-calendar-times text-primary text-3xl"></i>
                            </div>
                            <p class="text-gray-600 text-lg font-medium">No appointments today</p>
                            <p class="text-gray-500 text-sm mt-2">Enjoy your peaceful day!</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($today_appointments as $appointment): ?>
                                <div class="flex items-center justify-between p-5 bg-white border border-blue-100 rounded-xl shadow-sm hover:shadow-md transition-all card-hover">
                                    <div class="flex items-center space-x-4">
                                        <div class="avatar-initials">
                                            <?php echo strtoupper(substr($appointment['first_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800"><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-phone mr-1 text-primary"></i>
                                                <?php echo $appointment['phone']; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-gray-800"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                        <span class="status-badge 
                                            <?php 
                                            switch($appointment['status']) {
                                                case 'confirmed': echo 'status-confirmed'; break;
                                                case 'completed': echo 'status-completed'; break;
                                                case 'cancelled': echo 'status-cancelled'; break;
                                                default: echo 'status-pending';
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

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-purple-50 overflow-hidden wave-pattern">
                <div class="bg-secondary/10 p-6 border-b border-purple-100">
                    <div class="flex items-center space-x-3">
                        <div class="bg-secondary p-3 rounded-xl text-white">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-accent">Quick Actions</h3>
                            <p class="text-gray-600 font-medium">Navigate your workspace</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <a href="appointments.php" class="group bg-blue-50 p-6 rounded-xl text-center hover:bg-blue-100 transition-all transform hover:scale-[1.02] shadow-sm hover:shadow-md border border-blue-100">
                            <div class="bg-primary w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:rotate-6 transition-transform">
                                <i class="fas fa-calendar text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">All Appointments</p>
                            <p class="text-xs text-gray-600 mt-1">manage schedule</p>
                        </a>
                        <a href="patients.php" class="group bg-green-50 p-6 rounded-xl text-center hover:bg-green-100 transition-all transform hover:scale-[1.02] shadow-sm hover:shadow-md border border-green-100">
                            <div class="bg-green-400 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:rotate-6 transition-transform">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">User Records</p>
                            <p class="text-xs text-gray-600 mt-1">access history</p>
                        </a>
                        <a href="schedule.php" class="group bg-purple-50 p-6 rounded-xl text-center hover:bg-purple-100 transition-all transform hover:scale-[1.02] shadow-sm hover:shadow-md border border-purple-100">
                            <div class="bg-purple-400 w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:rotate-6 transition-transform">
                                <i class="fas fa-clock text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">My Schedule</p>
                            <p class="text-xs text-gray-600 mt-1">set availability</p>
                        </a>
                        <a href="profile.php" class="group bg-pink-50 p-6 rounded-xl text-center hover:bg-pink-100 transition-all transform hover:scale-[1.02] shadow-sm hover:shadow-md border border-pink-100">
                            <div class="bg-accent w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:rotate-6 transition-transform">
                                <i class="fas fa-user-cog text-white text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-800">Profile</p>
                            <p class="text-xs text-gray-600 mt-1">update info</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Wellness Icon -->
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="bg-primary w-14 h-14 rounded-full flex items-center justify-center shadow-lg animate-bounce">
            <i class="fas fa-spa text-white text-xl"></i>
        </div>
    </div>
</body>
</html>