<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../classes/AppointmentManager.php';

requireRole('doctor');

$database = new Database();
$db = $database->getConnection();
$appointmentManager = new AppointmentManager($db);

// Handle appointment status updates
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $appointmentManager->updateAppointmentStatus($_POST['appointment_id'], $_POST['status'], $_SESSION['user_id']);
        header('Location: appointments.php?updated=1');
        exit;
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Get appointments
$appointments = $appointmentManager->getDoctorAppointments($_SESSION['user_id'], $status_filter, $date_filter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Appointments - Doctor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .glass-effect { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); }
        .status-pending { @apply bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300; }
        .status-confirmed { @apply bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300; }
        .status-completed { @apply bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300; }
        .status-cancelled { @apply bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300; }
        .appointment-row { transition: all 0.2s ease; }
        .appointment-row:hover { background: linear-gradient(90deg, rgba(99, 102, 241, 0.02) 0%, rgba(168, 85, 247, 0.02) 100%); }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Enhanced Navigation with Glass Effect -->
    <nav class="glass-effect border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition-colors duration-200 group">
                        <div class="p-2 rounded-full bg-indigo-100 group-hover:bg-indigo-200 transition-colors">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </div>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <div class="flex items-center space-x-2">
                        <div class="w-1 h-8 bg-gradient-to-b from-indigo-400 to-purple-500 rounded-full"></div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            All Appointments
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3 bg-white/60 backdrop-blur rounded-full px-4 py-2 border border-white/30">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-md text-white text-xs"></i>
                        </div>
                        <span class="text-gray-700 font-medium">Dr. <?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
        <div class="mb-8 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-green-800 font-medium">Appointment status updated successfully!</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Enhanced Filters Card -->
        <div class="glass-effect rounded-3xl shadow-xl border border-white/20 mb-8 card-hover">
            <div class="bg-gradient-to-r from-indigo-500/10 to-purple-500/10 p-6 border-b border-white/20 rounded-t-3xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <i class="fas fa-filter text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Filter Appointments</h3>
                        <p class="text-gray-600 text-sm">Refine your appointment view</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form method="GET" class="flex flex-wrap gap-6 items-end">
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Status</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tags text-gray-400"></i>
                            </div>
                            <select name="status" class="w-full border-2 border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-white">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>📋 Pending</option>
                                <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>✅ Confirmed</option>
                                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>🎯 Completed</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="date" name="date" value="<?php echo $date_filter; ?>" 
                                   class="w-full border-2 border-gray-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium">
                            <i class="fas fa-search mr-2"></i>Apply Filters
                        </button>
                        <a href="appointments.php" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium">
                            <i class="fas fa-refresh mr-2"></i>Clear All
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enhanced Appointments Card -->
        <div class="glass-effect rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <!-- Header -->
            <div class="gradient-bg p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/10 rounded-full -ml-10 -mb-10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold">Your Appointments</h3>
                                <p class="text-white/80 mt-1">Manage and track all patient appointments</p>
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-2xl px-6 py-3">
                            <div class="text-center">
                                <div class="text-3xl font-bold"><?php echo count($appointments); ?></div>
                                <div class="text-white/80 text-sm">Total Appointments</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Table/Cards -->
            <div class="p-6">
                <?php if (!empty($appointments)): ?>
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-100">
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-600 uppercase tracking-wider">Patient Info</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-600 uppercase tracking-wider">Appointment</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                                    <th class="text-left py-4 px-2 text-sm font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment): ?>
                                <tr class="appointment-row border-b border-gray-100">
                                    <td class="py-6 px-2">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-lg"><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    <?php echo $appointment['gender'] ?? 'N/A'; ?> • 
                                                    <?php echo $appointment['date_of_birth'] ? (date('Y') - date('Y', strtotime($appointment['date_of_birth']))) . ' years old' : 'Age N/A'; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-2">
                                        <div class="space-y-1">
                                            <p class="font-bold text-gray-800 flex items-center">
                                                <i class="fas fa-calendar mr-2 text-indigo-500"></i>
                                                <?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?>
                                            </p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-clock mr-2 text-purple-500"></i>
                                                <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-6 px-2">
                                        <span class="px-3 py-2 text-sm font-semibold rounded-full 
                                            <?php 
                                            switch($appointment['status']) {
                                                case 'confirmed': echo 'status-confirmed'; break;
                                                case 'completed': echo 'status-completed'; break;
                                                case 'cancelled': echo 'status-cancelled'; break;
                                                default: echo 'status-pending';
                                            }
                                            ?>">
                                            <?php 
                                            $icons = [
                                                'pending' => '⏳',
                                                'confirmed' => '✅', 
                                                'completed' => '🎯',
                                                'cancelled' => '❌'
                                            ];
                                            echo $icons[$appointment['status']] . ' ' . ucfirst($appointment['status']); 
                                            ?>
                                        </span>
                                    </td>
                                    <td class="py-6 px-2">
                                        <div class="space-y-1">
                                            <p class="text-sm font-medium text-gray-800 flex items-center">
                                                <i class="fas fa-phone mr-2 text-green-500"></i>
                                                <?php echo $appointment['phone']; ?>
                                            </p>
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                                <?php echo substr($appointment['email'], 0, 20) . (strlen($appointment['email']) > 20 ? '...' : ''); ?>
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-6 px-2">
                                        <div class="flex space-x-2">
                                            <?php if ($appointment['status'] === 'pending'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-3 py-2 rounded-lg text-sm hover:from-green-600 hover:to-green-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                                    <i class="fas fa-check mr-1"></i>Accept
                                                </button>
                                            </form>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-2 rounded-lg text-sm hover:from-red-600 hover:to-red-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                                    <i class="fas fa-times mr-1"></i>Decline
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($appointment['status'] === 'confirmed'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:from-blue-600 hover:to-blue-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                                    <i class="fas fa-check-circle mr-1"></i>Complete
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            
                                            <a href="patient_details.php?patient_id=<?php echo $appointment['patient_id']; ?>" 
                                               class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-3 py-2 rounded-lg text-sm hover:from-indigo-600 hover:to-purple-600 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="lg:hidden space-y-4">
                        <?php foreach ($appointments as $appointment): ?>
                        <div class="bg-white/80 backdrop-blur rounded-2xl p-6 border border-white/30 shadow-lg card-hover">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-purple-500 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800"><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></h4>
                                        <p class="text-sm text-gray-600"><?php echo $appointment['gender'] ?? 'N/A'; ?> • <?php echo $appointment['date_of_birth'] ? (date('Y') - date('Y', strtotime($appointment['date_of_birth']))) . ' years' : 'Age N/A'; ?></p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
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
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Date & Time</p>
                                    <p class="font-semibold text-sm"><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Contact</p>
                                    <p class="text-sm font-medium"><?php echo $appointment['phone']; ?></p>
                                    <p class="text-sm text-gray-600 truncate"><?php echo $appointment['email']; ?></p>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2">
                                <?php if ($appointment['status'] === 'pending'): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-700 transition">
                                        <i class="fas fa-check mr-1"></i>Accept
                                    </button>
                                </form>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-red-700 transition">
                                        <i class="fas fa-times mr-1"></i>Decline
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($appointment['status'] === 'confirmed'): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-blue-700 transition">
                                        <i class="fas fa-check-circle mr-1"></i>Complete
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <a href="patient_details.php?patient_id=<?php echo $appointment['patient_id']; ?>" 
                                   class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-700 transition">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Appointments Found</h3>
                    <p class="text-gray-500 mb-4">There are no appointments matching your current filters.</p>
                    <a href="appointments.php" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-refresh mr-2"></i>View All Appointments
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-hide success messages
        setTimeout(() => {
            const successMsg = document.querySelector('.bg-gradient-to-r.from-green-50');
            if (successMsg) {
                successMsg.style.transition = 'all 0.5s ease';
                successMsg.style.opacity = '0';
                successMsg.style.transform = 'translateY(-20px)';
                setTimeout(() => successMsg.remove(), 500);
            }
        }, 5000);

        // Confirm status changes
        document.querySelectorAll('form button[type="submit"]').forEach(button => {
            if (button.textContent.includes('Decline') || button.textContent.includes('Complete')) {
                button.addEventListener('click', function(e) {
                    const action = this.textContent.includes('Decline') ? 'cancel' : 'mark as completed';
                    if (!confirm(`Are you sure you want to ${action} this appointment?`)) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>