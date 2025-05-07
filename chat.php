<?php
require_once 'backend/config.php';
session_start();

// Check login status
if (!isset($_SESSION['freelancer_id']) && !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['freelancer_id'];
$is_client = isset($_SESSION['user_id']) ? 1 : 0;

// Pastikan project_id ada
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid project']));
}

$project_id = intval($_GET['project_id']);

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    header('Content-Type: application/json');
    
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (project_id, sender_id, is_client, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $project_id, $current_user_id, $is_client, $message);
        
        if ($stmt->execute()) {
            $message_id = $conn->insert_id;
            
            $stmt_msg = $conn->prepare("SELECT m.*, 
                                      DATE_FORMAT(m.created_at, '%h:%i %p') as formatted_time,
                                      IF(m.is_client=1, u.name, f.full_name) as sender_name
                                      FROM messages m
                                      LEFT JOIN users u ON m.sender_id = u.id AND m.is_client=1
                                      LEFT JOIN freelancers f ON m.sender_id = f.id AND m.is_client=0
                                      WHERE m.id = ?");
            $stmt_msg->bind_param("i", $message_id);
            $stmt_msg->execute();
            $new_message = $stmt_msg->get_result()->fetch_assoc();
            
            echo json_encode([
                'status' => 'success',
                'message' => $new_message
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save message: ' . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Message cannot be empty'
        ]);
    }
    exit();
}

$project_stmt = $conn->prepare("SELECT p.*, 
                              f.full_name as freelancer_name, f.profile_picture as freelancer_photo,
                              u.name as client_name
                              FROM projects p
                              JOIN freelancers f ON p.freelancer_id = f.id
                              JOIN users u ON p.client_id = u.id
                              WHERE p.id = ?");
$project_stmt->bind_param("i", $project_id);
$project_stmt->execute();
$project = $project_stmt->get_result()->fetch_assoc();
$project_stmt->close();

if (!$project) {
    die("Project not found");
}

// Determine other user
$other_user_id = ($current_user_id == $project['client_id']) ? $project['freelancer_id'] : $project['client_id'];
$other_user_type = ($current_user_id == $project['client_id']) ? 'freelancer' : 'client';
$other_user_name = $other_user_type == 'freelancer' ? $project['freelancer_name'] : $project['client_name'];

// Get all messages for this project
$messages_stmt = $conn->prepare("SELECT m.*, 
                               DATE_FORMAT(m.created_at, '%h:%i %p') as formatted_time,
                               IF(m.is_client=1, u.name, f.full_name) as sender_name
                               FROM messages m
                               LEFT JOIN users u ON m.sender_id = u.id AND m.is_client=1
                               LEFT JOIN freelancers f ON m.sender_id = f.id AND m.is_client=0
                               WHERE m.project_id = ?
                               ORDER BY m.created_at ASC");
$messages_stmt->bind_param("i", $project_id);
$messages_stmt->execute();
$messages = $messages_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$messages_stmt->close();

// Mark messages as read
$read_stmt = $conn->prepare("UPDATE messages SET is_read = 1 
                           WHERE project_id = ? 
                           AND sender_id != ? 
                           AND is_read = 0");
$read_stmt->bind_param("ii", $project_id, $current_user_id);
$read_stmt->execute();
$read_stmt->close();

// For AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'messages' => $messages,
        'other_user' => [
            'id' => $other_user_id,
            'name' => $other_user_name,
            'type' => $other_user_type
        ],
        'project' => [
            'id' => $project_id,
            'title' => $project['title']
        ]
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - <?php echo htmlspecialchars($other_user_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chat-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #e9ecef;
        }
        .message {
            margin-bottom: 15px;
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
        }
        .message-sent {
            margin-left: auto;
            background-color: #007bff;
            color: white;
            border-radius: 18px 18px 0 18px;
        }
        .message-received {
            margin-right: auto;
            background-color: white;
            border-radius: 18px 18px 18px 0;
        }
        .chat-input {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header d-flex align-items-center">
            <a href="project.php?id=<?php echo $project_id; ?>" class="btn btn-sm btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex-grow-1">
                <h5 class="mb-0">
                    <span class="badge bg-info me-2">Project: <?php echo htmlspecialchars($project['title']); ?></span>
                    <?php echo htmlspecialchars($other_user_name); ?>
                </h5>
                <small class="text-muted">
                    <?php echo ucfirst($other_user_type); ?>
                </small>
            </div>
        </div>
        
        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo ($message['sender_id'] == $current_user_id) ? 'message-sent' : 'message-received'; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong><?php echo htmlspecialchars($message['sender_name'] ?? 'User'); ?></strong>
                        <small class="<?php echo ($message['sender_id'] == $current_user_id) ? 'text-white-50' : 'text-muted'; ?>">
                            <?php echo $message['formatted_time']; ?>
                            <?php if ($message['sender_id'] == $current_user_id && $message['is_read']): ?>
                                <i class="fas fa-check-double ms-1"></i>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div><?php echo htmlspecialchars($message['message']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="chat-input">
            <form id="message-form" class="d-flex">
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                <input type="text" name="message" class="form-control me-2" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        const chatMessages = $('#chat-messages');
        
        // Auto-scroll to bottom
        function scrollToBottom() {
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        }
        scrollToBottom();
        
        // Load messages periodically
        function loadMessages() {
            $.getJSON('chat.php?project_id=<?php echo $project_id; ?>', function(data) {
                if (data.messages && data.messages.length > 0) {
                    let messagesHtml = '';
                    
                    data.messages.forEach(msg => {
                        const isMe = msg.sender_id == <?= $current_user_id ?>;
                        messagesHtml += `
                            <div class="message ${isMe ? 'message-sent' : 'message-received'}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>${msg.sender_name || 'User'}</strong>
                                    <small class="${isMe ? 'text-white-50' : 'text-muted'}">
                                        ${msg.formatted_time}
                                        ${isMe && msg.is_read ? '<i class="fas fa-check-double ms-1"></i>' : ''}
                                    </small>
                                </div>
                                <div>${$('<div>').text(msg.message).html()}</div>
                            </div>
                        `;
                    });
                    
                    chatMessages.html(messagesHtml);
                    scrollToBottom();
                }
            });
        }
        
        // Handle message submission
        $('#message-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const messageInput = form.find('input[name="message"]');
            const message = messageInput.val().trim();
            
            if (message) {
                const submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: 'chat.php?project_id=<?php echo $project_id; ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status === 'success' && response.message) {
                            messageInput.val('');
                            displayNewMessage(response.message);
                        } else {
                            alert("Error: " + (response.message || 'Unknown error'));
                        }
                    },
                    error: function(xhr) {
                        alert("Network error. Please try again.");
                        console.error("Error:", xhr.responseText);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i>');
                    }
                });
            }
        });
        
        function displayNewMessage(message) {
            const isMe = message.sender_id == <?= $current_user_id ?>;
            const messageHtml = `
                <div class="message ${isMe ? 'message-sent' : 'message-received'}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>${message.sender_name || 'User'}</strong>
                        <small class="${isMe ? 'text-white-50' : 'text-muted'}">
                            ${message.formatted_time}
                        </small>
                    </div>
                    <div>${$('<div>').text(message.message).html()}</div>
                </div>
            `;
            $('#chat-messages').append(messageHtml);
            scrollToBottom();
        }
        
        // Auto-refresh every 2 seconds
        setInterval(loadMessages, 2000);
    });
    </script>
</body>
</html>