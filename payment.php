<?php
require_once 'backend/config.php';
require_once __DIR__ . '/vendor/autoload.php'; // Path ke autoload Composer

// Set Midtrans configuration
\Midtrans\Config::$serverKey = 'SB-Mid-server-POVgPWLqPgTc6ZOihxtbB2Jz'; // Ganti dengan server key Anda
\Midtrans\Config::$isProduction = false; // Set true untuk production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate project_id
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    die("Invalid project ID");
}

$project_id = intval($_GET['project_id']);
$user_id = $_SESSION['user_id'];

// Fetch project details
$sql = "SELECT p.*, f.full_name as freelancer_name, u.email, u.name as client_name 
        FROM projects p
        JOIN freelancers f ON p.freelancer_id = f.id
        JOIN users u ON p.client_id = u.id
        WHERE p.id = ? AND p.client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $project_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Project not found or access denied");
}

$project = $result->fetch_assoc();
$stmt->close();

// Calculate total with admin fee (10%)
$admin_fee = $project['price'] * 0.1;
$total = $project['price'] + $admin_fee;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Prepare transaction details
        $transaction_details = [
            'order_id' => 'WM-' . $project_id . '-' . time(),
            'gross_amount' => $total
        ];

        // Customer details
        $customer_details = [
            'first_name' => $project['client_name'],
            'email' => $project['email'],
            'phone' => $_POST['client_phone'] ?? ''
        ];

        // Item details
        $item_details = [
            [
                'id' => $project_id,
                'price' => $project['price'],
                'quantity' => 1,
                'name' => $project['title'] . ' - ' . $project['freelancer_name']
            ],
            [
                'id' => 'ADMIN',
                'price' => $admin_fee,
                'quantity' => 1,
                'name' => 'Biaya Admin'
            ]
        ];

        // Payment method
        $payment_method = $_POST['payment_method'] ?? 'bank_transfer';

        // Prepare transaction parameters
        $transaction = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            'payment_type' => $payment_method
        ];

        // Add specific payment method parameters
        if ($payment_method === 'bank_transfer') {
            $transaction['bank_transfer'] = [
                'bank' => $_POST['bank_code'] ?? 'bni' // bni, bca, mandiri, etc
            ];
        } elseif ($payment_method === 'gopay') {
            $transaction['gopay'] = [
                'enable_callback' => true,
                'callback_url' => 'https://yourdomain.com/payment_callback.php'
            ];
        }

        // Get Snap Token from Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($transaction);

        // Save transaction to database
        $insert_sql = "INSERT INTO payments (
                        project_id, order_id, amount, admin_fee, 
                        payment_method, snap_token, status
                      ) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param(
            "isddss", 
            $project_id, 
            $transaction_details['order_id'],
            $project['price'],
            $admin_fee,
            $payment_method,
            $snapToken
        );
        $stmt->execute();
        $payment_id = $conn->insert_id;
        $stmt->close();

        // Redirect to Midtrans payment page
        header("Location: payment_processing.php?snap_token=" . $snapToken);
        exit();

    } catch (Exception $e) {
        $error = "Payment processing failed: " . $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Workmates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Paytone+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/stylesdashboard.css">
    <style>
        .payment-method {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #1E3A5F;
            background-color: #f8f9fa;
        }
        .payment-method.active {
            border-color: #1E3A5F;
            background-color: #e9f0f5;
        }
        .bank-logo {
            height: 30px;
            margin-right: 10px;
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
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Payment Details</h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-4">
                            <h5>Project: <?= htmlspecialchars($project['title']) ?></h5>
                            <p>Freelancer: <?= htmlspecialchars($project['freelancer_name']) ?></p>
                            <div class="d-flex justify-content-between">
                                <span>Project Price:</span>
                                <strong>Rp <?= number_format($project['price'], 0, ',', '.') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Admin Fee (10%):</span>
                                <strong>Rp <?= number_format($admin_fee, 0, ',', '.') ?></strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Amount:</span>
                                <strong class="text-primary">Rp <?= number_format($total, 0, ',', '.') ?></strong>
                            </div>
                        </div>
                        
                        <form method="POST" id="payment-form">
                            <div class="mb-4">
                                <h5 class="mb-3">Contact Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="client_name" 
                                               value="<?= htmlspecialchars($project['client_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="client_phone" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" 
                                               value="<?= htmlspecialchars($project['email']) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="mb-3">Payment Method</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bank-transfer" value="bank_transfer" checked>
                                        <label class="form-check-label fw-bold" for="bank-transfer">
                                            Bank Transfer
                                        </label>
                                    </div>
                                    
                                    <div class="row mt-2" id="bank-options">
                                        <div class="col-md-4">
                                            <label class="payment-method">
                                                <input type="radio" name="bank_code" value="bca" checked hidden>
                                                <img src="image\bca.png" alt="BCA" class="bank-logo">
                                                BCA
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="payment-method">
                                                <input type="radio" name="bank_code" value="bni" hidden>
                                                <img src="image\BNI_logo.svg.png" alt="BNI" class="bank-logo">
                                                BNI
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="payment-method">
                                                <input type="radio" name="bank_code" value="mandiri" hidden>
                                                <img src="image\Bank_Mandiri_logo_2016.svg.png" alt="Mandiri" class="bank-logo">
                                                Mandiri
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="gopay" value="gopay">
                                        <label class="form-check-label fw-bold" for="gopay">
                                            GoPay
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="shopeepay" value="shopeepay">
                                        <label class="form-check-label fw-bold" for="shopeepay">
                                            ShopeePay
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#">Terms & Conditions</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                Proceed to Payment
                            </button>
                        </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Highlight selected payment method
        document.querySelectorAll('.payment-method').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(el => {
                    el.classList.remove('active');
                });
                this.classList.add('active');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Show/hide bank options based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const bankOptions = document.getElementById('bank-options');
                if (this.value === 'bank_transfer') {
                    bankOptions.style.display = 'flex';
                } else {
                    bankOptions.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
