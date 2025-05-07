<?php
require_once 'backend/dashboard_backend.php';

// Get unread message count for notification badge
$unreadCount = 0;
if (isset($_SESSION['freelancer_id'])) {
    if ($conn) { // Check if the connection is still open
        $stmt = $conn->prepare("SELECT COUNT(*) FROM general_messages 
                               WHERE freelancer_id = ? AND is_read = 0 AND is_client = 1");
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['freelancer_id']);
            $stmt->execute();
            $unreadCount = $stmt->get_result()->fetch_row()[0];
            $stmt->close(); // Close the statement after use
        } else {
            echo "Failed to prepare statement: " . $conn->error;
        }
    } else {
        echo "Database connection is closed.";
    }
}

// Close the connection here if you want to manage it in this script
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1E3A5F;
            --secondary-color: #2B50AA;
            --light-blue: #C2DBEF;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Ubuntu Sans', sans-serif;
            padding-top: 70px;
        }
        
        .navbar {
            background-color: var(--light-blue) !important;
            font-family: 'Paytone One', sans-serif;
        }
        
        .project-card {
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s;
            height: 100%;
        }
        
        .project-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .unread-count {
            font-size: 0.6rem;
            display: <?= $unreadCount > 0 ? 'block' : 'none' ?>;
        }
        
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
        }
        
        .dropdown-item {
            border-radius: 6px;
            margin: 2px 6px;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #E6F0FF;
            color: var(--secondary-color);
        }
        
        #sidebar {
            background: transparent;
        }
        
        .circular-progress {
            width: 100px;
            height: 100px;
        }
        
        .circular-progress circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
        }
        
        .circular-progress .bg {
            stroke: #EAEAEA;
        }
        
        .circular-progress .progress {
            stroke: var(--primary-color);
            stroke-dasharray: 251;
            transition: stroke-dashoffset 0.5s ease;
        }
        
        footer {
            background-color: #f1f3f5;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold me-auto" href="dashboardfreelancer.php">
                <img src="image/download.png" alt="icon" width="30" height="30" class="me-2">
                Workmates
            </a>
            <div class="nav-icons ms-auto d-flex align-items-center">
                <a href="#" class="me-2" data-bs-toggle="tooltip" title="WhatsApp">
                    <img src="image/Socialicons.png" alt="WhatsApp" width="20">
                </a> 
                
                <div class="dropdown me-3">
                    <a href="indexchat.php" class="position-relative">
                        <img src="image/Message.png" alt="Message" width="20">
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger unread-count">
                            <?= $unreadCount > 0 ? $unreadCount : '' ?>
                        </span>
                    </a>
                </div>
                
                <a href="dashboardfreelancer.php" class="nav-link me-3 fw-bold" style="color: black; font-weight: lighter ;">Dashboard</a>
                
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'image/profil.svg') ?>" 
                             alt="Profile" width="30" height="30" class="rounded-circle border border-2 border-primary">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="ProfileFreelancer.php">Profile</a></li>
                            <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="logout.php" method="post" id="logout-form">
                                    <button type="submit" class="dropdown-item" id="logout-btn">Log Out</button>
                                </form>
                            </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Profile -->
            <div class="col-md-3 p-3 text-dark" id="sidebar">
                <h5><strong>Good morning, </strong><?= htmlspecialchars($freelancer['full_name']) ?></h5>
                <div class="profile-card text-center">
                    <div class="progress-container mt-3 d-flex flex-column align-items-center">
                        <svg class="circular-progress" viewBox="0 0 100 100">
                            <circle class="bg" cx="50" cy="50" r="40"/>
                            <circle class="progress" cx="50" cy="50" r="40"
                                stroke-dashoffset="<?= 251 * (1 - $freelancer['avg_progress']/100) ?>"/>
                            <text x="50" y="55" text-anchor="middle" font-size="14" font-weight="bold" fill="#000">
                                <?= $freelancer['avg_progress'] ?>%
                            </text>
                        </svg>
                        <p class="mt-2 fw-bold">Project progress</p>
                    </div>
                    <p class="mb-0">Your income</p>
                    <h4 class="text-danger fw-bold">Rp. <?= number_format($freelancer['total_income'], 0, ',', '.') ?></h4>
                    <p class="mb-0">Total Projects</p>
                    <h4 class="text-danger"><?= $freelancer['total_projects'] ?></h4>
                </div>
            </div>
            
            <!-- Workspace -->
            <div class="col-md-9" id="workspace">
                <h4 style="font-family: 'Paytone One'">Workspace</h4>
                
                <!-- Filters -->
                <div class="d-flex gap-2 mb-4">
                    <select class="form-select" id="statusFilter">
                        <option value="all" selected>All status</option>
                        <option value="in_progress">In progress</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                    <input type="date" class="form-control" id="dateFilter">
                    <!-- <select class="form-select" id="jobTypeFilter">
                        <option value="all" selected>All kinds of job</option>
                        <option value="paid">Paid</option>
                        <option value="internship">Internship</option>
                        <option value="help">Help</option>
                    </select> -->
                    <button class="btn fw-bold <?= $freelancer['is_available'] ? 'btn-success' : 'btn-danger' ?>" 
                            id="availabilityBtn">
                        <?= $freelancer['is_available'] ? 'Available' : 'Not Available' ?>
                    </button>
                </div>

                <!-- Projects List -->
                <div class="row">
                    <?php if ($projects_result->num_rows > 0): ?>
                        <?php while($project = $projects_result->fetch_assoc()): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card project-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
                                            <span class="badge <?= 
                                                $project['status'] == 'in_progress' ? 'bg-warning text-dark' : 
                                                ($project['status'] == 'completed' ? 'bg-success' : 'bg-secondary') ?> 
                                                status-badge">
                                                <?= ucfirst(str_replace('_', ' ', $project['status'])) ?>
                                            </span>
                                        </div>
                                        <p class="card-text text-muted small mb-2">
                                            Deadline: <?= date('d M Y', strtotime($project['deadline'])) ?>
                                        </p>
                                        <div class="progress mb-3">
                                            <div class="progress-bar bg-primary" 
                                                 role="progressbar" 
                                                 style="width: <?= $project['progress'] ?>%" 
                                                 aria-valuenow="<?= $project['progress'] ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold">Rp. <?= number_format($project['price'], 0, ',', '.') ?></span>
                                            <a href="project.php?id=<?= $project['id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                No active projects found. Start by bidding on new projects!
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <h5>About Workmates</h5>
                    <p>Workmates connects freelancers with clients through professional profiles showcasing skills, portfolios, and certifications.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.html" class="text-decoration-none">Home</a></li>
                        <li><a href="#" class="text-decoration-none">About</a></li>
                        <li><a href="#" class="text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">Coding</a></li>
                        <li><a href="#" class="text-decoration-none">Design</a></li>
                        <li><a href="#" class="text-decoration-none">Consultation</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">© 2023 Workmates. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Update unread message count periodically
        function updateUnreadCount() {
            fetch('backend/get_unread_count.php')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.unread-count');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }
        
        // Update every 10 seconds
        setInterval(updateUnreadCount, 10000);
        
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Availability toggle
            document.getElementById('availabilityBtn')?.addEventListener('click', function() {
                const isAvailable = this.textContent === 'Available';
                const newState = !isAvailable;
                
                // Optimistic UI update
                this.textContent = newState ? 'Available' : 'Not Available';
                this.classList.toggle('btn-success');
                this.classList.toggle('btn-danger');
                
                // Send update to server
                fetch('backend/update_availability.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        available: newState
                    })
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert on error
                    this.textContent = isAvailable ? 'Available' : 'Not Available';
                    this.classList.toggle('btn-success');
                    this.classList.toggle('btn-danger');
                });
            });
            
            // Filter functionality
            const filters = {
                status: document.getElementById('statusFilter'),
                date: document.getElementById('dateFilter'),
                jobType: document.getElementById('jobTypeFilter')
            };
            
            Object.values(filters).forEach(filter => {
                filter.addEventListener('change', function() {
                    const params = new URLSearchParams();
                    
                    if (filters.status.value !== 'all') params.append('status', filters.status.value);
                    if (filters.date.value) params.append('date', filters.date.value);
                    if (filters.jobType.value !== 'all') params.append('job_type', filters.jobType.value);
                    
                    window.location.href = 'dashboardfreelancer.php?' + params.toString();
                });
            });
        });
    </script>
</body>
</html>