<?php
// update_progress.php

require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

// Validate inputs
$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$progress = isset($_POST['progress']) ? (int)$_POST['progress'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : 'pending';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($project_id <= 0 || $progress < 0 || $progress > 100) {
    die("Invalid input");
}

// Get user ID from session
$user_id = $_SESSION['freelancer_id'] ?? 0;
if ($user_id <= 0) {
    die("Unauthorized");
}

// Update progress and status
$update_query = "UPDATE projects SET progress = ?, status = ? WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("isi", $progress, $status, $project_id);
$stmt->execute();

// Add log entry
if (!empty($message)) {
    $log_query = "INSERT INTO project_logs (project_id, user_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($log_query);
    $stmt->bind_param("iis", $project_id, $user_id, $message);
    $stmt->execute();
}

// Close connection
$conn->close();

// Redirect back to project detail
header("Location: ../project.php?id=$project_id");
exit();