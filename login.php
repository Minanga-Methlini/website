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
    <title>Login - Medicare System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .medical-login-bg {
            background: linear-gradient(135deg, 
                rgba(59, 130, 246, 0.9) 0%, 
                rgba(147, 51, 234, 0.9) 50%, 
                rgba(79, 172, 254, 0.9) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><pattern id="medical-pattern" patternUnits="userSpaceOnUse" width="80" height="80"><circle cx="40" cy="40" r="2" fill="white" opacity="0.1"/><path d="M35 40h10M40 35v10" stroke="white" stroke-width="1" opacity="0.1"/></pattern></defs><rect width="400" height="400" fill="url(%23medical-pattern)"/></svg>');
            background-size: cover, 120px 120px;
            background-position: center, 0 0;
        }

        .login-card {
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
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .floating-element:nth-child(2) { top: 60%; left: 80%; animation-delay: 1s; }
        .floating-element:nth-child(3) { top: 80%; left: 20%; animation-delay: 2s; }
        .floating-element:nth-child(4) { top: 30%; left: 70%; animation-delay: 3s; }
        .floating-element:nth-child(5) { top: 70%; left: 60%; animation-delay: 4s; }
        .floating-element:nth-child(6) { top: 40%; left: 90%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(5deg); }
            66% { transform: translateY(10px) rotate(-3deg); }
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
        }

        .heartbeat {
            animation: heartbeat 2s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 50%, 100% { transform: scale(1); }
            25%, 75% { transform: scale(1.1); }
        }

        .login-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }

        .admin-badge { background: linear-gradient(45deg, #8b5cf6, #a855f7); color: white; }
        .doctor-badge { background: linear-gradient(45deg, #3b82f6, #60a5fa); color: white; }
        .patient-badge { background: linear-gradient(45deg, #10b981, #34d399); color: white; }
    </style>
</head>
<body class="medical-login-bg min-h-screen flex items-center justify-center relative">
    <!-- Floating Medical Elements -->
    <div class="floating-elements">
        <div class="floating-element">
            <i class="fas fa-stethoscope text-6xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-heartbeat text-5xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-user-md text-4xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-hospital text-5xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-pills text-4xl"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-syringe text-3xl"></i>
        </div>
    </div>

    <div class="login-card p-8 rounded-3xl shadow-2xl w-full max-w-md mx-4 relative z-10">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-heartbeat text-white text-3xl heartbeat"></i>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">
                Beyond Trust
            </h1>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome Back</h2>
            <p class="text-gray-600">Sign in to access your healthcare dashboard</p>
            
            <!-- Role Indicators -->
            <div class="mt-4 flex justify-center flex-wrap">
                <span class="role-badge admin-badge">
                    <i class="fas fa-user-shield mr-1"></i>Admin
                </span>
                <span class="role-badge doctor-badge">
                    <i class="fas fa-user-md mr-1"></i>Trainer
                </span>
                <span class="role-badge patient-badge">
                    <i class="fas fa-user mr-1"></i>User
                </span>
            </div>
        </div>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="bg-gradient-to-r from-red-100 to-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl mb-6 shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span><?php echo $error; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-3 flex items-center">
                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                    Email Address
                </label>
                <div class="relative">
                    <input type="email" name="email" required 
                           class="input-focus w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 bg-white/80"
                           placeholder="Enter your email address">
                    <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-3 flex items-center">
                    <i class="fas fa-lock mr-2 text-purple-500"></i>
                    Password
                </label>
                <div class="relative">
                    <input type="password" name="password" required 
                           class="input-focus w-full px-4 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-purple-500 bg-white/80"
                           placeholder="Enter your password">
                    <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <button type="submit" 
                    class="login-btn w-full text-white py-4 px-6 rounded-xl font-bold text-lg shadow-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Sign In to Dashboard
            </button>
        </form>

        <!-- Footer Links -->
        <div class="text-center mt-8 space-y-3">
            <p class="text-gray-600">
                Don't have an account? 
                <a href="register.php" class="text-blue-600 hover:text-purple-600 font-semibold hover:underline transition-colors">
                    <i class="fas fa-user-plus mr-1"></i>Sign up
                </a>
            </p>
            <a href="index.php" class="inline-flex items-center text-blue-600 hover:text-purple-600 font-semibold hover:underline transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Home
            </a>
        </div>

        <!-- Security Badge -->
        <div class="mt-6 text-center">
            <div class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-shield-alt mr-1"></i>
                Secure Healthcare Login
            </div>
        </div>
    </div>

    <!-- Decorative Elements -->
    <div class="absolute bottom-10 left-10 text-white/10">
        <i class="fas fa-dna text-6xl"></i>
    </div>
    <div class="absolute top-10 right-10 text-white/10">
        <i class="fas fa-microscope text-5xl"></i>
    </div>
    <div class="absolute top-1/2 left-10 text-white/10">
        <i class="fas fa-ambulance text-4xl"></i>
    </div>
    <div class="absolute bottom-1/4 right-10 text-white/10">
        <i class="fas fa-clinic-medical text-5xl"></i>
    </div>
</body>
</html>


