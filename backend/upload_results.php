<?php
// upload_results.php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

if (!isset($_POST['project_id']) || !is_numeric($_POST['project_id'])) {
    die("Invalid project ID");
}

$project_id = $_POST['project_id'];

// Check if freelancer is logged in
if (!isset($_SESSION['freelancer_id'])) {
    die("You must be logged in as a freelancer to upload files");
}

$freelancer_id = $_SESSION['freelancer_id'];

// Verify the freelancer exists and is assigned to this project
$check_freelancer = $conn->prepare("SELECT id FROM freelancers WHERE id = ? AND id IN (SELECT freelancer_id FROM projects WHERE id = ?)");
$check_freelancer->bind_param("ii", $freelancer_id, $project_id);
$check_freelancer->execute();
$check_freelancer->store_result();

if ($check_freelancer->num_rows === 0) {
    die("You are not authorized to upload files for this project");
}

// Check if file was uploaded without errors
if (!isset($_FILES['project_results']) || $_FILES['project_results']['error'] !== UPLOAD_ERR_OK) {
    die("Error uploading file");
}

// Validate file
$allowed_types = ['application/pdf', 'application/zip', 'application/msword', 
                 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                 'image/jpeg', 'image/png'];
$max_size = 10 * 1024 * 1024; // 10MB

if (!in_array($_FILES['project_results']['type'], $allowed_types) || 
    $_FILES['project_results']['size'] > $max_size) {
    die("Invalid file type or size too large");
}

// Create uploads directory if it doesn't exist
$upload_dir = 'backend/uploads/project_results/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$file_ext = pathinfo($_FILES['project_results']['name'], PATHINFO_EXTENSION);
$filename = 'project_' . $project_id . '_' . time() . '.' . $file_ext;
$destination = $upload_dir . $filename;

// Move uploaded file
if (!move_uploaded_file($_FILES['project_results']['tmp_name'], $destination)) {
    die("Failed to move uploaded file");
}

// Update database
$update_query = "UPDATE projects SET hasil_project = ? WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("si", $destination, $project_id);

if (!$stmt->execute()) {
    die("Error updating project: " . $conn->error);
}

// Add log entry
$message = isset($_POST['message']) ? $_POST['message'] : "Uploaded project results";
$log_query = "INSERT INTO project_logs (project_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($log_query);
$stmt->bind_param("iis", $project_id, $freelancer_id, $message);

if (!$stmt->execute()) {
    die("Error creating log entry: " . $conn->error);
}

$conn->close();

header("Location: ../project_detail.php?id=" . $project_id);
exit();
?>