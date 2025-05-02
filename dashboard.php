<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates Progress Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/stylesdashboard.css">
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
                <a href="#" class="nav-link me-2">Dashboard</a>
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="image/profil.svg" alt="Profile" width="30" height="30" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" href="#"> LogOut</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-content">
        <h2 class="progress-title">Progress</h2>
        
        <div class="progress-categories-wrapper">
            <div class="progress-categories">
                <div class="category" id="status-konfirmasi">
                    <div class="category-header" onclick="toggleCategory('status-konfirmasi')">
                        <span>Confirmation Status</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="category-content">
                        <div class="progress-cards">
                            <div class="progress-card">
                                <div class="progress-label">Coding</div>
                                <div class="circle-progress">
                                    <div class="progress-circle">
                                        <div class="inner-circle">
                                            <span class="percentage">0%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-status">Not yet confirmed</div>
                            </div>
                            <div class="progress-card">
                                <div class="progress-label">Web Design</div>
                                <div class="circle-progress">
                                    <div class="progress-circle complete">
                                        <div class="inner-circle">
                                            <span class="percentage">100%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-status">Confirmed</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="category" id="melakukan-dp">
                    <div class="category-header" onclick="toggleCategory('melakukan-dp')">
                        <span>Down Payment</span>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div class="category-content" style="display: none;">
                        <div class="empty-state">No data available</div>
                    </div>
                </div>
                
                <div class="category" id="sedang-dikerjakan">
                    <div class="category-header" onclick="toggleCategory('sedang-dikerjakan')">
                        <span>Being Worked On</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="category-content">
                        <div class="progress-cards">
                            <div class="progress-card">
                                <div class="progress-label">Graphic Design</div>
                                <div class="circle-progress">
                                    <div class="progress-circle in-progress-15">
                                        <div class="inner-circle">
                                            <span class="percentage">15%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-status">Currently researching</div>
                            </div>
                            <div class="progress-card">
                                <div class="progress-label">Web Design</div>
                                <div class="circle-progress">
                                    <div class="progress-circle in-progress-50">
                                        <div class="inner-circle">
                                            <span class="percentage">50%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-status">Under revision</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="category" id="evaluasi">
                    <div class="category-header" onclick="toggleCategory('evaluasi')">
                        <span>Evaluation</span>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div class="category-content" style="display: none;">
                        <div class="empty-state">No data available</div>
                    </div>
                </div>
                
                <div class="category" id="selesai">
                    <div class="category-header" onclick="toggleCategory('selesai')">
                        <span>Finished</span>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div class="category-content" style="display: none;">
                        <div class="empty-state">No data available</div>
                    </div>
                </div>
                
                <div class="category" id="pelunasan">
                    <div class="category-header" onclick="toggleCategory('pelunasan')">
                        <span>Repayment</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="category-content">
                        <div class="progress-cards">
                            <div class="progress-card">
                                <div class="progress-label">Graphic Design</div>
                                <div class="circle-progress">
                                    <div class="progress-circle">
                                        <div class="inner-circle">
                                            <span class="percentage">0%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-status">Not yet paid</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="category" id="dibatalkan">
                    <div class="category-header" onclick="toggleCategory('dibatalkan')">
                        <span>Canceled</span>
                        <i class="bi bi-chevron-right"></i>
                    </div>
                    <div class="category-content" style="display: none;">
                        <div class="empty-state">No data available</div>
                    </div>
                </div>
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
                                <li><a href="index.html">Home</a></li>
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

    <script>
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
        
        document.addEventListener('DOMContentLoaded', function() {
            const expandedCategories = ['status-konfirmasi', 'sedang-dikerjakan', 'pelunasan'];
            
            const allCategories = document.querySelectorAll('.category');
            allCategories.forEach(category => {
                const content = category.querySelector('.category-content');
                const icon = category.querySelector('.category-header i');
                const id = category.id;
                
                if (!expandedCategories.includes(id)) {
                    content.style.display = 'none';
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-right');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
