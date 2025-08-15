<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beyond Trust - Healthcare Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#10b981',
                        accent: '#f59e0b',
                        medical: {
                            blue: '#1e40af',
                            green: '#059669',
                            purple: '#7c3aed',
                            orange: '#ea580c'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.6s ease-out',
                        'bounce-gentle': 'bounceGentle 2s infinite',
                        'float': 'float 6s ease-in-out infinite'
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
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes bounceGentle {
            0%, 100% { transform: translateY(-5px); }
            50% { transform: translateY(5px); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .gradient-text {
            background: linear-gradient(135deg, #2563eb, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-bg {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(16, 185, 129, 0.1));
        }
        .card-hover {
            transition: all 0.4s ease;
        }
        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        .stat-counter {
            transition: all 0.3s ease;
        }
        .medical-pattern {
            background-image: 
                radial-gradient(circle at 25px 25px, rgba(37, 99, 235, 0.1) 2px, transparent 0),
                radial-gradient(circle at 75px 75px, rgba(16, 185, 129, 0.1) 2px, transparent 0);
            background-size: 100px 100px;
        }
    </style>
</head>

    <!-- Enhanced Navigation -->
    <nav class="glass-effect shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3 animate-fade-in">
                    <div class="relative">
                        <i class="fas fa-heartbeat text-primary text-3xl animate-bounce-gentle"></i>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-secondary rounded-full animate-ping"></div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold gradient-text">Beyond Trust</h1>
                        <p class="text-xs text-gray-500 font-medium">Healthcare Services</p>
                    </div>
                </div>
                <div>
<nav class="space-x-6 text-white">
        <a href="index.php" class="text-teal-300 font-semibold">Home</a>
        <a href="services.php" class="hover:text-teal-300 hover:scale-105 transition duration-300">Services</a>
        <a href="booking.html" class="hover:text-teal-300 hover:scale-105 transition duration-300">Book Now</a>
        <a href="website/contacts.html" class="hover:text-teal-300 hover:scale-105 transition duration-300">Contact Us</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="bg-primary text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl font-medium flex items-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="register.php" class="bg-secondary text-white px-6 py-2.5 rounded-xl hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl font-medium flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Enhanced Hero Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-20">
            <!-- Left Content -->
            <div class="relative animate-fade-in" styl="animation-delay: 0.3s">
                <div class="relative">
                    <img src="images/homepg.jpg"
                         alt="Modern Healthcare Technology" 
                         class="rounded-2xl shadow-2xl animate-float">
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-secondary rounded-full animate-ping"></div>
                            <span class="text-sm font-semibold text-gray-700">Online Now</span>
                        </div>
                    </div>
                    <div class="absolute -top-6 -right-6 bg-primary text-white p-4 rounded-xl shadow-xl">
                        <i class="fas fa-user-md text-2xl"></i>
                    </div>
                </div>
            </div>

        
            <!-- Right Content -->
            
<div class="animate-slide-up">
                <div class="mb-6">
                    <span class="bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold inline-flex items-center space-x-2">
                        <i class="fas fa-star text-accent"></i>
                        <span>Trusted by 1000+ Clients</span>
                    </span>
                </div>
                <h2 class="text-6xl font-bold text-gray-800 mb-6 leading-tight">
                    Healthcare 
                    <span class="gradient-text">Made Simple</span>
                </h2>
                <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                    Beyond Trust Health Care Services — Your health, our priority. Book, connect, and care, anytime, anywhere.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <a href="register.php" class="bg-primary text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-blue-700 transition-all duration-300 shadow-xl hover:shadow-2xl flex items-center justify-center space-x-2">
                        <i class="fas fa-rocket"></i>
                        <span>Get Started Today</span>
                    </a>
                    <a href="#features" class="border-2 border-primary text-primary px-8 py-4 rounded-xl text-lg font-semibold hover:bg-primary hover:text-white transition-all duration-300 flex items-center justify-center space-x-2">
                        <i class="fas fa-play"></i>
                        <span>Learn More</span>
                    </a>
                </div>
                <div class="flex items-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-shield-alt text-secondary"></i>
                        <span>HIPAA Compliant</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-lock text-secondary"></i>
                        <span>Secure & Private</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-secondary"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
             </div>
        </div>

        <!-- Enhanced Features Grid -->
        <div id="features" class="mb-20">
            <div class="text-center mb-16 animate-slide-up">
                <h3 class="text-4xl font-bold text-gray-800 mb-4">Comprehensive Healthcare Solutions</h3>
                <p class="text-xl text-gray-600">Designed for every stakeholder in the healthcare ecosystem</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Doctor Card -->
                <div class="bg-white p-8 rounded-2xl shadow-xl card-hover animate-scale-in relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary/10 to-primary/5 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-primary to-blue-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-user-md text-white text-3xl"></i>
                        </div>
                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Doctor Dashboard" 
                             class="w-full h-48 object-cover rounded-xl mb-6 shadow-lg">
                        <h4 class="text-2xl font-bold text-gray-800 mb-4">For Trainers</h4>
                        <p class="text-gray-600 mb-6">Simplify healthcare with easy scheduling, smooth client management, and instant access to health records — giving you more time to care for the people who matter most.</p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Smart Scheduling System</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Digital Health Records</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Client Communication Tools</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Patient Card -->
                <div class="bg-white p-8 rounded-2xl shadow-xl card-hover animate-scale-in relative overflow-hidden" style="animation-delay: 0.1s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-secondary to-green-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-user text-white text-3xl"></i>
                        </div>
                        <img src="https://img.freepik.com/free-photo/older-man-wheelchair-smiles-nurse-assistant-she-hands-him-glass-water_496169-2835.jpg?t=st=1754991801~exp=1754995401~hmac=ed40ee2c342e5b5e1c85cc6c64eafcdbe3374f19f7d66861c09fb9147fca9e65&w=1480" 
                             alt="Patient Experience" 
                             class="w-full h-48 object-cover rounded-xl mb-6 shadow-lg">
                        <h4 class="text-2xl font-bold text-gray-800 mb-4">For Patients</h4>
                        <p class="text-gray-600 mb-6">Take control of your health journey with easy appointment booking, health tracking, and direct communication with your healthcare providers.</p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Easy Online Booking</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Health History Tracking</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Secure Messaging</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Admin Card -->
                <div class="bg-white p-8 rounded-2xl shadow-xl card-hover animate-scale-in relative overflow-hidden" style="animation-delay: 0.2s">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-purple-500/5 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-cog text-white text-3xl"></i>
                        </div>
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Admin Dashboard" 
                             class="w-full h-48 object-cover rounded-xl mb-6 shadow-lg">
                        <h4 class="text-2xl font-bold text-gray-800 mb-4">For Admins</h4>
                        <p class="text-gray-600 mb-6">Complete system oversight with powerful analytics, user management, and comprehensive reporting tools to optimize healthcare operations.</p>
                        <ul class="space-y-2 text-sm text-gray-500">
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Advanced Analytics</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>User Management</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="fas fa-check text-secondary"></i>
                                <span>Detailed Reporting</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Statistics Section -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-3xl shadow-2xl p-12 relative overflow-hidden animate-slide-up">
            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
            
            <div class="relative">
                <div class="text-center mb-12">
                    <h3 class="text-4xl font-bold text-white mb-4">Trusted by Healthcare Professionals</h3>
                    <p class="text-xl text-blue-100">Join thousands who have transformed their healthcare experience</p>
                </div>

                <div class="grid md:grid-cols-4 gap-8 text-center">
                    <div class="stat-counter">
                        <div class="text-5xl font-bold text-white mb-3">1000+</div>
                        <div class="text-blue-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-users"></i>
                            <span>Happy Clients</span>
                        </div>
                        <div class="mt-2 w-16 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 95%"></div>
                        </div>
                    </div>
                    <div class="stat-counter" style="animation-delay: 0.1s">
                        <div class="text-5xl font-bold text-white mb-3">50+</div>
                        <div class="text-blue-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-user-md"></i>
                            <span>Expert Trainers</span>
                        </div>
                        <div class="mt-2 w-16 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 87%"></div>
                        </div>
                    </div>
                    <div class="stat-counter" style="animation-delay: 0.2s">
                        <div class="text-5xl font-bold text-white mb-3">5000+</div>
                        <div class="text-blue-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-calendar-check"></i>
                            <span>Appointments</span>
                        </div>
                        <div class="mt-2 w-16 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 92%"></div>
                        </div>
                    </div>
                    <div class="stat-counter" style="animation-delay: 0.3s">
                        <div class="text-5xl font-bold text-white mb-3">24/7</div>
                        <div class="text-blue-100 font-medium flex items-center justify-center space-x-2">
                            <i class="fas fa-headset"></i>
                            <span>Support</span>
                        </div>
                        <div class="mt-2 w-16 h-1 bg-white/30 mx-auto rounded-full">
                            <div class="h-full bg-white rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action Section -->
        <div class="text-center py-20 animate-slide-up">
            <div class="max-w-3xl mx-auto">
                <h3 class="text-4xl font-bold text-gray-800 mb-6">Ready to Transform Your Healthcare Experience?</h3>
                <p class="text-xl text-gray-600 mb-8">Join thousands of clients and healthcare providers who trust our platform for their medical needs.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="bg-primary text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-blue-700 transition-all duration-300 shadow-xl hover:shadow-2xl inline-flex items-center justify-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Account</span>
                    </a>
                    <a href="login.php" class="bg-secondary text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-green-700 transition-all duration-300 shadow-xl hover:shadow-2xl inline-flex items-center justify-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign In</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white py-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
        
        <div class="max-w-7xl mx-auto px-4 relative">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-heartbeat text-primary text-2xl"></i>
                        <h4 class="text-xl font-bold">Beyond Trust</h4>
                    </div>
                    <p class="text-gray-300 mb-4">Making healthcare accessible, efficient, and client-centered through innovative technology solutions.</p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary transition-all duration-300">
                            <i class="fab fa-facebook text-sm"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary transition-all duration-300">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCttfJcDqkWJAORC6ZJWiIIg" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary transition-all duration-300">
                            <i class="fab fa-youtube text-sm"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg">Quick Links</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-primary transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Services</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Trainers</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg">Support</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-primary transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4 text-lg">Contact Info</h4>
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
        const statsSection = document.querySelector('.bg-gradient-to-r.from-primary.to-secondary');
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
            const parallax = document.querySelector('.animate-float');
            if (parallax) {
                const speed = scrolled * -0.2;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });
    </script>
</body>
</html>