<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if patient is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'patient') {
    $_SESSION['redirect_after_login'] = 'patient_booking_history.php';
    header("Location: patient_auth.php");
    exit;
}

// Function to get full name
function getFullName($row) {
    $name_parts = [];
    if (!empty($row['first_name'])) $name_parts[] = $row['first_name'];
    if (!empty($row['middle_name'])) $name_parts[] = $row['middle_name'];
    if (!empty($row['last_name'])) $name_parts[] = $row['last_name'];
    if (!empty($row['suffix'])) $name_parts[] = $row['suffix'];
    return !empty($name_parts) ? implode(' ', $name_parts) : ($row['name'] ?? 'N/A');
}

// Get current user email
$user_email = $_SESSION['user_email'] ?? '';

// Get filter from GET
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow - Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            padding-top: 120px;
        }
        nav {
            background-color: #035270 !important;
            padding: 15px 20px !important;
            transition: padding 0.3s ease;
        }
        .navbar-brand {
            color: white;
            text-decoration: none;
            text-transform: uppercase;
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
            .nav-link:hover {
                border-bottom: 4px solid white;
                color: white !important;
            }
            .text-warning:hover {
                border-bottom: 4px solid #ffc107;
                color: #ffc107 !important;
            }
        }
        .nav-link.active {
            opacity: 1 !important;
            font-weight: 700;
            border-bottom: 4px solid white !important;
        }
        .text-warning {
            color: #ffc107;
        }
        .text-primary {
            color: #035270 !important;
        }
        .btn-primary {
            background-color: #035270 !important;
            border-color: #035270 !important;
        }
        .btn-primary:hover {
            background-color: #023d54 !important;
            border-color: #023d54 !important;
        }
        .history-card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
        }
        .history-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
        }
        .history-card .history-name {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
        }
        .history-card .history-details {
            font-size: 14px;
            color: #6c757d;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 140px;
            display: inline-block;
        }
        .detail-value {
            color: #212529;
        }
        .detail-row {
            padding: 6px 0;
            border-bottom: 1px solid #f1f3f5;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .btn-modal-yes {
            background-color: #dc3545 !important;
            color: white !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
            min-width: 120px;
            border-radius: 10px;
            font-weight: 700;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-modal-yes:hover {
            background-color: #bd2130 !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-modal-no {
            background-color: #6c757d !important;
            color: white !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
            min-width: 120px;
            border-radius: 10px;
            font-weight: 700;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-modal-no:hover {
            background-color: #5a6268 !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-modal-dark {
            background-color: #035270 !important;
            color: white !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
            min-width: 120px;
            border-radius: 10px;
            font-weight: 700;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-modal-dark:hover {
            background-color: #023d54 !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
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
        .card-pending {
            border-left: 4px solid #ffc107 !important;
        }
        .card-active {
            border-left: 4px solid #198754 !important;
        }
        .card-history {
            border-left: 4px solid #6c757d !important;
            opacity: 0.85;
        }
        .card-urgent {
            border-left: 4px solid #dc3545 !important;
        }
        .btn-close-custom {
            background-color: #e9ecef !important;
            color: #212529 !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
            min-width: 120px;
            border-radius: 10px;
            font-weight: 700;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .btn-close-custom:hover {
            background-color: #dee2e6 !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #02435c;
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
                background-color: rgba(255, 255, 255, 0.15);
                border-radius: 5px;
                padding-left: 10px !important;
            }
        }

        /* Header with filter */
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #035270;
            padding-bottom: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .history-header h2 {
            margin: 0;
            font-size: 28px;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-group label {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }
        .filter-group select {
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            font-size: 14px;
            background-color: white;
            cursor: pointer;
            min-width: 140px;
            height: 38px;
        }
        .filter-group select:focus {
            border-color: #035270;
            box-shadow: 0 0 0 0.2rem rgba(3, 82, 112, 0.25);
        }
        .filter-group .btn-clear-filter {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            text-decoration: none;
            height: 38px;
            display: inline-flex;
            align-items: center;
        }
        .filter-group .btn-clear-filter:hover {
            background-color: #5a6268;
            color: white;
            text-decoration: none;
        }
        .badge-urgent {
            background-color: #dc3545 !important;
            color: white !important;
        }
        .badge-pending {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .badge-operational {
            background-color: #198754 !important;
            color: white !important;
        }
        .badge-completed {
            background-color: #6c757d !important;
            color: white !important;
        }
        .badge-cancelled {
            background-color: #dc3545 !important;
            color: white !important;
        }
        .badge-declined {
            background-color: #dc3545 !important;
            color: white !important;
        }
        .history-card .badge {
            font-size: 12px !important;
            padding: 4px 12px !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container-fluid px-0">
            <div class="logo">
                <img src="images/tooth.webp" alt="DentFlow" style="width: 45px; height: 45px; object-fit: contain;">
                <a class="navbar-brand fw-bold mb-0" href="patient_home.php">DentFlow</a>
            </div>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileMenuToggle">
                <div class="navbar-nav ms-auto fw-medium align-items-lg-center text-center gap-lg-4 mt-2 mt-lg-0">
                    <a class="nav-link py-2 py-lg-0" href="patient_home.php">Home</a>
                    <a class="nav-link py-2 py-lg-0" href="patient_appointment.php">Appointment Form</a>
                    <a class="nav-link py-2 py-lg-0" href="patient_chat.php">Quick Chat</a>
                    <a class="nav-link active py-2 py-lg-0" href="patient_booking_history.php">Booking History</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'patient'): ?>
                        <button type="button" class="nav-link text-warning fw-bold py-2 py-lg-0 ms-lg-3" data-bs-toggle="modal" data-bs-target="#signOutModal">Logout</button>
                    <?php else: ?>
                        <a class="nav-link text-warning py-2 py-lg-0 ms-lg-3" href="patient_auth.php">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4" style="max-width: 900px;">
        <div class="card border-0 shadow-sm bg-white rounded-4">
            <div class="card-body p-4">
                
                <!-- Header with Filter -->
                <div class="history-header">
                    <h2 class="text-primary fw-bold">My Booking History</h2>
                    <div class="filter-group">
                        <label for="statusFilter">Filter:</label>
                        <select id="statusFilter" onchange="window.location.href='?status='+this.value">
                            <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Operational" <?php echo $status_filter === 'Operational' ? 'selected' : ''; ?>>Operational</option>
                            <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                        <?php if ($status_filter !== 'all'): ?>
                            <a href="?status=all" class="btn-clear-filter">Clear</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php 
                $user_email = $_SESSION['user_email'] ?? '';
                if (empty($user_email)):
                ?>
                    <div class="text-center py-5">
                        <p class="text-muted">Please sign in to view your booking history.</p>
                        <a href="patient_auth.php" class="btn btn-primary mt-2">Sign In</a>
                    </div>
                <?php 
                else:
                    $has_bookings = false;
                    foreach (array_reverse($_SESSION['booking_history']) as $booking):
                        if ($booking['email'] == $user_email):
                            // Apply status filter
                            if ($status_filter !== 'all' && $booking['status'] !== $status_filter) {
                                continue;
                            }
                            $has_bookings = true;
                            $status_class = '';
                            $badge_class = '';
                            
                            // Determine card border color
                            if ($booking['status'] === 'Pending') {
                                $status_class = 'card-pending';
                                $badge_class = 'badge-pending';
                            } elseif ($booking['status'] === 'Operational') {
                                $status_class = 'card-active';
                                $badge_class = 'badge-operational';
                            } elseif ($booking['status'] === 'Completed') {
                                $status_class = 'card-history';
                                $badge_class = 'badge-completed';
                            } elseif ($booking['status'] === 'Cancelled' || $booking['status'] === 'Declined') {
                                $status_class = 'card-urgent';
                                $badge_class = 'badge-cancelled';
                            }
                            
                            // Urgent badge
                            $is_urgent = isset($booking['urgency']) && $booking['urgency'] === 'Urgent';
                            if ($is_urgent) {
                                $status_class .= ' card-urgent';
                            }
                            
                            $full_name = getFullName($booking);
                ?>
                        <div class="history-card card border shadow-sm mb-3 p-3 <?php echo $status_class; ?>" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $booking['id']; ?>">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="history-name">
                                        <?php echo htmlspecialchars($full_name); ?>
                                        <?php if ($is_urgent): ?>
                                            <span class="badge badge-urgent ms-1">Urgent</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="history-details">
                                        <?php echo htmlspecialchars($booking['service'] ?? 'N/A'); ?> • 
                                        <?php echo htmlspecialchars($booking['appt_date'] . ' @ ' . $booking['appt_time']); ?>
                                        <span class="badge <?php echo $badge_class; ?> ms-2"><?php echo htmlspecialchars($booking['status']); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <?php if ($booking['status'] !== 'Declined' && $booking['status'] !== 'Operational' && $booking['status'] !== 'Completed' && $booking['status'] !== 'Cancelled'): ?>
                                        <button class="btn btn-sm btn-primary me-1" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $booking['id']; ?>">Edit</button>
                                    <?php endif; ?>
                                    <?php if ($booking['status'] !== 'Completed' && $booking['status'] !== 'Declined' && $booking['status'] !== 'Cancelled'): ?>
                                        <button class="btn btn-sm btn-danger" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $booking['id']; ?>">Delete</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Modal -->
                        <div class="modal fade" id="detailModal<?php echo $booking['id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 pt-4 px-4 pb-2">
                                        <h5 class="modal-title fw-bold text-dark">Booking Details</h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body px-4 py-3">
                                        <div class="detail-row"><span class="detail-label">Name:</span> <span class="detail-value"><?php echo htmlspecialchars($full_name); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">First Name:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['first_name'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Middle Name:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['middle_name'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Last Name:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['last_name'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Suffix:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['suffix'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['email'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Contact:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['contact'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Service:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['service'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Price:</span> <span class="detail-value">₱<?php echo number_format($booking['service_price'] ?? 0, 2); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Date:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['appt_date'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Time:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['appt_time'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Age Group:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['age_group'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Payment:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['payment_method'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Urgency:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['urgency'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Medical History:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['medical_history'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Message:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['message'] ?? 'N/A'); ?></span></div>
                                        <div class="detail-row"><span class="detail-label">Status:</span> <span class="detail-value">
                                            <span class="badge <?php 
                                                echo ($booking['status'] === 'Operational') ? 'badge-operational' : 
                                                     (($booking['status'] === 'Pending') ? 'badge-pending' : 
                                                     (($booking['status'] === 'Declined') ? 'badge-declined' : 
                                                     (($booking['status'] === 'Cancelled') ? 'badge-cancelled' : 'badge-completed')));
                                            ?>"><?php echo htmlspecialchars($booking['status'] ?? 'N/A'); ?></span>
                                        </span></div>
                                        <div class="detail-row"><span class="detail-label">Booked At:</span> <span class="detail-value"><?php echo htmlspecialchars($booking['booked_at'] ?? 'N/A'); ?></span></div>
                                    </div>
                                    <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex justify-content-center">
                                        <button type="button" class="btn-close-custom" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $booking['id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title fw-bold text-dark">Edit Booking</h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="app_process.php">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="update_booking">
                                            <input type="hidden" name="id" value="<?php echo $booking['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary small">Contact Number</label>
                                                <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($booking['contact']); ?>" pattern="^09\d{9}$" maxlength="11" required>
                                            </div>
                                            
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-secondary small">Appointment Date</label>
                                                    <input type="date" name="appt_date" class="form-control" value="<?php echo htmlspecialchars($booking['appt_date']); ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-secondary small">Appointment Time</label>
                                                    <input type="time" name="appt_time" class="form-control" value="<?php echo htmlspecialchars($booking['appt_time']); ?>" required>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-secondary small">Service</label>
                                                    <select name="service" class="form-select" required>
                                                        <?php foreach ($_SESSION['dental_services'] as $service): ?>
                                                            <option value="<?php echo htmlspecialchars($service['name']); ?>" data-price="<?php echo $service['price']; ?>" <?php echo ($service['name'] == $booking['service']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($service['name']); ?> - ₱<?php echo number_format($service['price'], 2); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-secondary small">Payment Method</label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="Cash" <?php echo ($booking['payment_method'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                                                        <option value="Gcash" <?php echo ($booking['payment_method'] == 'Gcash') ? 'selected' : ''; ?>>Gcash</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-secondary small">Age Option Category</label>
                                                    <select name="age_group" class="form-select" required>
                                                        <option value="Adult" <?php echo ($booking['age_group'] == 'Adult') ? 'selected' : ''; ?>>Adult</option>
                                                        <option value="Child" <?php echo ($booking['age_group'] == 'Child') ? 'selected' : ''; ?>>Child</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold text-danger small">Urgency Status</label>
                                                    <select name="urgency_status" class="form-select" required>
                                                        <option value="Not Urgent" <?php echo ($booking['urgency'] == 'Not Urgent') ? 'selected' : ''; ?>>Not Urgent</option>
                                                        <option value="Urgent" <?php echo ($booking['urgency'] == 'Urgent') ? 'selected' : ''; ?>>Urgent</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary small">Medical History</label>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="history[]" value="Hypertension" <?php echo (strpos($booking['medical_history'], 'Hypertension') !== false) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small">Hypertension</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="history[]" value="Diabetes" <?php echo (strpos($booking['medical_history'], 'Diabetes') !== false) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small">Diabetes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="history[]" value="Drug Allergies" <?php echo (strpos($booking['medical_history'], 'Drug Allergies') !== false) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small">Drug Allergies</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="history[]" value="Bleeding Disorders" <?php echo (strpos($booking['medical_history'], 'Bleeding Disorders') !== false) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small">Bleeding Disorders</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary small">Message / Symptoms</label>
                                                <textarea name="message" class="form-control" rows="2"><?php echo htmlspecialchars($booking['message']); ?></textarea>
                                            </div>
                                            
                                            <input type="hidden" name="service_price" value="<?php echo $booking['service_price']; ?>">
                                        </div>
                                        <div class="modal-footer border-0 d-flex gap-3 justify-content-center">
                                            <button type="submit" class="btn-modal-dark">Yes</button>
                                            <button type="button" class="btn-modal-no" data-bs-dismiss="modal">No</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal<?php echo $booking['id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <div class="modal-header border-0 pt-4 px-4 pb-2">
                                        <h5 class="modal-title fw-bold text-dark">Delete Booking</h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body px-4 py-3 text-center">
                                        <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to delete this booking?</p>
                                        <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                                        <a href="app_process.php?action=delete_booking&id=<?php echo $booking['id']; ?>" class="btn-modal-yes" style="text-decoration: none;">Yes</a>
                                        <button type="button" class="btn-modal-no" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    if (!$has_bookings):
                    ?>
                        <div class="text-center py-5">
                            <p class="text-muted">No bookings found for your account.</p>
                            <a href="patient_appointment.php" class="btn btn-primary mt-2">Book Appointment</a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sign Out Modal -->
    <div class="modal fade" id="signOutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Sign Out</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to sign out?</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a href="app_process.php?action=logout" class="btn-modal-yes" style="text-decoration: none;">Yes</a>
                    <button type="button" class="btn-modal-no" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>