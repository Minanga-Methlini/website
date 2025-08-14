<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<script>
// Sample appointment data (converted from PHP to JavaScript)
const appointments = [
    {
        id: 1,
        doctor: 'Dr. Sarah Johnson',
        specialty: 'General Pediatrics',
        department: 'Pediatrics',
        date: '2025-08-23',
        time: '11:30 AM',
        status: 'pending',
        type: 'Follow-up',
        location: 'Building A, Room 205',
        duration: '30 min',
        notes: 'Annual checkup and vaccination review'
    },
    {
        id: 2,
        doctor: 'Dr. John Smith',
        specialty: 'Interventional Cardiology',
        department: 'Cardiology',
        date: '2025-08-15',
        time: '11:00 AM',
        status: 'confirmed',
        type: 'Consultation',
        location: 'Building B, Room 302',
        duration: '45 min',
        notes: 'Cardiac stress test results discussion'
    },
    {
        id: 3,
        doctor: 'Dr. John Smith',
        specialty: 'Interventional Cardiology',
        department: 'Cardiology',
        date: '2025-08-14',
        time: '9:30 AM',
        status: 'completed',
        type: 'Procedure',
        location: 'Building B, Room 301',
        duration: '60 min',
        notes: 'Cardiac catheterization procedure completed successfully'
    },
    {
        id: 4,
        doctor: 'Dr. Emily Davis',
        specialty: 'Internal Medicine',
        department: 'Internal Medicine',
        date: '2025-07-28',
        time: '2:15 PM',
        status: 'completed',
        type: 'Routine',
        location: 'Building C, Room 105',
        duration: '30 min',
        notes: 'Blood pressure monitoring and medication adjustment'
    },
    {
        id: 5,
        doctor: 'Dr. Michael Brown',
        specialty: 'Sports Medicine',
        department: 'Orthopedics',
        date: '2025-07-20',
        time: '10:45 AM',
        status: 'completed',
        type: 'Follow-up',
        location: 'Building D, Room 201',
        duration: '25 min',
        notes: 'Knee rehabilitation progress evaluation'
    },
    {
        id: 6,
        doctor: 'Dr. Lisa Wilson',
        specialty: 'Dermatology',
        department: 'Dermatology',
        date: '2025-07-12',
        time: '3:00 PM',
        status: 'completed',
        type: 'Consultation',
        location: 'Building A, Room 310',
        duration: '40 min',
        notes: 'Skin examination and mole mapping'
    },
    {
        id: 7,
        doctor: 'Dr. Robert Taylor',
        specialty: 'Gastroenterology',
        department: 'Gastroenterology',
        date: '2025-06-15',
        time: '1:30 PM',
        status: 'cancelled',
        type: 'Procedure',
        location: 'Building B, Room 405',
        duration: '90 min',
        notes: 'Colonoscopy - rescheduled due to patient illness'
    }
];

