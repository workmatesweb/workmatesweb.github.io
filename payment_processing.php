<?php
require_once 'backend/config.php';
require_once __DIR__ . '/vendor/autoload.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-POVgPWLqPgTc6ZOihxtbB2Jz';
\Midtrans\Config::$isProduction = false;

session_start();

if (!isset($_GET['snap_token'])) {
    header("Location: freelancers.php");
    exit();
}

$snap_token = $_GET['snap_token'];

// Verify the token belongs to the user
$sql = "SELECT p.* FROM payments py
        JOIN projects p ON py.project_id = p.id
        WHERE py.snap_token = ? AND p.client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $snap_token, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid payment token");
}

$project = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - Workmates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="SB-Mid-client-Kq98wSCVkF1Jfe-Z"></script>
    <style>
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        #snap-container {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="payment-container">
            <div class="text-center mb-4">
                <h2>Complete Your Payment</h2>
                <p>Project: <?= htmlspecialchars($project['title']) ?></p>
            </div>
            
            <div id="snap-container">
                <!-- Midtrans Snap will be rendered here -->
            </div>
            
            <div class="text-center mt-4">
                <a href="project.php?id=<?= $project['id'] ?>" class="btn btn-outline-secondary">
                    Back to Project
                </a>
            </div>
        </div>
    </div>

    <script>
        // Embed Midtrans Snap
        snap.pay('<?= $snap_token ?>', {
            onSuccess: function(result) {
                window.location.href = 'payment_success.php?order_id=' + result.order_id;
            },
            onPending: function(result) {
                window.location.href = 'payment_pending.php?order_id=' + result.order_id;
            },
            onError: function(result) {
                window.location.href = 'payment_failed.php?order_id=' + result.order_id;
            },
            onClose: function() {
                // User closed the popup without finishing the payment
                console.log('Payment popup closed');
            }
        });
    </script>
</body>
</html>
