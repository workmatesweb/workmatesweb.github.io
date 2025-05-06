<?php
// backend/submit_rating.php

require_once 'config.php';
session_start();

// Check if the request is POST and user is logged in
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    die("Access denied");
}

// Validate required parameters
if (!isset($_POST['project_id']) || !isset($_POST['freelancer_id']) || !isset($_POST['rating'])) {
    header("HTTP/1.1 400 Bad Request");
    die("Missing required parameters");
}

$project_id = (int)$_POST['project_id'];
$freelancer_id = (int)$_POST['freelancer_id'];
$rating = (int)$_POST['rating'];
$review = isset($_POST['review']) ? trim($_POST['review']) : null;
$client_id = (int)$_SESSION['user_id'];

// Validate rating value
if ($rating < 1 || $rating > 5) {
    header("HTTP/1.1 400 Bad Request");
    die("Invalid rating value");
}

// Begin transaction
$conn->begin_transaction();

try {
    // 1. Verify the client owns this project
    $check_project = $conn->prepare("SELECT client_id, status FROM projects WHERE id = ?");
    $check_project->bind_param("i", $project_id);
    $check_project->execute();
    $project_data = $check_project->get_result()->fetch_assoc();
    
    if (!$project_data || $project_data['client_id'] != $client_id) {
        throw new Exception("You are not authorized to rate this project");
    }
    
    if ($project_data['status'] != 'completed') {
        throw new Exception("You can only rate completed projects");
    }
    
    // 2. Check if rating already exists
    $check_rating = $conn->prepare("SELECT id FROM project_ratings WHERE project_id = ? AND client_id = ?");
    $check_rating->bind_param("ii", $project_id, $client_id);
    $check_rating->execute();
    
    if ($check_rating->get_result()->num_rows > 0) {
        throw new Exception("You have already rated this project");
    }
    
    // 3. Insert the new rating
    $insert_rating = $conn->prepare("INSERT INTO project_ratings 
                                   (project_id, freelancer_id, client_id, rating, review, created_at) 
                                   VALUES (?, ?, ?, ?, ?, NOW())");
    $insert_rating->bind_param("iiiis", $project_id, $freelancer_id, $client_id, $rating, $review);
    
    if (!$insert_rating->execute()) {
        throw new Exception("Failed to save rating");
    }
    
    // 4. Update freelancer's average rating
    // First get all ratings for this freelancer
    $get_ratings = $conn->prepare("SELECT rating FROM project_ratings WHERE freelancer_id = ?");
    $get_ratings->bind_param("i", $freelancer_id);
    $get_ratings->execute();
    $ratings = $get_ratings->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $total_ratings = count($ratings);
    $sum_ratings = array_sum(array_column($ratings, 'rating'));
    $new_avg_rating = $total_ratings > 0 ? $sum_ratings / $total_ratings : 0;
    
    // Update freelancer's profile with new average rating
    $update_freelancer = $conn->prepare("UPDATE freelancers SET rating = ? WHERE id = ?");
    $update_freelancer->bind_param("di", $new_avg_rating, $freelancer_id);
    
    if (!$update_freelancer->execute()) {
        throw new Exception("Failed to update freelancer rating");
    }
    
    // 5. Mark project as rated
    $update_project = $conn->prepare("UPDATE projects SET is_rated = 1 WHERE id = ?");
    $update_project->bind_param("i", $project_id);
    
    if (!$update_project->execute()) {
        throw new Exception("Failed to update project status");
    }
    
    // Commit transaction if all queries succeeded
    $conn->commit();
    
    // Return success response
    header("Content-Type: application/json");
    echo json_encode([
        'success' => true,
        'message' => 'Rating submitted successfully',
        'new_avg_rating' => round($new_avg_rating, 2)
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    header("HTTP/1.1 400 Bad Request");
    header("Content-Type: application/json");
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connection
$conn->close();
?>