<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: admin/dashboard.php');
                        break;
                    case 'trainer':
                        header('Location: doctor/dashboard.php');
                        break;
                    case 'user':
                        header('Location: patient/dashboard.php');
                        break;
                }
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'User not found';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Beyond Trust Health Care</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #dbeafe 100%);
        }
        
        .login-container {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 25px 50px rgba(30, 58, 138, 0.1);
            border-radius: 20px;
            overflow: hidden;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e2e8f0;
        }
        
        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        
        .login-btn {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transition: all 0.3s ease;
        }
        
        .login-btn:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        
        .floating-icon {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        .heartbeat {
            animation: heartbeat 2s ease-in-out infinite;
        }
        
        @keyframes heartbeat {
            0%, 50%, 100% { transform: scale(1); }
            25%, 75% { transform: scale(1.1); }
        }
        
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            margin: 2px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .admin-badge { background: linear-gradient(45deg, #8b5cf6, #a855f7); color: white; }
        .trainer-badge { background: linear-gradient(45deg, #3b82f6, #60a5fa); color: white; }
        .user-badge { background: linear-gradient(45deg, #10b981, #34d399); color: white; }
        
        .error-message {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="login-container w-full max-w-md overflow-hidden">
        <!-- Header Section -->
        <div class="header-gradient text-white p-8 text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute top-10 left-20 text-6xl floating-icon" style="animation-delay: 0s;"><i class="fas fa-heartbeat"></i></div>
                <div class="absolute bottom-20 right-20 text-5xl floating-icon" style="animation-delay: 1s;"><i class="fas fa-user-md"></i></div>
            </div>
            <div class="relative z-10">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg heartbeat">
                    <i class="fas fa-sign-in-alt text-3xl text-indigo-600"></i>
                </div>
                <h1 class="text-3xl font-bold font-['Montserrat'] mb-2">Welcome Back</h1>
                <p class="text-indigo-100">Sign in to your healthcare account</p>
                
                <!-- Role Indicators -->
                <div class="mt-4 flex justify-center flex-wrap">
                    <span class="role-badge admin-badge">
                        <i class="fas fa-user-shield mr-1"></i>Admin
                    </span>
                    <span class="role-badge trainer-badge">
                        <i class="fas fa-user-md mr-1"></i>Trainer
                    </span>
                    <span class="role-badge user-badge">
                        <i class="fas fa-user mr-1"></i>User
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Form Section -->
        <div class="p-8">
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="error-message text-red-700 px-4 py-3 rounded-lg mb-6 flex items-start">
                    <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-envelope mr-2 text-indigo-600"></i>
                        Email Address
                    </label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required 
                               class="input-field w-full px-4 py-3 pl-12 rounded-lg"
                               placeholder="your@email.com">
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-lock mr-2 text-indigo-600"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required 
                               class="input-field w-full px-4 py-3 pl-12 rounded-lg"
                               placeholder="••••••••">
                        <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="forgot-password.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Forgot password?
                        </a>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="login-btn w-full text-white py-3 px-6 rounded-lg font-bold text-lg shadow-md">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
                
                <!-- Registration Link -->
                <div class="text-center text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign up
                    </a>
                </div>
            </form>
            
            <!-- Security Badge -->
            <div class="mt-8 text-center">
                <div class="inline-flex items-center bg-gray-100 text-gray-800 px-4 py-2 rounded-full text-sm font-medium">
                    <i class="fas fa-shield-alt text-indigo-600 mr-2"></i>
                    Secure Healthcare Login
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="fixed bottom-10 left-10 text-blue-100 text-6xl z-0">
        <i class="fas fa-heartbeat"></i>
    </div>
    <div class="fixed top-10 right-10 text-blue-100 text-5xl z-0">
        <i class="fas fa-stethoscope"></i>
    </div>
</body>
</html>