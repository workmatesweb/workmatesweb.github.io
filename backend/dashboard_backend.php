<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['freelancer_id'])) {
    header("Location: login.html");
    exit();
}


// Fetch freelancer data
$freelancer_id = $_SESSION['freelancer_id'];
$sql = "SELECT 
            f.full_name, 
            f.phone_number,
            f.email,
            f.availability,
            COUNT(p.id) AS total_projects,
            SUM(CASE WHEN p.status = 'completed' THEN p.price ELSE 0 END) AS total_income,
            AVG(p.progress) AS avg_progress
        FROM freelancers f
        LEFT JOIN projects p ON f.id = p.freelancer_id
        WHERE f.id = ?
        GROUP BY f.id";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $freelancer = $result->fetch_assoc();
    
    // Format values
    $freelancer['total_income'] = $freelancer['total_income'] ?? 0;
    $freelancer['avg_progress'] = round($freelancer['avg_progress'] ?? 0);
    $freelancer['is_available'] = $freelancer['availability'] == 1;
} else {
    die("Freelancer not found");
}

// Fetch projects based on filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$date_filter = isset($_GET['date']) ? $_GET['date'] : null;
$job_type_filter = isset($_GET['job_type']) ? $_GET['job_type'] : null;

$projects_sql = "SELECT * FROM projects WHERE freelancer_id = ?";
$params = array($freelancer_id);
$types = "i";

// Add filters to SQL query
if ($status_filter && $status_filter != 'all') {
    $projects_sql .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($date_filter) {
    $projects_sql .= " AND DATE(deadline) = ?";
    $params[] = $date_filter;
    $types .= "s";
}

if ($job_type_filter && $job_type_filter != 'all') {
    $projects_sql .= " AND job_type = ?";
    $params[] = $job_type_filter;
    $types .= "s";
}

$projects_sql .= " ORDER BY 
    CASE 
        WHEN status = 'in_progress' THEN 1
        WHEN status = 'pending' THEN 2
        WHEN status = 'completed' THEN 3
        ELSE 4
    END, deadline ASC
    LIMIT 20";

$stmt = $conn->prepare($projects_sql);

// Dynamically bind parameters
$stmt->bind_param($types, ...$params);
$stmt->execute();
$projects_result = $stmt->get_result();

?>