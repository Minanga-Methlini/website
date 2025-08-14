<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

// Check if user is logged in and is doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get doctor's appointments
$appointments_query = "
    SELECT a.*, 
           CONCAT(p.first_name, ' ', p.last_name) as patient_name,
           p.phone as patient_phone,
           p.email as patient_email
    FROM appointments a 
    JOIN users p ON a.patient_id = p.id 
    WHERE a.doctor_id = :doctor_id 
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
";
$appointments_stmt = $db->prepare($appointments_query);
$appointments_stmt->bindParam(':doctor_id', $_SESSION['user_id']);
$appointments_stmt->execute();
$appointments = $appointments_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];
    
    $update_query = "UPDATE appointments SET status = :status WHERE id = :id AND doctor_id = :doctor_id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':status', $new_status);
    $update_stmt->bindParam(':id', $appointment_id);
    $update_stmt->bindParam(':doctor_id', $_SESSION['user_id']);
    
    if ($update_stmt->execute()) {
        header('Location: appointments.php?updated=1');
        exit();
    }
}
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

        .appointment-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .status-pending { border-left-color: #f59e0b; background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); }
        .status-confirmed { border-left-color: #10b981; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05)); }
        .status-completed { border-left-color: #3b82f6; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05)); }
        .status-cancelled { border-left-color: #ef4444; background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05)); }

        .filter-btn {
            transition: all 0.3s ease;
            border-radius: 50px;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: white;
        }

        .floating-add {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #10b981, #3b82f6);
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
            animation: float 6s ease-in-out infinite;
        }

        .floating-add:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
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
                            All Appointments
                        </h1>
                        <p class="text-gray-600">Manage your patient appointments</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fas fa-calendar-check mr-1"></i>
                        <?php echo count($appointments); ?> Total Appointments
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="mb-8">
            <div class="glass-card rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Appointments</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="filter-btn active px-6 py-2 bg-white border border-gray-200" data-filter="all">
                        <i class="fas fa-list mr-2"></i>All Appointments
                    </button>
                    <button class="filter-btn px-6 py-2 bg-white border border-gray-200" data-filter="pending">
                        <i class="fas fa-clock mr-2"></i>Pending
                    </button>
                    <button class="filter-btn px-6 py-2 bg-white border border-gray-200" data-filter="confirmed">
                        <i class="fas fa-check-circle mr-2"></i>Confirmed
                    </button>
                    <button class="filter-btn px-6 py-2 bg-white border border-gray-200" data-filter="completed">
                        <i class="fas fa-check-double mr-2"></i>Completed
                    </button>
                    <button class="filter-btn px-6 py-2 bg-white border border-gray-200" data-filter="cancelled">
                        <i class="fas fa-times-circle mr-2"></i>Cancelled
                    </button>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="space-y-4">
            <?php if (empty($appointments)): ?>
                <div class="glass-card rounded-2xl p-12 text-center shadow-lg">
                    <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Appointments Found</h3>
                    <p class="text-gray-500">You don't have any appointments scheduled yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                    <?php $status = isset($appointment['status']) ? strtolower($appointment['status']) : 'pending'; ?>
                    <div class="appointment-card glass-card rounded-2xl p-6 shadow-lg status-<?php echo $status; ?>" 
                         data-status="<?php echo $status; ?>">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-green-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                        <?php echo strtoupper(substr($appointment['patient_name'], 0, 2)); ?>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($appointment['patient_name']); ?></h3>
                                        <div class="flex items-center text-gray-600 text-sm">
                                            <i class="fas fa-envelope mr-2"></i>
                                            <?php echo htmlspecialchars($appointment['patient_email']); ?>
                                            <?php if ($appointment['patient_phone']): ?>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-phone mr-2"></i>
                                                <?php echo htmlspecialchars($appointment['patient_phone']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                        <span><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-clock text-green-500 mr-2"></i>
                                        <span><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></span>
                                    </div>
                                    <div class="flex items-center text-gray-700">
                                        <i class="fas fa-stethoscope text-purple-500 mr-2"></i>
                                        <span><?php echo htmlspecialchars($appointment['appointment_type'] ?? 'General Consultation'); ?></span>
                                    </div>
                                </div>

                                <?php if ($appointment['symptoms']): ?>
                                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                        <h4 class="font-semibold text-gray-800 mb-1">Symptoms/Notes:</h4>
                                        <p class="text-gray-700 text-sm"><?php echo htmlspecialchars($appointment['symptoms']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="ml-6">
                                <!-- Status Badge -->
                                <div class="mb-4">
                                    <?php
                                    $status_colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                                        'completed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'cancelled' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $status_icons = [
                                        'pending' => 'fas fa-clock',
                                        'confirmed' => 'fas fa-check-circle',
                                        'completed' => 'fas fa-check-double',
                                        'cancelled' => 'fas fa-times-circle'
                                    ];
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold border <?php echo $status_colors[$status]; ?>">
                                        <i class="<?php echo $status_icons[$status]; ?> mr-1"></i>
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </div>

                                <!-- Update Status Form -->
                                <form method="POST" class="space-y-2">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" 
                                            class="w-full bg-gradient-to-r from-blue-500 to-green-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:from-blue-600 hover:to-green-600 transition-all">
                                        <i class="fas fa-save mr-1"></i>Update
                                    </button>
                                </form>

                                <!-- Action Buttons -->
                                <div class="mt-4 space-y-2">
                                    <button class="w-full bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-200 transition-all">
                                        <i class="fas fa-notes-medical mr-1"></i>Add Notes
                                    </button>
                                    <button class="w-full bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-200 transition-all">
                                        <i class="fas fa-prescription mr-1"></i>Prescribe
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Floating Add Button -->
        <a href="#" class="floating-add" title="Schedule New Appointment">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>

    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const appointmentCards = document.querySelectorAll('.appointment-card');

            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const filter = this.dataset.filter;

                    // Update active button
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Filter appointments
                    appointmentCards.forEach(card => {
                        if (filter === 'all' || card.dataset.status === filter) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // Success message
            if (new URLSearchParams(window.location.search).get('updated')) {
                const successMsg = document.createElement('div');
                successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                successMsg.innerHTML = '<i class="fas fa-check mr-2"></i>Appointment updated successfully!';
                document.body.appendChild(successMsg);

                setTimeout(() => successMsg.remove(), 3000);
            }
        });
    </script>
</body>
</html>
