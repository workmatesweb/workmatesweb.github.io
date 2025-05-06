<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit(json_encode(['error' => 'Unauthorized access']));
}

$client_id = $_SESSION['user_id'];

// Get projects for this client with freelancer details

$projects = [];

$sql = "SELECT 
            p.id, 
            p.title, 
            p.description, 
            p.price, 
            p.status, 
            p.progress, 
            p.deadline, 
            p.created_at,
            p.consultation_date,
            f.full_name as freelancer_name,
            f.profile_picture as freelancer_photo,
            f.job_title as freelancer_job_title,
            f.rating as freelancer_rating,
            f.location as freelancer_location
        FROM projects p
        JOIN freelancers f ON p.freelancer_id = f.id
        WHERE p.client_id = ?
        ORDER BY 
            CASE p.status
                WHEN 'pending' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'completed' THEN 3
                ELSE 4
            END,
            p.created_at DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("HTTP/1.1 500 Internal Server Error");
    exit(json_encode(['error' => 'Database error: ' . $conn->error]));
}

$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$stmt->close();
$conn->close();

// Categorize projects by status
$categorizedProjects = [
    'pending' => [],
    'in_progress' => [],
    'completed' => []
];

foreach ($projects as $project) {
    $categorizedProjects[$project['status']][] = $project;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($categorizedProjects);
?>