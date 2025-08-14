<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('patient');

$database = new Database();
$db = $database->getConnection();

// Get search parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$specialty_filter = isset($_GET['specialty']) ? sanitizeInput($_GET['specialty']) : '';
$department_filter = isset($_GET['department']) ? sanitizeInput($_GET['department']) : '';

// First, let's check what columns exist in doctor_profiles table
// You can uncomment this temporarily to see the table structure
/*
$desc_query = "DESCRIBE doctor_profiles";
$desc_stmt = $db->prepare($desc_query);
$desc_stmt->execute();
$columns = $desc_stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($columns);
echo "</pre>";
exit;
*/

// Modified query - assuming the column might be named differently
// Common alternatives: years_experience, experience, years_of_experience
$query = "SELECT u.id, u.first_name, u.last_name, u.email, u.phone, 
                 dp.specialization, dp.qualification, dp.consultation_fee,
                 d.name as department_name, d.id as department_id,
                 COALESCE(dp.experience_years, dp.years_experience, dp.experience, 0) as experience_years
          FROM users u 
          JOIN doctor_profiles dp ON u.id = dp.user_id
          JOIN departments d ON dp.department_id = d.id
          WHERE u.role = 'doctor'";

$params = [];

if (!empty($search)) {
    $query .= " AND (u.first_name LIKE :search OR u.last_name LIKE :search OR dp.specialization LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($specialty_filter)) {
    $query .= " AND dp.specialization LIKE :specialty";
    $params[':specialty'] = "%$specialty_filter%";
}

if (!empty($department_filter)) {
    $query .= " AND d.id = :department";
    $params[':department'] = $department_filter;
}

// Order by experience if column exists, otherwise by name
$query .= " ORDER BY COALESCE(dp.experience_years, dp.years_experience, dp.experience, 0) DESC, u.first_name ASC";

