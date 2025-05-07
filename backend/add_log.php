<?php
// add_log.php

require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

// Validate inputs
$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($project_id <= 0 || empty($message)) {
    die("Invalid input");
}

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    die("Unauthorized");
}

// Add log entry
$log_query = "INSERT INTO project_logs (project_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($log_query);
$stmt->bind_param("iis", $project_id, $user_id, $message);
$stmt->execute();

// Close connection
$conn->close();

// Redirect back to project detail
header("Location: project_detail.php?id=$project_id");
exit();