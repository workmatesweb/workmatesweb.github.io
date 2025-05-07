<?php
// project_detail.php

require_once 'backend/config.php';

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

// Fetch activity logs for this project
$logs_query = "SELECT pl.*, 
              COALESCE(f.full_name, u.name) AS user_name,
              COALESCE(f.profile_picture, 'image/default-profile.jpg') AS user_photo
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

// Fetch client rating for this project
$rating_query = "SELECT rating, review FROM project_ratings 
                WHERE project_id = ? AND client_id = ? AND freelancer_id = ?";
$stmt = $conn->prepare($rating_query);
$stmt->bind_param("iii", $project_id, $project['client_id'], $project['freelancer_id']);
$stmt->execute();
$rating_result = $stmt->get_result();
$rating = $rating_result->fetch_assoc();

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
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
        .footer-bottom .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .rating-stars {
            color: #ffc107;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg shadow-sm fixed-top" style="background-color: #C2DBEF; font-family: 'Paytone One'">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold me-auto" href="#">
                <img src="image/download.png" alt="icon" width="30" height="30" class="me-2">
                Workmates
            </a>
            <div class="nav-icons ms-auto d-flex align-items-center">
                <a href="#" class="me-2"><img src="image/Socialicons.png" alt="WhatsApp" width="20"></a> 
                <a href="#" class="me-2"><img src="image/Notification.png" alt="Notification" width="20"></a>
                <a href="#" class="me-2"><img src="image/Message.png" alt="Message" width="20"></a>
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="image/profil.svg" alt="Profile" width="30" height="30" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="pofileclient.php">Profile</a></li>
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
                        
                        <!-- Client Rating Section -->
                        <?php if ($rating): ?>
                        <div class="mt-4 border-top pt-3">
                            <h6 class="fw-bold">Client Rating</h6>
                            <div class="d-flex align-items-center mb-2">
                                <div class="rating-stars">
                                    <?php 
                                    $fullStars = floor($rating['rating']);
                                    $halfStar = ($rating['rating'] - $fullStars) >= 0.5 ? 1 : 0;
                                    $emptyStars = 5 - $fullStars - $halfStar;
                                    
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<i class="bi bi-star-fill"></i>';
                                    }
                                    if ($halfStar) {
                                        echo '<i class="bi bi-star-half"></i>';
                                    }
                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<i class="bi bi-star"></i>';
                                    }
                                    ?>
                                </div>
                                <span class="ms-2 fw-bold"><?php echo number_format($rating['rating'], 1); ?>/5</span>
                            </div>
                            <?php if (!empty($rating['review'])): ?>
                            <div class="review-box bg-light p-3 rounded">
                                <p class="mb-0"><?php echo htmlspecialchars($rating['review']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php elseif ($project['status'] == 'completed'): ?>
                        <div class="mt-4 border-top pt-3">
                            <p class="text-muted">No rating submitted yet for this project.</p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2 mt-3">
                            <a href="chat.php?project_id=<?php echo $project_id; ?>" class="btn btn-outline-primary">💬 Chat Now</a>
                            <?php if ($project['status'] != 'completed'): ?>
                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#updateProgressModal">
                                    Update Progress
                                </button>
                            <?php endif; ?>
                        </div>
                        <!-- Add this after the existing buttons in the card-body section -->
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
                            
                            <?php if ($project['status'] != 'completed'): ?>
                                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#uploadResultsModal">
                                    <i class="bi bi-upload"></i> Upload Interim Results
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Upload Results Modal -->
                    <div class="modal fade" id="uploadResultsModal" tabindex="-1" aria-labelledby="uploadResultsModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="backend/upload_results.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="uploadResultsModalLabel">Upload Project Results</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="project_results" class="form-label">Select File</label>
                                            <input class="form-control" type="file" id="project_results" name="project_results" required>
                                            <small class="text-muted">Accepted formats: PDF, ZIP, DOCX, JPG, PNG (Max 10MB)</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="results_message" class="form-label">Message (Optional)</label>
                                            <textarea class="form-control" id="results_message" name="message" rows="3"
                                                    placeholder="Describe what you're uploading"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Upload Results</button>
                                    </div>
                                </form>
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
            </div>
            
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Activity Log</h6>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addLogModal">
                        Add Log Entry
                    </button>
                </div>
                
                <div class="activity-logs">
                    <?php foreach ($logs as $log): ?>
                    <div class="log-entry mb-3 d-flex">
                        <img src="<?php echo htmlspecialchars($log['user_photo']); ?>" alt="User" class="img-circle-frame me-3" width="40" height="40">
                        <div>
                            <div class="d-flex align-items-center">
                                <strong class="me-2"><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></strong>
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

    <!-- Update Progress Modal -->
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
                                <option value="review" <?php echo $project['status'] == 'review' ? 'selected' : ''; ?>>Review Client</option>
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

    <!-- Add Log Modal -->
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

    <footer class="footer">
      <div class="container"> 
          <div class="footer-content">
              <div class="footer-sections">
                  <div class="footer-section">
                      <h5>Short Brief About Us</h5>
                      <p>WorkMates is a website that serves as a bridge between freelancers and those in need of their services. Through this platform, freelancers can create professional profiles that showcase their portfolios, certifications, and skills. This allows clients to easily assess and select the freelancers that best match their needs.</p>
                  </div>
                  
                  <div class="footer-links">
                      <div class="footer-section">
                          <h5>Website Links</h5>
                          <ul>
                              <li><a href="#">Home</a></li>
                              <li><a href="#">About</a></li>
                              <li><a href="#">Get in touch</a></li>
                              <li><a href="#">FAQs</a></li>
                          </ul>
                      </div>
                      
                      <div class="footer-section">
                          <h5>Services</h5>
                          <ul>
                              <li><a href="#">Architecture</a></li>
                              <li><a href="#">Buildings</a></li>
                              <li><a href="#">3d maps</a></li>
                              <li><a href="#">Structure design</a></li>
                          </ul>
                      </div>
                      
                      <div class="footer-section">
                          <h5>Developers</h5>
                          <ul>
                              <li><a href="#">Features</a></li>
                              <li><a href="#">Testimonials</a></li>
                              <li><a href="#">Referals</a></li>
                          </ul>
                      </div>
                  </div>
              </div>
              
              <div class="footer-subscribe">
                  <div class="subscribe-form">
                      <input type="email" placeholder="Enter your email">
                      <button>Subscribe Now</button>
                  </div>
                  <div class="social-icons">
                      <a href="#"><img src="image/facebook.png" alt="Facebook"></a>
                      <a href="#"><img src="image/ig.png" alt="Instagram"></a>
                      <a href="#"><img src="image/linkedln.png" alt="LinkedIn"></a>
                  </div>
              </div>
          </div>
      </div>
      
      <div class="footer-bottom">
          <div class="container">
              <div class="footer-brand">
                  <img src="image/download.png" alt="Workmates logo">
                  <span>Workmates</span>
              </div>
              <div class="language-selector">
                  <button class="active">English</button>
                  <button>Arabic</button>
                  <button>French</button>
              </div>
          </div>
      </div>
      
      <div class="copyright">
          <div class="container">
              <p>Non Copyrighted © 2022 Design and upload by rich technologies</p>
          </div>
      </div>
  </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('progress').addEventListener('input', function() {
            document.getElementById('progressValue').textContent = this.value + '%';
        });
    </script>
</body>
</html>
