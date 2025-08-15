<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Health Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4a6fa5;
            --primary-light: #6b8cbe;
            --primary-dark: #3a5a8a;
            --secondary: #ff7e5f;
            --accent: #6bd6e1;
            --text: #2d3748;
            --text-light: #718096;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --success: #48bb78;
            --warning: #ed8936;
            --error: #f56565;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 40px 30px;
            margin-bottom: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(74, 111, 165, 0.2);
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjA1KSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
            opacity: 0.3;
        }

        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header p {
            font-size: clamp(1rem, 2vw, 1.2rem);
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 25px;
            align-items: start;
        }

        .sidebar {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            height: fit-content;
            position: sticky;
            top: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .sidebar:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .profile-avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(255, 126, 95, 0.3);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 4px solid white;
        }

        .avatar:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 15px 30px rgba(255, 126, 95, 0.4);
        }

        .avatar::after {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            border: 2px dashed var(--accent);
            animation: spin 20s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .avatar:hover::after {
            opacity: 1;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .avatar-text {
            font-size: 3rem;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            font-family: 'Playfair Display', serif;
        }

        .avatar-icon {
            width: 60px;
            height: 60px;
            fill: white;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 10px;
            text-align: center;
            word-break: break-word;
            font-family: 'Playfair Display', serif;
        }

        .profile-email {
            color: var(--text-light);
            margin-bottom: 20px;
            text-align: center;
            word-break: break-word;
            font-size: 0.95rem;
        }

        .user-badge {
            background: rgba(107, 214, 225, 0.2);
            color: var(--primary-dark);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid var(--accent);
        }

        .nav-menu {
            list-style: none;
            margin-top: 30px;
        }

        .nav-item {
            margin-bottom: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background: rgba(74, 111, 165, 0.05);
        }

        .nav-link:hover {
            background: rgba(74, 111, 165, 0.1);
            color: var(--primary);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: linear-gradient(to right, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 5px 15px rgba(74, 111, 165, 0.3);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            fill: currentColor;
            flex-shrink: 0;
        }

        .main-content {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .main-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            font-family: 'Playfair Display', serif;
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, var(--secondary), var(--accent));
            border-radius: 3px;
        }

        .section-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            fill: var(--primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .label-icon {
            width: 16px;
            height: 16px;
            margin-right: 10px;
            fill: var(--primary-light);
        }

        .required {
            color: var(--error);
            margin-left: 4px;
        }

        .form-control {
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(74, 111, 165, 0.1);
        }

        .form-control:hover:not(:focus) {
            border-color: var(--primary-light);
        }

        .form-control[readonly] {
            background-color: var(--bg);
            cursor: not-allowed;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234a6fa5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
        }

        .update-btn {
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            align-self: flex-start;
            min-width: 160px;
            font-family: 'Poppins', sans-serif;
            box-shadow: 0 5px 15px rgba(255, 126, 95, 0.3);
            position: relative;
            overflow: hidden;
        }

        .update-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .update-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 126, 95, 0.4);
        }

        .update-btn:hover::before {
            left: 100%;
        }

        .update-btn:active {
            transform: translateY(0);
        }

        .btn-icon {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }

        .success-message {
            background: rgba(72, 187, 120, 0.1);
            color: var(--success);
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid var(--success);
            display: none;
            animation: slideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-weight: 500;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating animation for avatar */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .profile-container {
                grid-template-columns: 280px 1fr;
                gap: 20px;
            }
            
            .sidebar {
                padding: 25px;
            }
            
            .main-content {
                padding: 30px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header {
                padding: 30px 20px;
            }

            .profile-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .sidebar {
                order: 2;
                position: static;
                padding: 25px;
            }

            .main-content {
                order: 1;
                padding: 25px;
            }

            .nav-menu {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 10px;
                margin-top: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .update-btn {
                width: 100%;
                justify-self: stretch;
            }

            .avatar {
                width: 100px;
                height: 100px;
            }

            .avatar-text {
                font-size: 2.5rem;
            }

            .avatar-icon {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 25px 15px;
            }

            .main-content {
                padding: 20px;
            }

            .sidebar {
                padding: 20px;
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
                padding: 10px;
            }

            .header {
                padding: 20px 10px;
            }

            .main-content {
                padding: 15px;
            }

            .sidebar {
                padding: 15px;
            }

            .form-control {
                padding: 12px 14px;
                font-size: 0.9rem;
            }
        }

        /* Loading state */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading .update-btn {
            background: var(--text-light);
        }

        /* Pulse animation for loading */
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .loading .update-btn::after {
            content: '...';
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>My Health Profile</h1>
            <p>Manage your medical information and healthcare preferences</p>
        </div>

        <div class="profile-container">
            <div class="sidebar">
                <div class="profile-avatar">
                    <div class="avatar floating" id="avatar">
                        <!-- Avatar content will be dynamically generated -->
                    </div>
                    <div class="profile-name" id="profile-name">Loading...</div>
                    <div class="profile-email" id="profile-email">Loading...</div>
                    <div class="user-badge" id="user-role">Patient</div>
                </div>

                <nav>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                Personal Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13,12H16V15H13M7.5,12H10.5V15H7.5M13,7H16V10H13M7.5,7H10.5V10H7.5M16,17H17V19H16V17M17,7H19V9H17V7M7,17H9V19H7V17M9,7H10V10H9V7Z"/>
                                </svg>
                                Medical Records
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M12,3L2,12H5V20H19V12H22L12,3M12,7.7C14.1,7.7 15.8,9.4 15.8,11.5C15.8,14.5 12,18 12,18C12,18 8.2,14.5 8.2,11.5C8.2,9.4 9.9,7.7 12,7.7M12,10A1.5,1.5 0 0,0 10.5,11.5A1.5,1.5 0 0,0 12,13A1.5,1.5 0 0,0 13.5,11.5A1.5,1.5 0 0,0 12,10Z"/>
                                </svg>
                                Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24">
                                    <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                                </svg>
                                Settings
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
                    Personal Information
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
                                    <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                </svg>
                                Blood Type
                            </label>
                            <select id="blood-type" class="form-control">
                                <option value="">Unknown</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
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
                                    <path d="M18,15A4,4 0 0,1 22,19A4,4 0 0,1 18,23A4,4 0 0,1 14,19A4,4 0 0,1 18,15M18,17A2,2 0 0,0 16,19A2,2 0 0,0 18,21A2,2 0 0,0 20,19A2,2 0 0,0 18,17M6.05,14.54C6.05,14.54 7.46,13.12 7.47,10.3C7.11,8.11 7.97,5.54 9.94,3.58C12.87,0.65 17.14,0.17 19.5,2.5C21.83,4.86 21.35,9.13 18.42,12.06C16.46,14.03 13.89,14.89 11.7,14.53C8.88,14.54 7.46,15.95 7.46,15.95L3.22,20.19L1.81,18.78L6.05,14.54M18.07,3.93C16.5,2.37 13.5,2.84 11.35,5C9.21,7.14 8.73,10.15 10.29,11.71C11.86,13.27 14.86,12.79 17,10.65C19.16,8.5 19.63,5.5 18.07,3.93Z"/>
                                </svg>
                                Address
                            </label>
                            <textarea id="address" class="form-control" placeholder="Enter your address"></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M18.5,3.5L20.5,5.5L5.5,20.5L3.5,18.5L18.5,3.5M7,4C8.66,4 10,5.34 10,7C10,8.66 8.66,10 7,10C5.34,10 4,8.66 4,7C4,5.34 5.34,4 7,4M17,14C18.66,14 20,15.34 20,17C20,18.66 18.66,20 17,20C15.34,20 14,18.66 14,17C14,15.34 15.34,14 17,14M7,6C6.45,6 6,6.45 6,7C6,7.55 6.45,8 7,8C7.55,8 8,7.55 8,7C8,6.45 7.55,6 7,6M17,16C16.45,16 16,16.45 16,17C16,17.55 16.45,18 17,18C17.55,18 18,17.55 18,17C18,16.45 17.55,16 17,16Z"/>
                                </svg>
                                Allergies
                            </label>
                            <textarea id="allergies" class="form-control" placeholder="List any known allergies"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="update-btn">
                        <svg class="btn-icon" viewBox="0 0 24 24">
                            <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16.17L19,7.83V19M12,12C10.34,12 9,13.34 9,15S10.34,18 12,18S15,16.66 15,15S13.66,12 12,12M6,6H15V10H6V6Z"/>
                        </svg>
                        Update Information
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
                    fullName: "Minanga Perera",
                    email: "minanga.p@healthcare.com",
                    phone: "+94765432123",
                    dateOfBirth: "2003-04-21",
                    gender: "female",
                    bloodType: "B+",
                    address: "123 Medical Lane, Colombo 2",
                    allergies: "Penicillin, Peanuts",
                    memberSince: "Jul 21, 2025",
                    role: "Patient",
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
                document.getElementById('blood-type').value = this.userData.bloodType || '';
                document.getElementById('address').value = this.userData.address || '';
                document.getElementById('allergies').value = this.userData.allergies || '';
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
                    bloodType: document.getElementById('blood-type').value,
                    address: document.getElementById('address').value,
                    allergies: document.getElementById('allergies').value
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
                submitBtn.innerHTML = '<span>Saving</span>';

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
                }, 1500);
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
                    case 'Personal Information':
                        // Already showing profile form
                        break;
                    case 'Medical Records':
                        this.showMedicalRecords();
                        break;
                    case 'Appointments':
                        this.showAppointments();
                        break;
                    case 'Settings':
                        this.showSettings();
                        break;
                }
            }

            showMedicalRecords() {
                const mainContent = document.querySelector('.main-content');
                mainContent.innerHTML = `
                    <div id="success-message" class="success-message">
                        Medical records updated successfully!
                    </div>

                    <h2 class="section-title">
                        <svg class="section-icon" viewBox="0 0 24 24">
                            <path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13,12H16V15H13M7.5,12H10.5V15H7.5M13,7H16V10H13M7.5,7H10.5V10H7.5M16,17H17V19H16V17M17,7H19V9H17V7M7,17H9V19H7V17M9,7H10V10H9V7Z"/>
                        </svg>
                        Medical Records
                    </h2>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M18,15A4,4 0 0,1 22,19A4,4 0 0,1 18,23A4,4 0 0,1 14,19A4,4 0 0,1 18,15M18,17A2,2 0 0,0 16,19A2,2 0 0,0 18,21A2,2 0 0,0 20,19A2,2 0 0,0 18,17M6.05,14.54C6.05,14.54 7.46,13.12 7.47,10.3C7.11,8.11 7.97,5.54 9.94,3.58C12.87,0.65 17.14,0.17 19.5,2.5C21.83,4.86 21.35,9.13 18.42,12.06C16.46,14.03 13.89,14.89 11.7,14.53C8.88,14.54 7.46,15.95 7.46,15.95L3.22,20.19L1.81,18.78L6.05,14.54M18.07,3.93C16.5,2.37 13.5,2.84 11.35,5C9.21,7.14 8.73,10.15 10.29,11.71C11.86,13.27 14.86,12.79 17,10.65C19.16,8.5 19.63,5.5 18.07,3.93Z"/>
                                </svg>
                                Medical History
                            </label>
                            <textarea class="form-control" placeholder="Describe your medical history" rows="5">${this.userData.medicalHistory || ''}</textarea>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M18.5,3.5L20.5,5.5L5.5,20.5L3.5,18.5L18.5,3.5M7,4C8.66,4 10,5.34 10,7C10,8.66 8.66,10 7,10C5.34,10 4,8.66 4,7C4,5.34 5.34,4 7,4M17,14C18.66,14 20,15.34 20,17C20,18.66 18.66,20 17,20C15.34,20 14,18.66 14,17C14,15.34 15.34,14 17,14M7,6C6.45,6 6,6.45 6,7C6,7.55 6.45,8 7,8C7.55,8 8,7.55 8,7C8,6.45 7.55,6 7,6M17,16C16.45,16 16,16.45 16,17C16,17.55 16.45,18 17,18C17.55,18 18,17.55 18,17C18,16.45 17.55,16 17,16Z"/>
                                </svg>
                                Current Medications
                            </label>
                            <textarea class="form-control" placeholder="List your current medications" rows="5">${this.userData.medications || ''}</textarea>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M19,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.9 20.1,3 19,3M19,5V19H5V5H19M17,17H7V15H17V17M17,13H7V11H17V13M17,9H7V7H17V9Z"/>
                                </svg>
                                Immunization Records
                            </label>
                            <textarea class="form-control" placeholder="List your immunization records" rows="5">${this.userData.immunizations || ''}</textarea>
                        </div>
                    </div>

                    <button type="button" class="update-btn" id="save-medical-records">
                        <svg class="btn-icon" viewBox="0 0 24 24">
                            <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16.17L19,7.83V19M12,12C10.34,12 9,13.34 9,15S10.34,18 12,18S15,16.66 15,15S13.66,12 12,12M6,6H15V10H6V6Z"/>
                        </svg>
                        Save Records
                    </button>
                `;

                // Bind medical records save button
                document.getElementById('save-medical-records').addEventListener('click', () => {
                    this.showSuccessMessage('Medical records saved successfully!');
                });
            }

            showAppointments() {
                const mainContent = document.querySelector('.main-content');
                mainContent.innerHTML = `
                    <div id="success-message" class="success-message">
                        Appointment booked successfully!
                    </div>

                    <h2 class="section-title">
                        <svg class="section-icon" viewBox="0 0 24 24">
                            <path d="M12,3L2,12H5V20H19V12H22L12,3M12,7.7C14.1,7.7 15.8,9.4 15.8,11.5C15.8,14.5 12,18 12,18C12,18 8.2,14.5 8.2,11.5C8.2,9.4 9.9,7.7 12,7.7M12,10A1.5,1.5 0 0,0 10.5,11.5A1.5,1.5 0 0,0 12,13A1.5,1.5 0 0,0 13.5,11.5A1.5,1.5 0 0,0 12,10Z"/>
                        </svg>
                        Appointments
                    </h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                </svg>
                                Upcoming Appointments
                            </label>
                            <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                                    <div style="background: #f0f7ff; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#4a6fa5">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M7.07,18.28C7.5,17.38 10.12,16.5 12,16.5C13.88,16.5 16.5,17.38 16.93,18.28C15.57,19.36 13.86,20 12,20C10.14,20 8.43,19.36 7.07,18.28M18.36,16.83C16.93,15.09 13.46,14.5 12,14.5C10.54,14.5 7.07,15.09 5.64,16.83C4.62,15.5 4,13.82 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,13.82 19.38,15.5 18.36,16.83M12,6C10.06,6 8.5,7.56 8.5,9.5C8.5,11.44 10.06,13 12,13C13.94,13 15.5,11.44 15.5,9.5C15.5,7.56 13.94,6 12,6M12,11A1.5,1.5 0 0,1 10.5,9.5A1.5,1.5 0 0,1 12,8A1.5,1.5 0 0,1 13.5,9.5A1.5,1.5 0 0,1 12,11Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text);">Dr. Samantha Silva</div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">Cardiologist</div>
                                        <div style="font-size: 0.85rem; color: var(--primary); margin-top: 5px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#4a6fa5" style="vertical-align: middle; margin-right: 5px;">
                                                <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                            </svg>
                                            Aug 15, 2025 - 10:30 AM
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; margin-bottom: 15px;">
                                    <div style="background: #f0f7ff; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#4a6fa5">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M7.07,18.28C7.5,17.38 10.12,16.5 12,16.5C13.88,16.5 16.5,17.38 16.93,18.28C15.57,19.36 13.86,20 12,20C10.14,20 8.43,19.36 7.07,18.28M18.36,16.83C16.93,15.09 13.46,14.5 12,14.5C10.54,14.5 7.07,15.09 5.64,16.83C4.62,15.5 4,13.82 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,13.82 19.38,15.5 18.36,16.83M12,6C10.06,6 8.5,7.56 8.5,9.5C8.5,11.44 10.06,13 12,13C13.94,13 15.5,11.44 15.5,9.5C15.5,7.56 13.94,6 12,6M12,11A1.5,1.5 0 0,1 10.5,9.5A1.5,1.5 0 0,1 12,8A1.5,1.5 0 0,1 13.5,9.5A1.5,1.5 0 0,1 12,11Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text);">Dr. Raj Patel</div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">General Physician</div>
                                        <div style="font-size: 0.85rem; color: var(--primary); margin-top: 5px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#4a6fa5" style="vertical-align: middle; margin-right: 5px;">
                                                <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                            </svg>
                                            Sep 2, 2025 - 2:15 PM
                                        </div>
                                    </div>
                                </div>
                                <button class="update-btn" style="width: 100%; justify-content: center;">
                                    <svg class="btn-icon" viewBox="0 0 24 24">
                                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                                    </svg>
                                    Book New Appointment
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <svg class="label-icon" viewBox="0 0 24 24">
                                    <path d="M17,10H7V8H17M17,13H7V11H17M14,16H7V14H14M12,2A1,1 0 0,1 13,3A1,1 0 0,1 12,4A1,1 0 0,1 11,3A1,1 0 0,1 12,2M19,3H14.82C14.4,1.84 13.3,1 12,1C10.7,1 9.6,1.84 9.18,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z"/>
                                </svg>
                                Past Appointments
                            </label>
                            <div style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                                    <div style="background: #f5f5f5; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#666">
                                            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M7.07,18.28C7.5,17.38 10.12,16.5 12,16.5C13.88,16.5 16.5,17.38 16.93,18.28C15.57,19.36 13.86,20 12,20C10.14,20 8.43,19.36 7.07,18.28M18.36,16.83C16.93,15.09 13.46,14.5 12,14.5C10.54,14.5 7.07,15.09 5.64,16.83C4.62,15.5 4,13.82 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,13.82 19.38,15.5 18.36,16.83M12,6C10.06,6 8.5,7.56 8.5,9.5C8.5,11.44 10.06,13 12,13C13.94,13 15.5,11.44 15.5,9.5C15.5,7.56 13.94,6 12,6M12,11A1.5,1.5 0 0,1 10.5,9.5A1.5,1.5 0 0,1 12,8A1.5,1.5 0 0,1 13.5,9.5A1.5,1.5 0 0,1 12,11Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text);">Dr. Lisa Wong</div>
                                        <div style="font-size: 0.9rem; color: var(--text-light);">Dermatologist</div>
                                        <div style="font-size: 0.85rem; color: #666; margin-top: 5px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#666" style="vertical-align: middle; margin-right: 5px;">
                                                <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/>
                                            </svg>
                                            Jun 10, 2025 - 9:00 AM
                                        </div>
                                    </div>
                                </div>
                                <button class="update-btn" style="width: 100%; justify-content: center; background: #f5f5f5; color: #666;">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="#666">
                                        <path d="M14,12L10,8V16M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z"/>
                                    </svg>
                                    View All History
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                // Bind appointment booking button
                document.querySelectorAll('.update-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.showSuccessMessage('Appointment booked successfully!');
                    });
                });
            }

            showSettings() {
                const mainContent = document.querySelector('.main-content');
                mainContent.innerHTML = `
                    <div id="success-message" class="success-message">
                        Preferences saved successfully!
                    </div>

                    <h2 class="section-title">
                        <svg class="section-icon" viewBox="0 0 24 24">
                            <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
                        </svg>
                        Settings
                    </h2>

                    <form id="preferences-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
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
                                        <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.22,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.22,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.68 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z"/>
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
                                        <path d="M14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10A2,2 0 0,1 14,12M22,12A10,10 0 0,1 2,12A10,10 0 0,1 12,2A10,10 0 0,1 22,12M20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12M15,12A3,3 0 0,0 12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12Z"/>
                                    </svg>
                                    Privacy
                                </label>
                                <select class="form-control">
                                    <option value="public">Public Profile</option>
                                    <option value="friends" selected>Friends Only</option>
                                    <option value="private">Private</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M21,16.5C21,16.88 20.79,17.21 20.47,17.38L12.57,21.82C12.41,21.94 12.21,22 12,22C11.79,22 11.59,21.94 11.43,21.82L3.53,17.38C3.21,17.21 3,16.88 3,16.5V7.5C3,7.12 3.21,6.79 3.53,6.62L11.43,2.18C11.59,2.06 11.79,2 12,2C12.21,2 12.41,2.06 12.57,2.18L20.47,6.62C20.79,6.79 21,7.12 21,7.5V16.5M12,4.15L5,8.09V15.91L12,19.85L19,15.91V8.09L12,4.15Z"/>
                                    </svg>
                                    Notifications
                                </label>
                                <select class="form-control">
                                    <option value="all" selected>All Notifications</option>
                                    <option value="important">Important Only</option>
                                    <option value="none">None</option>
                                </select>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M22,6C22,4.89 21.1,4 20,4H4C2.89,4 2,4.89 2,6V18C2,19.1 2.9,20 4,20H20C21.1,20 22,19.1 22,18V6M20,6L12,11L4,6H20M20,18H4V8L12,13L20,8V18Z"/>
                                    </svg>
                                    Email Preferences
                                </label>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 10px;">
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" checked> Appointment Reminders
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox" checked> Test Results
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox"> Health Tips
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px;">
                                        <input type="checkbox"> Newsletter
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
                document.getElementById('preferences-form').addEventListener('submit', (e) => {
                    e.preventDefault();
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
                        Personal Information
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
                                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22S19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                                    </svg>
                                    Blood Type
                                </label>
                                <select id="blood-type" class="form-control">
                                    <option value="">Unknown</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
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
                                        <path d="M18,15A4,4 0 0,1 22,19A4,4 0 0,1 18,23A4,4 0 0,1 14,19A4,4 0 0,1 18,15M18,17A2,2 0 0,0 16,19A2,2 0 0,0 18,21A2,2 0 0,0 20,19A2,2 0 0,0 18,17M6.05,14.54C6.05,14.54 7.46,13.12 7.47,10.3C7.11,8.11 7.97,5.54 9.94,3.58C12.87,0.65 17.14,0.17 19.5,2.5C21.83,4.86 21.35,9.13 18.42,12.06C16.46,14.03 13.89,14.89 11.7,14.53C8.88,14.54 7.46,15.95 7.46,15.95L3.22,20.19L1.81,18.78L6.05,14.54M18.07,3.93C16.5,2.37 13.5,2.84 11.35,5C9.21,7.14 8.73,10.15 10.29,11.71C11.86,13.27 14.86,12.79 17,10.65C19.16,8.5 19.63,5.5 18.07,3.93Z"/>
                                    </svg>
                                    Address
                                </label>
                                <textarea id="address" class="form-control" placeholder="Enter your address"></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">
                                    <svg class="label-icon" viewBox="0 0 24 24">
                                        <path d="M18.5,3.5L20.5,5.5L5.5,20.5L3.5,18.5L18.5,3.5M7,4C8.66,4 10,5.34 10,7C10,8.66 8.66,10 7,10C5.34,10 4,8.66 4,7C4,5.34 5.34,4 7,4M17,14C18.66,14 20,15.34 20,17C20,18.66 18.66,20 17,20C15.34,20 14,18.66 14,17C14,15.34 15.34,14 17,14M7,6C6.45,6 6,6.45 6,7C6,7.55 6.45,8 7,8C7.55,8 8,7.55 8,7C8,6.45 7.55,6 7,6M17,16C16.45,16 16,16.45 16,17C16,17.55 16.45,18 17,18C17.55,18 18,17.55 18,17C18,16.45 17.55,16 17,16Z"/>
                                    </svg>
                                    Allergies
                                </label>
                                <textarea id="allergies" class="form-control" placeholder="List any known allergies"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="update-btn">
                            <svg class="btn-icon" viewBox="0 0 24 24">
                                <path d="M17,3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V7L17,3M19,19H5V5H16.17L19,7.83V19M12,12C10.34,12 9,13.34 9,15S10.34,18 12,18S15,16.66 15,15S13.66,12 12,12M6,6H15V10H6V6Z"/>
                            </svg>
                            Update Information
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
        // testSwitchUser({fullName: "John Doe", email: "john.doe@healthcare.com", phone: "+1234567890"});
        // testSwitchUser({fullName: "Sarah Johnson", email: "sarah.j@healthcare.com", phone: "+9876543210"});

    </script>
</body>
</html>