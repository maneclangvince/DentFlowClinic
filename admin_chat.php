<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['redirect_after_login'] = 'admin_chat.php';
    header("Location: admin_login.php");
    exit;
}

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Get all unique patients who have sent messages
$patients = [];
foreach ($_SESSION['chats'] as $msg) {
    if ($msg['sender'] === 'patient' && !empty($msg['sender_email'])) {
        $email = $msg['sender_email'];
        if (!isset($patients[$email])) {
            $patients[$email] = [
                'email' => $email,
                'name' => $msg['sender_name'] ?? $email,
                'last_message' => $msg['message_text'] ?? '',
                'timestamp' => $msg['timestamp'] ?? ''
            ];
        } else {
            // Update last message if this is newer
            if (strtotime($msg['timestamp'] ?? '') > strtotime($patients[$email]['timestamp'] ?? '')) {
                $patients[$email]['last_message'] = $msg['message_text'] ?? '';
                $patients[$email]['timestamp'] = $msg['timestamp'] ?? '';
            }
        }
    }
}

// Sort patients by last message time (newest first)
usort($patients, function($a, $b) {
    return strtotime($b['timestamp'] ?? '') - strtotime($a['timestamp'] ?? '');
});

// Get current selected patient (from GET or first one)
$selected_patient = isset($_GET['patient']) ? $_GET['patient'] : (!empty($patients) ? $patients[0]['email'] : '');
$selected_patient_name = '';
foreach ($patients as $p) {
    if ($p['email'] === $selected_patient) {
        $selected_patient_name = $p['name'];
        break;
    }
}

// Filter chats for selected patient
$filtered_chats = array_filter($_SESSION['chats'], function($msg) use ($selected_patient) {
    if ($msg['sender'] === 'patient' && isset($msg['sender_email']) && $msg['sender_email'] === $selected_patient) {
        return true;
    }
    if ($msg['sender'] === 'admin' && isset($msg['recipient']) && $msg['recipient'] === $selected_patient) {
        return true;
    }
    return false;
});

// Sort chats by timestamp
usort($filtered_chats, function($a, $b) {
    return strtotime($a['timestamp'] ?? '') - strtotime($b['timestamp'] ?? '');
});

