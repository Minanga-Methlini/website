<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('admin');

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $query = "INSERT INTO departments (name, description) VALUES (:name, :description)";
                $stmt = $db->prepare($query);
                if ($stmt->execute([':name' => $_POST['name'], ':description' => $_POST['description']])) {
                    $success = "Department created successfully!";
                } else {
                    $error = "Error creating department.";
                }
                break;
            case 'update':
                $query = "UPDATE departments SET name = :name, description = :description WHERE id = :id";
                $stmt = $db->prepare($query);
                if ($stmt->execute([':name' => $_POST['name'], ':description' => $_POST['description'], ':id' => $_POST['dept_id']])) {
                    $success = "Department updated successfully!";
                } else {
                    $error = "Error updating department.";
                }
                break;
            case 'delete':
                $query = "DELETE FROM departments WHERE id = :id";
                $stmt = $db->prepare($query);
                if ($stmt->execute([':id' => $_POST['dept_id']])) {
                    $success = "Department deleted successfully!";
                } else {
                    $error = "Error deleting department.";
                }
                break;
        }
    }
}

// Get all departments with trainer count
$query = "SELECT d.*, COUNT(dp.id) as trainer_count 
          FROM departments d 
          LEFT JOIN trainer_profiles dp ON d.id = dp.department_id 
          GROUP BY d.id 
          ORDER BY d.name";
