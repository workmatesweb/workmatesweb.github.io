<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['freelancer_id'])) {
    header("HTTP/1.1 403 Forbidden");
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);
$available = isset($data['available']) ? (bool)$data['available'] : false;
$freelancer_id = $_SESSION['freelancer_id'];

try {
    // First check if column exists
    $check_column = $conn->query("SHOW COLUMNS FROM freelancers LIKE 'availability'");
    if ($check_column->num_rows === 0) {
        // Create the column if it doesn't exist
        $conn->query("ALTER TABLE freelancers ADD COLUMN availability TINYINT(1) DEFAULT 1");
    }

    // Update availability
    $stmt = $conn->prepare("UPDATE freelancers SET availability = ? WHERE id = ?");
    $stmt->bind_param("ii", $available, $freelancer_id);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}