// Get current patient name for the chat header
$current_patient_name = $selected_patient_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow Admin - Quick Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }
        nav {
            background-color: #212529 !important;
            padding: 15px 20px !important;
            transition: padding 0.3s ease;
        }
        .navbar-brand {
            color: #0dcaf0 !important;
            text-decoration: none;
            font-weight: bold;
            font-size: 24px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            border-bottom: 4px solid transparent;
            transition: border-color 0.2s ease, opacity 0.2s ease;
            background: none;
            border: none;
        }
        @media (min-width: 992px) {
            nav {
                padding: 20px 100px !important;
            }
            .nav-link {
                padding: 6px 4px !important;
            }
            .nav-link:hover, .nav-link.active {
                border-bottom: 4px solid #0dcaf0;
                color: #0dcaf0 !important;
                background: transparent !important;
            }
            .text-danger-custom:hover {
                border-bottom: 4px solid #dc3545;
                color: #dc3545 !important;
            }
        }
        .text-danger-custom {
            color: #dc3545;
        }
        .nav-link.active {
            opacity: 1 !important;
            font-weight: 700;
            border-bottom: 4px solid #0dcaf0 !important;
        }
        .chat-container {
            padding-top: 115px;
            padding-bottom: 40px;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            justify-content: center;
        }
        .chat-wrapper {
            width: 100%;
            max-width: 1100px;
            display: flex;
            gap: 0;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
        }
        .patient-list {
            width: 320px;
            min-width: 320px;
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            max-height: 600px;
        }
        .patient-list-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
            background: white;
            flex-shrink: 0;
        }
        .patient-list-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 16px;
            color: #212529;
        }
        .patient-list-header .badge {
            font-size: 12px;
        }
        .patient-list-items {
            overflow-y: auto;
            flex: 1;
        }
        .patient-list-items::-webkit-scrollbar {
            width: 6px;
        }
        .patient-list-items::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .patient-list-items::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .patient-item {
            padding: 14px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f1f3f5;
            transition: background 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .patient-item:hover {
            background: #e9ecef;
        }
        .patient-item.active {
            background: #0dcaf0;
            color: #212529;
        }
        .patient-item.active .patient-name {
            color: #212529;
        }
        .patient-item.active .patient-last-msg {
            color: #212529;
        }
        .patient-item .patient-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #035270;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }
        .patient-item .patient-info {
            flex: 1;
            min-width: 0;
        }
        .patient-item .patient-name {
            font-weight: 600;
            font-size: 14px;
            color: #212529;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .patient-item .patient-last-msg {
            font-size: 12px;
            color: #6c757d;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .patient-item .patient-time {
            font-size: 10px;
            color: #6c757d;
            flex-shrink: 0;
        }
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-height: 600px;
        }
        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e9ecef;
            background: white;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-header .chat-patient-name {
            font-weight: 700;
            font-size: 18px;
            color: #212529;
        }
        .chat-header .chat-patient-email {
            font-size: 13px;
            color: #6c757d;
        }
        .chat-messages {
            flex: 1;
            padding: 20px 24px;
            overflow-y: auto;
            background: #f8f9fa;
        }
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .chat-messages::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .bubble-admin {
            background-color: #212529 !important;
            color: #0dcaf0 !important;
        }
        .bubble-patient {
            background-color: white !important;
            color: #212529 !important;
            border: 1px solid #dee2e6 !important;
        }
        .chat-input-area {
            padding: 16px 24px;
            border-top: 1px solid #e9ecef;
            background: white;
            flex-shrink: 0;
        }
        .send-btn-custom {
            background-color: #6c757d;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
            height: 52px;
            border-radius: 8px;
        }
        .send-btn-custom.ready-to-send {
            background-color: #0dcaf0;
            color: #212529;
        }
        .send-btn-custom:hover {
            background-color: #0dcaf0 !important;
            color: #212529;
        }
        .form-control {
            font-size: 16px !important;
            height: 52px !important;
            border-radius: 8px !important;
        }
        .btn-modal-yes {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
        }
        .btn-modal-yes:hover {
            background-color: #bd2130 !important;
            border-color: #bd2130 !important;
        }
        .btn-modal-no {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
        }
        .btn-modal-no:hover {
            background-color: #5a6268 !important;
            border-color: #5a6268 !important;
        }
        .modal-content {
            border-radius: 20px !important;
            background-color: #ffffff !important;
        }
        .modal-header {
            border-bottom: 1px solid #e9ecef !important;
            background-color: #ffffff !important;
            padding: 20px 24px !important;
        }
        .modal-header h5 {
            font-size: 24px !important;
        }
        .modal-footer {
            border-top: 1px solid #e9ecef !important;
            background-color: #ffffff !important;
            padding: 20px 24px !important;
        }
        .modal-body {
            padding: 24px !important;
            background-color: #ffffff !important;
        }
        .modal-body p {
            font-size: 18px !important;
        }
        .modal-dialog-centered {
            display: flex !important;
            align-items: center !important;
            min-height: calc(100% - 1rem) !important;
        }
        .text-danger-modal {
            color: #dc3545 !important;
            font-weight: 600 !important;
        }
        @media (min-width: 576px) {
            .modal-dialog-centered {
                min-height: calc(100% - 3.5rem) !important;
            }
        }
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #1a1d20;
                border-radius: 8px;
                padding: 15px;
                margin-top: 15px;
            }
            .nav-link {
                border-bottom: none !important;
                padding: 10px 0 !important;
                width: 100%;
                text-align: center;
            }
            .nav-link.active {
                background-color: rgba(13, 202, 240, 0.15);
                border-radius: 5px;
                padding-left: 10px !important;
            }
            .chat-wrapper {
                flex-direction: column;
                max-width: 100%;
                border-radius: 12px;
            }
            .patient-list {
                width: 100%;
                min-width: unset;
                max-height: 250px;
                border-right: none;
                border-bottom: 1px solid #e9ecef;
            }
            .chat-area {
                max-height: 500px;
            }
            .chat-container {
                padding-top: 110px;
            }
        }
        .no-patient-selected {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
            font-size: 16px;
        }
        .clear-btn-custom {
            background-color: #dc3545;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
            height: 42px;
            border-radius: 8px;
            padding: 0 20px;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
        }
        .clear-btn-custom:hover {
            background-color: #bd2130 !important;
            color: white;
            text-decoration: none;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container-fluid px-0">
            <div class="logo">
                <a class="navbar-brand fw-bold mb-0" href="admin_dashboard.php">DentFlow Admin</a>
            </div>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileMenuToggle">
                <div class="navbar-nav ms-auto fw-medium align-items-lg-center text-center gap-lg-4 mt-2 mt-lg-0">
                    <a class="nav-link py-2 py-lg-0" href="admin_tracking.php">Patient Lists</a>
                    <a class="nav-link active py-2 py-lg-0" href="admin_chat.php">Quick Chat</a>
                    <a class="nav-link py-2 py-lg-0" href="admin_dashboard.php">Dashboard</a>
                    <a class="nav-link text-danger-custom fw-bold py-2 py-lg-0 ms-lg-3" href="app_process.php?action=logout">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="chat-container px-3">
        <div class="chat-wrapper">
            <!-- Patient List -->
            <div class="patient-list">
                <div class="patient-list-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-users me-2"></i>Patients</h5>
                    <span class="badge bg-secondary"><?php echo count($patients); ?></span>
                </div>
                <div class="patient-list-items">
                    <?php if (empty($patients)): ?>
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <p class="mb-0">No patients found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($patients as $patient): ?>
                            <div class="patient-item <?php echo $selected_patient === $patient['email'] ? 'active' : ''; ?>" 
                                 onclick="window.location.href='?patient=<?php echo urlencode($patient['email']); ?>'">
                                <div class="patient-avatar">
                                    <?php echo strtoupper(substr($patient['name'], 0, 1)); ?>
                                </div>
                                <div class="patient-info">
                                    <div class="patient-name"><?php echo htmlspecialchars($patient['name']); ?></div>
                                    <div class="patient-last-msg"><?php echo htmlspecialchars($patient['last_message']); ?></div>
                                </div>
                                <div class="patient-time">
                                    <?php 
                                    if (!empty($patient['timestamp'])) {
                                        echo date('h:i A', strtotime($patient['timestamp']));
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <?php if (empty($selected_patient) || empty($patients)): ?>
                    <div class="chat-header">
                        <div>
                            <div class="chat-patient-name">Select a patient</div>
                            <div class="chat-patient-email">Choose a patient from the left to start chatting</div>
                        </div>
                    </div>
                    <div class="chat-messages">
                        <div class="no-patient-selected">
                            <div class="text-center">
                                <i class="fas fa-comment-dots" style="font-size: 48px; color: #dee2e6; display: block; margin-bottom: 16px;"></i>
                                <p class="mb-0">Select a patient to start chatting</p>
                            </div>
                        </div>
                    </div>
                    <div class="chat-input-area">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control" placeholder="Select a patient first..." disabled>
                            <button class="btn send-btn-custom fw-bold px-4" disabled>Send</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="chat-header">
                        <div>
                            <div class="chat-patient-name"><?php echo htmlspecialchars($current_patient_name); ?></div>
                            <div class="chat-patient-email"><?php echo htmlspecialchars($selected_patient); ?></div>
                        </div>
                        <button class="clear-btn-custom" data-bs-toggle="modal" data-bs-target="#clearChatModal">Clear Chat</button>
                    </div>

                    <div class="chat-messages" id="chatMessageWindow">
                        <?php if (empty($filtered_chats)): ?>
                            <div class="text-center text-muted py-5 small">No messages yet. Start the conversation!</div>
                        <?php else: ?>
                            <?php foreach ($filtered_chats as $msg): ?>
                                <?php $isAdmin = (isset($msg['sender']) && $msg['sender'] === 'admin'); ?>
                                <div class="mb-3 d-flex flex-column <?php echo $isAdmin ? 'align-items-end' : 'align-items-start'; ?>">
                                    <span class="text-muted fw-bold mb-1" style="font-size: 11px;">
                                        <?php echo $isAdmin ? 'You' : htmlspecialchars($msg['sender_name']); ?>
                                    </span>
                                    <div class="p-2 rounded-3 shadow-sm <?php echo $isAdmin ? 'bubble-admin' : 'bubble-patient'; ?>" style="max-width: 80%; font-size: 13px;">
                                        <?php echo htmlspecialchars($msg['message_text']); ?>
                                    </div>
                                    <span class="text-muted mt-1" style="font-size: 9px;"><?php echo date('h:i A', strtotime($msg['timestamp'] ?? '')); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="chat-input-area">
                        <form method="POST" action="app_process.php" id="chatForm">
                            <input type="hidden" name="action" value="admin_send_message">
                            <input type="hidden" name="recipient_email" value="<?php echo htmlspecialchars($selected_patient); ?>">
                            <div class="d-flex gap-2 align-items-center">
                                <input type="text" id="message_input" name="message_text" class="form-control shadow-sm border" placeholder="Type your message..." autocomplete="off" required>
                                <button class="btn send-btn-custom fw-bold px-4 shadow-sm flex-shrink-0" id="send_button" type="submit">Send</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Clear Chat Confirmation Modal -->
    <div class="modal fade" id="clearChatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Chat</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear chat with <strong><?php echo htmlspecialchars($current_patient_name); ?></strong>?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a href="app_process.php?action=clear_chat&patient=<?php echo urlencode($selected_patient); ?>" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('load', () => {
            const windowObj = document.getElementById('chatMessageWindow');
            if (windowObj) {
                windowObj.scrollTop = windowObj.scrollHeight;
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const messageInput = document.getElementById('message_input');
            const sendButton = document.getElementById('send_button');
            const chatForm = document.getElementById('chatForm');

            if (messageInput && sendButton) {
                function updateSendButtonState() {
                    if (messageInput.value.trim() !== '') {
                        sendButton.classList.add('ready-to-send');
                    } else {
                        sendButton.classList.remove('ready-to-send');
                    }
                }

                messageInput.addEventListener('input', updateSendButtonState);

                if (chatForm) {
                    chatForm.addEventListener('submit', function(e) {
                        if (messageInput.value.trim() === '') {
                            e.preventDefault();
                            return false;
                        }
                    });
                }

                updateSendButtonState();
            }
        });
    </script>
</body>
</html>