<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../classes/DoctorProfile.php';

requireRole('trainer');

$database = new Database();
$db = $database->getConnection();

// Initialize trainer profile object correctly
$trainerProfile = new TrainerProfile($db);

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

// Get all departments for dropdown (if needed)
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
    <title>Trainer Profile Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="min-h-screen py-8 relative" 
      style="background: linear-gradient(135deg, #ec6d2dff 0%, #cc12f1ff 50%, #09f1d3ff 100%); 
             background-size: cover;
             background-position: center;">


    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/50 backdrop-blur border-b border-white/20 p-4 flex justify-between items-center">
        <a href="dashboard.php" class="flex items-center text-indigo-600 hover:text-indigo-800">
            <i class="fas fa-arrow-left mr-2"></i> Dashboard
        </a>
        <div class="flex items-center space-x-4">
            <span class="font-medium"><?php echo $_SESSION['first_name']; ?></span>
            <a href="../includes/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-full hover:bg-red-600 transition">
                Logout
            </a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 rounded shadow flex items-center">
            <i class="fas fa-check-circle text-green-600 mr-3"></i>
            <span class="text-green-800"><?php echo $success_message; ?></span>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 rounded shadow flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
            <span class="text-red-800"><?php echo $error_message; ?></span>
        </div>
        <?php endif; ?>

        <!-- Profile Form -->
        <form method="POST" class="bg-white rounded-3xl shadow-lg p-8">
            <input type="hidden" name="action" value="update_profile">

            <div class="grid lg:grid-cols-2 gap-8">
                <!-- Personal Info -->
                <div class="space-y-6">
                    <h3 class="text-xl font-semibold text-gray-800">Personal Information</h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">First Name</label>
                        <input type="text" name="first_name" value="<?php echo $profile['first_name']; ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Last Name</label>
                        <input type="text" name="last_name" value="<?php echo $profile['last_name']; ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Phone</label>
                        <input type="tel" name="phone" value="<?php echo $profile['phone']; ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" value="<?php echo $profile['email']; ?>" readonly class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                    </div>
                </div>

                <!-- Professional Info -->
                <div class="space-y-6">
                    <h3 class="text-xl font-semibold text-gray-800">Professional Information</h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Department</label>
                        <input type="text" value="<?php echo $profile['department_name'] ?? 'Not assigned'; ?>" readonly class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Specialization</label>
                        <input type="text" name="specialization" value="<?php echo $profile['specialization']; ?>" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500" placeholder="e.g., Cardiology">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Bio</label>
                        <textarea name="bio" rows="4" class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500"><?php echo $profile['bio']; ?></textarea>
                        <span class="text-xs text-gray-400 mt-1" id="bioCount">0 characters</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-4">
                <button type="reset" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition">Reset</button>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">Update Profile</button>
            </div>
        </form>
    </div>

    <script>
        // Bio character counter
        const bioTextarea = document.querySelector('textarea[name="bio"]');
        const bioCounter = document.getElementById('bioCount');
        bioTextarea.addEventListener('input', () => {
            const length = bioTextarea.value.length;
            bioCounter.textContent = `${length} characters`;
            bioCounter.className = length > 500 ? 'text-xs text-red-500' : 'text-xs text-gray-400';
        });
        bioTextarea.dispatchEvent(new Event('input'));
    </script>
</body>
</html>
