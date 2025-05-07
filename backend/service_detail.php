<?php
require_once 'config.php';
session_start();

// Check if user is logged in (either as freelancer or client)
if (isset($_SESSION['freelancer_id'])) {
    header("Location: http://localhost/23si2/workmates.github.io/login.php");
    exit();
}

// Get freelancer ID from URL
$freelancer_id = intval($_GET['id']);

// Fetch freelancer data
$sql = "SELECT * FROM freelancers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: http://localhost/23si2/workmatesweb.github.io/freelancers.php");
    exit();
}

$freelancer = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prevent freelancer from hiring themselves
    if (isset($_SESSION['freelancer_id']) && $_SESSION['freelancer_id'] == $freelancer_id) {
        $error = "You cannot hire yourself as a freelancer";
    } else {
        $description = $conn->real_escape_string($_POST['description']);
        $consultation_date = $conn->real_escape_string($_POST['date']);
        $price = isset($_POST['custom_price']) && is_numeric($_POST['custom_price']) 
                  ? floatval($_POST['custom_price']) 
                  : 800000; // Default price
        
        // Determine client ID (either from user_id session or prevent if freelancer)
        $client_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        if ($client_id) {
            $insert_sql = "INSERT INTO projects (freelancer_id, client_id, description, consultation_date, price, status) 
                           VALUES (?, ?, ?, ?, ?, 'pending')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iissd", $freelancer_id, $client_id, $description, $consultation_date, $price);
            
            if ($insert_stmt->execute()) {
                $project_id = $conn->insert_id;
                header("Location: http://localhost/23si2/workmatesweb.github.io/payment.php?project_id=" . $project_id);
                exit();
            } else {
                $error = "Error creating project: " . $conn->error;
            }
            
            $insert_stmt->close();
        } else {
            $error = "Only clients can hire freelancers";
        }
    }
}

$conn->close();
?>