try {
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If the above query fails, try a simpler version without experience column
    $query = "SELECT u.id, u.first_name, u.last_name, u.email, u.phone, 
                     dp.specialization, dp.qualification, dp.consultation_fee,
                     d.name as department_name, d.id as department_id,
                     0 as experience_years
              FROM users u 
              JOIN doctor_profiles dp ON u.id = dp.user_id
              JOIN departments d ON dp.department_id = d.id
              WHERE u.role = 'doctor'";

    $params = [];

    if (!empty($search)) {
        $query .= " AND (u.first_name LIKE :search OR u.last_name LIKE :search OR dp.specialization LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($specialty_filter)) {
        $query .= " AND dp.specialization LIKE :specialty";
        $params[':specialty'] = "%$specialty_filter%";
    }

    if (!empty($department_filter)) {
        $query .= " AND d.id = :department";
        $params[':department'] = $department_filter;
    }

    $query .= " ORDER BY u.first_name ASC";

    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all departments for filter dropdown
$dept_query = "SELECT id, name FROM departments ORDER BY name";
$dept_stmt = $db->prepare($dept_query);
$dept_stmt->execute();
$departments = $dept_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique specializations for filter
$spec_query = "SELECT DISTINCT specialization FROM doctor_profiles ORDER BY specialization";
$spec_stmt = $db->prepare($spec_query);
$spec_stmt->execute();
$specializations = $spec_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Doctors - Medicare System</title>
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

        .doctor-pattern {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23eff6ff" opacity="0.1"><circle cx="50" cy="30" r="12"/><path d="M30 50c0-10 9-18 20-18s20 8 20 18v20H30V50z"/><path d="M40 25h20v5H40zM42 30h16v3H42z"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 150px;
        }

        .search-input {
            transition: all 0.3s ease;
        }

        .search-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        }

        .filter-btn {
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
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

        .doctor-card {
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }

        .doctor-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .book-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="medical-bg min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-sm shadow-lg border-b border-green-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="dashboard.php" class="bg-gradient-to-r from-green-500 to-blue-600 p-2 rounded-xl hover:from-green-600 hover:to-blue-700 transition-all">
                        <i class="fas fa-arrow-left text-white text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">Find Doctors</h1>
                        <p class="text-sm text-gray-500">Discover healthcare specialists</p>
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
        <!-- Search and Filter Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-green-100 overflow-hidden mb-8">
            <div class="hero-pattern p-6 border-b border-gray-100">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-search text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Search Doctors</h3>
                        <p class="text-gray-600 font-medium">Find the right healthcare provider for you</p>
                    </div>
                </div>

                <!-- Search Form -->
                <form method="GET" class="space-y-4">
                    <!-- Search Bar -->
                    <div class="relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search by doctor name or specialty..." 
                               class="search-input w-full px-6 py-4 pl-12 border-2 border-gray-200 rounded-2xl focus:outline-none focus:border-green-500 bg-white/90 text-lg">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                    </div>

                    <!-- Filter Options -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select name="department" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 bg-white/90">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['id']; ?>" <?php echo ($department_filter == $dept['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                            <select name="specialty" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 bg-white/90">
                                <option value="">All Specializations</option>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?php echo $spec['specialization']; ?>" <?php echo ($specialty_filter == $spec['specialization']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($spec['specialization']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg font-semibold">
                                <i class="fas fa-search mr-2"></i>Search Doctors
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Available Doctors</h2>
                <p class="text-gray-600"><?php echo count($doctors); ?> doctors found</p>
            </div>
        </div>

        <!-- Doctors Grid -->
        <?php if (empty($doctors)): ?>
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-100 p-12 text-center">
                <div class="bg-gradient-to-br from-gray-100 to-gray-200 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-md text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">No doctors found</h3>
                <p class="text-gray-600 mb-6">Try adjusting your search criteria or browse all doctors.</p>
                <a href="doctors.php" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-full hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-users mr-2"></i>View All Doctors
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($doctors as $doctor): ?>
                    <div class="doctor-card rounded-2xl p-6 border shadow-lg card-hover">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    <?php echo strtoupper(substr($doctor['first_name'], 0, 1) . substr($doctor['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($doctor['specialization']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            <?php if ($doctor['experience_years'] > 0): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-graduation-cap mr-3 text-blue-500"></i>
                                <span><?php echo $doctor['experience_years']; ?> years experience</span>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-building mr-3 text-green-500"></i>
                                <span><?php echo htmlspecialchars($doctor['department_name']); ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-certificate mr-3 text-purple-500"></i>
                                <span><?php echo htmlspecialchars($doctor['qualification']); ?></span>
                            </div>
                            <?php if ($doctor['consultation_fee']): ?>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-dollar-sign mr-3 text-yellow-500"></i>
                                <span>$<?php echo number_format($doctor['consultation_fee'], 2); ?> consultation fee</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold border border-blue-300">
                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </span>
                            <?php if ($doctor['experience_years'] > 0): ?>
                            <span class="bg-gradient-to-r from-green-100 to-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-semibold border border-green-300">
                                <?php echo $doctor['experience_years']; ?>+ Years
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex space-x-3">
                            <a href="book-appointment.php?doctor_id=<?php echo $doctor['id']; ?>" 
                               class="book-btn text-white px-6 py-3 rounded-xl font-semibold flex-1 text-center hover:shadow-lg">
                                <i class="fas fa-calendar-plus mr-2"></i>Book Appointment
                            </a>
                            <button onclick="showDoctorDetails(<?php echo $doctor['id']; ?>)" 
                                    class="border-2 border-gray-200 text-gray-600 px-4 py-3 rounded-xl hover:border-green-500 hover:text-green-600 transition-colors">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Floating Health Animation -->
    <div class="fixed bottom-6 right-6 pointer-events-none">
        <div class="bg-gradient-to-r from-green-500 to-teal-600 w-16 h-16 rounded-full flex items-center justify-center shadow-lg animate-bounce">
            <i class="fas fa-heart text-white text-2xl heartbeat"></i>
        </div>
    </div>

    <!-- Doctor Details Modal -->
    <div id="doctorModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-90vh overflow-y-auto">
            <div class="hero-pattern p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Doctor Details</h3>
                    <button onclick="closeDoctorDetails()" class="text-gray-500 hover:text-gray-700 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div id="modalContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function showDoctorDetails(doctorId) {
            // This would typically make an AJAX request to get doctor details
            // For now, we'll show basic info
            const modal = document.getElementById('doctorModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-green-500 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading doctor details...</p>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Simulate loading - in real implementation, make AJAX call here
            setTimeout(() => {
                content.innerHTML = `
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4">
                            DR
                        </div>
                        <h4 class="text-2xl font-bold text-gray-800 mb-2">Dr. Sample Doctor</h4>
                        <p class="text-gray-600">Detailed information would be loaded here via AJAX</p>
                    </div>
                    <div class="space-y-4">
                        <p class="text-gray-600">In a real implementation, this would show:</p>
                        <ul class="text-gray-600 space-y-2 ml-4">
                            <li>• Detailed biography</li>
                            <li>• Education and certifications</li>
                            <li>• Available time slots</li>
                            <li>• Patient reviews</li>
                            <li>• Contact information</li>
                        </ul>
                    </div>
                    <div class="mt-6 text-center">
                        <button onclick="closeDoctorDetails()" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all">
                            Close
                        </button>
                    </div>
                `;
            }, 1500);
        }

        function closeDoctorDetails() {
            const modal = document.getElementById('doctorModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('doctorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDoctorDetails();
            }
        });
    </script>
</body>
</html>