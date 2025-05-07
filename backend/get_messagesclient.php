<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['freelancer_id'])) {
    die(json_encode(['error' => 'Not authenticated']));
}

$freelancerId =  $_SESSION['freelancer_id'];
$clientId = (int)$_GET['user_id'];

// Get messages between this client and freelancer
$stmt = $conn->prepare("SELECT *, sender_id = ? as is_client 
                        FROM general_messages 
                        WHERE (client_id = ? AND freelancer_id = ?)
                        ORDER BY created_at ASC");
$stmt->bind_param("iii", $clientId, $clientId, $freelancerId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Mark messages as read
$conn->query("UPDATE general_messages SET is_read = TRUE 
              WHERE freelancer_id = $freelancerId 
              AND client_id = $clientId
              AND is_client = 0");

echo json_encode($messages);

$stmt->close();
$conn->close();
?>