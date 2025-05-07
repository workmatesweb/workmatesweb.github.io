<?php
// update_project_status.php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

if (!isset($_POST['project_id']) || !is_numeric($_POST['project_id'])) {
    die("Invalid project ID");
}

$project_id = $_POST['project_id'];
$new_status = $_POST['new_status'] ?? '';
$message = $_POST['message'] ?? '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;

// Validate status
$allowed_statuses = ['completed', 'revision'];
if (!in_array($new_status, $allowed_statuses)) {
    die("Invalid status");
}

// Update project status
$update_query = "UPDATE projects SET status = ?, progress = ? WHERE id = ?";
$stmt = $conn->prepare($update_query);
$progress = ($new_status == 'completed') ? 100 : 80;
$stmt->bind_param("sii", $new_status, $progress, $project_id);

if (!$stmt->execute()) {
    die("Error updating project status: " . $conn->error);
}

// Add log entry
$log_query = "INSERT INTO project_logs (project_id, id_user, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($log_query);
$log_message = ($new_status == 'completed') 
    ? "Marked project as complete" . (!empty($message) ? ": $message" : "")
    : "Requested project revision" . (!empty($message) ? ": $message" : "");
$stmt->bind_param("iis", $project_id, $_SESSION['user_id'], $log_message);
$stmt->execute();

// If completed and rating provided, save rating
if ($new_status == 'completed' && $rating !== null && $rating >= 1 && $rating <= 5) {
    // First get client_id and freelancer_id from the project
    $project_query = "SELECT client_id, freelancer_id FROM projects WHERE id = ?";
    $stmt_project = $conn->prepare($project_query);
    $stmt_project->bind_param("i", $project_id);
    $stmt_project->execute();
    $project = $stmt_project->get_result()->fetch_assoc();
    
    if ($project) {
        $rating_query = "INSERT INTO project_ratings 
                        (project_id, client_id, freelancer_id, rating, review) 
                        VALUES (?, ?, ?, ?, ?) 
                        ON DUPLICATE KEY UPDATE 
                        rating = VALUES(rating), review = VALUES(review)";
        
        $stmt = $conn->prepare($rating_query);
        $stmt->bind_param("iiiis", 
            $project_id, 
            $project['client_id'], 
            $project['freelancer_id'], 
            $rating, 
            $message);
        
        if (!$stmt->execute()) {
            die("Error saving rating: " . $conn->error);
        }
    }
}

$conn->close();
header("Location: ../detail_project.php?id=" . $project_id);
exit();
?>