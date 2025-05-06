<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Fetch client data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $client = $result->fetch_assoc();
} else {
    die("User not found");
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update personal information
    if (isset($_POST['update_personal'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);
        
        $update_sql = "UPDATE users SET 
                      name = '$name',
                      email = '$email',
                      phone_number = '$phone_number'
                      WHERE id = $user_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = "Personal information updated successfully";
            // Update session name if it exists
            if (isset($_SESSION['name'])) {
                $_SESSION['name'] = $name;
            }
        } else {
            $_SESSION['error'] = "Error updating personal information: " . $conn->error;
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: ../pofileclient.php");
    exit();
}

$conn->close();
?>