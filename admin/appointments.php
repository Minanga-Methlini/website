<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../classes/AppointmentManager.php';

requireRole('admin');

$database = new Database();
$db = $database->getConnection();
$appointmentManager = new AppointmentManager($db);

// Handle appointment status updates
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $appointmentManager->updateAppointmentStatusAdmin($_POST['appointment_id'], $_POST['status']);
        $success = "Appointment status updated successfully!";
    }
}

// Get all appointments
$appointments = $appointmentManager->getAllAppointments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Management - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e'
                        },
                        accent: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9'
                        },
                        success: {
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a'
                        },
                        warning: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706'
                        },
                        danger: {
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-subtle': 'bounceSubtle 2s infinite',
                        'pulse-gentle': 'pulseGentle 3s ease-in-out infinite'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounceSubtle {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-3px); }
            60% { transform: translateY(-2px); }
        }
        @keyframes pulseGentle {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .status-indicator {
            position: relative;
            overflow: hidden;
        }
        .status-indicator::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        .status-indicator:hover::before {
            left: 100%;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-indigo-50 to-purple-100">
    <!-- Header with Gradient -->
    <header class="gradient-bg shadow-2xl">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="glass text-white px-4 py-2 rounded-full hover:bg-white/30 transition-all duration-300 group">
                        <i class="fas fa-arrow-left mr-2 group-hover:animate-bounce-subtle"></i> 
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold tracking-tight">Appointment Management</h1>
                        <p class="text-white/80 text-sm mt-1">Monitor and manage all appointments</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="glass text-white px-4 py-2 rounded-full">
                        <i class="fas fa-user-circle mr-2"></i>
                        <span class="font-medium"><?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-red-500/90 hover:bg-red-600 text-white px-6 py-3 rounded-full transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
        <!-- Success Message with Animation -->
        <?php if (isset($success)): ?>
            <div class="animate-slide-up bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-2xl mr-3 animate-bounce-subtle"></i>
                    <span class="font-medium"><?php echo $success; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $totalAppointments = count($appointments);
            $pendingCount = count(array_filter($appointments, function($app) { return $app['status'] === 'pending'; }));
            $confirmedCount = count(array_filter($appointments, function($app) { return $app['status'] === 'confirmed'; }));
            $completedCount = count(array_filter($appointments, function($app) { return $app['status'] === 'completed'; }));
            ?>
            
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl p-6 text-white shadow-lg card-hover animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Appointments</p>
                        <p class="text-3xl font-bold"><?php echo $totalAppointments; ?></p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-6 text-white shadow-lg card-hover animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold"><?php echo $pendingCount; ?></p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-clock text-2xl animate-pulse-gentle"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-2xl p-6 text-white shadow-lg card-hover animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Confirmed</p>
                        <p class="text-3xl font-bold"><?php echo $confirmedCount; ?></p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl p-6 text-white shadow-lg card-hover animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold"><?php echo $completedCount; ?></p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-medal text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments List Card -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 overflow-hidden animate-fade-in card-hover">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-3 rounded-full">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">All Appointments</h3>
                            <p class="text-white/80">Manage appointment schedules and status</p>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <div class="bg-white/20 px-4 py-2 rounded-full text-white font-semibold">
                            <i class="fas fa-list mr-2"></i><?php echo count($appointments); ?> appointments
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user-injured mr-2 text-blue-500"></i>Patient
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user-md mr-2 text-green-500"></i>Doctor
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-calendar-day mr-2 text-purple-500"></i>Date & Time
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Status
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-2 text-orange-500"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200/50">
                        <?php foreach ($appointments as $appointment): ?>
                            <tr class="hover:bg-white/80 transition-all duration-200 group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-3 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-200">
                                            <i class="fas fa-user text-white text-lg"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-lg font-bold text-gray-900">
                                                <?php echo htmlspecialchars($appointment['patient_first'] . ' ' . $appointment['patient_last']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-envelope mr-1 text-blue-400"></i>
                                                <?php echo htmlspecialchars($appointment['patient_email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-green-400 to-green-600 p-3 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-200">
                                            <i class="fas fa-user-md text-white text-lg"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-lg font-bold text-gray-900">
                                                Dr. <?php echo htmlspecialchars($appointment['doctor_first'] . ' ' . $appointment['doctor_last']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-stethoscope mr-1 text-green-400"></i>
                                                <?php echo htmlspecialchars($appointment['specialization'] ?? 'General'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="space-y-2">
                                        <div class="flex items-center text-gray-900 font-bold">
                                            <i class="fas fa-calendar mr-2 text-purple-500"></i>
                                            <?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-clock mr-2 text-orange-500"></i>
                                            <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="status-indicator px-4 py-2 text-sm font-bold rounded-full shadow-md
                                        <?php 
                                        switch($appointment['status']) {
                                            case 'confirmed': 
                                                echo 'bg-gradient-to-r from-green-400 to-green-600 text-white'; 
                                                $icon = 'fas fa-check-circle';
                                                break;
                                            case 'completed': 
                                                echo 'bg-gradient-to-r from-blue-400 to-blue-600 text-white'; 
                                                $icon = 'fas fa-medal';
                                                break;
                                            case 'cancelled': 
                                                echo 'bg-gradient-to-r from-red-400 to-red-600 text-white'; 
                                                $icon = 'fas fa-times-circle';
                                                break;
                                            default: 
                                                echo 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white';
                                                $icon = 'fas fa-clock';
                                        }
                                        ?>">
                                        <i class="<?php echo $icon; ?> mr-2"></i>
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="relative">
                                        <select onchange="updateStatus(<?php echo $appointment['id']; ?>, this.value)" 
                                                class="appearance-none bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold px-6 py-3 pr-10 rounded-xl border-none focus:outline-none focus:ring-4 focus:ring-indigo-300 cursor-pointer shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                                            <option value="" class="bg-white text-gray-800">Change Status</option>
                                            <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?> class="bg-white text-gray-800">⏳ Pending</option>
                                            <option value="confirmed" <?php echo $appointment['status'] === 'confirmed' ? 'selected' : ''; ?> class="bg-white text-gray-800">✅ Confirmed</option>
                                            <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?> class="bg-white text-gray-800">🏆 Completed</option>
                                            <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?> class="bg-white text-gray-800">❌ Cancelled</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-white"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="bg-gray-100 p-6 rounded-full">
                                            <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                                        </div>
                                        <div class="text-gray-500">
                                            <p class="text-xl font-semibold">No appointments found</p>
                                            <p class="text-sm mt-1">There are currently no appointments in the system.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function updateStatus(appointmentId, status) {
            if (status && confirm('🔄 Are you sure you want to update this appointment status?')) {
                // Show loading state
                const selectElement = event.target;
                const originalHTML = selectElement.innerHTML;
                selectElement.disabled = true;
                selectElement.innerHTML = '<option>Updating...</option>';
                
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="appointment_id" value="${appointmentId}">
                    <input type="hidden" name="status" value="${status}">
                `;
                document.body.appendChild(form);
                form.submit();
            } else {
                // Reset select if cancelled
                event.target.selectedIndex = 0;
            }
        }

        // Add smooth scroll to top on page load
        window.addEventListener('load', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Add loading animation on form submission
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animation to table rows
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.1}s`;
                row.classList.add('animate-fade-in');
            });
        });
    </script>
</body>
</html>