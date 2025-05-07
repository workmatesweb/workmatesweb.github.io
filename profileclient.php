<?php
require_once 'backend/profileclient.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - Client Profile</title>
    <link rel="stylesheet" href="style/style3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
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
                <a href="dashboard.php" class="nav-link me-2">Dashboard</a>
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="image/profil.svg" alt="Profile" width="30" height="30" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="ProfileClient.php">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
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
                    <h5 style="margin-bottom: 20px;"><strong>Good morning,</strong> <?php echo htmlspecialchars($client['name']); ?></h5>
                    <div class="progress-box mt-3">
                        <p class="fw-bold">Account created on</p>
                        <h2 class="text-danger fw-bold">
                            <?php 
                            $created_at = new DateTime($client['created_at']);
                            echo $created_at->format('d M Y'); 
                            ?>
                        </h2>
                        <p>Last active: 
                            <?php 
                            $last_activity = new DateTime($client['last_activity']);
                            echo $last_activity->format('d M Y H:i');
                            ?>
                        </p>
                    </div>
                </div>
            </div>
    
            <!-- Edit Profile -->
            <div class="col-md-9">
                <h4 class="fw-bold">Client Profile</h4>
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-md-12">
                        <form method="POST" action="backend/profileclient.php">
                            <div class="profile-card p-3 rounded shadow-sm">
                                <div class="d-flex justify-content-between">
                                    <h5>Personal Information</h5>
                                    <button type="submit" name="update_personal" class="btn btn-outline-dark">Save Changes</button>
                                </div>
                                <hr>
                                <div class="d-flex align-items-center mb-4">
                                    <img src="image/paige-cody-ITTqjS3UpoY-unsplash.jpg" alt="Profile" width="60" height="60" class="rounded-circle me-3">
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary">Change Photo</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Full Name:</strong></label>
                                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($client['name']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Email:</strong></label>
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($client['email']); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Phone number:</strong></label>
                                        <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($client['phone_number'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer remains the same as in your original code -->
    <footer class="footer">
        <!-- ... existing footer code ... -->
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>