<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

requireRole('admin');

$database = new Database();
$db = $database->getConnection();

// Get comprehensive statistics
$stats = [];

// User statistics
$query = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$stmt = $db->prepare($query);
$stmt->execute();
$user_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Appointment statistics
$query = "SELECT status, COUNT(*) as count FROM appointments GROUP BY status";
$stmt = $db->prepare($query);
$stmt->execute();
$appointment_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Monthly appointment trends (18 months for better visualization)
$query = "SELECT DATE_FORMAT(appointment_date, '%Y-%m') as month, 
                 DATE_FORMAT(appointment_date, '%M %Y') as month_name,
                 COUNT(*) as count 
          FROM appointments 
          WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 18 MONTH)
          GROUP BY month, month_name
          ORDER BY month";
$stmt = $db->prepare($query);
$stmt->execute();
$monthly_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// User registration trends
$query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                 DATE_FORMAT(created_at, '%M %Y') as month_name,
                 COUNT(*) as count 
          FROM users 
          WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 18 MONTH)
          GROUP BY month, month_name
          ORDER BY month";
$stmt = $db->prepare($query);
$stmt->execute();
$user_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Appointment status trends (12 months)
$query = "SELECT DATE_FORMAT(appointment_date, '%Y-%m') as month, 
                 DATE_FORMAT(appointment_date, '%M %Y') as month_name,
                 status,
                 COUNT(*) as count 
          FROM appointments 
          WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
          GROUP BY month, month_name, status 
          ORDER BY month, status";
$stmt = $db->prepare($query);
$stmt->execute();
$status_trends = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Department statistics with more details
$query = "SELECT d.name, 
                 COUNT(DISTINCT dp.id) as trainer_count, 
                 COUNT(DISTINCT a.id) as appointment_count,
                 COUNT(DISTINCT CASE WHEN a.status = 'completed' THEN a.id END) as completed_appointments,
                 COUNT(DISTINCT CASE WHEN a.status = 'scheduled' THEN a.id END) as scheduled_appointments
          FROM departments d
          LEFT JOIN trainer_profiles dp ON d.id = dp.department_id
          LEFT JOIN users u ON dp.user_id = u.id AND u.role = 'trainer'
          LEFT JOIN appointments a ON u.id = a.trainer_id
          GROUP BY d.id, d.name
          ORDER BY appointment_count DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$dept_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Summary statistics for cards
$query = "SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'user') as total_users,
    (SELECT COUNT(*) FROM users WHERE role = 'trainer') as total_trainers,
    (SELECT COUNT(*) FROM appointments) as total_appointments,
    (SELECT COUNT(*) FROM appointments WHERE status = 'completed') as completed_appointments,
    (SELECT COUNT(*) FROM appointments WHERE DATE(appointment_date) = CURDATE()) as today_appointments,
    (SELECT COUNT(*) FROM users WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)) as new_users_month";
