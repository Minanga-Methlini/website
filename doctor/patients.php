<?php
// Static sample data - no database connection required
$stats = [
    'total_patients' => 156,
    'active_patients' => 142,
    'inactive_patients' => 14,
    'new_today' => 3
];

// Sample patient data
$patients = [
    [
        'id' => 1,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@email.com',
        'phone' => '+1-555-0123',
        'date_of_birth' => '1985-03-15',
        'blood_group' => 'A+',
        'status' => 'active',
        'total_appointments' => 12,
        'last_visit' => '2024-08-10'
    ],
    [
        'id' => 2,
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'email' => 'sarah.johnson@email.com',
        'phone' => '+1-555-0456',
        'date_of_birth' => '1990-07-22',
        'blood_group' => 'B+',
        'status' => 'active',
        'total_appointments' => 8,
        'last_visit' => '2024-08-12'
    ],
    [
        'id' => 3,
        'first_name' => 'Michael',
        'last_name' => 'Brown',
        'email' => 'michael.brown@email.com',
        'phone' => '+1-555-0789',
        'date_of_birth' => '1978-11-05',
        'blood_group' => 'O-',
        'status' => 'active',
        'total_appointments' => 15,
        'last_visit' => '2024-08-08'
    ],
    [
        'id' => 4,
        'first_name' => 'Emily',
        'last_name' => 'Davis',
        'email' => 'emily.davis@email.com',
        'phone' => '+1-555-0321',
        'date_of_birth' => '1995-02-18',
        'blood_group' => 'AB+',
        'status' => 'active',
        'total_appointments' => 5,
        'last_visit' => '2024-08-14'
    ],
    [
        'id' => 5,
        'first_name' => 'David',
        'last_name' => 'Wilson',
        'email' => 'david.wilson@email.com',
        'phone' => '+1-555-0654',
        'date_of_birth' => '1982-09-30',
        'blood_group' => 'A-',
        'status' => 'inactive',
        'total_appointments' => 3,
        'last_visit' => '2024-07-20'
    ],
    [
        'id' => 6,
        'first_name' => 'Lisa',
        'last_name' => 'Anderson',
        'email' => 'lisa.anderson@email.com',
        'phone' => '+1-555-0987',
        'date_of_birth' => '1988-12-12',
        'blood_group' => 'O+',
        'status' => 'active',
        'total_appointments' => 9,
        'last_visit' => '2024-08-11'
    ]
];

// Handle search and filters (for demonstration)
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Filter patients based on search and status
$filtered_patients = $patients;

if (!empty($search)) {
    $filtered_patients = array_filter($filtered_patients, function($patient) use ($search) {
        $searchLower = strtolower($search);
        return strpos(strtolower($patient['first_name']), $searchLower) !== false ||
               strpos(strtolower($patient['last_name']), $searchLower) !== false ||
               strpos(strtolower($patient['email']), $searchLower) !== false ||
               strpos($patient['phone'], $search) !== false;
    });
}

if (!empty($status_filter)) {
    $filtered_patients = array_filter($filtered_patients, function($patient) use ($status_filter) {
        return $patient['status'] === $status_filter;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records - Medicare Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .logo-text h1 {
            font-size: 1.5rem;
            color: #4f46e5;
            font-weight: 600;
        }

        .logo-text p {
            font-size: 0.9rem;
            color: #6b7280;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: background 0.2s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .page-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .stat-icon.total { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-icon.active { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.inactive { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.new { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stat-info p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .search-section {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .filter-select {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .patients-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .table-header {
            background: #f8fafc;
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .patient-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .patient-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .patient-details h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .patient-details p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fef3c7;
            color: #92400e;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
        }

        .btn-info {
            background: #0ea5e9;
            color: white;
        }

        .btn-info:hover {
            background: #0284c7;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .demo-notice {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .demo-notice i {
            color: #d97706;
            font-size: 1.2rem;
        }

        .demo-notice p {
            color: #92400e;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input {
                min-width: auto;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="logo-text">
                <h1>Doctor</h1>
                <p>Medicare Management System</p>
            </div>
        </div>
        <div class="user-info">
            <div class="status-indicator"></div>
            <span>Dr. John Smith</span>
            <a href="#" class="logout-btn" onclick="alert('This is a demo page. No logout functionality.')">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <div class="container">


        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <div class="page-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h1>Patient Records</h1>
                    <p>Manage and access patient medical history</p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_patients']); ?></h3>
                    <p>Total Patients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['active_patients']); ?></h3>
                    <p>Active Patients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon inactive">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['inactive_patients']); ?></h3>
                    <p>Inactive Patients</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon new">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['new_today']); ?></h3>
                    <p>New Today</p>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Search patients by name, phone, or email..."
                       value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                
                <a href="?" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
                
                <a href="#" class="btn btn-success" onclick="alert('This is a demo page. Add patient functionality not implemented.')">
                    <i class="fas fa-plus"></i>
                    Add Patient
                </a>
            </form>
        </div>

        <!-- Patients Table -->
        <div class="patients-table">
            <div class="table-header">
                <h2 class="table-title">Patient List</h2>
                <span><?php echo count($filtered_patients); ?> patients found</span>
            </div>
            
            <?php if (empty($filtered_patients)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No Patients Found</h3>
                    <p>No patients match your search criteria. Try adjusting your filters.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Contact Info</th>
                                <th>Age</th>
                                <th>Blood Group</th>
                                <th>Total Visits</th>
                                <th>Last Visit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered_patients as $patient): ?>
                            <tr>
                                <td>
                                    <div class="patient-info">
                                        <div class="patient-avatar">
                                            <?php 
                                            $initials = strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1));
                                            echo $initials;
                                            ?>
                                        </div>
                                        <div class="patient-details">
                                            <h4><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h4>
                                            <p>ID: #<?php echo str_pad($patient['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['phone']); ?></p>
                                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['email']); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    if ($patient['date_of_birth']) {
                                        $birthDate = new DateTime($patient['date_of_birth']);
                                        $today = new DateTime();
                                        $age = $today->diff($birthDate)->y;
                                        echo $age . ' years';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($patient['blood_group'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge"><?php echo $patient['total_appointments']; ?> visits</span>
                                </td>
                                <td>
                                    <?php 
                                    if ($patient['last_visit']) {
                                        echo date('M d, Y', strtotime($patient['last_visit']));
                                    } else {
                                        echo 'No visits';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $patient['status']; ?>">
                                        <?php echo ucfirst($patient['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="#" class="btn btn-info btn-sm" onclick="alert('View patient details - Demo functionality')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-warning btn-sm" onclick="alert('Edit patient - Demo functionality')">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-primary btn-sm" onclick="alert('Patient appointments - Demo functionality')">
                                            <i class="fas fa-calendar"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-submit search form on filter change
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });

        // Show demo alerts for non-functional buttons
        function showDemoAlert(action) {
            alert(`This is a demo page. ${action} functionality is not implemented.`);
            return false;
        }
    </script>
</body>
</html>