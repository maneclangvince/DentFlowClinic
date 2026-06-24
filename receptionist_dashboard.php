<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if receptionist is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'receptionist') {
    header("Location: receptionist_login.php");
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

// Force reload inventory from database
loadSessionFromDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow Front Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f4f6f9;
            padding-top: 120px;
        }
        nav {
            background-color: #4b5563 !important;
            padding: 15px 20px !important;
            transition: padding 0.3s ease;
        }
        .navbar-brand {
            color: white !important;
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
                border-bottom: 4px solid white;
                color: white !important;
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
            border-bottom: 4px solid white !important;
        }
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #3a424a;
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
        .card {
            border-radius: 20px !important;
        }
        .card h5 {
            font-size: 22px !important;
        }
        .table td, .table th {
            vertical-align: middle !important;
            text-align: center !important;
            font-size: 16px !important;
            padding: 16px 12px !important;
        }
        .table th {
            font-weight: 700 !important;
            color: #495057 !important;
            font-size: 17px !important;
        }
        .badge {
            font-weight: 600 !important;
            padding: 8px 16px !important;
            font-size: 14px !important;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .badge.bg-success {
            background-color: #198754 !important;
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
        .btn-modal-warning {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
            font-size: 18px !important;
            padding: 12px 32px !important;
        }
        .btn-modal-warning:hover {
            background-color: #e0a800 !important;
            border-color: #e0a800 !important;
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
        .form-control {
            border-radius: 10px !important;
            border: 1px solid #ced4da !important;
            padding: 14px 18px !important;
            font-size: 18px !important;
            height: 56px !important;
        }
        .form-control:focus {
            border-color: #4b5563 !important;
            box-shadow: 0 0 0 0.25rem rgba(75, 85, 99, 0.25) !important;
        }
        .form-label {
            font-size: 16px !important;
            font-weight: 600 !important;
        }
        .form-select {
            border-radius: 10px !important;
            font-size: 18px !important;
            height: 56px !important;
            padding: 12px 16px !important;
        }
        .btn-dark {
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
            font-size: 18px !important;
            padding: 14px !important;
            height: 56px !important;
        }
        .btn-dark:hover {
            background-color: #374151 !important;
            border-color: #374151 !important;
        }
        .btn-danger-custom {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
            font-size: 14px !important;
            padding: 8px 16px !important;
        }
        .btn-danger-custom:hover {
            background-color: #bd2130 !important;
            border-color: #bd2130 !important;
        }
        .btn-outline-secondary {
            font-size: 14px !important;
            padding: 8px 16px !important;
        }
        .table-billing th, .table-billing td {
            width: 20% !important;
        }
        .table-billing th:last-child, .table-billing td:last-child {
            width: 25% !important;
        }
        .table-inventory th, .table-inventory td {
            width: 20% !important;
        }
        .container {
            max-width: 1200px !important;
        }
        .btn-sm {
            padding: 10px 20px !important;
            font-size: 15px !important;
        }
        .form-text {
            font-size: 15px !important;
        }
        .modal-title {
            font-size: 24px !important;
            font-weight: 700 !important;
        }
        .search-box {
            position: relative;
        }
        .search-box .form-control {
            padding-left: 56px !important;
        }
        .search-box .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 22px;
            pointer-events: none;
        }
        .table-row-hidden {
            display: none !important;
        }
        .payment-status-btn {
            cursor: pointer;
            border: none;
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        .payment-status-btn:hover {
            transform: scale(1.05);
            opacity: 0.85;
        }
        .payment-status-btn.unpaid {
            background-color: #dc3545;
            color: white;
        }
        .payment-status-btn.paid {
            background-color: #198754;
            color: white;
            cursor: pointer;
        }
        .payment-status-btn.paid:hover {
            transform: scale(1.05);
            opacity: 0.85;
        }
        .action-buttons-inventory {
            display: flex;
            gap: 6px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .action-buttons-inventory .btn {
            white-space: nowrap;
        }
        .billing-scroll {
            max-height: 300px;
            overflow-y: scroll !important;
            overflow-x: hidden;
        }
        .billing-scroll::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        .billing-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
            box-shadow: inset 0 0 5px #ddd;
        }
        .billing-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .billing-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .billing-scroll table {
            margin-bottom: 0;
        }
        .billing-scroll thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .billing-scroll thead th {
            background-color: #e9ecef !important;
            border-bottom: 2px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .billing-scroll tbody tr:last-child td {
            border-bottom: none;
        }
        .billing-table-wrapper {
            position: relative;
        }
        .billing-table-wrapper .billing-scroll {
            border-radius: 8px;
        }
        .filter-section {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .filter-section .filter-label {
            font-weight: 600;
            color: #495057;
            font-size: 16px;
        }
        .filter-section .filter-buttons {
            display: flex;
            gap: 6px;
        }
        .filter-section .filter-buttons .btn-filter {
            padding: 6px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid #dee2e6;
            background: white;
            color: #495057;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .filter-section .filter-buttons .btn-filter:hover {
            border-color: #adb5bd;
            background: #f8f9fa;
        }
        .filter-section .filter-buttons .btn-filter.active-all {
            border-color: #4b5563;
            background: #4b5563;
            color: white;
        }
        .filter-section .filter-buttons .btn-filter.active-unpaid {
            border-color: #dc3545;
            background: #dc3545;
            color: white;
        }
        .filter-section .filter-buttons .btn-filter.active-paid {
            border-color: #198754;
            background: #198754;
            color: white;
        }
        .filter-section .filter-buttons .btn-filter.active-all:hover {
            background: #374151;
            border-color: #374151;
        }
        .filter-section .filter-buttons .btn-filter.active-unpaid:hover {
            background: #bd2130;
            border-color: #bd2130;
        }
        .filter-section .filter-buttons .btn-filter.active-paid:hover {
            background: #146c43;
            border-color: #146c43;
        }
        .billing-row-hidden {
            display: none !important;
        }
        .billing-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .billing-header h5 {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container-fluid px-0">
            <div class="logo">
                <a class="navbar-brand fw-bold mb-0" href="receptionist_dashboard.php">DentFlow Front Desk</a>
            </div>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileMenuToggle">
                <div class="navbar-nav ms-auto fw-medium align-items-lg-center text-center gap-lg-4 mt-2 mt-lg-0">
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'receptionist'): ?>
                        <a class="nav-link active py-2 py-lg-0" href="receptionist_dashboard.php">Dashboard</a>
                        <a class="nav-link text-danger-custom fw-bold py-2 py-lg-0 ms-lg-3" href="app_process.php?action=logout">Logout</a>
                    <?php else: ?>
                        <a class="nav-link text-warning-custom fw-bold py-2 py-lg-0" href="receptionist_login.php">Login</a>
                        <a class="nav-link active py-2 py-lg-0" href="receptionist_dashboard.php">Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <!-- Total Patients Served Cards -->
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

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h5 class="fw-bold mb-4 text-secondary">Clinic Status Configuration</h5>
                    <form method="POST" action="app_process.php">
                        <input type="hidden" name="action" value="toggle_clinic">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Clinic Operational State</label>
                            <select name="is_open" class="form-select fw-bold">
                                <option value="Open" <?php echo (isset($_SESSION['dentist_status']) && $_SESSION['dentist_status'] === 'Open') ? 'selected' : ''; ?>>Open (Accepting Appointments)</option>
                                <option value="Closed" <?php echo (isset($_SESSION['dentist_status']) && $_SESSION['dentist_status'] === 'Closed') ? 'selected' : ''; ?>>Closed (Emergencies Only)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Active Schedule Hours</label>
                            <input type="text" name="schedule_hours" class="form-control text-dark" value="8:00 AM - 5:00 PM" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 fw-bold mt-2">Update Status</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <div class="billing-header">
                        <h5 class="fw-bold mb-0 text-secondary">Billing & Checkout</h5>
                        <div class="filter-section">
                            <span class="filter-label">Filter:</span>
                            <div class="filter-buttons">
                                <button class="btn-filter active-all" data-filter="all" onclick="filterBilling('all')">All</button>
                                <button class="btn-filter" data-filter="unpaid" onclick="filterBilling('unpaid')">Unpaid</button>
                                <button class="btn-filter" data-filter="paid" onclick="filterBilling('paid')">Paid</button>
                            </div>
                        </div>
                    </div>

                    <div class="billing-table-wrapper">
                        <?php 
                        $billing_count = 0;
                        foreach ($_SESSION['appointments'] as $p) {
                            if (!isset($p['payment_status']) || $p['payment_status'] !== 'Paid') {
                                $billing_count++;
                            }
                        }
                        $scroll_class = ($billing_count > 3) ? 'billing-scroll' : '';
                        ?>
                        <div class="<?php echo $scroll_class; ?>">
                            <table class="table table-billing align-middle table-sm" id="billingTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Patient</th>
                                        <th class="text-center">Service</th>
                                        <th class="text-center">Fee</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="billingBody">
                                    <?php 
                                    $has_pending = false;
                                    foreach ($_SESSION['appointments'] as $p): 
                                        $full_name = getFullName($p);
                                        $is_paid = isset($p['payment_status']) && $p['payment_status'] === 'Paid';
                                        if (!$is_paid) $has_pending = true;
                                        $status_class = $is_paid ? 'paid' : 'unpaid';
                                    ?>
                                    <tr class="billing-row" data-status="<?php echo $status_class; ?>">
                                        <td class="text-center"><strong><?php echo htmlspecialchars($full_name); ?></strong></td>
                                        <td class="text-center"><?php echo htmlspecialchars($p['service'] ?? 'N/A'); ?></td>
                                        <td class="text-center fw-bold text-dark">₱<?php echo number_format($p['service_price'] ?? 0, 2); ?></td>
                                        <td class="text-center">
                                            <?php if ($is_paid): ?>
                                                <button class="payment-status-btn paid" data-bs-toggle="modal" data-bs-target="#unpayModal<?php echo $p['id']; ?>">
                                                    Paid
                                                </button>
                                            <?php else: ?>
                                                <button class="payment-status-btn unpaid" data-bs-toggle="modal" data-bs-target="#payModal<?php echo $p['id']; ?>">
                                                    Unpaid
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <?php if (!$is_paid): ?>
                                    <!-- Pay Modal -->
                                    <div class="modal fade" id="payModal<?php echo $p['id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header border-0 pt-4 px-4 pb-2">
                                                    <h5 class="modal-title fw-bold text-dark">Process Payment</h5>
                                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="app_process.php">
                                                    <div class="modal-body px-4 py-3 text-center">
                                                        <input type="hidden" name="action" value="process_payment">
                                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                        <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to mark this as paid?</p>
                                                        <p class="text-muted mt-2" style="font-size: 16px;">Patient: <strong><?php echo htmlspecialchars($full_name); ?></strong><br>Amount: <strong>₱<?php echo number_format($p['service_price'] ?? 0, 2); ?></strong></p>
                                                    </div>
                                                    <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                                                        <button type="submit" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;">Yes</button>
                                                        <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <!-- Unpay Modal -->
                                    <div class="modal fade" id="unpayModal<?php echo $p['id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header border-0 pt-4 px-4 pb-2">
                                                    <h5 class="modal-title fw-bold text-dark">Revert Payment</h5>
                                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="app_process.php">
                                                    <div class="modal-body px-4 py-3 text-center">
                                                        <input type="hidden" name="action" value="unprocess_payment">
                                                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                        <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to mark this as unpaid?</p>
                                                        <p class="text-muted mt-2" style="font-size: 16px;">Patient: <strong><?php echo htmlspecialchars($full_name); ?></strong><br>Amount: <strong>₱<?php echo number_format($p['service_price'] ?? 0, 2); ?></strong></p>
                                                        <p class="text-danger mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                                                        <button type="submit" class="btn btn-modal-warning px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;">Yes</button>
                                                        <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if (!$has_pending && empty($_SESSION['appointments'])): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">No appointments found.</td></tr>
                                    <?php elseif (!$has_pending && !empty($_SESSION['appointments'])): ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">All payments are completed.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 mt-4 bg-white">
            <div class="row g-3">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3 text-secondary">Add Inventory Item</h5>
                    <form method="POST" action="app_process.php" class="row g-2">
                        <input type="hidden" name="action" value="add_inventory_item">
                        <div class="col-md-8">
                            <input type="text" name="item" class="form-control" placeholder="Item Name" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-dark w-100 fw-bold">Add Item</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3 text-secondary">Search Item</h5>
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search inventory items..." onkeyup="filterItems()">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 mt-4 bg-white">
            <h5 class="fw-bold mb-4 text-secondary">Live Supply</h5>
            <div class="table-responsive">
                <table class="table table-inventory align-middle" id="inventoryTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="border-top-left-radius: 8px;">Item</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Stock Status</th>
                            <th class="text-center" style="border-top-right-radius: 8px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryBody">
                        <?php 
                        $inventory_items = $_SESSION['inventory'] ?? [];
                        foreach ($inventory_items as $id => $item): 
                            $quantity = $item['quantity'] ?? 0;
                            $low_limit = $item['low_stock_limit'] ?? 10;
                            if ($quantity == 0) {
                                $status_class = 'bg-danger';
                                $status_text = 'Out of Stock';
                            } elseif ($quantity <= $low_limit) {
                                $status_class = 'bg-warning text-dark';
                                $status_text = 'Low Stock';
                            } else {
                                $status_class = 'bg-success';
                                $status_text = 'In Stock';
                            }
                            $item_id = $item['id'] ?? $id;
                        ?>
                        <tr class="inventory-row" data-item="<?php echo strtolower(htmlspecialchars($item['item'])); ?>">
                            <td class="text-center"><strong><?php echo htmlspecialchars($item['item']); ?></strong></td>
                            <td class="text-center font-monospace text-dark">₱<?php echo number_format($item['price'] ?? 0, 2); ?></td>
                            <td class="text-center"><?php echo $quantity; ?> units</td>
                            <td class="text-center">
                                <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td class="text-center">
                                <div class="action-buttons-inventory">
                                    <button class="btn btn-sm btn-outline-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#invModal<?php echo $item_id; ?>">Adjust</button>
                                    <button class="btn btn-sm btn-danger-custom fw-bold" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $item_id; ?>">Delete</button>
                                </div>
                            </td>
                        </tr>

                        <!-- Adjust Inventory Modal -->
                        <div class="modal fade" id="invModal<?php echo $item_id; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 pt-4 px-4 pb-2">
                                        <h5 class="modal-title fw-bold text-dark">Adjust Supply</h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="app_process.php">
                                        <div class="modal-body px-4 py-3">
                                            <input type="hidden" name="action" value="modify_item">
                                            <input type="hidden" name="id" value="<?php echo $item_id; ?>">
                                            
                                            <div class="text-center mb-4">
                                                <p class="text-muted mb-0" style="font-size: 16px;">Adjusting:</p>
                                                <h5 class="fw-bold text-dark"><?php echo htmlspecialchars($item['item']); ?></h5>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary">Quantity</label>
                                                <input type="number" name="quantity" id="quantity_<?php echo $item_id; ?>" class="form-control" value="<?php echo $quantity; ?>" required min="0">
                                                <div class="form-text text-muted">Enter 0 for out of stock.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-secondary">Price (₱)</label>
                                                <input type="number" step="0.01" name="price" id="price_<?php echo $item_id; ?>" class="form-control" value="<?php echo number_format($item['price'] ?? 0, 2); ?>" required min="0">
                                            </div>
                                            
                                            <div class="mt-3 p-3 bg-light rounded-3" id="statusPreview_<?php echo $item_id; ?>">
                                                <p class="text-muted mb-1" style="font-size: 15px;">Current Status:</p>
                                                <span class="badge <?php echo $status_class; ?> px-3 py-2" style="font-size: 16px;"><?php echo $status_text; ?></span>
                                                <?php if ($quantity > 0): ?>
                                                    <span class="badge bg-secondary px-3 py-2 ms-2" style="font-size: 16px;"><?php echo $quantity; ?> units in stock</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                                            <button type="submit" class="btn btn-dark px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; font-size: 18px;">Save Changes</button>
                                            <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal<?php echo $item_id; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 pt-4 px-4 pb-2">
                                        <h5 class="modal-title fw-bold text-dark">Delete Item</h5>
                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body px-4 py-3 text-center">
                                        <p class="fw-bold mb-0 text-dark" style="font-size: 20px;">Are you sure you want to delete this item?</p>
                                        <p class="text-muted mt-2" style="font-size: 18px;">Item: <strong><?php echo htmlspecialchars($item['item']); ?></strong></p>
                                        <p class="text-danger" style="font-size: 16px;">This action cannot be undone.</p>
                                    </div>
                                    <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                                        <a href="app_process.php?action=delete_inventory_item&id=<?php echo $item_id; ?>" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                                        <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function filterItems() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.getElementsByClassName('inventory-row');
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const itemName = row.getAttribute('data-item');
                if (itemName && itemName.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        function filterBilling(filter) {
            const rows = document.querySelectorAll('.billing-row');
            const buttons = document.querySelectorAll('.btn-filter');
            
            buttons.forEach(btn => {
                btn.className = 'btn-filter';
                if (btn.dataset.filter === filter) {
                    btn.classList.add('active-' + filter);
                }
            });
            
            rows.forEach(row => {
                const status = row.dataset.status;
                if (filter === 'all') {
                    row.classList.remove('billing-row-hidden');
                } else if (filter === 'unpaid' && status === 'unpaid') {
                    row.classList.remove('billing-row-hidden');
                } else if (filter === 'paid' && status === 'paid') {
                    row.classList.remove('billing-row-hidden');
                } else {
                    row.classList.add('billing-row-hidden');
                }
            });
        }

        // Live preview for inventory status change
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[id^="quantity_"]').forEach(function(input) {
                input.addEventListener('input', function() {
                    const id = this.id.replace('quantity_', '');
                    const quantity = parseInt(this.value) || 0;
                    const statusPreview = document.getElementById('statusPreview_' + id);
                    
                    if (statusPreview) {
                        let statusClass = '';
                        let statusText = '';
                        const lowLimit = 10;
                        
                        if (quantity === 0) {
                            statusClass = 'bg-danger';
                            statusText = 'Out of Stock';
                        } else if (quantity <= lowLimit) {
                            statusClass = 'bg-warning text-dark';
                            statusText = 'Low Stock';
                        } else {
                            statusClass = 'bg-success';
                            statusText = 'In Stock';
                        }
                        
                        statusPreview.innerHTML = `
                            <p class="text-muted mb-1" style="font-size: 15px;">Current Status:</p>
                            <span class="badge ${statusClass} px-3 py-2" style="font-size: 16px;">${statusText}</span>
                            ${quantity > 0 ? `<span class="badge bg-secondary px-3 py-2 ms-2" style="font-size: 16px;">${quantity} units in stock</span>` : ''}
                        `;
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>