<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
        }

        .header {
            background: white;
            padding: 30px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        .header h1 {
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .header p {
            color: #6c757d;
            font-size: clamp(1rem, 2vw, 1.1rem);
        }

        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            align-items: start;
        }

        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .profile-avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 25px;
        }

        .avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #4285f4, #5a9cfc);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.3);
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .avatar:hover {
            transform: scale(1.05);
        }

        .avatar-text {
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }

        .avatar-icon {
            width: 50px;
            height: 50px;
            fill: white;
        }

        .profile-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            text-align: center;
            word-break: break-word;
        }

        .profile-email {
            color: #6c757d;
            margin-bottom: 15px;
            text-align: center;
            word-break: break-word;
            font-size: 0.9rem;
        }

        .user-badge {
            background: #d4edda;
            color: #155724;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .nav-menu {
            list-style: none;
            margin-top: 25px;
        }

        .nav-item {
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .nav-link:hover {
            background: #f8f9fa;
            color: #4285f4;
        }

        .nav-link.active {
            background: #4285f4;
            color: white;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            fill: currentColor;
            flex-shrink: 0;
        }

        .main-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .section-icon {
            width: 22px;
            height: 22px;
            margin-right: 10px;
            fill: #4285f4;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .label-icon {
            width: 14px;
            height: 14px;
            margin-right: 8px;
            fill: #6c757d;
        }

        .required {
            color: #dc3545;
            margin-left: 4px;
        }

        .form-control {
            padding: 12px 14px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }

        .form-control:focus {
            outline: none;
            border-color: #4285f4;
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.1);
        }

        .form-control:hover:not(:focus) {
            border-color: #4285f4;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        .update-btn {
            background: linear-gradient(135deg, #4285f4, #5a9cfc);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            align-self: flex-start;
            min-width: 140px;
        }

        .update-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(66, 133, 244, 0.3);
        }

        .update-btn:active {
            transform: translateY(0);
        }

        .btn-icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            display: none;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .profile-container {
                grid-template-columns: 280px 1fr;
                gap: 15px;
            }
            
            .sidebar {
                padding: 20px;
            }
            
            .main-content {
                padding: 25px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .profile-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .sidebar {
                order: 2;
                position: static;
                padding: 20px;
            }

            .main-content {
                order: 1;
                padding: 20px;
            }

            .nav-menu {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 8px;
                margin-top: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .update-btn {
                width: 100%;
                justify-self: stretch;
            }

            .avatar {
                width: 80px;
                height: 80px;
            }

            .avatar-text {
                font-size: 2rem;
            }

            .avatar-icon {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 20px 15px;
            }

            .main-content {
                padding: 15px;
            }

            .sidebar {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 320px) {
            .container {
                padding: 5px;
            }

            .header {
                padding: 15px 10px;
            }

            .main-content {
                padding: 12px;
            }

            .sidebar {
                padding: 12px;
            }

            .form-control {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading .update-btn {
            background: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>My Profile</h1>
            <p>Manage your account settings and preferences</p>
        </div>

        <div class="profile-container">
            <div class="sidebar">
                <div class="profile-avatar">
                    <div class="avatar" id="avatar">
                        <!-- Avatar content will be dynamically generated -->
                    </div>
                    <div class="profile-name" id="profile-name">Loading...</div>
                    <div class="profile-email" id="profile-email">Loading...</div>
                    <div class="user-badge" id="user-role">User</div>
                </div>

                <nav>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                Profile Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M18,8A6,6 0 0,1 12,14A6,6 0 0,1 6,8A6,6 0 0,1 12,2A6,6 0 0,1 18,8M12,16A4,4 0 0,1 16,20H20V22H4V20A4,4 0 0,1 8,16H12Z"/>
                                </svg>
                                Change Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                                </svg>
                                Preferences
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="main-content">
                <div id="success-message" class="success-message">
                    Profile updated successfully!
                </div>

                <h2 class="section-title">
                    <svg class="section-icon" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Profile Information
                </h2>

                <form id="profile-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                                </svg>
                                Full Name<span class="required">*</span>
                            </label>
                            <input type="text" id="full-name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                                </svg>
                                Email<span class="required">*</span>
                            </label>
                            <input type="email" id="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                                </svg>
                                Phone
                            </label>
                            <input type="tel" id="phone" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                </svg>
                                Date of Birth
                            </label>
                            <input type="date" id="date-of-birth" class="form-control">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                </svg>
                                Gender
                            </label>
                            <select id="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                                <option value="other">Other</option>
                                <option value="prefer-not-to-say">Prefer not to say</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                </svg>
                                Member Since
                            </label>
                            <input type="text" id="member-since" class="form-control" readonly>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                </svg>
                                Address
                            </label>
                            <textarea id="address" class="form-control" placeholder="Enter your address"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="update-btn">
                        <svg class="btn-icon" viewBox="0 0 24 24">
                            <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16.17L19,7.83V19M12,12C10.34,12 9,13.34 9,15S10.34,18 12,18S15,16.66 15,15S13.66,12 12,12M6,6H15V10H6V6Z"/>
                        </svg>
                        Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // User data management
        class UserProfile {
            constructor() {
                this.userData = this.loadUserData();
                this.init();
            }

            // Simulate loading user data (in real app, this would be an API call)
            loadUserData() {
                // Check if user data exists in memory (simulating backend)
                const savedData = this.getSavedData();
                
                if (savedData) {
                    return savedData;
                }

                // Default user data - you can change this to test different users
                return {
                    fullName: "Thiseni",
                    email: "thiseni@gmail.com",
                    phone: "+94784334851",
                    dateOfBirth: "2003-04-21",
                    gender: "female",
                    address: "Union Place, Colombo 2",
                    memberSince: "Jul 21, 2025",
                    role: "User",
                    avatar: null // Will be generated from name initials
                };
            }

            // Simulate data persistence (in real app, this would be backend storage)
            getSavedData() {
                // In a real application, this would fetch from backend/database
                // For demo purposes, we'll use a simple object
                return window.tempUserData || null;
            }

            saveData(data) {
                // In a real application, this would save to backend/database
                window.tempUserData = { ...this.userData, ...data };
                this.userData = window.tempUserData;
            }

            init() {
                this.updateUI();
                this.bindEvents();
            }

            updateUI() {
                // Update sidebar profile info
                document.getElementById('profile-name').textContent = this.userData.fullName;
                document.getElementById('profile-email').textContent = this.userData.email;
                document.getElementById('user-role').textContent = this.userData.role;
                
                // Generate avatar
                this.generateAvatar();
                
                // Update form fields
                document.getElementById('full-name').value = this.userData.fullName;
                document.getElementById('email').value = this.userData.email;
                document.getElementById('phone').value = this.userData.phone || '';
                document.getElementById('date-of-birth').value = this.userData.dateOfBirth || '';
                document.getElementById('gender').value = this.userData.gender || '';
                document.getElementById('address').value = this.userData.address || '';
                document.getElementById('member-since').value = this.userData.memberSince;
            }

            generateAvatar() {
                const avatar = document.getElementById('avatar');
                const name = this.userData.fullName;
                
                if (this.userData.avatar) {
                    // If user has uploaded an avatar (for future enhancement)
                    avatar.innerHTML = `<img src="${this.userData.avatar}" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
                } else if (name) {
                    // Generate initials avatar
                    const initials = name.split(' ')
                        .map(word => word.charAt(0))
                        .join('')
                        .toUpperCase()
                        .slice(0, 2);
                    
                    avatar.innerHTML = `<span class="avatar-text">${initials}</span>`;
                } else {
                    // Default user icon
                    avatar.innerHTML = `
                        <svg class="avatar-icon" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    `;
                }
            }

            bindEvents() {
                // Form submission
                document.getElementById('profile-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleFormSubmit();
                });

                // Navigation clicks
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.handleNavClick(e.target.closest('.nav-link'));
                    });
                });

                // Real-time name update
                document.getElementById('full-name').addEventListener('input', (e) => {
                    const newName = e.target.value;
                    document.getElementById('profile-name').textContent = newName || 'No Name';
                    this.generateAvatar();
                });

                // Real-time email update
                document.getElementById('email').addEventListener('input', (e) => {
                    document.getElementById('profile-email').textContent = e.target.value || 'No Email';
                });

                // Add loading states to inputs
                this.addInputEffects();
            }

            handleFormSubmit() {
                const formData = {
                    fullName: document.getElementById('full-name').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    dateOfBirth: document.getElementById('date-of-birth').value,
                    gender: document.getElementById('gender').value,
                    address: document.getElementById('address').value
                };

                // Validate required fields
                if (!formData.fullName || !formData.email) {
                    alert('Please fill in all required fields.');
                    return;
                }

                // Add loading state
                const form = document.getElementById('profile-form');
                const submitBtn = form.querySelector('.update-btn');
                const originalText = submitBtn.innerHTML;
                
                form.classList.add('loading');
                submitBtn.innerHTML = '<span>Updating...</span>';

                // Simulate API call delay
                setTimeout(() => {
                    // Save data
                    this.saveData(formData);
                    
                    // Update UI
                    this.updateUI();
                    
                    // Show success message
                    this.showSuccessMessage();
                    
                    // Remove loading state
                    form.classList.remove('loading');
                    submitBtn.innerHTML = originalText;
                }, 1000);
            }

            handleNavClick(link) {
                // Remove active class from all links
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                link.classList.add('active');
                
                // Get the section name
                const sectionText = link.textContent.trim();
                
                // Update main content based on selection
                this.updateMainContent(sectionText);
            }

            updateMainContent(section) {
                const mainContent = document.querySelector('.main-content');
                const sectionTitle = mainContent.querySelector('.section-title');
                const sectionIcon = sectionTitle.querySelector('.section-icon');
                
                switch(section) {
                    case 'Profile Information':
                        // Already showing profile form
                        break;
                    case 'Change Password':
                        this.showPasswordForm();
                        break;
                    case 'Preferences':
                        this.showPreferencesForm();
                        break;
                }
            }

            showPasswordForm() {
                const mainContent = document.querySelector('.main-content');
                mainContent.innerHTML = `
                    <div id="success-message" class="success-message">
                        Password changed successfully!
                    </div>

                    <h2 class="section-title">
                        <svg class="section-icon" viewBox="0 0 24 24">
                            <path d="M18,8A6,6 0 0,1 12,14A6,6 0 0,1 6,8A6,6 0 0,1 12,2A6,6 0 0,1 18,8M12,16A4,4 0 0,1 16,20H20V22H4V20A4,4 0 0,1 8,16H12Z"/>
                        </svg>
                        Change Password
                    </h2>

                    <form id="password-form">
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                                    </svg>
                                    Current Password<span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" required placeholder="Enter current password">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                                    </svg>
                                    New Password<span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" required placeholder="Enter new password" minlength="8">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                                    </svg>
                                    Confirm New Password<span class="required">*</span>
                                </label>
                                <input type="password" class="form-control" required placeholder="Confirm new password" minlength="8">
                            </div>
                        </div>

                        <button type="submit" class="update-btn">
                            <svg class="btn-icon" viewBox="0 0 24 24">
                                <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                            </svg>
                            Change Password
                        </button>
                    </form>
                `;

                // Bind password form events
                this.bindPasswordFormEvents();
            }

            showPreferencesForm() {
                const mainContent = document.querySelector('.main-content');
                mainContent.innerHTML = `
                    <div id="success-message" class="success-message">
                        Preferences saved successfully!
                    </div>

                    <h2 class="section-title">
                        <svg class="section-icon" viewBox="0 0 24 24">
                            <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                        </svg>
                        Preferences
                    </h2>

                    <form id="preferences-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M17.5,12A1.5,1.5 0 0,1 16,10.5A1.5,1.5 0 0,1 17.5,9A1.5,1.5 0 0,1 19,10.5A1.5,1.5 0 0,1 17.5,12M14.5,8A1.5,1.5 0 0,1 13,6.5A1.5,1.5 0 0,1 14.5,5A1.5,1.5 0 0,1 16,6.5A1.5,1.5 0 0,1 14.5,8M9.5,8A1.5,1.5 0 0,1 8,6.5A1.5,1.5 0 0,1 9.5,5A1.5,1.5 0 0,1 11,6.5A1.5,1.5 0 0,1 9.5,8M6.5,12A1.5,1.5 0 0,1 5,10.5A1.5,1.5 0 0,1 6.5,9A1.5,1.5 0 0,1 8,10.5A1.5,1.5 0 0,1 6.5,12M12,3A9,9 0 0,0 3,12A9,9 0 0,0 12,21A8.5,8.5 0 0,0 20.5,12.5M11,2V6H13V2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 11,2Z"/>
                                    </svg>
                                    Theme
                                </label>
                                <select class="form-control">
                                    <option value="light" selected>Light</option>
                                    <option value="dark">Dark</option>
                                    <option value="auto">Auto</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,11C19,14.53 16.39,17.44 13,17.93V21H11V17.93C7.61,17.44 5,14.53 5,11H7A5,5 0 0,0 12,16A5,5 0 0,0 17,11H19Z"/>
                                    </svg>
                                    Language
                                </label>
                                <select class="form-control">
                                    <option value="en" selected>English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="de">German</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29C10,4.19 10,4.1 10,4A2,2 0 0,1 12,2A2,2 0 0,1 14,4C14,4.1 14,4.19 14,4.29C16.97,5.17 19,7.9 19,11V17L21,19M14,21A2,2 0 0,1 12,23A2,2 0 0,1 10,21"/>
                                    </svg>
                                    Notifications
                                </label>
                                <select class="form-control">
                                    <option value="all" selected>All Notifications</option>
                                    <option value="important">Important Only</option>
                                    <option value="none">None</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4M12,6A6,6 0 0,0 6,12A6,6 0 0,0 12,18A6,6 0 0,0 18,12A6,6 0 0,0 12,6Z"/>
                                    </svg>
                                    Privacy
                                </label>
                                <select class="form-control">
                                    <option value="public">Public Profile</option>
                                    <option value="friends" selected>Friends Only</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M21,16.5C21,16.88 20.79,17.21 20.47,17.38L12.57,21.82C12.41,21.94 12.21,22 12,22C11.79,22 11.59,21.94 11.43,21.82L3.53,17.38C3.21,17.21 3,16.88 3,16.5V7.5C3,7.12 3.21,6.79 3.53,6.62L11.43,2.18C11.59,2.06 11.79,2 12,2C12.21,2 12.41,2.06 12.57,2.18L20.47,6.62C20.79,6.79 21,7.12 21,7.5V16.5M12,4.15L5,8.09V15.91L12,19.85L19,15.91V8.09L12,4.15Z"/>
                                    </svg>
                                    Email Preferences
                                </label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px;">
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" checked> Newsletter
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" checked> Product Updates
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox"> Marketing
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="update-btn">
                            <svg class="btn-icon" viewBox="0 0 24 24">
                                <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16.17L19,7.83V19M12,12C10.34,12 9,13.34 9,15S10.34,18 12,18S15,16.66 15,15S13.66,12 12,12M6,6H15V10H6V6Z"/>
                            </svg>
                            Save Preferences
                        </button>
                    </form>
                `;

                // Bind preferences form events
                this.bindPreferencesFormEvents();
            }

            bindPasswordFormEvents() {
                document.getElementById('password-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    const currentPassword = e.target.querySelector('input[type="password"]:nth-of-type(1)').value;
                    const newPassword = e.target.querySelector('input[type="password"]:nth-of-type(2)').value;
                    const confirmPassword = e.target.querySelector('input[type="password"]:nth-of-type(3)').value;
                    
                    if (newPassword !== confirmPassword) {
                        alert('New passwords do not match!');
                        return;
                    }
                    
                    if (newPassword.length < 8) {
                        alert('Password must be at least 8 characters long!');
                        return;
                    }
                    
                    // Simulate password change
                    this.showSuccessMessage('Password changed successfully!');
                    e.target.reset();
                });
            }

            bindPreferencesFormEvents() {
                document.getElementById('preferences-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    
                    // Simulate saving preferences
                    this.showSuccessMessage('Preferences saved successfully!');
                });
            }

            showSuccessMessage(message = 'Profile updated successfully!') {
                const successMessage = document.getElementById('success-message');
                if (successMessage) {
                    successMessage.textContent = message;
                    successMessage.style.display = 'block';
                    
                    // Scroll to top to show the message
                    document.querySelector('.main-content').scrollTop = 0;
                    
                    // Hide the message after 3 seconds
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 3000);
                }
            }

            addInputEffects() {
                // Add focus effects to form controls
                document.addEventListener('focus', (e) => {
                    if (e.target.classList.contains('form-control')) {
                        e.target.parentElement.style.transform = 'translateY(-2px)';
                        e.target.parentElement.style.transition = 'transform 0.3s ease';
                    }
                }, true);
                
                document.addEventListener('blur', (e) => {
                    if (e.target.classList.contains('form-control')) {
                        e.target.parentElement.style.transform = 'translateY(0)';
                    }
                }, true);
            }

            // Method to simulate switching users (for testing different users)
            switchUser(newUserData) {
                this.userData = { ...this.userData, ...newUserData };
                this.saveData(this.userData);
                this.updateUI();
                
                // Reset to profile information view
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                document.querySelector('.nav-link').classList.add('active');
                this.showProfileForm();
            }

            showProfileForm() {
                const mainContent = document.querySelector('.main-content');
                mainContent.innerHTML = `
                    <div id="success-message" class="success-message">
                        Profile updated successfully!
                    </div>

                    <h2 class="section-title">
                        <svg class="section-icon" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Profile Information
                    </h2>

                    <form id="profile-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>
                                    </svg>
                                    Full Name<span class="required">*</span>
                                </label>
                                <input type="text" id="full-name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                                    </svg>
                                    Email<span class="required">*</span>
                                </label>
                                <input type="email" id="email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z"/>
                                    </svg>
                                    Phone
                                </label>
                                <input type="tel" id="phone" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                    </svg>
                                    Date of Birth
                                </label>
                                <input type="date" id="date-of-birth" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                    </svg>
                                    Gender
                                </label>
                                <select id="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                    <option value="other">Other</option>
                                    <option value="prefer-not-to-say">Prefer not to say</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                    </svg>
                                    Member Since
                                </label>
                                <input type="text" id="member-since" class="form-control" readonly>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                    </svg>
                                    Address
                                </label>
                                <textarea id="address" class="form-control" placeholder="Enter your address"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="update-btn">
                            <svg class="btn-icon" viewBox="0 0 24 24">
                                <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16.17L19,7.83V19M12,12C10.34,12 9,13.34 9,15S10.34,18 12,18S15,16.66 15,15S13.66,12 12,12M6,6H15V10H6V6Z"/>
                            </svg>
                            Update Profile
                        </button>
                    </form>
                `;
                
                // Re-bind events and update UI
                this.updateUI();
                this.bindEvents();
            }
        }

        // Initialize the profile when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            window.userProfile = new UserProfile();
        });

        // Example function to switch users (for testing purposes)
        // You can call this from the browser console to test different users
        function testSwitchUser(userData) {
            if (window.userProfile) {
                window.userProfile.switchUser(userData);
            }
        }

        // Example usage in console:
        // testSwitchUser({fullName: "John Doe", email: "john.doe@example.com", phone: "+1234567890"});
        // testSwitchUser({fullName: "Sarah Johnson", email: "sarah.j@company.com", phone: "+9876543210"});

    </script>
</body>
</html>