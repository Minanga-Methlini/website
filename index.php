<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyond Trust - Healthcare Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    },
                    colors: {
                        primary: '#4f46e5',
                        secondary: '#059669',
                        accent: '#f97316',
                        medical: {
                            blue: '#4338ca',
                            green: '#047857',
                            purple: '#7c3aed',
                            orange: '#ea580c'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 1.2s ease-in-out',
                        'slide-right': 'slideRight 0.8s ease-out',
                        'scale-in': 'scaleIn 0.7s ease-out',
                        'pulse-slow': 'pulseSlow 3s infinite',
                        'float-vertical': 'floatVertical 5s ease-in-out infinite'
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
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes pulseSlow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes floatVertical {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .glass-effect {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .gradient-text {
            background: linear-gradient(135deg, #4f46e5, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-bg {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(5, 150, 105, 0.08));
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
        }
        .stat-counter {
            transition: all 0.4s ease;
        }
        .medical-pattern {
            background-image: 
                radial-gradient(circle at 10px 10px, rgba(79, 70, 229, 0.1) 1.5px, transparent 0),
                radial-gradient(circle at 90px 90px, rgba(5, 150, 105, 0.1) 1.5px, transparent 0);
            background-size: 100px 100px;
        }
        .rotating-icon {
            animation: rotate 8s linear infinite;
        }
    </style>
</head>
<body class="min-h-screen py-8 relative" 
      style="background: linear-gradient(135deg, #f3bbc7ff 0%, #f5dd9cff 50%, #c7eea8ff 100%), url('images/background.jpg'); 
             background-size: cover;
             background-position: center;">


    <!-- Enhanced Navigation -->
    <nav class="glass-effect shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3 animate-fade-in">
                    <div class="relative">
                        <i class="fas fa-heartbeat text-primary text-3xl animate-pulse-slow"></i>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-accent rounded-full animate-ping"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold gradient-text font-serif">Beyond Trust</h1>
                        <p class="text-xs text-gray-600 font-medium">Compassionate Healthcare</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="bg-primary text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg font-medium flex items-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="hidden sm:inline">Login</span>
                    </a>
                    <a href="register.php" class="bg-accent text-white px-5 py-2 rounded-lg hover:bg-orange-600 transition-all duration-300 shadow-md hover:shadow-lg font-medium flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span class="hidden sm:inline">Register</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Enhanced Hero Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-20">
            <!-- Left Content -->
            <div class="relative animate-fade-in">
                <div class="relative">
                    <img src="images/homepg.jpg"
                         alt="Modern Healthcare Technology" 
                         class="rounded-2xl shadow-2xl animate-float-vertical">
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-accent rounded-full animate-ping"></div>
                            <span class="text-sm font-semibold text-gray-700">Online Now</span>
                        </div>
                    </div>
                    <div class="absolute -top-6 -right-6 bg-primary text-white p-4 rounded-xl shadow-xl">
                        <i class="fas fa-stethoscope text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="animate-slide-right">
                <div class="mb-6">
                    <span class="bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold inline-flex items-center space-x-2">
                        <i class="fas fa-award text-accent"></i>
                        <span>Trusted by 1000+ Clients</span>
                    </span>
                </div>
                <h2 class="text-5xl font-bold text-gray-800 mb-6 leading-tight font-serif">
                    Personalized 
                    <span class="gradient-text">Healthcare</span>
                </h2>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    Beyond Trust Health Care Services — Your health, our priority. Book, connect, and care, anytime, anywhere.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="register.php" class="bg-primary text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                        <i class="fas fa-rocket"></i>
                        <span>Get Started Today</span>
                    </a>
                    <a href="#features" class="border-2 border-primary text-primary px-6 py-3 rounded-lg text-lg font-semibold hover:bg-primary/10 transition-all duration-300 flex items-center justify-center space-x-2">
                        <i class="fas fa-play"></i>
                        <span>Learn More</span>
                    </a>
                </div>
                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-accent"></i>
                        <span>HIPAA Compliant</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-lock text-accent"></i>
                        <span>Secure & Private</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-accent"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Features Grid -->
        <div id="features" class="mb-20">
            <div class="text-center mb-16 animate-slide-right">
                <h3 class="text-4xl font-bold text-gray-800 mb-4 font-serif">Comprehensive Healthcare Solutions</h3>
                <p class="text-lg text-gray-600">Designed for every stakeholder in the healthcare ecosystem</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Trainer Card -->
                <div class="bg-white p-6 rounded-xl shadow-lg card-hover animate-scale-in relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/10 to-primary/5 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-primary to-indigo-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 shadow-md">
                            <i class="fas fa-user-md text-white text-2xl"></i>
                        </div>
                        <img src="images/fitness.jpg" 
                             alt="Trainer Dashboard" 
                             class="w-full h-48 object-cover rounded-lg mb-6 shadow-md">
                        <h4 class="text-xl font-bold text-gray-800 mb-4 font-serif">For Trainers</h4>
                        <p class="text-gray-600 mb-4">Simplify healthcare with easy scheduling, smooth client management, and instant access to health records — giving you more time to care for the people who matter most.</p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Smart Scheduling System</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Digital Health Records</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Client Communication Tools</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Patient Card -->
                <div class="bg-white p-6 rounded-xl shadow-lg card-hover animate-scale-in relative overflow-hidden" style="animation-delay: 0.1s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-accent/10 to-accent/5 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-accent to-orange-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 shadow-md">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <img src="images/services.jpg" 
                             alt="Patient Experience" 
                             class="w-full h-48 object-cover rounded-lg mb-6 shadow-md">
                        <h4 class="text-xl font-bold text-gray-800 mb-4 font-serif">For Patients</h4>
                        <p class="text-gray-600 mb-4">Take control of your health journey with easy appointment booking, health tracking, and direct communication with your healthcare providers.</p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Easy Online Booking</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Health History Tracking</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Secure Messaging</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Admin Card -->
                <div class="bg-white p-6 rounded-xl shadow-lg card-hover animate-scale-in relative overflow-hidden" style="animation-delay: 0.2s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-secondary to-green-600 w-16 h-16 rounded-xl flex items-center justify-center mx-auto mb-6 shadow-md">
                            <i class="fas fa-cog text-white text-2xl"></i>
                        </div>
                        <img src="images/accountant-office.jpg" 
                             alt="Admin Dashboard" 
                             class="w-full h-48 object-cover rounded-lg mb-6 shadow-md">
                        <h4 class="text-xl font-bold text-gray-800 mb-4 font-serif">For Admins</h4>
                        <p class="text-gray-600 mb-4">Complete system oversight with powerful analytics, user management, and comprehensive reporting tools to optimize healthcare operations.</p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Advanced Analytics</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>User Management</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-accent"></i>
                                <span>Detailed Reporting</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Statistics Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-emerald-600 rounded-2xl shadow-xl p-10 relative overflow-hidden animate-slide-right">
            <div class="absolute inset-0 bg-white/10 backdrop-blur-md"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24 rotating-icon"></div>
            
            <div class="relative">
                <div class="text-center mb-12">
                    <h3 class="text-4xl font-bold text-white mb-4 font-serif">Trusted by Healthcare Professionals</h3>
                    <p class="text-lg text-indigo-100">Join thousands who have transformed their healthcare experience</p>
                </div>

                <div class="grid md:grid-cols-4 gap-6 text-center">
                    <div class="stat-counter">
                        <div class="text-4xl font-bold text-white mb-2">1000+</div>
                        <div class="text-indigo-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-users"></i>
                            <span>Happy Clients</span>
                        </div>
                        <div class="mt-2 w-12 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 95%"></div>
                        </div>
                    </div>
                    <div class="stat-counter" style="animation-delay: 0.1s">
                        <div class="text-4xl font-bold text-white mb-2">50+</div>
                        <div class="text-indigo-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-user-md"></i>
                            <span>Expert Trainers</span>
                        </div>
                        <div class="mt-2 w-12 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 87%"></div>
                        </div>
                    </div>
                    <div class="stat-counter" style="animation-delay: 0.2s">
                        <div class="text-4xl font-bold text-white mb-2">5000+</div>
                        <div class="text-indigo-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-calendar-check"></i>
                            <span>Appointments</span>
                        </div>
                        <div class="mt-2 w-12 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 92%"></div>
                        </div>
                    </div>
                    <div class="stat-counter" style="animation-delay: 0.3s">
                        <div class="text-4xl font-bold text-white mb-2">24/7</div>
                        <div class="text-indigo-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-headset"></i>
                            <span>Support</span>
                        </div>
                        <div class="mt-2 w-12 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="text-center py-16 animate-fade-in">
            <div class="max-w-3xl mx-auto">
                <h3 class="text-4xl font-bold text-gray-800 mb-6 font-serif">Ready to Transform Your Healthcare Experience?</h3>
                <p class="text-lg text-gray-600 mb-8">Join thousands of clients and healthcare providers who trust our platform for their medical needs.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="bg-primary text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center justify-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Account</span>
                    </a>
                    <a href="login.php" class="bg-accent text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-orange-600 transition-all duration-300 shadow-lg hover:shadow-xl inline-flex items-center justify-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Footer -->
    <footer class="bg-gradient-to-r from-gray-900 to-gray-800 text-white py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32 rotating-icon"></div>
        
        <div class="max-w-7xl mx-auto px-4 relative">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-heartbeat text-primary text-2xl"></i>
                        <h4 class="text-xl font-bold font-serif">Beyond Trust</h4>
                    </div>
                    <p class="text-gray-300 mb-4">Making healthcare accessible, efficient, and client-centered through innovative technology solutions.</p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary transition-all duration-300">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary transition-all duration-300">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCttfJcDqkWJAORC6ZJWiIIg" class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary transition-all duration-300">
                            <i class="fab fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg font-serif">Quick Links</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-primary transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Services</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Trainers</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg font-serif">Support</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-primary transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg font-serif">Contact Info</h4>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-phone text-primary"></i>
                            <span>+1 (999) 987-24567</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-envelope text-primary"></i>
                            <span>info@beyondm.com</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <span>1A,Main road, Malabe</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8 text-center">
                <p class="text-gray-400">
                    &copy; 2025 Beyond Trust Healthcare System. All rights reserved. 
                    <span class="text-primary">Made with ❤️ for better healthcare</span>
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animate statistics on scroll
        const observerOptions = {
            threshold: 0.3,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    const counters = entry.target.querySelectorAll('.stat-counter');
                    counters.forEach((counter, index) => {
                        setTimeout(() => {
                            counter.style.transform = 'translateY(0)';
                            counter.style.opacity = '1';
                        }, index * 100);
                    });
                }
            });
        }, observerOptions);

        // Observe statistics section
        const statsSection = document.querySelector('.bg-gradient-to-r.from-indigo-600.to-emerald-600');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Add loading animation
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });

        // Parallax effect for hero image
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.animate-float-vertical');
            if (parallax) {
                const speed = scrolled * -0.15;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });
    </script>
</body>
</html>