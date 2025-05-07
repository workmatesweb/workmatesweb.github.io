<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Not authenticated']));
}

$data = json_decode(file_get_contents('php://input'), true);
$freelancerId = (int)$data['freelancer_id'];
$message = $conn->real_escape_string($data['message']);
$clientId = $_SESSION['user_id'];
$isClient = 1; // Since this is from client side

$stmt = $conn->prepare("INSERT INTO general_messages (client_id, freelancer_id, sender_id, is_client, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiis", $clientId, $freelancerId, $clientId, $isClient, $message);
$success = $stmt->execute();

echo json_encode(['success' => $success]);

$stmt->close();
$conn->close();
?>