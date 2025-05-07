<?php
session_start();
require_once 'backend/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==================== FUNGSI CHAT ==================== //
function getMessages($clientId, $freelancerId) {
    global $conn;
    $sql = "SELECT gm.*, 
                   gm.sender_id = ? as is_client, 
                   u.name as sender_name,
                   u.profile_picture as sender_photo
            FROM general_messages gm
            JOIN users u ON gm.sender_id = u.id
            WHERE (gm.client_id = ? AND gm.freelancer_id = ?)
            ORDER BY gm.created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $clientId, $clientId, $freelancerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function markMessagesAsRead($clientId, $freelancerId) {
    global $conn;
    $sql = "UPDATE general_messages SET is_read = TRUE 
            WHERE client_id = ? AND freelancer_id = ? AND is_client = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $clientId, $freelancerId);
    $stmt->execute();
}

function getClientInfo($clientId) {
    global $conn;
    $sql = "SELECT id, name, profile_picture FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ==================== HANDLE FORM SUBMIT ==================== //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message'])) {
        $clientId = (int)$_POST['client_id'];
        $message = trim($_POST['message']);
        $freelancerId = $_SESSION['freelancer_id'];
        
        if (!empty($message)) {
            $sql = "INSERT INTO general_messages (client_id, freelancer_id, sender_id, is_client, message) 
                    VALUES (?, ?, ?, 0, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiis", $clientId, $freelancerId, $freelancerId, $message);
            $stmt->execute();
            
            // Return JSON response for AJAX
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
            exit;
        }
    }
    
    // Handle AJAX refresh request
    if (isset($_POST['refresh'])) {
        $clientId = (int)$_POST['client_id'];
        $freelancerId = $_SESSION['freelancer_id'];
        $messages = getMessages($clientId, $freelancerId);
        
        ob_start();
        displayMessages($messages);
        $html = ob_get_clean();
        
        header('Content-Type: application/json');
        echo json_encode(['html' => $html]);
        exit;
    }
}

// ==================== DISPLAY FUNCTIONS ==================== //
function displayMessages($messages) {
    if (empty($messages)) {
        echo '<div class="text-center text-muted py-5">
                <i class="bi bi-chat-square-text" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Mulai percakapan Anda</h5>
                <p>Kirim pesan pertama untuk memulai diskusi</p>
              </div>';
        return;
    }
    
    foreach ($messages as $message) {
        $isClient = $message['is_client'];
        $senderPhoto = htmlspecialchars($message['sender_photo'] ?? 'image/profil.svg');
        $messageTime = date('H:i', strtotime($message['created_at']));
        
        echo '<div class="mb-3 ' . ($isClient ? 'text-start' : 'text-end') . '">
                <div class="d-flex align-items-end ' . ($isClient ? 'justify-content-start' : 'justify-content-end') . '">
                    ' . ($isClient ? '<img src="' . $senderPhoto . '" width="32" height="32" class="rounded-circle me-2">' : '') . '
                    
                    <div class="' . ($isClient ? 'bg-light' : 'bg-primary text-white') . ' p-3 rounded" style="max-width: 70%;">
                        ' . htmlspecialchars($message['message']) . '
                        <div class="small ' . ($isClient ? 'text-muted' : 'text-white-50') . '">
                            ' . $messageTime . '
                            ' . (!$isClient ? '<i class="fas fa-check' . ($message['is_read'] ? '-double' : '') . ' ms-1"></i>' : '') . '
                        </div>
                    </div>
                    
                    ' . (!$isClient ? '<img src="' . htmlspecialchars($_SESSION['profile_picture'] ?? 'image/profil.svg') . '" width="32" height="32" class="rounded-circle ms-2">' : '') . '
                </div>
              </div>';
    }
}

// ==================== PAGE LOAD ==================== //
if (!isset($_SESSION['freelancer_id']) || !isset($_GET['client_id'])) {
    header("Location: indexchat.php");
    exit;
}

$freelancerId = $_SESSION['freelancer_id'];
$clientId = (int)$_GET['client_id'];
$clientInfo = getClientInfo($clientId);

