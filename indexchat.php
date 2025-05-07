<?php
require_once 'backend/chat_functions.php';

// Check if user is a freelancer
if (!isset($_SESSION['freelancer_id'])) {
    header("Location: login.php");
    exit();
}

$freelancerId = $_SESSION['freelancer_id'];
$chatPartners = getChatPartners($freelancerId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workmates - My Chats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .chat-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        .unread-badge {
            font-size: 0.75rem;
        }
        .last-message {
            font-size: 0.9rem;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .time-ago {
            font-size: 0.8rem;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="dashboardfreelancer.php">
                <img src="image/download.png" alt="Workmates" width="30" height="30" class="d-inline-block align-top">
                Workmates
            </a>
            <div class="d-flex align-items-center">
                <a href="dashboardfreelancer.php" class="btn btn-outline-secondary me-2">Dashboard</a>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'image/profil.svg'); ?>" width="30" height="30" class="rounded-circle">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profilefreelancer.php">Profile</a></li>
                        <li><a class="dropdown-item" href="indexchat.php">Messages</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Chats</h5>
                        <span class="badge bg-primary"><?php echo count($chatPartners); ?></span>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (empty($chatPartners)): ?>
                            <div class="list-group-item text-center py-4 text-muted">
                                No chats yet
                            </div>
                        <?php else: ?>
                            <?php foreach ($chatPartners as $partner): ?>
                                <a href="chatgeneral.php?client_id=<?php echo $partner['client_id']; ?>" 
                                   class="list-group-item list-group-item-action chat-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo htmlspecialchars($partner['client_photo'] ?: 'image/profil.svg'); ?>" 
                                             width="40" height="40" class="rounded-circle me-3">
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($partner['client_name']); ?></h6>
                                            <small class="last-message" style="max-width: 180px; display: inline-block;">
                                                <?php echo htmlspecialchars($partner['last_message_content'] ?? ''); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="time-ago d-block">
                                            <?php echo timeAgo($partner['last_message_time']); ?>
                                        </small>
                                        <?php if ($partner['unread_count'] > 0): ?>
                                            <span class="badge bg-danger unread-badge"><?php echo $partner['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="text-center text-muted">
                            <i class="bi bi-chat-square-text" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">Select a chat to view messages</h5>
                            <p>Or start a new conversation from a client's profile</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>