function getStatusClass(status) {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'confirmed':
            return 'bg-blue-100 text-blue-800';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getStatusIcon(status) {
    switch (status) {
        case 'completed':
            return '✓';
        case 'confirmed':
            return '●';
        case 'pending':
            return '○';
        case 'cancelled':
            return '✕';
        default:
            return '○';
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}
</script>

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i>
                        Appointment History
                    </h1>
                    <p class="text-gray-600 mt-1">View and manage your past and upcoming appointments</p>
                </div>
                <button onclick="exportHistory()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-download"></i>
                    Export History
                </button>
            </div>

            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search by doctor, specialty, or department..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        onkeyup="filterAppointments()"
                    />
                </div>
                
                <select id="statusFilter" onchange="filterAppointments()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </select>

                <select id="dateFilter" onchange="filterAppointments()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Dates</option>
                    <option value="recent">Last 30 Days</option>
                    <option value="older">Older than 30 Days</option>
                </select>
            </div>
        </div>

        <!-- Appointments List -->
        <div id="appointmentsList" class="space-y-4">
            <!-- Appointments will be dynamically generated by JavaScript -->
        </div>

        <!-- No Results Message -->
        <div id="noResults" class="bg-white rounded-lg shadow-sm p-12 text-center" style="display: none;">
            <i class="fas fa-calendar text-gray-400 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No appointments found</h3>
            <p class="text-gray-600">Try adjusting your search criteria or filters.</p>
        </div>

        <!-- Summary Stats -->
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600" id="completedCount">4</div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600" id="confirmedCount">1</div>
                    <div class="text-sm text-gray-600">Confirmed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600" id="pendingCount">1</div>
                    <div class="text-sm text-gray-600">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600" id="cancelledCount">1</div>
                    <div class="text-sm text-gray-600">Cancelled</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Generate appointment cards
function generateAppointmentCards() {
    const appointmentsList = document.getElementById('appointmentsList');
    appointmentsList.innerHTML = '';

    appointments.forEach(appointment => {
        const card = document.createElement('div');
        card.className = 'appointment-card bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow';
        card.setAttribute('data-doctor', appointment.doctor.toLowerCase());
        card.setAttribute('data-specialty', appointment.specialty.toLowerCase());
        card.setAttribute('data-department', appointment.department.toLowerCase());
        card.setAttribute('data-status', appointment.status);
        card.setAttribute('data-date', appointment.date);

        const statusClass = getStatusClass(appointment.status);
        const statusIcon = getStatusIcon(appointment.status);
        const formattedDate = formatDate(appointment.date);

        // Generate action buttons based on status
        let actionButtons = `
            <button onclick="viewDetails(${appointment.id})" class="flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium">
                <i class="fas fa-eye"></i>
                View Details
            </button>
        `;

        if (appointment.status === 'completed') {
            actionButtons += `
                <button onclick="downloadReport(${appointment.id})" class="flex items-center gap-1 text-green-600 hover:text-green-700 text-sm font-medium">
                    <i class="fas fa-download"></i>
                    Download Report
                </button>
            `;
        }

        if (appointment.status === 'pending' || appointment.status === 'confirmed') {
            actionButtons += `
                <button onclick="rescheduleAppointment(${appointment.id})" class="flex items-center gap-1 text-orange-600 hover:text-orange-700 text-sm font-medium">
                    <i class="fas fa-calendar-alt"></i>
                    Reschedule
                </button>
                <button onclick="cancelAppointment(${appointment.id})" class="flex items-center gap-1 text-red-600 hover:text-red-700 text-sm font-medium">
                    <i class="fas fa-times-circle"></i>
                    Cancel
                </button>
            `;
        }

        card.innerHTML = `
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Doctor Info -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-md text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">${appointment.doctor}</h3>
                            <p class="text-blue-600 font-medium">${appointment.specialty}</p>
                            <p class="text-gray-600 text-sm">${appointment.department}</p>
                        </div>
                    </div>

                    <!-- Appointment Details -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="text-right">
                            <div class="flex items-center gap-2 text-gray-900 font-medium">
                                <i class="fas fa-calendar"></i>
                                ${formattedDate}
                            </div>
                            <div class="flex items-center gap-2 text-gray-600 text-sm mt-1">
                                <i class="fas fa-clock"></i>
                                ${appointment.time} • ${appointment.duration}
                            </div>
                            <div class="flex items-center gap-2 text-gray-600 text-sm mt-1">
                                <i class="fas fa-map-marker-alt"></i>
                                ${appointment.location}
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex flex-col items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                                ${statusIcon} ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                            </span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                ${appointment.type}
                            </span>
                        </div>
                    </div>
                </div>

                ${appointment.notes ? `
                <!-- Notes -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-gray-600 text-sm">
                        <span class="font-medium">Notes:</span> ${appointment.notes}
                    </p>
                </div>
                ` : ''}

                <!-- Actions -->
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2">
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;

        appointmentsList.appendChild(card);
    });
}
function filterAppointments() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const appointmentCards = document.querySelectorAll('.appointment-card');
    const noResults = document.getElementById('noResults');
    
    let visibleCount = 0;
    const now = new Date();
    const thirtyDaysAgo = new Date(now.getTime() - (30 * 24 * 60 * 60 * 1000));

    appointmentCards.forEach(card => {
        const doctor = card.dataset.doctor;
        const specialty = card.dataset.specialty;
        const department = card.dataset.department;
        const status = card.dataset.status;
        const appointmentDate = new Date(card.dataset.date);

        // Check search term
        const matchesSearch = doctor.includes(searchTerm) || 
                             specialty.includes(searchTerm) || 
                             department.includes(searchTerm);

        // Check status filter
        const matchesStatus = statusFilter === 'all' || status === statusFilter;

        // Check date filter
        let matchesDate = true;
        if (dateFilter === 'recent') {
            matchesDate = appointmentDate >= thirtyDaysAgo;
        } else if (dateFilter === 'older') {
            matchesDate = appointmentDate < thirtyDaysAgo;
        }

        // Show/hide card based on filters
        if (matchesSearch && matchesStatus && matchesDate) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show/hide no results message
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    
    // Update summary counts
    updateSummaryCounts();
}

// Update summary counts based on visible appointments
function updateSummaryCounts() {
    const visibleCards = document.querySelectorAll('.appointment-card[style="display: block;"], .appointment-card:not([style*="display: none"])');
    
    let counts = {
        completed: 0,
        confirmed: 0,
        pending: 0,
        cancelled: 0
    };

    visibleCards.forEach(card => {
        if (card.style.display !== 'none') {
            const status = card.dataset.status;
            if (counts.hasOwnProperty(status)) {
                counts[status]++;
            }
        }
    });

    document.getElementById('completedCount').textContent = counts.completed;
    document.getElementById('confirmedCount').textContent = counts.confirmed;
    document.getElementById('pendingCount').textContent = counts.pending;
    document.getElementById('cancelledCount').textContent = counts.cancelled;
}

// Action functions
function viewDetails(appointmentId) {
    alert(Viewing details for appointment ID: ${appointmentId});
    // In a real application, this would open a modal or navigate to a details page
}

function downloadReport(appointmentId) {
    alert(Downloading report for appointment ID: ${appointmentId});
    // In a real application, this would trigger a file download
}

function rescheduleAppointment(appointmentId) {
    if (confirm('Are you sure you want to reschedule this appointment?')) {
        alert(Rescheduling appointment ID: ${appointmentId});
        // In a real application, this would open a rescheduling interface
    }
}

function cancelAppointment(appointmentId) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        alert(Cancelling appointment ID: ${appointmentId});
        // In a real application, this would update the appointment status
        // You could also update the UI to reflect the cancellation
    }
}

function exportHistory() {
    alert('Exporting appointment history...');
    // In a real application, this would generate and download a PDF or CSV file
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Generate appointment cards
    generateAppointmentCards();
    // Set initial summary counts
    updateSummaryCounts();
});
</script>

</body>
</html>