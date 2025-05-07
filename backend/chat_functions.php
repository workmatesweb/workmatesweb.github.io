<?php
session_start();
require_once 'config.php';

function getChatPartners($freelancerId) {
    global $conn;
    
    $sql = "SELECT 
                u.id as client_id,
                u.name as client_name,
                u.profile_picture as client_photo,
                MAX(gm.created_at) as last_message_time,
                (SELECT message FROM general_messages 
                 WHERE client_id = u.id AND freelancer_id = ?
                 ORDER BY created_at DESC LIMIT 1) as last_message_content,
                COUNT(CASE WHEN gm.is_read = FALSE AND gm.is_client = 1 THEN 1 END) as unread_count
            FROM 
                general_messages gm
            JOIN 
                users u ON gm.client_id = u.id
            WHERE 
                gm.freelancer_id = ?
            GROUP BY 
                u.id, u.name, u.profile_picture
            ORDER BY 
                MAX(gm.created_at) DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $freelancerId, $freelancerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $partners = [];
    while ($row = $result->fetch_assoc()) {
        $partners[] = $row;
    }
    
    return $partners;
}

function getMessages($clientId, $freelancerId) {
    global $conn;
    
    $sql = "SELECT 
                gm.*, 
                gm.sender_id = ? as is_client,
                u.name as sender_name,
                u.profile_picture as sender_photo
            FROM 
                general_messages gm
            JOIN
                users u ON gm.sender_id = u.id
            WHERE 
                (gm.client_id = ? AND gm.freelancer_id = ?)
            ORDER BY 
                gm.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $clientId, $clientId, $freelancerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    return $messages;
}

function getClientInfo($clientId) {
    global $conn;
    
    $sql = "SELECT 
                id as client_id, 
                name as client_name, 
                profile_picture as client_photo 
            FROM 
                users 
            WHERE 
                id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

function markMessagesAsRead($clientId, $freelancerId) {
    global $conn;
    
    $sql = "UPDATE 
                general_messages 
            SET 
                is_read = TRUE 
            WHERE 
                client_id = ? 
                AND freelancer_id = ? 
                AND is_client = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $clientId, $freelancerId);
    $stmt->execute();
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $timeDiff = time() - $time;
    
    if ($timeDiff < 60) {
        return "Just now";
    } elseif ($timeDiff < 3600) {
        $mins = floor($timeDiff / 60);
        return $mins . " min" . ($mins == 1 ? "" : "s") . " ago";
    } elseif ($timeDiff < 86400) {
        $hours = floor($timeDiff / 3600);
        return $hours . " hour" . ($hours == 1 ? "" : "s") . " ago";
    } elseif ($timeDiff < 2592000) {
        $days = floor($timeDiff / 86400);
        return $days . " day" . ($days == 1 ? "" : "s") . " ago";
    } else {
        return date("M j, Y", $time);
    }
}
?>