$stmt = $db->prepare($query);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management - Admin Dashboard</title>
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
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-subtle': 'bounceSubtle 2s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite alternate'
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
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulseGlow {
            0% { box-shadow: 0 0 5px rgba(139, 92, 246, 0.3); }
            100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.6), 0 0 30px rgba(139, 92, 246, 0.4); }
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
            transition: all 0.4s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .department-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(255,255,255,0.7));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        .department-card:hover {
            background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
        }
        .department-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            animation: float 6s ease-in-out infinite;
        }
        .stats-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="min-h-screen py-8 relative" 
      style="background: linear-gradient(135deg, #ec6d2dff 0%, #cc12f1ff 50%, #09f1d3ff 100%); 
             background-size: cover;
             background-position: center;">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 z-0">
        <div class="absolute top-10 left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float"></div>
        <div class="absolute top-0 right-4 w-72 h-72 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-float" style="animation-delay: 4s;"></div>
    </div>

    <!-- Header with Enhanced Gradient -->
    <header class="gradient-bg shadow-2xl relative z-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="glass text-white px-4 py-2 rounded-full hover:bg-white/30 transition-all duration-300 group">
                        <i class="fas fa-arrow-left mr-2 group-hover:animate-bounce-subtle"></i> 
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <div class="text-white">
                        <h1 class="text-3xl font-bold tracking-tight">Department Management</h1>
                        <p class="text-white/80 text-sm mt-1">Organize and manage departments</p>
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

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-8 relative z-10">
        <!-- Alert Messages with Enhanced Animation -->
        <?php if (isset($success)): ?>
            <div class="animate-slide-up bg-gradient-to-r from-green-400 to-green-600 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-2xl mr-3 animate-bounce-subtle"></i>
                    <span class="font-medium"><?php echo $success; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="animate-slide-up bg-gradient-to-r from-red-400 to-red-600 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-red-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-2xl mr-3 animate-bounce-subtle"></i>
                    <span class="font-medium"><?php echo $error; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stats-card rounded-2xl p-6 text-center">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-glow">
                    <i class="fas fa-building text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo count($departments); ?></h3>
                <p class="text-gray-600 font-medium">Total Departments</p>
            </div>
            
            <div class="stats-card rounded-2xl p-6 text-center">
                <div class="bg-gradient-to-r from-green-500 to-green-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-glow" style="animation-delay: 0.5s;">
                    <i class="fas fa-user-md text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo array_sum(array_column($departments, 'trainer_count')); ?></h3>
                <p class="text-gray-600 font-medium">Total Trainers</p>
            </div>
            
            <div class="stats-card rounded-2xl p-6 text-center">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-glow" style="animation-delay: 1s;">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800"><?php echo count($departments) > 0 ? round(array_sum(array_column($departments, 'trainer_count')) / count($departments), 1) : 0; ?></h3>
                <p class="text-gray-600 font-medium">Avg Trainers/Dept</p>
            </div>
        </div>

        <!-- Create Department Card -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 overflow-hidden animate-fade-in card-hover">
            <div class="bg-gradient-to-r from-accent-500 to-primary-500 px-8 py-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-3 rounded-full animate-float">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Create New Department</h3>
                        <p class="text-white/80">Add a new department</p>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-building mr-2 text-accent-500"></i>Department Name
                            </label>
                            <input type="text" name="name" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-accent-500 focus:ring-4 focus:ring-accent-500/20 transition-all duration-300"
                                   placeholder="e.g., Cardiology, Emergency, etc.">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-align-left mr-2 text-primary-500"></i>Description
                            </label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 transition-all duration-300"
                                      placeholder="Brief description of the department..."></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-gradient-to-r from-accent-500 to-primary-500 text-white px-8 py-4 rounded-2xl hover:from-accent-600 hover:to-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                            <i class="fas fa-plus mr-3"></i>Create Department
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Departments Grid -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 overflow-hidden animate-fade-in card-hover">
            <div class="bg-gradient-to-r from-primary-500 to-accent-500 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-3 rounded-full animate-float">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">All Departments</h3>
                            <p class="text-white/80">Manage existing departments</p>
                        </div>
                    </div>
                    <div class="bg-white/20 px-4 py-2 rounded-full text-white font-semibold">
                        <?php echo count($departments); ?> departments
                    </div>
                </div>
            </div>
            
            <?php if (empty($departments)): ?>
                <div class="p-12 text-center">
                    <div class="flex flex-col items-center space-y-4">
                        <div class="bg-gray-100 p-8 rounded-full">
                            <i class="fas fa-building text-6xl text-gray-400"></i>
                        </div>
                        <div class="text-gray-500">
                            <p class="text-2xl font-bold">No departments found</p>
                            <p class="text-sm mt-2">Create your first department to get started.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 p-8">
                    <?php foreach ($departments as $index => $dept): ?>
                        <div class="department-card rounded-2xl p-6 animate-fade-in" 
                             style="animation-delay: <?php echo $index * 0.1; ?>s;">
                            <div class="flex items-center justify-between mb-6">
                                <div class="department-icon w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-hospital text-white text-xl"></i>
                                </div>
                                <div class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 text-sm font-bold px-4 py-2 rounded-full shadow-md">
                                    <i class="fas fa-user-md mr-1"></i>
                                    <?php echo $dept['trainer_count']; ?> Trainers
                                </div>
                            </div>
                            
                            <h4 class="text-xl font-bold text-gray-800 mb-3"><?php echo htmlspecialchars($dept['name']); ?></h4>
                            <p class="text-gray-600 text-sm mb-6 leading-relaxed"><?php echo htmlspecialchars($dept['description']); ?></p>
                            
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                <button onclick="editDepartment(<?php echo htmlspecialchars(json_encode($dept)); ?>)" 
                                        class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-4 py-2 rounded-xl hover:from-indigo-600 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105 text-sm font-semibold">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </button>
                                <button onclick="deleteDepartment(<?php echo $dept['id']; ?>, '<?php echo htmlspecialchars($dept['name']); ?>')" 
                                        class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105 text-sm font-semibold">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enhanced Modal for Editing -->
    <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-all duration-300">
            <div class="text-center mb-6">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-edit text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Edit Department</h3>
                <p class="text-gray-600">Update department information</p>
            </div>
            <form id="editForm" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="dept_id" id="editDeptId">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building mr-2 text-indigo-500"></i>Department Name
                    </label>
                    <input type="text" name="name" id="editName" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all duration-300">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left mr-2 text-purple-500"></i>Description
                    </label>
                    <textarea name="description" id="editDescription" rows="3" 
                              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all duration-300"></textarea>
                </div>
                
                <div class="flex space-x-4 pt-4">
                    <button type="button" onclick="closeModal()" 
                            class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-xl hover:bg-gray-300 transition-all duration-300 font-semibold">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-3 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 font-semibold">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editDepartment(dept) {
            document.getElementById('editDeptId').value = dept.id;
            document.getElementById('editName').value = dept.name;
            document.getElementById('editDescription').value = dept.description;
            
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.bg-white').classList.add('scale-100');
                modal.querySelector('.bg-white').classList.remove('scale-95');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('editModal');
            modal.querySelector('.bg-white').classList.add('scale-95');
            modal.querySelector('.bg-white').classList.remove('scale-100');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function deleteDepartment(deptId, deptName) {
            if (confirm(`🗑️ Are you sure you want to delete "${deptName}" department?\n\nThis action cannot be undone and may affect associated trainers.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="dept_id" value="${deptId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Add loading animation to form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            submitButton.disabled = true;
        });

        document.getElementById('editForm').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            submitButton.disabled = true;
        });
    </script>
</body>
</html>