<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'backend/freelancers_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - Find Freelancers</title>
    
    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style/cssfreelancer.css">
    
    <style>
        /* Custom Styles */
        :root {
            --primary-color: #1E3A5F;
            --secondary-color: #C2DBEF;
            --accent-color: #1BCF6B;
            --dark-text: #333333;
            --light-text: #6C757D;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            padding-top: 80px;
        }
        
        .navbar {
            background-color: var(--secondary-color) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-btn:hover {
            background-color: #2c4d7a;
            transform: translateY(-2px);
        }
        
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .card.h-100 {
            display: flex;
            flex-direction: column;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        .fotoprofil {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .skills span {
            display: inline-block;
            background-color: #e9f0f5;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .filter-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-title svg {
            fill: var(--accent-color);
        }
        
        .form-check {
            margin-bottom: 10px;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 40px 0 0;
            margin-top: 50px;
        }
        
        .footer-brand img {
            height: 30px;
            margin-right: 10px;
        }
        
        .social-icons img {
            width: 24px;
            margin-right: 10px;
        }
        
        .language-selector button {
            margin-right: 5px;
            padding: 2px 8px;
            font-size: 0.8rem;
        }
        
        .language-selector button.active {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <img src="image/download.png" alt="Workmates Logo" width="30" height="30" class="me-2">
                Workmates
            </a>
            
            <div class="d-flex align-items-center">
                <div class="d-none d-md-flex gap-3 me-4">
                    <a href="#" class="text-dark"><img src="image/Socialicons.png" alt="WhatsApp" width="20"></a>
                    <a href="#" class="text-dark"><img src="image/Notification.png" alt="Notifications" width="20"></a>
                    <a href="#" class="text-dark"><img src="image/Message.png" alt="Messages" width="20"></a>
                </div>
                
                <a href="dashboard.php" class="nav-link me-3 d-none d-md-block">Dashboard</a>
                
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="image/profil.svg" alt="Profile" width="30" height="30" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="pofileclient.php">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        <div class="row g-4">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filter-section sticky-top" style="top: 90px;">
                    <div class="filter-title">
                        <svg width="20" height="12" viewBox="0 0 20 12">
                            <path fill-rule="evenodd" d="M7.778,12.000 L12.222,12.000 L12.222,10.000 L7.778,10.000 L7.778,12.000 ZM-0.000,-0.000 L-0.000,2.000 L20.000,2.000 L20.000,-0.000 L-0.000,-0.000 ZM3.333,7.000 L16.667,7.000 L16.667,5.000 L3.333,5.000 L3.333,7.000 Z"/>
                        </svg>
                        <h5 class="mb-0">Filter Jobs</h5>
                    </div>
                    
                    <form id="filterForm" method="GET">
                        <!-- Categories Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Categories</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="Coding" id="coding" <?php echo in_array('Coding', $filters['categories']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="coding">Coding</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="Graphic Design" id="graphicDesign" <?php echo in_array('Graphic Design', $filters['categories']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="graphicDesign">Graphic Design</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="Web Design" id="webDesign" <?php echo in_array('Web Design', $filters['categories']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="webDesign">Web Design</label>
                            </div>
                        </div>
                        
                        <!-- Experience Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Experience Level</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="experience[]" value="Beginner" id="beginner" <?php echo in_array('Beginner', $filters['experience']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="beginner">Beginner</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="experience[]" value="Intermediate" id="intermediate" <?php echo in_array('Intermediate', $filters['experience']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="intermediate">Intermediate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="experience[]" value="Expert" id="expert" <?php echo in_array('Expert', $filters['experience']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="expert">Expert</label>
                            </div>
                        </div>
                        
                        <!-- Salary Range Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Expected Salary</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="salary_range[]" value="under_300" id="under300" <?php echo in_array('under_300', $filters['salary_range']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="under300">Under IDR 300.000</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="salary_range[]" value="300_800" id="300to800" <?php echo in_array('300_800', $filters['salary_range']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="300to800">IDR 300.000 - 800.000</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="salary_range[]" value="above_900" id="above900" <?php echo in_array('above_900', $filters['salary_range']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="above900">Above IDR 900.000</label>
                            </div>
                        </div>
                        
                        <!-- Location Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Location</h6>
                            <select class="form-select" name="location">
                                <option value="">All Locations</option>
                                <option value="Medan" <?php echo $filters['location'] === 'Medan' ? 'selected' : ''; ?>>Medan</option>
                                <option value="Jakarta" <?php echo $filters['location'] === 'Jakarta' ? 'selected' : ''; ?>>Jakarta</option>
                                <option value="Bandung" <?php echo $filters['location'] === 'Bandung' ? 'selected' : ''; ?>>Bandung</option>
                                <option value="Surabaya" <?php echo $filters['location'] === 'Surabaya' ? 'selected' : ''; ?>>Surabaya</option>
                            </select>
                        </div>
                        
                        <!-- Gender Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Gender</h6>
                            <select class="form-select" name="gender">
                                <option value="">All Genders</option>
                                <option value="Male" <?php echo $filters['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $filters['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        
                        <!-- English Level Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">English Level</h6>
                            <select class="form-select" name="english_level">
                                <option value="">All Levels</option>
                                <option value="A1" <?php echo $filters['english_level'] === 'A1' ? 'selected' : ''; ?>>A1 (Beginner)</option>
                                <option value="A2" <?php echo $filters['english_level'] === 'A2' ? 'selected' : ''; ?>>A2 (Elementary)</option>
                                <option value="B1" <?php echo $filters['english_level'] === 'B1' ? 'selected' : ''; ?>>B1 (Intermediate)</option>
                                <option value="B2" <?php echo $filters['english_level'] === 'B2' ? 'selected' : ''; ?>>B2 (Upper Intermediate)</option>
                                <option value="C1" <?php echo $filters['english_level'] === 'C1' ? 'selected' : ''; ?>>C1 (Advanced)</option>
                                <option value="C2" <?php echo $filters['english_level'] === 'C2' ? 'selected' : ''; ?>>C2 (Proficient)</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <button type="reset" class="btn btn-outline-secondary" id="resetFilters">Reset Filters</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Freelancers Listing -->
            <div class="col-lg-9">
                <div class="row row-cols-1 row-cols-md-2 g-4" id="freelancersContainer">
                    <?php if (count($freelancers) > 0) { ?>
                        <?php foreach ($freelancers as $freelancer) { ?>
                            <div class="col">
                                <div class="card h-100">
                                    <img src="<?php echo htmlspecialchars($freelancer['profile_picture'] ?: 'image/foto1.png'); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($freelancer['full_name']); ?>">
                                    
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    <?php echo htmlspecialchars($freelancer['full_name']); ?>
                                                    <?php if ($freelancer['rating']) { ?>
                                                        <span class="text-warning">
                                                            <?php echo str_repeat('★', min(5, max(0, (int)$freelancer['rating']))); ?>
                                                        </span>
                                                    <?php } ?>
                                                </h5>
                                                <p class="text-muted mb-2">
                                                    <?php echo htmlspecialchars($freelancer['job_title']); ?>
                                                </p>
                                            </div>
                                            <img class="fotoprofil" 
                                                 src="<?php echo htmlspecialchars($freelancer['profile_picture'] ?: 'image/foto1.png'); ?>" 
                                                 alt="<?php echo htmlspecialchars($freelancer['full_name']); ?>">
                                        </div>
                                        
                                        <div class="skills mb-3">
                                            <?php 
                                                $skills = array_filter(explode(',', $freelancer['skills']));
                                                foreach ($skills as $skill) { 
                                            ?>
                                                <span><?php echo htmlspecialchars(trim($skill)); ?></span>
                                            <?php } ?>
                                        </div>
                                        
                                        <div class="details mb-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Location:</span>
                                                <span><?php echo htmlspecialchars($freelancer['location']); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Salary:</span>
                                                <span>IDR <?php echo number_format($freelancer['expected_salary'], 0, ',', '.'); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Experience:</span>
                                                <span><?php echo htmlspecialchars($freelancer['experience_level']); ?></span>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <form action="freelancer_detail.php" method="GET" class="me-2">
                                    <input type="hidden" name="id" value="<?php echo $freelancer['id']; ?>">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        View Profile
                                    </button>
                                </form>
                                <button class="btn btn-primary start-chat" 
                                        data-freelancer-id="<?php echo $freelancer['id']; ?>"
                                        data-freelancer-name="<?php echo htmlspecialchars($freelancer['full_name']); ?>">
                                    <i class="fas fa-comment-dots"></i> Chat
                                </button>
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                No freelancers found matching your criteria. Try adjusting your filters.
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
      <!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chat with <span id="freelancerName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="chat-container" style="height: 400px; overflow-y: auto;" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="mt-3">
                    <form id="sendMessageForm">
                        <input type="hidden" id="freelancerId">
                        <div class="input-group">
                            <input type="text" class="form-control" id="messageInput" placeholder="Type your message...">
                            <button class="btn btn-primary" type="submit">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize chat modal
document.querySelectorAll('.start-chat').forEach(button => {
    button.addEventListener('click', function() {
        const freelancerId = this.getAttribute('data-freelancer-id');
        const freelancerName = this.getAttribute('data-freelancer-name');
        
        document.getElementById('freelancerName').textContent = freelancerName;
        document.getElementById('freelancerId').value = freelancerId;
        
        // Load messages
        loadMessages(freelancerId);
        
        // Show modal
        const chatModal = new bootstrap.Modal(document.getElementById('chatModal'));
        chatModal.show();
    });
});

// Handle message submission
document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const freelancerId = document.getElementById('freelancerId').value;
    const message = document.getElementById('messageInput').value;
    
    if (message.trim() === '') return;
    
    fetch('backend/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            freelancer_id: freelancerId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('messageInput').value = '';
            loadMessages(freelancerId);
        }
    });
});

function loadMessages(freelancerId) {
    fetch(`backend/get_messages.php?freelancer_id=${freelancerId}`)
        .then(response => response.json())
        .then(messages => {
            const chatContainer = document.getElementById('chatMessages');
            chatContainer.innerHTML = '';
            
            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `mb-2 ${msg.is_client ? 'text-end' : 'text-start'}`;
                messageDiv.innerHTML = `
                    <div class="${msg.is_client ? 'bg-primary text-white' : 'bg-light'} p-2 rounded d-inline-block">
                        ${msg.message}
                        <div class="small text-muted">${new Date(msg.created_at).toLocaleTimeString()}</div>
                    </div>
                `;
                chatContainer.appendChild(messageDiv);
            });
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
}
</script>
    </main>


    <!-- Footer -->
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
                        
                        <div class="social-icons mt-3">
                            <a href="#" class="me-2"><img src="image/facebook.png" alt="Facebook"></a>
                            <a href="#" class="me-2"><img src="image/ig.png" alt="Instagram"></a>
                            <a href="#"><img src="image/linkedln.png" alt="LinkedIn"></a>
                        </div>
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

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX Filtering
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const params = new URLSearchParams();
            
            // Add all form data to params
            for (const [key, value] of formData.entries()) {
                if (value) {
                    if (key.endsWith('[]')) {
                        params.append(key, value);
                    } else {
                        params.set(key, value);
                    }
                }
            }
            
            // Add AJAX flag
            params.set('ajax', '1');
            
            // Show loading state
            const container = document.getElementById('freelancersContainer');
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading freelancers...</p>
                </div>
            `;
            
            // Send AJAX request
            fetch('backend/freelancers_backend.php?' + params.toString())
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.length > 0) {
                        container.innerHTML = data.map(freelancer => `
                            <div class="col">
                                <div class="card h-100">
                                    <img src="${freelancer.profile_picture || 'image/default-profile.jpg'}" 
                                         class="card-img-top" 
                                         alt="${freelancer.full_name}">
                                    
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    ${freelancer.full_name}
                                                    ${freelancer.rating ? `<span class="text-warning">${'★'.repeat(Math.min(5, Math.max(0, parseInt(freelancer.rating)))}</span>` : ''}
                                                </h5>
                                                <p class="text-muted mb-2">
                                                    ${freelancer.job_title}
                                                </p>
                                            </div>
                                            <img class="fotoprofil" 
                                                 src="${freelancer.profile_picture || 'image/download.png'}" 
                                                 alt="${freelancer.full_name}">
                                        </div>
                                        
                                        <div class="skills mb-3">
                                            ${freelancer.skills.split(',').filter(skill => skill.trim()).map(skill => `
                                                <span>${skill.trim()}</span>
                                            `).join('')}
                                        </div>
                                        
                                        <div class="details mb-4">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Location:</span>
                                                <span>${freelancer.location}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Salary:</span>
                                                <span>IDR ${parseInt(freelancer.expected_salary).toLocaleString('id-ID')}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Experience:</span>
                                                <span>${freelancer.experience_level}</span>
                                            </div>
                                        </div>
                                        
                                        <form action="freelancer_detail.php" method="GET" class="mt-auto">
                                            <input type="hidden" name="id" value="${freelancer.id}">
                                            <button type="submit" class="profile-btn w-100">
                                                View Profile
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = `
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    No freelancers found matching your criteria. Try adjusting your filters.
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                An error occurred while loading freelancers. Please try again.
                            </div>
                        </div>
                    `;
                });
        });

        // Reset filters
        document.getElementById('resetFilters').addEventListener('click', function() {
            window.location.href = 'freelancers.php';
        });
    </script>
</body>
</html>