<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../classes/UserManager.php';

requireRole('admin');

$database = new Database();
$db = $database->getConnection();
$userManager = new UserManager($db);

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                try {
                    $userManager->createUser($_POST);
                    $success = "User created successfully!";
                } catch (Exception $e) {
                    $error = "Error creating user: " . $e->getMessage();
                }
                break;
            case 'update':
                try {
                    $userManager->updateUser($_POST['user_id'], $_POST);
                    $success = "User updated successfully!";
                } catch (Exception $e) {
                    $error = "Error updating user: " . $e->getMessage();
                }
                break;
            case 'delete':
                try {
                    $userManager->deleteUser($_POST['user_id']);
                    $success = "User deleted successfully!";
                } catch (Exception $e) {
                    $error = "Error deleting user: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get all users
$users = $userManager->getAllUsers();

// Get departments for doctor creation
$departments = getAllDepartments($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
                        'bounce-subtle': 'bounceSubtle 2s infinite'
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
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="min-h-screen py-8 relative" 
      style="background: linear-gradient(135deg, #ec6d2dff 0%, #cc12f1ff 50%, #09f1d3ff 100%); 
             background-size: cover;
             background-position: center;">
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
                        <h1 class="text-3xl font-bold tracking-tight">User Management</h1>
                        <p class="text-white/80 text-sm mt-1">Manage system users and permissions</p>
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
        <!-- Alert Messages with Animation -->
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

        <!-- Create User Card -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 overflow-hidden animate-fade-in card-hover">
            <div class="bg-gradient-to-r from-accent-500 to-primary-500 px-8 py-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-3 rounded-full">
                        <i class="fas fa-user-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Create New User</h3>
                        <p class="text-white/80">Add a new user to the system</p>
                    </div>
                </div>
            </div>
            <div class="p-8">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="action" value="create">
                    
                    <!-- Basic Information Grid -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-user mr-2 text-accent-500"></i>Username
                            </label>
                            <input type="text" name="username" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-accent-500 focus:ring-4 focus:ring-accent-500/20 transition-all duration-300">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-envelope mr-2 text-primary-500"></i>Email
                            </label>
                            <input type="email" name="email" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20 transition-all duration-300">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-lock mr-2 text-red-500"></i>Password
                            </label>
                            <input type="password" name="password" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-4 focus:ring-red-500/20 transition-all duration-300">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-user-tag mr-2 text-indigo-500"></i>Role
                            </label>
                            <select name="role" required onchange="toggleRoleFields(this.value)"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all duration-300">
                                <option value="">Select Role</option>
                                <option value="admin">👑 Admin</option>
                                <option value="doctor">🩺 Doctor</option>
                                <option value="patient">👤 Patient</option>
                            </select>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-id-badge mr-2 text-green-500"></i>First Name
                            </label>
                            <input type="text" name="first_name" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-id-badge mr-2 text-green-500"></i>Last Name
                            </label>
                            <input type="text" name="last_name" required 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-phone mr-2 text-blue-500"></i>Phone
                            </label>
                            <input type="text" name="phone" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                        </div>
                    </div>
                    
                    <!-- trainer Specific Fields -->
                    <div id="doctor-fields" class="hidden animate-slide-up">
                        <div class="bg-green-50 rounded-2xl p-6 border-2 border-green-200">
                            <h4 class="text-lg font-bold text-green-800 mb-4">
                                <i class="fas fa-user-md mr-2"></i>trainer Information
                            </h4>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Department</label>
                                    <select name="department_id" 
                                            class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300">
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Specialization</label>
                                    <input type="text" name="specialization" 
                                           class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300">
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Bio</label>
                                <textarea name="bio" rows="3" 
                                          class="w-full px-4 py-3 border-2 border-green-200 rounded-xl focus:outline-none focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- user Specific Fields -->
                    <div id="patient-fields" class="hidden animate-slide-up">
                        <div class="bg-blue-50 rounded-2xl p-6 border-2 border-blue-200">
                            <h4 class="text-lg font-bold text-blue-800 mb-4">
                                <i class="fas fa-user mr-2"></i>User Information
                            </h4>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Date of Birth</label>
                                    <input type="date" name="date_of_birth" 
                                           class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Gender</label>
                                    <select name="gender" 
                                            class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                                        <option value="">Select Gender</option>
                                        <option value="male">♂️ Male</option>
                                        <option value="female">♀️ Female</option>
                                        <option value="other">⚧️ Other</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">Address</label>
                                    <input type="text" name="address" 
                                           class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-gradient-to-r from-accent-500 to-primary-500 text-white px-8 py-4 rounded-2xl hover:from-accent-600 hover:to-primary-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-semibold">
                            <i class="fas fa-plus mr-3"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users List Card -->
        <div class="bg-white/70 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 overflow-hidden animate-fade-in card-hover">
            <div class="bg-gradient-to-r from-primary-500 to-accent-500 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-3 rounded-full">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">All Users</h3>
                            <p class="text-white/80">Manage existing users</p>
                        </div>
                    </div>
                    <div class="bg-white/20 px-4 py-2 rounded-full text-white font-semibold">
                        <?php echo count($users); ?> users
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user mr-2"></i>User
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-shield-alt mr-2"></i>Role
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-address-book mr-2"></i>Contact
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-2"></i>Details
                            </th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/50 divide-y divide-gray-200/50">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-white/80 transition-colors duration-200">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gradient-to-br from-accent-400 to-primary-500 p-3 rounded-2xl shadow-lg">
                                            <i class="fas fa-user text-white text-lg"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-lg font-bold text-gray-900">
                                                <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                                            </div>
                                            <div class="text-sm text-gray-500 font-medium">@<?php echo $user['username']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="px-4 py-2 text-sm font-bold rounded-full shadow-md
                                        <?php 
                                        switch($user['role']) {
                                            case 'admin': echo 'bg-gradient-to-r from-purple-400 to-purple-600 text-white'; break;
                                            case 'trainer': echo 'bg-gradient-to-r from-green-400 to-green-600 text-white'; break;
                                            case 'user': echo 'bg-gradient-to-r from-blue-400 to-blue-600 text-white'; break;
                                        }
                                        ?>">
                                        <?php 
                                        switch($user['role']) {
                                            case 'admin': echo '👑 ' . ucfirst($user['role']); break;
                                            case 'trainer': echo '🩺 ' . ucfirst($user['role']); break;
                                            case 'user': echo '👤 ' . ucfirst($user['role']); break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm">
                                    <div class="space-y-1">
                                        <div class="flex items-center text-gray-900 font-medium">
                                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                            <?php echo $user['email']; ?>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-phone mr-2 text-green-500"></i>
                                            <?php echo $user['phone'] ?: 'N/A'; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm text-gray-600">
                                    <?php if ($user['role'] === 'trainer'): ?>
                                        <div class="space-y-1">
                                            <div class="font-semibold text-green-700">
                                                <i class="fas fa-stethoscope mr-1"></i>
                                                <?php echo $user['specialization'] ?: 'General'; ?>
                                            </div>
                                            <div class="text-gray-500">
                                                <i class="fas fa-hospital mr-1"></i>
                                                <?php echo $user['department_name']; ?>
                                            </div>
                                        </div>
                                    <?php elseif ($user['role'] === 'user'): ?>
                                        <div class="space-y-1">
                                            <div class="font-medium">
                                                <i class="fas fa-venus-mars mr-1"></i>
                                                <?php echo $user['gender'] ? ucfirst($user['gender']) : 'N/A'; ?>
                                            </div>
                                            <div class="text-gray-500">
                                                <i class="fas fa-birthday-cake mr-1"></i>
                                                <?php echo $user['date_of_birth'] ? date('M j, Y', strtotime($user['date_of_birth'])) : 'N/A'; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="font-semibold text-purple-700">
                                            <i class="fas fa-crown mr-1"></i>
                                            System Administrator
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                                                class="bg-gradient-to-r from-indigo-400 to-indigo-600 text-white px-4 py-2 rounded-xl hover:from-indigo-500 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <button onclick="deleteUser(<?php echo $user['id']; ?>)" 
                                                class="bg-gradient-to-r from-red-400 to-red-600 text-white px-4 py-2 rounded-xl hover:from-red-500 hover:to-red-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleRoleFields(role) {
            const doctorFields = document.getElementById('trainer-fields');
            const patientFields = document.getElementById('user-fields');
            
            // Hide both sections first
            doctorFields.classList.add('hidden');
            patientFields.classList.add('hidden');
            
            // Show relevant section with animation
            if (role === 'trainer') {
                doctorFields.classList.remove('hidden');
                setTimeout(() => {
                    doctorFields.classList.add('animate-slide-up');
                }, 10);
            } else if (role === 'user') {
                patientFields.classList.remove('hidden');
                setTimeout(() => {
                    patientFields.classList.add('animate-slide-up');
                }, 10);
            }
        }

        function editUser(user) {
            // Create edit modal or redirect to edit page
            alert('Edit functionality - User ID: ' + user.id);
        }

        function deleteUser(userId) {
            if (confirm('⚠️ Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Add loading animation to form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            submitButton.disabled = true;
        });
    </script>
</body>
</html>