markMessagesAsRead($clientId, $freelancerId);
$messages = getMessages($clientId, $freelancerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - Chat dengan <?= htmlspecialchars($clientInfo['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .chat-container {
            height: calc(100vh - 180px);
            overflow-y: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            margin-bottom: 8px;
            position: relative;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        .client-message {
            background-color: #e9ecef;
            border-bottom-left-radius: 4px;
        }
        
        .freelancer-message {
            background-color: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message-time {
            font-size: 0.75rem;
            opacity: 0.8;
            margin-top: 4px;
            display: inline-block;
        }
        
        .chat-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .chat-input {
            border-top: 1px solid #e9ecef;
            padding: 15px;
            background-color: #fff;
            border-radius: 0 0 10px 10px;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .typing-indicator {
            font-size: 0.9rem;
            color: #6c757d;
            font-style: italic;
            height: 20px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .scroll-down-btn {
            position: absolute;
            right: 20px;
            bottom: 80px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .scroll-down-btn.visible {
            opacity: 1;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--primary-color);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboardfreelancer.php">
                <img src="image/download.png" alt="Workmates" width="30" height="30" class="d-inline-block align-top me-2">
                Workmates
            </a>
            <div class="d-flex align-items-center">
                <a href="dashboardfreelancer.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-home"></i>
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                        <img src="<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'image/profil.svg') ?>" width="30" height="30" class="rounded-circle">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profilefreelancer.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="indexchat.php"><i class="fas fa-comments me-2"></i>Messages</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header chat-header d-flex align-items-center py-3">
                        <a href="indexchat.php" class="btn btn-sm btn-light me-2 d-lg-none">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <img src="<?= htmlspecialchars($clientInfo['profile_picture'] ?? 'image/profil.svg') ?>" 
                             width="40" height="40" class="rounded-circle me-3">
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($clientInfo['name']) ?></h5>
                            <div class="typing-indicator" id="typingIndicator"></div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0 position-relative">
                        <div class="chat-container" id="chatMessages">
                            <?php displayMessages($messages); ?>
                        </div>
                        <button class="scroll-down-btn" id="scrollDownBtn">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </div>
                    
                    <div class="card-footer chat-input">
                        <form id="messageForm" method="POST" class="position-relative">
                            <input type="hidden" name="client_id" value="<?= $clientId ?>">
                            <div class="input-group">
                                <input type="text" name="message" class="form-control" 
                                       placeholder="Ketik pesan..." id="messageInput" autocomplete="off" required>
                                <button type="submit" class="btn btn-primary" id="sendButton">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatContainer = document.getElementById('chatMessages');
            const messageForm = document.getElementById('messageForm');
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const typingIndicator = document.getElementById('typingIndicator');
            const scrollDownBtn = document.getElementById('scrollDownBtn');
            
            // Auto-scroll to bottom on initial load
            scrollToBottom();
            
            // Check for new messages every 2 seconds
            const refreshInterval = setInterval(fetchNewMessages, 2000);
            
            // Handle form submission
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                if (message === '') return;
                
                sendButton.disabled = true;
                sendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `client_id=<?= $clientId ?>&message=${encodeURIComponent(message)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        messageInput.value = '';
                        fetchNewMessages();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                });
            });
            
            // Typing indicator
            let typingTimeout;
            messageInput.addEventListener('input', function() {
                // In a real app, you would send a "typing" event to the server here
                typingIndicator.textContent = 'Mengetik...';
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    typingIndicator.textContent = '';
                }, 2000);
            });
            
            // Scroll down button
            chatContainer.addEventListener('scroll', function() {
                const scrollThreshold = 100;
                const isNearBottom = chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight > scrollThreshold;
                scrollDownBtn.classList.toggle('visible', isNearBottom);
            });
            
            scrollDownBtn.addEventListener('click', scrollToBottom);
            
            // Functions
            function fetchNewMessages() {
                fetch(`?client_id=<?= $clientId ?>&refresh=1`)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newMessages = doc.getElementById('chatMessages').innerHTML;
                        
                        // Only update if messages changed
                        if (newMessages !== chatContainer.innerHTML) {
                            const wasAtBottom = isAtBottom();
                            chatContainer.innerHTML = newMessages;
                            
                            if (wasAtBottom) {
                                scrollToBottom();
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching messages:', error));
            }
            
            function scrollToBottom() {
                chatContainer.scrollTop = chatContainer.scrollHeight;
                scrollDownBtn.classList.remove('visible');
            }
            
            function isAtBottom() {
                return chatContainer.scrollHeight - chatContainer.scrollTop - chatContainer.clientHeight < 50;
            }
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                clearInterval(refreshInterval);
            });
        });
    </script>
</body>
</html>