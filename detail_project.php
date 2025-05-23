<?php
// project_detail.php

require_once 'backend/config.php';
session_start();

// Check if project ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid project ID");
}

$project_id = $_GET['id'];

// Fetch project details with freelancer and client info
$project_query = "SELECT p.*, 
                 f.full_name AS freelancer_name, f.email AS freelancer_email, 
                 f.profile_picture AS freelancer_photo, f.job_title AS freelancer_job_title,
                 f.rating AS freelancer_rating, f.experience_level AS freelancer_experience,
                 u.name AS client_name, u.email AS client_email
                 FROM projects p
                 JOIN freelancers f ON p.freelancer_id = f.id
                 JOIN users u ON p.client_id = u.id
                 WHERE p.id = ?";
$stmt = $conn->prepare($project_query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project_result = $stmt->get_result();

if ($project_result->num_rows === 0) {
    die("Project not found");
}

$project = $project_result->fetch_assoc();

// Check if current user is the client for this project
$is_client = isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $project['client_id']);

// Fetch activity logs for this project
$logs_query = "SELECT pl.*, 
              COALESCE(f.full_name, u.name) AS user_name,
              COALESCE(f.profile_picture, 'image/foto1.png') AS user_photo
              FROM project_logs pl
              LEFT JOIN freelancers f ON pl.user_id = f.id AND f.id = ?
              LEFT JOIN users u ON pl.user_id = u.id AND u.id = ?
              WHERE pl.project_id = ?
              ORDER BY pl.created_at DESC";
$stmt = $conn->prepare($logs_query);
$stmt->bind_param("iii", $project['freelancer_id'], $project['client_id'], $project_id);
$stmt->execute();
$logs_result = $stmt->get_result();
$logs = $logs_result->fetch_all(MYSQLI_ASSOC);

