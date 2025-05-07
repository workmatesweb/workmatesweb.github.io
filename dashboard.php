<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates Client Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #C2DBEF;
            --secondary-color: #2c3e50;
            --accent-color: #3498db;
            --text-color: #333;
            --light-text: #777;
            --border-color: #e0e0e0;
        }
        
        body {
            font-family: 'Ubuntu Sans', sans-serif;
            color: var(--text-color);
            padding-top: 70px;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-family: 'Paytone One', sans-serif;
            font-size: 1.5rem;
        }
        
        .search-input {
            width: 300px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            padding-left: 15px;
        }
        
        .main-content {
            margin-top: 30px;
            margin-bottom: 50px;
        }
        
        .progress-title {
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--secondary-color);
        }
        
        .progress-categories-wrapper {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }
        
        .category {
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .category-header {
            padding: 15px 20px;
            background-color: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            transition: background-color 0.2s;
        }
        
        .category-header:hover {
            background-color: #e9ecef;
        }
        
        .category-content {
            padding: 15px;
            background-color: white;
        }
        
        .progress-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .progress-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        
        .progress-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }
        
        .circle-progress {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }
        
        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: conic-gradient(var(--accent-color) 0%, #e0e0e0 0%);
        }
        
        .progress-circle.complete {
            background: conic-gradient(#2ecc71 100%, #e0e0e0 0%);
        }
        
        .progress-circle.in-progress-15 {
            background: conic-gradient(#f39c12 15%, #e0e0e0 0%);
        }
        
        .progress-circle.in-progress-50 {
            background: conic-gradient(#f39c12 50%, #e0e0e0 0%);
        }
        
        .inner-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .progress-status {
            text-align: center;
            font-size: 0.9rem;
            color: var(--light-text);
        }
        
        .empty-state {
            text-align: center;
            padding: 30px;
            color: var(--light-text);
            font-style: italic;
        }
        
        .footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 50px 0 0;
        }
        
        .footer-section h5 {
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer-section ul {
            list-style: none;
            padding-left: 0;
        }
        
        .footer-section ul li {
            margin-bottom: 10px;
        }
        
        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .footer-section ul li a:hover {
            color: white;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px 0;
            margin-top: 30px;
        }
        
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
        
        .language-selector button {
            background: none;
            border: none;
            color: #bdc3c7;
            margin-right: 10px;
            cursor: pointer;
        }
        
        .language-selector button.active {
            color: white;
            font-weight: 600;
        }
        
        .copyright {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 15px 0;
            font-size: 0.8rem;
            color: #bdc3c7;
        }

        .social-icons img {
             width: 30px;
            height: 30px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .search-input {
                width: 200px;
            }
            
            .nav-icons a {
                margin-right: 10px;
            }
        }
        
        @media (max-width: 768px) {
            .search-input {
                width: 150px;
            }
            
            .progress-cards {
                grid-template-columns: 1fr;
            }
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
                <a href="#" class="nav-link me-3">Dashboard</a>
                <a href="freelancers.php" class="nav-link me-3">Find Freelancers</a>
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

    <div class="container main-content">
        <h2 class="progress-title">My Projects</h2>
        
        <div class="progress-categories-wrapper">
            <div class="progress-categories">
                <div class="category" id="pending-projects">
                    <div class="category-header" onclick="toggleCategory('pending-projects')">
                        <span>Pending Projects</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="category-content">
                        <div class="progress-cards" id="pending-cards">
                            <!-- Projects will be loaded here -->
                            <div class="empty-state">Loading projects...</div>
                        </div>
                    </div>
                </div>
                
                <div class="category" id="in-progress-projects">
                    <div class="category-header" onclick="toggleCategory('in-progress-projects')">
                        <span>Projects In Progress</span>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div class="category-content" style="display: none;">
                        <div class="progress-cards" id="in-progress-cards">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                </div>
                
                <div class="category" id="completed-projects">
                    <div class="category-header" onclick="toggleCategory('completed-projects')">
                        <span>Completed Projects</span>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div class="category-content" style="display: none;">
                        <div class="progress-cards" id="completed-cards">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script>
        // Function to toggle category visibility
        function toggleCategory(id) {
            const category = document.getElementById(id);
            const content = category.querySelector('.category-content');
            const icon = category.querySelector('.category-header i');
            
            if (content.style.display === 'none' || !content.style.display) {
                content.style.display = 'block';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-right');
            }
        }
        
        // Function to create a project card
        function createProjectCard(project) {
    const card = document.createElement('div');
    card.className = 'progress-card';
    
    let statusText = '';
    let progressClass = '';
    
    switch(project.status) {
        case 'pending':
            statusText = 'Waiting for confirmation';
            progressClass = '';
            break;
        case 'in_progress':
            statusText = getStatusText(project.progress);
            progressClass = `in-progress-${project.progress}`;
            break;
        case 'completed':
            statusText = 'Project completed';
            progressClass = 'complete';
            break;
    }
    
    // Create a link that wraps the card content
    const link = document.createElement('a');
    link.href = `detail_project.php?id=${project.id}`;
    link.style.textDecoration = 'none';
    link.style.color = 'inherit';
    
    link.innerHTML = `
        <div class="progress-label">${project.title}</div>
        <div class="freelancer-name small text-muted mb-2">With ${project.freelancer_name}</div>
        <div class="circle-progress">
            <div class="progress-circle ${progressClass}">
                <div class="inner-circle">
                    <span class="percentage">${project.progress}%</span>
                </div>
            </div>
        </div>
        <div class="progress-status">${statusText}</div>
        ${project.deadline ? `<div class="deadline small mt-2">Deadline: ${new Date(project.deadline).toLocaleDateString()}</div>` : ''}
    `;
    
    card.appendChild(link);
    return card;
}
        
        // Helper function to get status text based on progress
        function getStatusText(progress) {
            if (progress < 25) return 'Initial research';
            if (progress < 50) return 'Work in progress';
            if (progress < 75) return 'Halfway done';
            if (progress < 100) return 'Finalizing';
            return 'Completed';
        }
        
        // Load projects from backend
        async function loadProjects() {
            try {
                const response = await fetch('backend/dashboarduser.php');
                const projects = await response.json();
                
                // Clear loading messages
                document.getElementById('pending-cards').innerHTML = '';
                document.getElementById('in-progress-cards').innerHTML = '';
                document.getElementById('completed-cards').innerHTML = '';
                
                // Handle empty states
                if (projects.pending.length === 0) {
                    document.getElementById('pending-cards').innerHTML = '<div class="empty-state">No pending projects</div>';
                } else {
                    projects.pending.forEach(project => {
                        document.getElementById('pending-cards').appendChild(createProjectCard(project));
                    });
                }
                
                if (projects.in_progress.length === 0) {
                    document.getElementById('in-progress-cards').innerHTML = '<div class="empty-state">No projects in progress</div>';
                } else {
                    projects.in_progress.forEach(project => {
                        document.getElementById('in-progress-cards').appendChild(createProjectCard(project));
                    });
                }
                
                if (projects.completed.length === 0) {
                    document.getElementById('completed-cards').innerHTML = '<div class="empty-state">No completed projects</div>';
                } else {
                    projects.completed.forEach(project => {
                        document.getElementById('completed-cards').appendChild(createProjectCard(project));
                    });
                }
                
            } catch (error) {
                console.error('Error loading projects:', error);
                document.getElementById('pending-cards').innerHTML = '<div class="empty-state">Error loading projects</div>';
            }
        }
        
        // Logout functionality
        document.getElementById('logout-btn').addEventListener('click', function() {
            fetch('../logout.php')
                .then(() => {
                    window.location.href = 'login.php';
                });
        });
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            loadProjects();
            
            // Expand the pending projects by default
            document.getElementById('pending-projects').querySelector('.category-content').style.display = 'block';
        });

        
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>