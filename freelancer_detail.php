<?php
require_once 'backend/service_detail.php';
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - <?php echo htmlspecialchars($freelancer['job_title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/Paymentstyle.css">
    <style>
        .card {
            border-radius: 15px;
            overflow: hidden;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color: #1E3A5F;
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        textarea {
            min-height: 120px;
        }
    </style>
</head>
<body>
    <!-- [Keep your existing navbar code] -->

    <!-- Kartu Kontainer -->
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow-lg p-4" style="max-width: 1300px; width: 100%;">
            <div class="d-flex align-items-center">
                <!-- Gambar -->
                <div class="me-4">
                    <img src="<?php echo htmlspecialchars($freelancer['profile_picture'] ?: 'image/default-profile.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($freelancer['full_name']); ?>" 
                         class="rounded img-fluid" style="width: 300px; height: auto;">
                </div>
                
                <!-- Konten -->
                <div class="w-100">
                    <h2 class="fw-bold"><?php echo htmlspecialchars($freelancer['job_title']); ?></h2>
                    <p class="text-muted"><?php echo htmlspecialchars($freelancer['bio']); ?></p>
                    <p class="fs-5 fw-bold">Starting From: <span class="text-primary">Rp. <?php echo number_format($freelancer['expected_salary'], 0, ',', '.'); ?></span></p>
                </div>
            </div>
            
            <!-- Form Pemesanan -->
            <form method="POST" action="backend/service_detail.php?id=<?php echo $freelancer_id; ?>">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <label for="description" class="fw-bold">Description:</label>
                    <textarea id="description" name="description" class="form-control mb-2" 
                              placeholder="Describe the concept you want to build" required></textarea>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="date" class="fw-bold">Consultation Schedule:</label>
                            <input type="date" id="date" name="date" class="form-control mb-2" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Offer Price:</label>
                            <div class="d-flex flex-column">
                                <div class="form-check">
                                    <input type="radio" name="price_option" id="harga1" class="form-check-input" value="default" checked>
                                    <label for="harga1" class="form-check-label fw-bold">Rp. <?php echo number_format($freelancer['expected_salary'], 0, ',', '.'); ?></label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="price_option" id="harga2" class="form-check-input" value="custom">
                                    <input type="number" name="custom_price" class="form-control" placeholder="Enter Your Budget" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" required>
                        <label class="form-check-label" for="terms">I agree to the Terms & Conditions.</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- [Keep your existing footer code] -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable/disable custom price input
        document.querySelectorAll('input[name="price_option"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const customPriceInput = document.querySelector('input[name="custom_price"]');
                customPriceInput.disabled = this.value !== 'custom';
                if (this.value !== 'custom') {
                    customPriceInput.value = '';
                }
            });
        });
        
        // Set minimum date to today
        document.getElementById('date').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>