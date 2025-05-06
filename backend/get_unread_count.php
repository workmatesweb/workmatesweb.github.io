<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['freelancer_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM general_messages 
                       WHERE freelancer_id = ? AND is_read = 0 AND is_client = 1");
$stmt->bind_param("i", $_SESSION['freelancer_id']);
$stmt->execute();
$count = $stmt->get_result()->fetch_row()[0];

echo json_encode(['count' => $count]);

$stmt->close();
$conn->close();
?>