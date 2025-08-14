<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitizeInput($_POST['role']);
    $phone = sanitizeInput($_POST['phone']);
    // Remove this line if not using date_of_birth
    // $date_of_birth = sanitizeInput($_POST['date_of_birth']);
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!in_array($role, ['doctor', 'patient'])) {
        $error = 'Invalid role selected';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if username or email already exists
        $check_query = "SELECT * FROM users WHERE username = :username OR email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            // Hash password and insert user
            $hashed_password = hashPassword($password);
            $created_at = date('Y-m-d H:i:s');
            
            // Remove date_of_birth from the query
            $insert_query = "INSERT INTO users (first_name, last_name, username, email, password, role, phone, created_at) VALUES (:first_name, :last_name, :username, :email, :password, :role, :phone, :created_at)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->bindParam(':first_name', $first_name);
            $insert_stmt->bindParam(':last_name', $last_name);
            $insert_stmt->bindParam(':username', $username);
            // Remove this line if not using date_of_birth
            // $insert_stmt->bindParam(':date_of_birth', $date_of_birth);
            $insert_stmt->bindParam(':email', $email);
            $insert_stmt->bindParam(':password', $hashed_password);
            $insert_stmt->bindParam(':role', $role);
            $insert_stmt->bindParam(':phone', $phone);
            $insert_stmt->bindParam(':created_at', $created_at);
            
            if ($insert_stmt->execute()) {
                $success = 'Registration successful! You can now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Medicare System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .medical-register-bg {
            background: linear-gradient(135deg, 
                rgba(16, 185, 129, 0.9) 0%, 
                rgba(59, 130, 246, 0.9) 50%, 
                rgba(139, 92, 246, 0.9) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><pattern id="medical-pattern" patternUnits="userSpaceOnUse" width="80" height="80"><circle cx="40" cy="40" r="2" fill="white" opacity="0.1"/><path d="M35 40h10M40 35v10" stroke="white" stroke-width="1" opacity="0.1"/></pattern></defs><rect width="400" height="400" fill="url(%23medical-pattern)"/></svg>');
            background-size: cover, 120px 120px;
            background-position: center, 0 0;
        }

        .register-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .floating-element {
            position: absolute;
            color: rgba(255, 255, 255, 0.1);
            animation: float 8s ease-in-out infinite;
        }

        .floating-element:nth-child(1) { top: 10%; left: 15%; animation-delay: 0s; }
        .floating-element:nth-child(2) { top: 70%; left: 85%; animation-delay: 1.5s; }
        .floating-element:nth-child(3) { top: 90%; left: 10%; animation-delay: 3s; }
        .floating-element:nth-child(4) { top: 20%; left: 80%; animation-delay: 4.5s; }
        .floating-element:nth-child(5) { top: 60%; left: 20%; animation-delay: 6s; }
        .floating-element:nth-child(6) { top: 30%; left: 90%; animation-delay: 7.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-30px) rotate(8deg); }
            66% { transform: translateY(15px) rotate(-5deg); }
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        }

        .pulse-icon {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }

        .register-btn {
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }

        .register-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #2563eb 50%, #7c3aed 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(16, 185, 129, 0.3);
        }

        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .role-option {
            position: relative;
            cursor: pointer;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .role-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .role-option input:checked + .role-content {
            color: white;
        }

        .role-option input:checked ~ .role-bg {
            opacity: 1;
        }

        .role-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 0.75rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .doctor-bg { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
        .patient-bg { background: linear-gradient(135deg, #10b981, #34d399); }

        .role-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="medical-register-bg min-h-screen py-8 relative">
    <!-- Floating Medical Elements -->
    <div class="floating-elements">
        <div class="floating-element">
            <i class="fas fa-user-plus text-6xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-clipboard-list text-5xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-user-md text-4xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-hospital-user text-5xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-id-card text-4xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-file-medical text-3xl"></i>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4">
        <div class="register-card p-8 rounded-3xl shadow-2xl relative z-10">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="bg-gradient-to-r from-green-500 via-blue-500 to-purple-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-user-plus text-white text-3xl pulse-icon"></i>
                </div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 via-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                    Join Medicare System
                </h1>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Create Your Account</h2>
                <p class="text-gray-600">Register to access comprehensive healthcare services</p>
            </div>

            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="bg-gradient-to-r from-green-100 to-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span><?php echo $success; ?></span>
                    </div>
                    <div class="mt-2">
                        <a href="login.php" class="text-green-800 font-semibold hover:underline">
                            <i class="fas fa-sign-in-alt mr-1"></i>Go to Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="bg-gradient-to-r from-red-100 to-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span><?php echo $error; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" class="space-y-6">
                <!-- Personal Information Section -->
                <div class="form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                First Name *
                            </label>
                            <input type="text" name="first_name" required 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 bg-white/80"
                                   placeholder="Enter first name">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Last Name *
                            </label>
                            <input type="text" name="last_name" required 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 bg-white/80"
                                   placeholder="Enter last name">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Username *
                            </label>
                            <input type="text" name="username" required 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 bg-white/80"
                                   placeholder="Choose username">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Date of Birth
                            </label>
                            <input type="date" name="date_of_birth" 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-green-500 bg-white/80">
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-envelope mr-2 text-purple-500"></i>
                        Contact Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Email Address *
                            </label>
                            <input type="email" name="email" required 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 bg-white/80"
                                   placeholder="Enter email address">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Phone Number
                            </label>
                            <input type="tel" name="phone" 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 bg-white/80"
                                   placeholder="Enter phone number">
                        </div>
                    </div>
                </div>

                <!-- Account Security Section -->
                <div class="form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lock mr-2 text-red-500"></i>
                        Account Security
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Password *
                            </label>
                            <input type="password" name="password" required 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 bg-white/80"
                                   placeholder="Enter password">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Confirm Password *
                            </label>
                            <input type="password" name="confirm_password" required 
                                   class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 bg-white/80"
                                   placeholder="Confirm password">
                        </div>
                    </div>
                </div>

                <!-- Role Selection Section -->
                <div class="form-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-green-500"></i>
                        Account Type *
                    </h3>
                    
                    <div class="role-selector">
                        <div class="role-option">
                            <input type="radio" name="role" value="doctor" id="doctor" class="sr-only">
                            <div class="role-bg doctor-bg"></div>
                            <label for="doctor" class="role-content cursor-pointer">
                                <i class="fas fa-user-md text-2xl mb-2 block"></i>
                                <div class="font-semibold">Doctor</div>
                                <div class="text-sm opacity-80">Healthcare Provider</div>
                            </label>
                        </div>
                        
                        <div class="role-option">
                            <input type="radio" name="role" value="patient" id="patient" class="sr-only">
                            <div class="role-bg patient-bg"></div>
                            <label for="patient" class="role-content cursor-pointer">
                                <i class="fas fa-user text-2xl mb-2 block"></i>
                                <div class="font-semibold">Patient</div>
                                <div class="text-sm opacity-80">Healthcare Seeker</div>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" 
                        class="register-btn w-full text-white py-4 px-6 rounded-xl font-bold text-lg shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </form>

            <!-- Footer Links -->
            <div class="text-center mt-8 space-y-3">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="login.php" class="text-blue-600 hover:text-purple-600 font-semibold hover:underline transition-colors">
                        <i class="fas fa-sign-in-alt mr-1"></i>Sign in
                    </a>
                </p>
                <a href="index.php" class="inline-flex items-center text-green-600 hover:text-blue-600 font-semibold hover:underline transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Home
                </a>
            </div>

            <!-- Security Badge -->
            <div class="mt-6 text-center">
                <div class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secure Registration Process
                </div>
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute bottom-10 left-10 text-white/10">
        <i class="fas fa-hospital text-6xl"></i>
    </div>
    <div class="absolute top-10 right-10 text-white/10">
        <i class="fas fa-notes-medical text-5xl"></i>
    </div>
    <div class="absolute top-1/2 left-10 text-white/10">
        <i class="fas fa-hand-holding-medical text-4xl"></i>
    </div>
    <div class="absolute bottom-1/4 right-10 text-white/10">
        <i class="fas fa-medical-kit text-5xl"></i>
    </div>
</body>
</html>