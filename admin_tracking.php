<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['redirect_after_login'] = 'admin_tracking.php';
    header("Location: admin_login.php");
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

// Get date filter
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get filter type
$filter_type = isset($_GET['filter']) ? $_GET['filter'] : 'today';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow Admin - Patient Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            padding-top: 120px;
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
        }
        .stats-card {
            border-radius: 16px !important;
            transition: transform 0.2s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .stats-number {
            font-size: 32px !important;
            font-weight: 700 !important;
            color: #000000 !important;
        }
        .stats-label {
            font-size: 20px !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px;
            text-transform: none !important;
            color: #000000 !important;
        }
        .patient-card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }
        .patient-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
        }
        .patient-card .patient-name {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
        }
        .patient-card .patient-details {
            font-size: 14px;
            color: #6c757d;
        }
        .btn-accept {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: white !important;
        }
        .btn-accept:hover {
            background-color: #146c43 !important;
            border-color: #146c43 !important;
        }
        .btn-decline {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        .btn-decline:hover {
            background-color: #bd2130 !important;
            border-color: #bd2130 !important;
        }
        .btn-done {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: white !important;
        }
        .btn-done:hover {
            background-color: #146c43 !important;
            border-color: #146c43 !important;
        }
        .btn-cancel {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        .btn-cancel:hover {
            background-color: #bd2130 !important;
            border-color: #bd2130 !important;
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
        .patient-card .btn {
            font-size: 14px;
            padding: 6px 18px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
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
        .header-pending {
            color: #ffc107 !important;
        }
        .header-active {
            color: #198754 !important;
        }
        .header-history {
            color: #6c757d !important;
        }
        .border-pending {
            border-top: 4px solid #ffc107 !important;
        }
        .border-active {
            border-top: 4px solid #198754 !important;
        }
        .border-history {
            border-top: 4px solid #6c757d !important;
        }
        .action-buttons {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .action-buttons .btn {
            white-space: nowrap;
        }

        /* Filter Styles - Right Aligned */
        .filter-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .filter-box {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #e9ecef;
        }
        .filter-box .quick-filters {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .filter-box .quick-filters .btn-quick {
            background-color: #e9ecef;
            color: #495057;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            height: 36px;
            display: inline-flex;
            align-items: center;
        }
        .filter-box .quick-filters .btn-quick:hover {
            background-color: #dee2e6;
        }
        .filter-box .quick-filters .btn-quick.active {
            background-color: #212529;
            color: white;
        }
        .filter-box .quick-filters .btn-quick.active:hover {
            background-color: #1a1d20;
        }
        .filter-divider {
            color: #dee2e6;
            font-size: 18px;
            padding: 0 4px;
        }
        .filter-box .date-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-box .date-input-group label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }
        .filter-box .form-control {
            padding: 6px 12px !important;
            border-radius: 8px !important;
            border: 1px solid #ced4da !important;
            height: 36px !important;
            font-size: 14px !important;
            background-color: #ffffff !important;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            width: 150px;
        }
        .filter-box .form-control:focus {
            border-color: #212529 !important;
            box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.15) !important;
        }
        .filter-box .btn-clear-filter {
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
            height: 36px;
            display: inline-flex;
            align-items: center;
        }
        .filter-box .btn-clear-filter:hover {
            background-color: #5a6268;
            color: white;
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .filter-container {
                justify-content: center;
            }
            .filter-box {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
                padding: 14px 16px;
            }
            .filter-box .quick-filters {
                justify-content: center;
            }
            .filter-box .date-input-group {
                justify-content: center;
                flex-wrap: wrap;
            }
            .filter-box .form-control {
                width: 100%;
            }
            .filter-divider {
                display: none;
            }
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
                    <a class="nav-link active py-2 py-lg-0" href="admin_tracking.php">Patient Lists</a>
                    <a class="nav-link py-2 py-lg-0" href="admin_chat.php">Quick Chat</a>
                    <a class="nav-link py-2 py-lg-0" href="admin_dashboard.php">Dashboard</a>
                    <a class="nav-link text-danger-custom fw-bold py-2 py-lg-0 ms-lg-3" href="app_process.php?action=logout">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5" style="max-width: 1100px;">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm text-center h-100 rounded-4 stats-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <span class="stats-label fw-bold mb-2">Total Patients Served Per Day</span>
                        <?php 
                        date_default_timezone_set('Asia/Manila');
                        $today_counter = 0;
                        $today_date = date('Y-m-d');
                        foreach ($_SESSION['appointments'] as $item) {
                            if (isset($item['status']) && $item['status'] === 'Completed') {
                                $completed_date = isset($item['completed_date']) ? $item['completed_date'] : '';
                                if ($completed_date === $today_date) {
                                    $today_counter++;
                                }
                            }
                        }
                        ?>
                        <p class="stats-number mb-0"><?php echo $today_counter; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm text-center h-100 rounded-4 stats-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <span class="stats-label fw-bold mb-2">Total Patients Served All Time</span>
                        <?php 
                        $alltime_counter = 0; 
                        foreach ($_SESSION['appointments'] as $item) {
                            if (isset($item['status']) && $item['status'] === 'Completed') {
                                $alltime_counter++;
                            }
                        }
                        ?>
                        <p class="stats-number mb-0"><?php echo $alltime_counter; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter - Right Aligned -->
        <div class="filter-container">
            <div class="filter-box">
                <div class="quick-filters">
                    <a href="?filter=today" class="btn-quick <?php echo $filter_type === 'today' ? 'active' : ''; ?>">Today</a>
                    <a href="?filter=tomorrow" class="btn-quick <?php echo $filter_type === 'tomorrow' ? 'active' : ''; ?>">Tomorrow</a>
                    <a href="?filter=week" class="btn-quick <?php echo $filter_type === 'week' ? 'active' : ''; ?>">This Week</a>
                    <a href="?filter=month" class="btn-quick <?php echo $filter_type === 'month' ? 'active' : ''; ?>">This Month</a>
                    <a href="?filter=all" class="btn-quick <?php echo $filter_type === 'all' ? 'active' : ''; ?>">All</a>
                </div>
                <span class="filter-divider">|</span>
                <div class="date-input-group">
                    <label for="dateFilter">Select date:</label>
                    <input type="date" id="dateFilter" class="form-control" value="<?php echo $date_filter; ?>" onchange="window.location.href='?date='+this.value">
                    <?php if ($filter_type !== 'today' && $filter_type !== 'all'): ?>
                        <a href="?filter=today" class="btn-clear-filter">Clear</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pending Appointments -->
        <div class="card border-0 shadow-sm bg-white rounded-4 mb-4 border-pending">
            <div class="card-body p-4">
                <h3 class="section-title header-pending">Pending Appointments</h3>
                <?php 
                $pending_empty = true;
                foreach ($_SESSION['appointments'] as $idx => $row): 
                    // Apply date filter
                    if ($filter_type !== 'all') {
                        $appt_date = $row['appt_date'] ?? '';
                        if ($filter_type === 'today' && $appt_date !== date('Y-m-d')) continue;
                        if ($filter_type === 'tomorrow' && $appt_date !== date('Y-m-d', strtotime('+1 day'))) continue;
                        if ($filter_type === 'week') {
                            $week_start = date('Y-m-d', strtotime('monday this week'));
                            $week_end = date('Y-m-d', strtotime('sunday this week'));
                            if ($appt_date < $week_start || $appt_date > $week_end) continue;
                        }
                        if ($filter_type === 'month') {
                            $month_start = date('Y-m-01');
                            $month_end = date('Y-m-t');
                            if ($appt_date < $month_start || $appt_date > $month_end) continue;
                        }
                        if ($filter_type === 'date' && $appt_date !== $date_filter) continue;
                    }
                    if (isset($row['status']) && $row['status'] === 'Pending'):
                        $pending_empty = false;
                        $full_name = getFullName($row);
                ?>
                    <div class="patient-card card border shadow-sm mb-3 p-3 card-pending patient-card-clickable" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $idx; ?>">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="patient-name">
                                    <?php echo htmlspecialchars($full_name); ?>
                                </div>
                                <div class="patient-details">
                                    <?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?> • 
                                    <?php echo htmlspecialchars($row['appt_date'] . ' @ ' . $row['appt_time']); ?>
                                    <?php if (isset($row['urgency']) && $row['urgency'] === 'Urgent'): ?>
                                        <span class="badge bg-danger ms-2">Urgent</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="action-buttons">
                                    <button class="btn btn-accept fw-bold me-2" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#acceptConfirmModal" data-bs-href="app_process.php?action=accept_appt&id=<?php echo $row['id']; ?>">Accept</button>
                                    <button class="btn btn-decline fw-bold" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#declineConfirmModal" data-bs-href="app_process.php?action=decline_appt&id=<?php echo $row['id']; ?>">Decline</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Modal -->
                    <div class="modal fade" id="detailModal<?php echo $idx; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header border-0 pt-4 px-4 pb-2">
                                    <h5 class="modal-title fw-bold text-dark">Patient Details</h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body px-4 py-3">
                                    <div class="detail-row"><span class="detail-label">Name:</span> <span class="detail-value"><?php echo htmlspecialchars($full_name); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">First Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['first_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Middle Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['middle_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Last Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['last_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Suffix:</span> <span class="detail-value"><?php echo htmlspecialchars($row['suffix'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value"><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Contact:</span> <span class="detail-value"><?php echo htmlspecialchars($row['contact'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Service:</span> <span class="detail-value"><?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Date:</span> <span class="detail-value"><?php echo htmlspecialchars($row['appt_date'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Time:</span> <span class="detail-value"><?php echo htmlspecialchars($row['appt_time'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Age Group:</span> <span class="detail-value"><?php echo htmlspecialchars($row['age_group'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Payment:</span> <span class="detail-value"><?php echo htmlspecialchars($row['payment_method'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Urgency:</span> <span class="detail-value"><?php echo htmlspecialchars($row['urgency'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Medical History:</span> <span class="detail-value"><?php echo htmlspecialchars($row['medical_history'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Message:</span> <span class="detail-value"><?php echo htmlspecialchars($row['message'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Status:</span> <span class="detail-value"><span class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['status'] ?? 'N/A'); ?></span></span></div>
                                </div>
                                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-secondary px-4 py-2 fw-bold" style="border-radius: 10px;" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endif;
                endforeach; 
                if ($pending_empty):
                ?>
                    <div class="text-center text-muted py-4 small fw-medium">No pending appointments found for this date.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Active Queue -->
        <div class="card border-0 shadow-sm bg-white rounded-4 mb-4 border-active">
            <div class="card-body p-4">
                <h3 class="section-title header-active">Active Queue (Accepted Patients)</h3>
                <?php 
                $ops_empty = true;
                foreach ($_SESSION['appointments'] as $idx => $row): 
                    // Apply date filter
                    if ($filter_type !== 'all') {
                        $appt_date = $row['appt_date'] ?? '';
                        if ($filter_type === 'today' && $appt_date !== date('Y-m-d')) continue;
                        if ($filter_type === 'tomorrow' && $appt_date !== date('Y-m-d', strtotime('+1 day'))) continue;
                        if ($filter_type === 'week') {
                            $week_start = date('Y-m-d', strtotime('monday this week'));
                            $week_end = date('Y-m-d', strtotime('sunday this week'));
                            if ($appt_date < $week_start || $appt_date > $week_end) continue;
                        }
                        if ($filter_type === 'month') {
                            $month_start = date('Y-m-01');
                            $month_end = date('Y-m-t');
                            if ($appt_date < $month_start || $appt_date > $month_end) continue;
                        }
                        if ($filter_type === 'date' && $appt_date !== $date_filter) continue;
                    }
                    if (isset($row['status']) && $row['status'] === 'Operational'):
                        $ops_empty = false;
                        $full_name = getFullName($row);
                ?>
                    <div class="patient-card card border shadow-sm mb-3 p-3 card-active patient-card-clickable" data-bs-toggle="modal" data-bs-target="#detailModalActive<?php echo $idx; ?>">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="patient-name">
                                    <?php echo htmlspecialchars($full_name); ?>
                                </div>
                                <div class="patient-details">
                                    <?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?> • 
                                    <?php echo htmlspecialchars($row['appt_date'] . ' @ ' . $row['appt_time']); ?>
                                    <?php if (isset($row['urgency']) && $row['urgency'] === 'Urgent'): ?>
                                        <span class="badge bg-danger ms-2">Urgent</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="action-buttons">
                                    <button class="btn btn-done fw-bold me-2" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#doneConfirmModal" data-bs-href="app_process.php?action=done_appt&id=<?php echo $row['id']; ?>">Done</button>
                                    <button class="btn btn-cancel fw-bold" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#cancelConfirmModal" data-bs-href="app_process.php?action=cancel_appt&id=<?php echo $row['id']; ?>">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Modal Active -->
                    <div class="modal fade" id="detailModalActive<?php echo $idx; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header border-0 pt-4 px-4 pb-2">
                                    <h5 class="modal-title fw-bold text-dark">Patient Details</h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body px-4 py-3">
                                    <div class="detail-row"><span class="detail-label">Name:</span> <span class="detail-value"><?php echo htmlspecialchars($full_name); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">First Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['first_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Middle Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['middle_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Last Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['last_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Suffix:</span> <span class="detail-value"><?php echo htmlspecialchars($row['suffix'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value"><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Contact:</span> <span class="detail-value"><?php echo htmlspecialchars($row['contact'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Service:</span> <span class="detail-value"><?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Date:</span> <span class="detail-value"><?php echo htmlspecialchars($row['appt_date'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Time:</span> <span class="detail-value"><?php echo htmlspecialchars($row['appt_time'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Age Group:</span> <span class="detail-value"><?php echo htmlspecialchars($row['age_group'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Payment:</span> <span class="detail-value"><?php echo htmlspecialchars($row['payment_method'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Urgency:</span> <span class="detail-value"><?php echo htmlspecialchars($row['urgency'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Medical History:</span> <span class="detail-value"><?php echo htmlspecialchars($row['medical_history'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Message:</span> <span class="detail-value"><?php echo htmlspecialchars($row['message'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Status:</span> <span class="detail-value"><span class="badge bg-success"><?php echo htmlspecialchars($row['status'] ?? 'N/A'); ?></span></span></div>
                                </div>
                                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-secondary px-4 py-2 fw-bold" style="border-radius: 10px;" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endif;
                endforeach; 
                if ($ops_empty):
                ?>
                    <div class="text-center text-muted py-4 small fw-medium">No active patients in the queue for this date.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Past History Logs -->
        <div class="card border-0 shadow-sm bg-white rounded-4 border-history">
            <div class="card-body p-4">
                <h3 class="section-title header-history">Past History Logs</h3>
                <?php 
                $history_empty = true;
                foreach ($_SESSION['appointments'] as $idx => $row): 
                    // Apply date filter
                    if ($filter_type !== 'all') {
                        $appt_date = $row['appt_date'] ?? '';
                        if ($filter_type === 'today' && $appt_date !== date('Y-m-d')) continue;
                        if ($filter_type === 'tomorrow' && $appt_date !== date('Y-m-d', strtotime('+1 day'))) continue;
                        if ($filter_type === 'week') {
                            $week_start = date('Y-m-d', strtotime('monday this week'));
                            $week_end = date('Y-m-d', strtotime('sunday this week'));
                            if ($appt_date < $week_start || $appt_date > $week_end) continue;
                        }
                        if ($filter_type === 'month') {
                            $month_start = date('Y-m-01');
                            $month_end = date('Y-m-t');
                            if ($appt_date < $month_start || $appt_date > $month_end) continue;
                        }
                        if ($filter_type === 'date' && $appt_date !== $date_filter) continue;
                    }
                    if (isset($row['status']) && $row['status'] !== 'Pending' && $row['status'] !== 'Operational'):
                        $history_empty = false;
                        $full_name = getFullName($row);
                ?>
                    <div class="patient-card card border shadow-sm mb-3 p-3 card-history patient-card-clickable" data-bs-toggle="modal" data-bs-target="#detailModalHistory<?php echo $idx; ?>">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="patient-name text-secondary">
                                    <?php echo htmlspecialchars($full_name); ?>
                                </div>
                                <div class="patient-details">
                                    <?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?> • 
                                    <?php echo htmlspecialchars($row['appt_date'] . ' @ ' . $row['appt_time']); ?>
                                    <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($row['status']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="text-muted">Completed</span>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Modal History -->
                    <div class="modal fade" id="detailModalHistory<?php echo $idx; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header border-0 pt-4 px-4 pb-2">
                                    <h5 class="modal-title fw-bold text-dark">Patient Details</h5>
                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body px-4 py-3">
                                    <div class="detail-row"><span class="detail-label">Name:</span> <span class="detail-value"><?php echo htmlspecialchars($full_name); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">First Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['first_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Middle Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['middle_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Last Name:</span> <span class="detail-value"><?php echo htmlspecialchars($row['last_name'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Suffix:</span> <span class="detail-value"><?php echo htmlspecialchars($row['suffix'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Email:</span> <span class="detail-value"><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Contact:</span> <span class="detail-value"><?php echo htmlspecialchars($row['contact'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Service:</span> <span class="detail-value"><?php echo htmlspecialchars($row['service'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Date:</span> <span class="detail-value"><?php echo htmlspecialchars($row['appt_date'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Time:</span> <span class="detail-value"><?php echo htmlspecialchars($row['appt_time'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Age Group:</span> <span class="detail-value"><?php echo htmlspecialchars($row['age_group'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Payment:</span> <span class="detail-value"><?php echo htmlspecialchars($row['payment_method'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Urgency:</span> <span class="detail-value"><?php echo htmlspecialchars($row['urgency'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Medical History:</span> <span class="detail-value"><?php echo htmlspecialchars($row['medical_history'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Message:</span> <span class="detail-value"><?php echo htmlspecialchars($row['message'] ?? 'N/A'); ?></span></div>
                                    <div class="detail-row"><span class="detail-label">Status:</span> <span class="detail-value"><span class="badge bg-secondary"><?php echo htmlspecialchars($row['status'] ?? 'N/A'); ?></span></span></div>
                                </div>
                                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-secondary px-4 py-2 fw-bold" style="border-radius: 10px;" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endif;
                endforeach; 
                if ($history_empty):
                ?>
                    <div class="text-center text-muted py-4 small fw-medium">No past logs found for this date.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Accept Confirmation Modal -->
    <div class="modal fade" id="acceptConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Accept Appointment</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to accept this appointment?</p>
                    <p class="text-muted mt-2" style="font-size: 16px;">This will move the patient to the active queue.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a id="confirmAcceptBtn" href="#" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decline Confirmation Modal -->
    <div class="modal fade" id="declineConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Decline Appointment</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to decline this appointment?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a id="confirmDeclineBtn" href="#" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Done Confirmation Modal -->
    <div class="modal fade" id="doneConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Complete Appointment</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure this patient is done?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a id="confirmDoneBtn" href="#" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelConfirmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Cancel Appointment</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to cancel this appointment?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <a id="confirmCancelBtn" href="#" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Accept modal
            const acceptModal = document.getElementById('acceptConfirmModal');
            if (acceptModal) {
                acceptModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const targetUrl = button.getAttribute('data-bs-href');
                    const confirmBtn = acceptModal.querySelector('#confirmAcceptBtn');
                    if (confirmBtn && targetUrl) {
                        confirmBtn.setAttribute('href', targetUrl);
                    }
                });
            }

            // Decline modal
            const declineModal = document.getElementById('declineConfirmModal');
            if (declineModal) {
                declineModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const targetUrl = button.getAttribute('data-bs-href');
                    const confirmBtn = declineModal.querySelector('#confirmDeclineBtn');
                    if (confirmBtn && targetUrl) {
                        confirmBtn.setAttribute('href', targetUrl);
                    }
                });
            }

            // Done modal
            const doneModal = document.getElementById('doneConfirmModal');
            if (doneModal) {
                doneModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const targetUrl = button.getAttribute('data-bs-href');
                    const confirmBtn = doneModal.querySelector('#confirmDoneBtn');
                    if (confirmBtn && targetUrl) {
                        confirmBtn.setAttribute('href', targetUrl);
                    }
                });
            }

            // Cancel modal
            const cancelModal = document.getElementById('cancelConfirmModal');
            if (cancelModal) {
                cancelModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const targetUrl = button.getAttribute('data-bs-href');
                    const confirmBtn = cancelModal.querySelector('#confirmCancelBtn');
                    if (confirmBtn && targetUrl) {
                        confirmBtn.setAttribute('href', targetUrl);
                    }
                });
            }

            // Make sure card clicks work - prevent button clicks from triggering card click
            document.querySelectorAll('.patient-card-clickable').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('.btn') || e.target.closest('.action-buttons')) {
                        e.stopPropagation();
                        return;
                    }
                });
            });
        });
    </script>
</body>
</html>