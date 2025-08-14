<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../classes/DoctorProfile.php';

requireRole('trainer');

$database = new Database();
$db = $database->getConnection();
$trainerrProfile = new DtrainerProfile($db);

$success_message = '';
$error_message = '';

// Handle profile update
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'phone' => $_POST['phone'],
        'specialization' => $_POST['specialization'],
        'bio' => $_POST['bio']
    ];
    
    if ($trainerProfile->updateProfile($_SESSION['user_id'], $data)) {
        $success_message = 'Profile updated successfully!';
        $_SESSION['first_name'] = $data['first_name'];
        $_SESSION['last_name'] = $data['last_name'];
    } else {
        $error_message = 'Failed to update profile. Please try again.';
    }
}

// Get current profile data
$profile = $trainerProfile->gettrainerProfile($_SESSION['user_id']);

// Get all departments for dropdown
$query = "SELECT * FROM departments ORDER BY name";
$stmt = $db->prepare($query);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Trainer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .input-focus { transition: all 0.3s ease; }
        .input-focus:focus { transform: scale(1.02); }
        .glass-effect { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); }
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
                            Profile Settings
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3 bg-white/60 backdrop-blur rounded-full px-4 py-2 border border-white/30">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-md text-white text-xs"></i>
                        </div>
                        <span class="text-gray-700 font-medium"> <?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-full hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success/Error Messages with Enhanced Styling -->
        <?php if ($success_message): ?>
        <div class="mb-8 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-green-800 font-medium"><?php echo $success_message; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="mb-8 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-red-800 font-medium"><?php echo $error_message; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Profile Card -->
        <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden border border-white/20 card-hover">
            <!-- Header with Gradient -->
            <div class="gradient-bg p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/10 rounded-full -ml-10 -mb-10"></div>
                <div class="relative">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center">
                            <i class="fas fa-user-md text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold">Trainer Profile</h3>
                            <p class="text-white/80 mt-1">Manage your professional information and preferences</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="POST" class="p-8">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="grid lg:grid-cols-2 gap-8">
                    <!-- Personal Information Card -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-800">Personal Information</h4>
                        </div>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" value="<?php echo $profile['first_name']; ?>" 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" value="<?php echo $profile['last_name']; ?>" 
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all" required>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="phone" value="<?php echo $profile['phone']; ?>" 
                                           class="w-full border-2 border-gray-200 rounded-xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" value="<?php echo $profile['email']; ?>" 
                                           class="w-full border-2 border-gray-200 rounded-xl pl-12 pr-4 py-3 bg-gray-50 text-gray-600" readonly>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Email cannot be modified for security reasons
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Professional Information Card -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-stethoscope text-purple-600"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-800">Professional Information</h4>
                        </div>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Department</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-building text-gray-400"></i>
                                    </div>
                                    <input type="text" value="<?php echo $profile['department_name'] ?? 'Not assigned'; ?>" 
                                           class="w-full border-2 border-gray-200 rounded-xl pl-12 pr-4 py-3 bg-gray-50 text-gray-600" readonly>
                                </div>
                                <p class="text-xs text-gray-500 mt-2 flex items-center">
                                    <i class="fas fa-lock mr-1"></i>
                                    Contact administrator to modify department assignment
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Specialization</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-certificate text-gray-400"></i>
                                    </div>
                                    <input type="text" name="specialization" value="<?php echo $profile['specialization']; ?>" 
                                           class="w-full border-2 border-gray-200 rounded-xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all"
                                           placeholder="e.g., Interventional Cardiology">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Professional Bio</label>
                                <textarea name="bio" rows="4" 
                                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent input-focus transition-all resize-none"
                                          placeholder="Share your experience, expertise, and professional background..."><?php echo $profile['bio']; ?></textarea>
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-xs text-gray-500">Describe your experience</p>
                                    <span class="text-xs text-gray-400" id="bioCount">0 characters</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end pt-8 border-t border-gray-200 mt-8">
                    <div class="flex space-x-4">
                        <button type="button" onclick="location.reload()" 
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                            <i class="fas fa-undo mr-2"></i>Reset Changes
                        </button>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium">
                            <i class="fas fa-save mr-2"></i>Update Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Account Information Card -->
        <div class="mt-8 glass-effect rounded-3xl shadow-xl overflow-hidden border border-white/20 card-hover">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Account Information</h3>
                </div>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-2xl">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-plus text-white"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Account Created</label>
                            <p class="text-lg font-bold text-gray-800"><?php echo date('F j, Y', strtotime($profile['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-2xl">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Last Updated</label>
                            <p class="text-lg font-bold text-gray-800"><?php echo date('F j, Y g:i A', strtotime($profile['updated_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Bio character counter
        const bioTextarea = document.querySelector('textarea[name="bio"]');
        const bioCounter = document.getElementById('bioCount');
        
        function updateBioCounter() {
            const length = bioTextarea.value.length;
            bioCounter.textContent = `${length} characters`;
            bioCounter.className = length > 500 ? 'text-xs text-red-500' : 'text-xs text-gray-400';
        }
        
        bioTextarea.addEventListener('input', updateBioCounter);
        updateBioCounter(); // Initialize counter
        
        // Form validation enhancement
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('border-red-300', 'bg-red-50');
                    this.classList.remove('border-gray-200');
                } else {
                    this.classList.remove('border-red-300', 'bg-red-50');
                    this.classList.add('border-gray-200');
                }
            });
        });
        
        // Smooth scroll to error messages
        if (document.querySelector('.bg-gradient-to-r.from-red-50')) {
            document.querySelector('.bg-gradient-to-r.from-red-50').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    </script>
</body>
</html>