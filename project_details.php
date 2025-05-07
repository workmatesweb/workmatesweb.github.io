<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="style/cssprogress.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top" style="background-color: #C2DBEF;">

        <div class="container" style="font-family: 'Paytone One'">
            <a class="navbar-brand text-dark fw-bold me-auto" href="#mainContent">
                <img src="image/download.png" alt="icon" width="30" height="30" class="me-1">
                Workmates
            </a>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Ikon Media Sosial -->
                    <li class="nav-item d-flex align-items-center gap-2">
                        <a href="#"><img src="image/Socialicons.png" alt="WhatsApp" width="23" class="me-2"></a>
                        <a href="#"><img src="image/Notification.png" alt="Message" width="25" class="me-2"></a>
                        <a href="#"><img src="image/Message.png" alt="Notification" width="25"></a>
                    </li>
                    <li class="nav-item d-flex align-items-center ms-2">
                        <a class="nav-link active" href="#">Dashboard</a>
                    </li>

                    <!-- Profil User -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="image/profil.svg" alt="icon" width="40" height="40" class="img-circle-frame">
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
                    </li>
                </ul>
            </div>  
        </div>
    </nav>
    <div class="container my-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Project Progress</h5>
                <span class="badge bg-warning text-dark">In Progress</span>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Project Details</h6>
                        <p class="mb-1"><strong>Project Name:</strong> Website A</p>
                        <p class="mb-1"><strong>Client:</strong> John Doe</p>
                        <p class="mb-1"><strong>Deadline:</strong> 12 March 2025</p>
                        <p class="mb-1"><strong>Price:</strong> IDR 5,000,000</p>
                        <p class="mb-3"><strong>Payment Status:</strong> Down Payment</p>
                        <a href="chat.html" class="btn btn-outline-primary">💬 Chat Now</a>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Progress</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <input type="checkbox" checked> Client & Project Setup ✅
                            </li>
                            <li class="list-group-item">
                                <input type="checkbox" checked> Planning & Design ✅
                            </li>
                            <li class="list-group-item">
                                <input type="checkbox"> Development ⏳
                            </li>
                            <li class="list-group-item">
                                <input type="checkbox"> Testing & Debugging ⏳
                            </li>
                            <li class="list-group-item">
                                <input type="checkbox"> Deployment & Final Review ⏳
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <h6 class="fw-bold">Activity Log</h6>
                <ul class="list-unstyled">
                    <li>📅 <b>February 10, 2025</b> – Freelancer uploaded the latest revision.</li>
                    <li>📅 <b>February 8, 2025</b> – Client requested a color change in the design.</li>
                    <li>📅 <b>February 5, 2025</b> – Project officially started.</li>
                </ul>
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
</body>
</html>




