<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - Jasa Web Programmer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/Paymentstyle.css">
</head>
<body>

    <!-- Navbar -->
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
                <div class="dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <img src="image/profil.svg" alt="Profile" width="30" height="30" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profil</a></li>
                        <li><a class="dropdown-item" href="#">Pengaturan</a></li>
                        <li><a class="dropdown-item" href="#">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Kartu Kontainer -->
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow-lg p-4" style="max-width: 1300px; width: 100%;">
            <div class="d-flex align-items-center">
                <!-- Gambar -->
                <div class="me-4">
                    <img src="image/Gambar programmer.jpg" alt="Jasa Web Programmer" class="rounded img-fluid" style="width: 300px; height: auto;">
                </div>
                
                <!-- Konten -->
                <div class="w-100">
                    <h2 class="fw-bold">WEB Programmer Services</h2>
                    <p class="text-muted">Need help building a website? Hire me as your expert programmer! I am ready to assist in web development from start to finish, according to your desired concept.</p>
                    <p class="fs-5 fw-bold">Starting From: <span class="text-primary">Rp. 800.000</span></p>
                </div>
            </div>
            
            <!-- Form Pemesanan -->
                <label for="description" class="fw-bold">Description::</label>
                <textarea id="description" class="form-control mb-2" placeholder="Describe the concept you want to build"></textarea>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="date" class="fw-bold">Consultation Schedule::</label>
                        <input type="date" id="date" class="form-control mb-2" value="2025-02-18">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Offer Price:</label>
                        <div class="d-flex flex-column">
                            <div class="form-check">
                                <input type="radio" name="harga" id="harga1" class="form-check-input" checked>
                                <label for="harga1" class="form-check-label fw-bold">Rp. 800.000</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="harga" id="harga2" class="form-check-input">
                                <input type="text" class="form-control" placeholder="Enter Your Budget">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-2">
                    <input type="checkbox" class="me-2"> <label>I agree to the Terms & Conditions.</label>
                </div>
                <form action="proses.html" method="GET">
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <footer class="footer">
      <div class="container"> 
          <div class="footer-content">
              <div class="footer-sections">
                  <div class="footer-section">
                      <h5>Short Brief About Us</h5>
                      <p>WorkMates is a website that serves as a bridge between freelancers and those in need of their services. Through this platform, freelancers can create professional profiles that showcase their portfolios, certifications, and skills. This allows clients to easily assess and select the freelancers that best match their needs.</p>
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
                  <img src="download.png" alt="Workmates logo">
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
