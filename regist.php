<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style/registstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <h1>Registration Freelancer</h1>
        <div class="icon-container">
            <img src="image/download.png" width="100" height="90">
        </div>
        <form action="backend/process_register.php" method="POST">


        <div class="container">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" required>
            </div>
            <div class="form-group">
                <label>Id Number</label>
                <input type="text" name="id_number" required>
            </div>
            <div class="form-group">
                <label>Birth Date</label>
                <input type="date" name="birth_date" required>
            </div>
            <div class="form-group">
                <label>Region</label>
                <input type="text" name="region" required>
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text"  name="address" required>
            </div>
            <div class="form-group">
                <label>Postal Code</label>
                <input type="text" name="postal_code" required>
            </div>
            <div class="form-group">
                <label>Bank Account Number</label>
                <input type="text" name="bank_account" required>
            </div>
            <div class="button-container">
                <button type="submit">Register</button>
            </div>
        </div>
    </form>

    </div>
</body>
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-sections">
                    <div class="footer-section">
                        <h5>Short Brief About Us</h5>
                        <p>Lorem ipsum dolor sit amet consectetur. Bibendum consequat laoreet turpis in pellentesque sem id ut. Feugiat quam porttitor in augue sed quis pellentesque quam purus. Ac euismod ac proin vitae vulputate.</p>
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
                        <a href="#"><img src="image/images.jpeg" alt="Instagram"></a>
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
</html>