// Check if rating already exists
$rating_exists = false;
if ($is_client && $project['status'] == 'completed') {
    $rating_check = $conn->prepare("SELECT * FROM project_ratings WHERE project_id = ? AND client_id = ?");
    $rating_check->bind_param("ii", $project_id, $_SESSION['user_id']);
    $rating_check->execute();
    $rating_exists = $rating_check->get_result()->num_rows > 0;
    $rating_check->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Detail - <?php echo htmlspecialchars($project['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .img-circle-frame {
            border-radius: 50%;
            object-fit: cover;
            width: 40px;
            height: 40px;
        }
        .progress-step {
            position: relative;
            padding-left: 30px;
            margin-bottom: 15px;
        }
        .progress-step .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 24px;
            height: 24px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            font-size: 12px;
        }
        .progress-step.completed .step-number {
            background: #198754;
        }
        .progress-step.active .step-number {
            background: #ffc107;
            color: #000;
        }
        .rating-stars {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
        }
        .rating-stars .fas {
            color: #ffc107;
        }
        .rating-stars:hover .fas {
            color: #ffc107;
        }
        .rating-stars .far:hover {
            color: #ffc107;
        }
        .footer {
            margin-top: 20px;
            background-color: white;
            color: black;
            padding: 40px 0;
            font-family: Arial, sans-serif;
        }
        .footer-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .footer-sections {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
        }
        .footer-section {
            flex: 1;
            min-width: 200px;
        }
        .footer-section h5 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        .footer-section p {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }
        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-section ul li {
            margin-bottom: 8px;
        }
        .footer-section ul li a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
        }
        .footer-section ul li a:hover {
            text-decoration: underline;
        }
        .footer-subscribe {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .social-icons {
            display: flex;
            gap: 15px;
        }
        .social-icons img {
            width: 30px;
            height: 30px;
        }
        .footer-bottom {
            background-color: #4584FF;
            padding: 15px 0;
            margin-top: 30px;
        }
        .footer-brand {
            display: flex;
            align-items: center;
        }
        .footer-brand img {
            width: 35px;
            height: 35px;
            margin-right: 10px;
        }
        .footer-brand span {
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .card-footer {
            padding: 15px 20px;
            font-size: 0.9rem;
            color: #555;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm fixed-top" style="background-color: #C2DBEF;">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold me-auto" href="#">
                <img src="image/download.png" alt="icon" width="30" height="30" class="me-2">
                Workmates
            </a>

            <form class="d-flex me-auto" role="search">
                <input class="form-control search-input me-2" type="search" placeholder="Search..." aria-label="Search">
                <button class="btn btn-outline-dark" type="submit"><i class="bi bi-search"></i></button>
            </form>
            
            <div class="nav-icons ms-auto d-flex align-items-center">
                <a href="#" class="me-2"><img src="image/Socialicons.png" alt="WhatsApp" width="20"></a> 
                <a href="#" class="me-2"><img src="image/Notification.png" alt="Notification" width="20"></a>
                <a href="#" class="me-2"><img src="image/Message.png" alt="Message" width="20"></a>
                <a href="dashboard.php" class="nav-link me-2">Dashboard</a>
                <a href="freelancers.php" class="nav-link me-2">Find Freelancers</a>
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="image/profil.svg" alt="Profile" width="30" height="30" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profileclient.php">Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="logout.php" method="post" id="logout-form">
                                <button type="submit" class="dropdown-item" id="logout-btn">Log Out</button>
                            </form>
                        </li>
                    </ul>
                </div>

                <script>
                document.getElementById('logout-btn').addEventListener('click', function(e) {
                    e.preventDefault();
                    if(confirm('Are you sure you want to log out?')) {
                        document.getElementById('logout-form').submit();
                    }
                });
                </script>
            </div>
        </div>
    </nav>

    <br><br>

    <div class="container my-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Project Progress - <?php echo htmlspecialchars($project['title']); ?></h5>
                <span class="badge bg-<?php 
                    echo $project['status'] == 'completed' ? 'success' : 
                         ($project['status'] == 'in_progress' ? 'warning' : 'secondary'); 
                ?> text-dark">
                    <?php echo ucwords(str_replace('_', ' ', $project['status'])); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Project Details</h6>
                        <p class="mb-1"><strong>Project Name:</strong> <?php echo htmlspecialchars($project['title']); ?></p>
                        <p class="mb-1"><strong>Description:</strong> <?php echo htmlspecialchars($project['description'] ?? 'No description'); ?></p>
                        <p class="mb-1"><strong>Client:</strong> <?php echo htmlspecialchars($project['client_name']); ?></p>
                        <p class="mb-1"><strong>Freelancer:</strong> <?php echo htmlspecialchars($project['freelancer_name']); ?>
                            <span class="badge bg-info text-dark ms-2"><?php echo htmlspecialchars($project['freelancer_job_title']); ?></span>
                            <span class="badge bg-light text-dark ms-1">
                                <?php echo htmlspecialchars($project['freelancer_experience']); ?> 
                                (Rating: <?php echo $project['freelancer_rating'] ?? 'N/A'; ?>)
                            </span>
                        </p>
                        <p class="mb-1"><strong>Deadline:</strong> <?php echo $project['deadline'] ? date('d F Y', strtotime($project['deadline'])) : 'Not set'; ?></p>
                        <p class="mb-1"><strong>Consultation Date:</strong> <?php echo $project['consultation_date'] ? date('d F Y', strtotime($project['consultation_date'])) : 'Not scheduled'; ?></p>
                        <p class="mb-1"><strong>Price:</strong> IDR <?php echo number_format($project['price'], 0, ',', '.'); ?></p>
                        <p class="mb-3"><strong>Progress:</strong> <?php echo $project['progress']; ?>%</p>
                        
                        <div class="d-flex gap-2">
                            <a href="chat.php?project_id=<?php echo $project_id; ?>" class="btn btn-outline-primary">💬 Chat Now</a>
                            
                            <?php if ($project['status'] != 'completed'): ?>
                                <!-- Button to trigger Complete modal -->
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeProjectModal">
                                    <i class="bi bi-check-circle"></i> Mark as Complete
                                </button>
                                
                                <!-- Button to trigger Revision modal -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#requestRevisionModal">
                                    <i class="bi bi-arrow-counterclockwise"></i> Request Revision
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="mt-3">
                            <?php if (!empty($project['hasil_project'])): ?>
                                <div class="mb-2">
                                    <h6 class="fw-bold">Project Results</h6>
                                    <a href="<?php echo htmlspecialchars($project['hasil_project']); ?>" 
                                    class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="bi bi-download"></i> Download Current Results
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Complete Project Modal -->
                        <div class="modal fade" id="completeProjectModal" tabindex="-1" aria-labelledby="completeProjectModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="backend/update_project_status.php" method="POST">
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <input type="hidden" name="new_status" value="completed">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="completeProjectModalLabel">Mark Project as Complete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="completion_message" class="form-label">Completion Message (Optional)</label>
                                                <textarea class="form-control" id="completion_message" name="message" rows="3"
                                                        placeholder="Add any final notes about this project"></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="completion_rating" class="form-label">Rate this project (Optional)</label>
                                                <select class="form-select" id="completion_rating" name="rating">
                                                    <option value="">Select rating</option>
                                                    <option value="5">★★★★★ - Excellent</option>
                                                    <option value="4">★★★★☆ - Very Good</option>
                                                    <option value="3">★★★☆☆ - Good</option>
                                                    <option value="2">★★☆☆☆ - Fair</option>
                                                    <option value="1">★☆☆☆☆ - Poor</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Confirm Completion</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Request Revision Modal -->
                        <div class="modal fade" id="requestRevisionModal" tabindex="-1" aria-labelledby="requestRevisionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="backend/update_project_status.php" method="POST">
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <input type="hidden" name="new_status" value="revision">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="requestRevisionModalLabel">Request Project Revision</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="revision_message" class="form-label">Revision Request Details</label>
                                                <textarea class="form-control" id="revision_message" name="message" rows="3" required
                                                        placeholder="Describe what needs to be revised"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning">Submit Revision Request</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Progress Tracking</h6>
                        <div class="progress mb-4" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped <?php echo $project['status'] == 'completed' ? 'bg-success' : ''; ?>" 
                                 role="progressbar" style="width: <?php echo $project['progress']; ?>%" 
                                 aria-valuenow="<?php echo $project['progress']; ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo $project['progress']; ?>%
                            </div>
                        </div>
                        
                        <div class="progress-steps">
                            <div class="progress-step <?php echo $project['progress'] >= 20 ? 'completed' : ($project['progress'] > 0 ? 'active' : ''); ?>">
                                <span class="step-number">1</span>
                                <h6>Client & Project Setup</h6>
                                <p class="text-muted small">Initial project setup and requirements gathering</p>
                            </div>
                            
                            <div class="progress-step <?php echo $project['progress'] >= 40 ? 'completed' : ($project['progress'] > 20 ? 'active' : ''); ?>">
                                <span class="step-number">2</span>
                                <h6>Planning & Design</h6>
                                <p class="text-muted small">Creating project plan and design mockups</p>
                            </div>
                            
                            <div class="progress-step <?php echo $project['progress'] >= 60 ? 'completed' : ($project['progress'] > 40 ? 'active' : ''); ?>">
                                <span class="step-number">3</span>
                                <h6>Development</h6>
                                <p class="text-muted small">Implementing the project requirements</p>
                            </div>
                            
                            <div class="progress-step <?php echo $project['progress'] >= 80 ? 'completed' : ($project['progress'] > 60 ? 'active' : ''); ?>">
                                <span class="step-number">4</span>
                                <h6>Testing & Debugging</h6>
                                <p class="text-muted small">Quality assurance and bug fixing</p>
                            </div>
                            
                            <div class="progress-step <?php echo $project['progress'] >= 100 ? 'completed' : ($project['progress'] > 80 ? 'active' : ''); ?>">
                                <span class="step-number">5</span>
                                <h6>Deployment & Final Review</h6>
                                <p class="text-muted small">Delivering the final product</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client-specific controls -->
                <?php if ($is_client): ?>
                    <?php if ($project['status'] == 'completed' && !$rating_exists): ?>
                    <div class="mt-4 p-3 border rounded bg-light">
                        <h5>Rate This Project</h5>
                        <form id="ratingForm" action="backend/submit_rating.php" method="POST">
                            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                            <input type="hidden" name="freelancer_id" value="<?php echo $project['freelancer_id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating-stars">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                    <input type="hidden" name="rating" id="ratingValue" value="0">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="review" class="form-label">Review (Optional)</label>
                                <textarea class="form-control" id="review" name="review" rows="3"></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Rating</button>
                        </form>
                    </div>
                    <?php elseif ($rating_exists): ?>
                    <div class="mt-4 p-3 border rounded bg-light">
                        <h5>Your Rating</h5>
                        <p>Thank you for rating this project!</p>
                    </div>
                    <?php endif; ?>
                    
                   
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Activity Log</h6>
                    <?php if (!$is_client): ?>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addLogModal">
                        Add Log Entry
                    </button>
                    <?php endif; ?>
                </div>
                
                <div class="activity-logs">
                    <?php foreach ($logs as $log): ?>
                    <div class="log-entry mb-3 d-flex">
                        <img src="<?php echo htmlspecialchars($log['user_photo']) ?? 'image/foto1.png' ?>" alt="User" class="img-circle-frame me-3" width="40" height="40">
                        <div>
                            <div class="d-flex align-items-center">
                                <strong class="me-2"><?php echo htmlspecialchars($log['user_name'] ?? ''); ?></strong>
                                <small class="text-muted"><?php echo date('M j, Y H:i', strtotime($log['created_at'])); ?></small>
                            </div>
                            <p class="mb-0"><?php echo htmlspecialchars($log['message']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <div class="text-center py-3 text-muted">
                        No activity logs yet.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Progress Modal (for freelancer) -->
    <?php if (!$is_client): ?>
    <div class="modal fade" id="updateProgressModal" tabindex="-1" aria-labelledby="updateProgressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="backend/update_progress.php" method="POST">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateProgressModalLabel">Update Project Progress</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="progress" class="form-label">Current Progress (%)</label>
                            <input type="range" class="form-range" min="0" max="100" step="5" 
                                   id="progress" name="progress" value="<?php echo $project['progress']; ?>">
                            <div class="text-center mt-2">
                                <span id="progressValue" class="fw-bold"><?php echo $project['progress']; ?>%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Project Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" <?php echo $project['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $project['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $project['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Update Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" 
                                      placeholder="Describe what has been completed"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Log Modal (for freelancer) -->
    <div class="modal fade" id="addLogModal" tabindex="-1" aria-labelledby="addLogModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_log.php" method="POST">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLogModalLabel">Add Activity Log</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="log_message" class="form-label">Message</label>
                            <textarea class="form-control" id="log_message" name="message" rows="3" required 
                                      placeholder="Describe the activity or update"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-section">
                        <h5>About Workmates</h5>
                        <p>Workmates connects businesses with skilled freelancers across various industries. Our platform makes it easy to find the perfect talent for your projects.</p>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <div class="footer-section">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="index.html" class="text-decoration-none">Home</a></li>
                            <li><a href="#" class="text-decoration-none">About Us</a></li>
                            <li><a href="#" class="text-decoration-none">Freelancers</a></li>
                            <li><a href="#" class="text-decoration-none">Projects</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <div class="footer-section">
                        <h5>Services</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none">Web Development</a></li>
                            <li><a href="#" class="text-decoration-none">Graphic Design</a></li>
                            <li><a href="#" class="text-decoration-none">Digital Marketing</a></li>
                            <li><a href="#" class="text-decoration-none">Content Writing</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4">
                    <div class="footer-section">
                        <h5>Support</h5>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-decoration-none">FAQ</a></li>
                            <li><a href="#" class="text-decoration-none">Contact Us</a></li>
                            <li><a href="#" class="text-decoration-none">Privacy Policy</a></li>
                            <li><a href="#" class="text-decoration-none">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-lg-2">
                    <div class="footer-section">
                        <h5>Subscribe</h5>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Your email">
                        </div>
                        <button class="btn btn-primary w-100">Subscribe</button>
                        
                        
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-brand d-flex align-items-center">
                        <img src="image/download.png" alt="Workmates Logo" height="30">
                        <span class="ms-2 fw-bold">Workmates</span>
                    </div>
                </div>
                
                <div class="col-md-6 text-md-end">
                    <div class="language-selector mt-3 mt-md-0">
                        <button class="active">English</button>
                        <button>Bahasa</button>
                        <button>日本語</button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <p class="small text-muted mb-0">
                    &copy; <?php echo date('Y'); ?> Workmates. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update progress value display when slider changes
        document.getElementById('progress')?.addEventListener('input', function() {
            document.getElementById('progressValue').textContent = this.value + '%';
        });

        // Rating stars functionality
        document.querySelectorAll('.rating-stars .far, .rating-stars .fas').forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                const stars = this.parentElement.querySelectorAll('i');
                
                // Update visual stars
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
                
                // Update hidden input value
                document.getElementById('ratingValue').value = rating;
            });
        });
        
        // Form submission handling
        document.getElementById('ratingForm')?.addEventListener('submit', function(e) {
            if (document.getElementById('ratingValue').value == '0') {
                e.preventDefault();
                alert('Please select a rating before submitting');
            }
        });
    </script>
</body>
</html>