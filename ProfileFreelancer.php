<?php
require_once 'backend/profile_backend.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - Profile</title>
    <!-- [Keep all your existing CSS links] -->
    <link rel="stylesheet" href="style/style3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Add this to your existing CSS */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>
<body>
    <!-- [Keep your existing navbar code] -->
    <nav class="navbar navbar-expand-lg shadow-sm fixed-top" style="background-color: #C2DBEF;">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold me-auto" href="#">
                <img src="image/download.png" alt="icon" width="30" height="30" class="me-2">
                Workmates
            </a>
            <div class="nav-icons ms-auto d-flex align-items-center">
                <a href="#" class="me-2"><img src="image/Socialicons.png" alt="WhatsApp" width="20"></a> 
                <a href="#" class="me-2"><img src="image/Notification.png" alt="Notification" width="20"></a>
                <a href="#" class="me-2"><img src="image/Message.png" alt="Message" width="20"></a>
                <a href="dashboardfreelancer.php" class="nav-link me-2">Dashboard</a>
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
    
    <div class="container mt-5 pt-3">
    <!-- Display success/error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 p-3" id="sidebar">
            <div class="profile-card-one p-3 text-center">
                <div class="mb-3">
                    <img src="<?php echo htmlspecialchars($freelancer['profile_picture']); ?>" 
                         alt="Profile" width="100" height="100" class="rounded-circle">
                </div>
                <h5 style="margin-bottom: 20px;"><strong>Good morning,</strong> <?php echo htmlspecialchars($freelancer['full_name']); ?></h5>
                <div class="progress-box mt-3">
                    <p class="fw-bold">Congratulations, you have completed</p>
                    <h2 class="text-danger fw-bold"><?php echo htmlspecialchars($freelancer['completed_projects'] ?? '0'); ?></h2>
                    <p>project<?php echo ($freelancer['completed_projects'] != 1) ? 's' : ''; ?></p>
                    <p class="mt-2">
                        <span class="badge bg-<?php echo $freelancer['availability'] ? 'success' : 'secondary'; ?>">
                            <?php echo $freelancer['availability'] ? 'Available' : 'Not Available'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="col-md-9">
            <h4 class="fw-bold">Edit Profile</h4>
            <div class="row">
                <!-- Personal Information -->
                <div class="col-md-6">
                    <form method="POST" action="backend/profile_backend.php" enctype="multipart/form-data">
                        <div class="profile-card p-3 rounded shadow-sm">
                            <div class="d-flex justify-content-between">
                                <h5>Personal Information</h5>
                                <button type="submit" name="update_personal" class="btn btn-outline-dark">Save</button>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label"><strong>Profile Picture:</strong></label>
                                <input type="file" name="profile_picture" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Full Name:</strong></label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($freelancer['full_name']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Username:</strong></label>
                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($freelancer['username'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Email:</strong></label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($freelancer['email'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Phone number:</strong></label>
                                <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($freelancer['phone_number']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>ID Number:</strong></label>
                                <input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars($freelancer['id_number']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Birth Date:</strong></label>
                                <input type="date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($freelancer['birth_date']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Gender:</strong></label>
                                <select name="gender" class="form-select">
                                    <option value="Male" <?php echo ($freelancer['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo ($freelancer['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Documents & Job Information -->
                <div class="col-md-6">
                    <form method="POST" action="backend/profile_backend.php">
                        <div class="profile-card p-3 rounded shadow-sm">
                            <div class="d-flex justify-content-between">
                                <h5>Professional Information</h5>
                                <button type="submit" name="update_professional" class="btn btn-outline-dark">Save</button>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label"><strong>Job Title:</strong></label>
                                <input type="text" name="job_title" class="form-control" value="<?php echo htmlspecialchars($freelancer['job_title']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Experience Level:</strong></label>
                                <select name="experience_level" class="form-select">
                                    <option value="Beginner" <?php echo ($freelancer['experience_level'] == 'Beginner') ? 'selected' : ''; ?>>Beginner</option>
                                    <option value="Intermediate" <?php echo ($freelancer['experience_level'] == 'Intermediate') ? 'selected' : ''; ?>>Intermediate</option>
                                    <option value="Expert" <?php echo ($freelancer['experience_level'] == 'Expert') ? 'selected' : ''; ?>>Expert</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Job Category:</strong></label>
                                <select name="job_category" class="form-select">
                                    <option value="Coding" <?php echo ($freelancer['job_category'] == 'Coding') ? 'selected' : ''; ?>>Coding</option>
                                    <option value="Graphic Design" <?php echo ($freelancer['job_category'] == 'Graphic Design') ? 'selected' : ''; ?>>Graphic Design</option>
                                    <option value="Web Design" <?php echo ($freelancer['job_category'] == 'Web Design') ? 'selected' : ''; ?>>Web Design</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Skills (comma separated):</strong></label>
                                <textarea name="skills" class="form-control"><?php echo htmlspecialchars($freelancer['skills']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Expected Salary:</strong></label>
                                <input type="number" step="0.01" name="expected_salary" class="form-control" value="<?php echo htmlspecialchars($freelancer['expected_salary']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>English Level:</strong></label>
                                <select name="english_level" class="form-select">
                                    <option value="A1" <?php echo ($freelancer['english_level'] == 'A1') ? 'selected' : ''; ?>>A1 (Beginner)</option>
                                    <option value="A2" <?php echo ($freelancer['english_level'] == 'A2') ? 'selected' : ''; ?>>A2 (Elementary)</option>
                                    <option value="B1" <?php echo ($freelancer['english_level'] == 'B1') ? 'selected' : ''; ?>>B1 (Intermediate)</option>
                                    <option value="B2" <?php echo ($freelancer['english_level'] == 'B2') ? 'selected' : ''; ?>>B2 (Upper Intermediate)</option>
                                    <option value="C1" <?php echo ($freelancer['english_level'] == 'C1') ? 'selected' : ''; ?>>C1 (Advanced)</option>
                                    <option value="C2" <?php echo ($freelancer['english_level'] == 'C2') ? 'selected' : ''; ?>>C2 (Proficient)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Availability:</strong></label>
                                <select name="availability" class="form-select">
                                    <option value="1" <?php echo $freelancer['availability'] ? 'selected' : ''; ?>>Available</option>
                                    <option value="0" <?php echo !$freelancer['availability'] ? 'selected' : ''; ?>>Not Available</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Bio (Max 255 chars):</strong></label>
                                <textarea name="bio" class="form-control" maxlength="255"><?php echo htmlspecialchars($freelancer['bio']); ?></textarea>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Address Information -->
                    <form method="POST" action="backend/profile_backend.php" class="mt-3">
                        <div class="profile-card p-3 rounded shadow-sm">
                            <div class="d-flex justify-content-between">
                                <h5>Address Information</h5>
                                <button type="submit" name="update_address" class="btn btn-outline-dark">Save</button>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label"><strong>Region:</strong></label>
                                <input type="text" name="region" class="form-control" value="<?php echo htmlspecialchars($freelancer['region']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Address:</strong></label>
                                <textarea name="address" class="form-control"><?php echo htmlspecialchars($freelancer['address']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Postal Code:</strong></label>
                                <input type="text" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($freelancer['postal_code']); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Bank Account:</strong></label>
                                <input type="text" name="bank_account" class="form-control" value="<?php echo htmlspecialchars($freelancer['bank_account']); ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- [Keep your existing footer code] -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-sections">
                    <div class="footer-section">
                        <h5>Short Brief About Us</h5>
                        <p>WorkMates is a website that serves as a bridge between freelancers and those in need of their services. Through this platform, freelancers can create professional profiles that showcase their portfolios, certifications, and skills. This allows clients to easily assess and select the freelancers that best match their needs..</p>
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>