$stmt = $db->prepare($query);
$stmt->execute();
$summary_stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Reports - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8B5CF6',
                        secondary: '#06B6D4',
                        accent: '#F59E0B',
                        success: '#10B981',
                        danger: '#EF4444',
                        dark: '#1F2937'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'scale-in': 'scaleIn 0.4s ease-out'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
        .stat-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-left: 4px solid;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen">
    <!-- Enhanced Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-primary hover:text-purple-800 transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-arrow-left text-lg"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <div class="h-8 w-px bg-gray-300"></div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        System Reports
                    </h1>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fas fa-user-shield text-primary"></i>
                        <span class="font-medium">Admin: <?php echo $_SESSION['first_name']; ?></span>
                    </div>
                    <a href="../includes/logout.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-2.5 rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8 space-y-8">
        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <!-- Total Patients Card -->
            <div class="stat-card border-primary p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover animate-slide-up">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                        <p class="text-2xl font-bold text-primary"><?php echo number_format($summary_stats['total_users']); ?></p>
                    </div>
                    <div class="p-3 bg-primary/10 rounded-lg">
                        <i class="fas fa-users text-primary text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Doctors Card -->
            <div class="stat-card border-secondary p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Trainers</p>
                        <p class="text-2xl font-bold text-secondary"><?php echo number_format($summary_stats['total_trainers']); ?></p>
                    </div>
                    <div class="p-3 bg-secondary/10 rounded-lg">
                        <i class="fas fa-user-md text-secondary text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Appointments Card -->
            <div class="stat-card border-success p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Appointments</p>
                        <p class="text-2xl font-bold text-success"><?php echo number_format($summary_stats['total_appointments']); ?></p>
                    </div>
                    <div class="p-3 bg-success/10 rounded-lg">
                        <i class="fas fa-calendar-check text-success text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Appointments Card -->
            <div class="stat-card border-accent p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover animate-slide-up" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                        <p class="text-2xl font-bold text-accent"><?php echo number_format($summary_stats['completed_appointments']); ?></p>
                    </div>
                    <div class="p-3 bg-accent/10 rounded-lg">
                        <i class="fas fa-check-circle text-accent text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments Card -->
            <div class="stat-card border-blue-500 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover animate-slide-up" style="animation-delay: 0.4s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Today's Apps</p>
                        <p class="text-2xl font-bold text-blue-500"><?php echo number_format($summary_stats['today_appointments']); ?></p>
                    </div>
                    <div class="p-3 bg-blue-500/10 rounded-lg">
                        <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- New Users This Month Card -->
            <div class="stat-card border-purple-500 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 card-hover animate-slide-up" style="animation-delay: 0.5s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">New Users</p>
                        <p class="text-2xl font-bold text-purple-500"><?php echo number_format($summary_stats['new_users_month']); ?></p>
                    </div>
                    <div class="p-3 bg-purple-500/10 rounded-lg">
                        <i class="fas fa-user-plus text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

         <!-- Enhanced Department Statistics -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl animate-scale-in" style="animation-delay: 0.3s">
            <div class="p-8 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">Department Performance</h3>
                        <p class="text-gray-500 text-sm mt-1">Comprehensive department analytics</p>
                    </div>
                    <div class="p-3 bg-accent/10 rounded-lg">
                        <i class="fas fa-building text-accent text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Trainers</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Appointments</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Scheduled</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $max_appointments = max(1, max(array_column($dept_stats, 'appointment_count')));
                        foreach ($dept_stats as $index => $dept): 
                            $completion_rate = $dept['appointment_count'] > 0 ? round(($dept['completed_appointments'] / $dept['appointment_count']) * 100) : 0;
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200" style="animation: slideUp 0.6s ease-out; animation-delay: <?php echo $index * 0.1; ?>s; animation-fill-mode: both;">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                                                <i class="fas fa-hospital text-white text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($dept['name']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-md text-secondary mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-900"><?php echo $dept['trainer_count']; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-900"><?php echo number_format($dept['appointment_count']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        <span class="text-sm font-semibold text-success"><?php echo number_format($dept['completed_appointments']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-accent mr-2"></i>
                                        <span class="text-sm font-semibold text-accent"><?php echo number_format($dept['scheduled_appointments']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 rounded-full h-3 shadow-inner">
                                                <div class="bg-gradient-to-r from-primary to-secondary h-3 rounded-full transition-all duration-1000 ease-out" 
                                                     style="width: <?php echo min(100, ($dept['appointment_count'] / $max_appointments) * 100); ?>%"></div>
                                            </div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-700">
                                            <?php echo $completion_rate; ?>%
                                        </div>
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
        // Chart.js default configuration
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.padding = 20;

        // Custom gradient function
        function createGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        // Monthly Appointment Trends Chart
        const appointmentTrendsCtx = document.getElementById('appointmentTrendsChart').getContext('2d');
        const appointmentGradient = createGradient(appointmentTrendsCtx, 'rgba(139, 92, 246, 0.3)', 'rgba(139, 92, 246, 0.05)');
        
        new Chart(appointmentTrendsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_trends, 'month_name')); ?>,
                datasets: [{
                    label: 'Total Appointments',
                    data: <?php echo json_encode(array_column($monthly_trends, 'count')); ?>,
                    borderColor: '#8B5CF6',
                    backgroundColor: appointmentGradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8B5CF6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#8B5CF6',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#8B5CF6',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // User Registration Trends Chart
        const userTrendsCtx = document.getElementById('userTrendsChart').getContext('2d');
        const userGradient = createGradient(userTrendsCtx, 'rgba(16, 185, 129, 0.3)', 'rgba(16, 185, 129, 0.05)');
        
        new Chart(userTrendsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($user_trends, 'month_name')); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_column($user_trends, 'count')); ?>,
                    borderColor: '#10B981',
                    backgroundColor: userGradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#10B981',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#10B981',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart',
                    delay: 500
                }
            }
        });

        // Appointment Status Trends Chart
        const statusTrendsCtx = document.getElementById('statusTrendsChart').getContext('2d');
        
        // Process status trends data for multiple lines
        const statusData = <?php echo json_encode($status_trends); ?>;
        const months = [...new Set(statusData.map(item => item.month_name))];
        const statuses = [...new Set(statusData.map(item => item.status))];
        
        const statusColors = {
            'scheduled': { border: '#3B82F6', bg: 'rgba(59, 130, 246, 0.1)' },
            'completed': { border: '#10B981', bg: 'rgba(16, 185, 129, 0.1)' },
            'cancelled': { border: '#EF4444', bg: 'rgba(239, 68, 68, 0.1)' },
            'no_show': { border: '#F59E0B', bg: 'rgba(245, 158, 11, 0.1)' }
        };

        const datasets = statuses.map((status, index) => {
            const data = months.map(month => {
                const found = statusData.find(item => item.month_name === month && item.status === status);
                return found ? found.count : 0;
            });
            
            const color = statusColors[status] || { border: '#6B7280', bg: 'rgba(107, 114, 128, 0.1)' };
            
            return {
                label: status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' '),
                data: data,
                borderColor: color.border,
                backgroundColor: color.bg,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: color.border,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: color.border,
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 3
            };
        });

        new Chart(statusTrendsCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart',
                    delay: 1000
                }
            }
        });

        // Add smooth scroll behavior for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add loading animation complete callback
        window.addEventListener('load', function() {
            // Trigger progress bar animations after page load
            setTimeout(() => {
                document.querySelectorAll('.bg-gradient-to-r.from-primary.to-secondary').forEach(bar => {
                    bar.style.transform = 'scaleX(1)';
                });
            }, 1500);
        });

        // Add hover effects for cards
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 10px 25px -3px rgba(0, 0, 0, 0.1)';
            });
        });

        // Add dynamic chart resizing
        window.addEventListener('resize', function() {
            Chart.instances.forEach(chart => {
                chart.resize();
            });
        });

        // Add print functionality
        function printReports() {
            window.print();
        }

        // Add export functionality (if needed)
        function exportToCSV() {
            // Implementation for CSV export
            console.log('Export to CSV functionality can be added here');
        }
    </script>

    <!-- Print Styles -->
    <style media="print">
        body { 
            background: white !important; 
        }
        .gradient-bg, .glass-effect {
            background: white !important;
            backdrop-filter: none !important;
        }
        .shadow-xl, .shadow-lg {
            box-shadow: none !important;
        }
        nav {
            display: none !important;
        }
        .card-hover:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
</body>
</html>