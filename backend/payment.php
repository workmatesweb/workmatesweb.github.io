<?php
require_once 'config.php';
session_start();
// Check if project ID is provided
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    header("Location: http://localhost/workmatesweb.github.io/freelancers.php");
    exit();
}


$project_id = intval($_GET['project_id']);

// Fetch project data
$sql = "SELECT p.*, f.full_name, f.profile_picture 
        FROM projects p
        JOIN freelancers f ON p.freelancer_id = f.id
        WHERE p.id = ? AND p.client_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $project_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();



if ($result->num_rows === 0) {
    header("Location: http://localhost/workmatesweb.github.io/freelancers.php");
    exit();
}

$project = $result->fetch_assoc();
$stmt->close();

// Calculate total with admin fee (10%)
$admin_fee = $project['price'] * 0.1;
$total = $project['price'] + $admin_fee;

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $client_phone = $conn->real_escape_string($_POST['client_phone']);
    $client_email = $conn->real_escape_string($_POST['client_email']);
    
    // Process payment (in a real app, this would integrate with a payment gateway)
    $payment_sql = "INSERT INTO payments (project_id, amount, admin_fee, payment_method, status)
                    VALUES (?, ?, ?, ?, 'pending')";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("idds", $project_id, $total, $admin_fee, $payment_method);
    
    // Update client info
    $client_sql = "UPDATE users SET 
                   full_name = ?, 
                   phone_number = ?, 
                   email = ?
                   WHERE id = ?";
    $client_stmt = $conn->prepare($client_sql);
    $client_stmt->bind_param("sssi", $client_name, $client_phone, $client_email, $_SESSION['user_id']);
    
    if ($payment_stmt->execute() && $client_stmt->execute()) {
        // In a real app, redirect to payment gateway
        header("Location: payment_success.php?project_id=" . $project_id);
        exit();
    } else {
        $error = "Error processing payment: " . $conn->error;
    }
    
    $payment_stmt->close();
    $client_stmt->close();
}

$conn->close();
?>