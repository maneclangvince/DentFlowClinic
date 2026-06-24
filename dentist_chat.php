<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if dentist is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'dentist') {
    header("Location: dentist_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow Admin - Quick Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            .text-warning-custom:hover {
                border-bottom: 4px solid #ffc107;
                color: #ffc107 !important;
            }
        }
        .text-danger-custom {
            color: #dc3545;
        }
        .text-warning-custom {
            color: #ffc107;
        }
        .nav-link.active {
            opacity: 1 !important;
            font-weight: 700;
            border-bottom: 4px solid #0dcaf0 !important;
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
        .clear-btn-custom {
            background-color: #dc3545;
            color: white;
            border: none;
            transition: background-color 0.3s ease;
            height: 52px;
            border-radius: 8px;
            padding: 0 30px;
            font-weight: 700;
            font-size: 16px;
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
        .form-control, .form-select {
            font-size: 16px !important;
            height: 52px !important;
            border-radius: 8px !important;
        }
        .bubble-dentist {
            background-color: #212529 !important;
            color: #0dcaf0 !important;
        }
        .bubble-patient {
            background-color: #e9ecef !important;
            color: #212529 !important;
        }
        .chat-screen-wrapper {
            padding-top: 115px;
            padding-bottom: 40px;
            box-sizing: border-box;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chat-container-box {
            width: 100%;
            max-width: 900px;
        }
        .card.custom-chat-card {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
        }
        .to-label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            white-space: nowrap;
        }
        .error-text {
            color: #dc3545;
            font-size: 14px;
            font-weight: 600;
            margin-top: 4px;
            display: none;
        }
        .error-text.show {
            display: block;
        }
        .form-control.is-invalid-custom {
            border-color: #dc3545 !important;
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
            .chat-screen-wrapper {
                padding-top: 110px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container-fluid px-0">
            <div class="logo">
                <a class="navbar-brand fw-bold mb-0" href="dentist_tracking.php">DentFlow Admin</a>
            </div>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileMenuToggle">
                <div class="navbar-nav ms-auto fw-medium align-items-lg-center text-center gap-lg-4 mt-2 mt-lg-0">
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'dentist'): ?>
                        <a class="nav-link py-2 py-lg-0" href="dentist_tracking.php">Patient Lists</a>
                        <a class="nav-link active py-2 py-lg-0" href="dentist_chat.php">Quick Chat</a>
                        <a class="nav-link text-danger-custom fw-bold py-2 py-lg-0 ms-lg-3" href="app_process.php?action=logout">Logout</a>
                    <?php else: ?>
                        <a class="nav-link py-2 py-lg-0" href="dentist_tracking.php">Patient Lists</a>
                        <a class="nav-link active py-2 py-lg-0" href="dentist_chat.php">Quick Chat</a>
                        <a class="nav-link text-warning-custom fw-bold py-2 py-lg-0" href="dentist_login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="chat-screen-wrapper px-3">
        <div class="chat-container-box">
            <div class="card border-0 custom-chat-card rounded-4 overflow-hidden">
                <div class="card-body p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <h2 class="fw-bold mb-0 text-dark">Quick Chat</h2>
                        <button class="clear-btn-custom" data-bs-toggle="modal" data-bs-target="#clearChatModal">Clear Chat</button>
                    </div>
                    
                    <div class="border rounded-3 p-4 bg-light mb-3 overflow-auto" style="height: 450px;" id="chatMessageWindow">
                        <?php if (empty($_SESSION['chats'])): ?>
                            <div class="text-center text-muted py-5 small">No chat history recorded.</div>
                        <?php else: ?>
                            <?php foreach ($_SESSION['chats'] as $msg): ?>
                                <?php $isDentist = (isset($msg['sender']) && $msg['sender'] === 'dentist'); ?>
                                <div class="mb-3 d-flex flex-column <?php echo $isDentist ? 'align-items-end' : 'align-items-start'; ?>">
                                    <span class="text-muted fw-bold mb-1" style="font-size: 11px;">
                                        <?php echo htmlspecialchars($msg['sender_name']); ?>
                                        <?php if (isset($msg['recipient'])): ?>
                                            <span class="text-muted fw-normal">→ <?php echo htmlspecialchars($msg['recipient']); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <div class="p-2 rounded-3 shadow-sm <?php echo $isDentist ? 'bubble-dentist' : 'bubble-patient'; ?>" style="max-width: 80%; font-size: 13px;">
                                        <?php echo htmlspecialchars($msg['message_text'] ?? $msg['text']); ?>
                                    </div>
                                    <span class="text-muted mt-1" style="font-size: 9px;"><?php echo htmlspecialchars($msg['timestamp'] ?? ''); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="app_process.php" id="chatForm">
                        <input type="hidden" name="action" value="dentist_send_message">
                        <div class="d-flex gap-2 align-items-center mb-2">
                            <span class="to-label">To:</span>
                            <input type="text" id="recipient_input" name="recipient_email" class="form-control" placeholder="Enter patient email" autocomplete="off" required style="flex: 1;">
                            <div class="error-text" id="userError">User doesn't exist.</div>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" id="message_input" name="message_text" class="form-control shadow-sm border" placeholder="Type your message..." autocomplete="off" required>
                            <button class="btn send-btn-custom fw-bold px-4 shadow-sm flex-shrink-0" id="send_button" type="submit">Send</button>
                        </div>
                    </form>
                </div>
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
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear all chat history?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a href="app_process.php?action=clear_chat" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
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
            const recipientInput = document.getElementById('recipient_input');
            const sendButton = document.getElementById('send_button');
            const chatForm = document.getElementById('chatForm');
            const userError = document.getElementById('userError');

            <?php 
            $valid_emails = [];
            foreach ($_SESSION['patient_records'] as $patient) {
                if (!empty($patient['email'])) {
                    $valid_emails[] = $patient['email'];
                }
            }
            foreach ($_SESSION['booking_history'] as $booking) {
                if (!empty($booking['email']) && !in_array($booking['email'], $valid_emails)) {
                    $valid_emails[] = $booking['email'];
                }
            }
            $valid_emails[] = 'dentist@dentflow.com';
            ?>

            const validEmails = <?php echo json_encode($valid_emails); ?>;

            function updateSendButtonState() {
                if (messageInput.value.trim() !== '' && recipientInput.value.trim() !== '') {
                    sendButton.classList.add('ready-to-send');
                } else {
                    sendButton.classList.remove('ready-to-send');
                }
            }

            function validateRecipient() {
                const email = recipientInput.value.trim().toLowerCase();
                if (email === '') {
                    recipientInput.classList.remove('is-invalid-custom');
                    userError.classList.remove('show');
                    return true;
                }
                if (validEmails.includes(email)) {
                    recipientInput.classList.remove('is-invalid-custom');
                    userError.classList.remove('show');
                    return true;
                } else {
                    recipientInput.classList.add('is-invalid-custom');
                    userError.classList.add('show');
                    return false;
                }
            }

            recipientInput.addEventListener('input', function() {
                validateRecipient();
                updateSendButtonState();
            });

            messageInput.addEventListener('input', updateSendButtonState);

            chatForm.addEventListener('submit', function(e) {
                if (!validateRecipient()) {
                    e.preventDefault();
                    return false;
                }
                if (messageInput.value.trim() === '') {
                    e.preventDefault();
                    return false;
                }
            });

            updateSendButtonState();
        });
    </script>
</body>
</html>