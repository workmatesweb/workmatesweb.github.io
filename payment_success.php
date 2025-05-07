<?php
require_once 'backend/config.php';
session_start();

if (!isset($_GET['order_id'])) {
    header("Location: freelancers.php");
    exit();
}

$order_id = $_GET['order_id'];

// Get payment details
$sql = "SELECT py.*, p.title 
        FROM payments py
        JOIN projects p ON py.project_id = p.id
        WHERE py.order_id = ? AND p.client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid order ID");
}

$payment = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Workmates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#28a745" class="bi bi-check-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                            </svg>
                        </div>
                        <h2 class="mb-3">Payment Successful!</h2>
                        <p class="lead">Thank you for your payment.</p>
                        
                        <div class="card mt-4 mb-4">
                            <div class="card-body text-start">
                                <h5 class="card-title">Payment Details</h5>
                                <p><strong>Order ID:</strong> <?= htmlspecialchars($payment['order_id']) ?></p>
                                <p><strong>Project:</strong> <?= htmlspecialchars($payment['title']) ?></p>
                                <p><strong>Amount:</strong> Rp <?= number_format($payment['amount'] + $payment['admin_fee'], 0, ',', '.') ?></p>
                                <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment['payment_method']) ?></p>
                            </div>
                        </div>
                        
                        <a href="detail_project.php?id=<?= $payment['project_id'] ?>" class="btn btn-primary">
                            View Project
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
