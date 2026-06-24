<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow Admin - Operations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            padding-top: 60px;
        }
        .operation-card {
            border-radius: 16px !important;
            transition: transform 0.2s ease;
            display: flex;
            flex-direction: column;
        }
        .operation-card:hover {
            transform: translateY(-3px);
        }
        .operation-card.danger {
            border-left: 4px solid #dc3545 !important;
        }
        .operation-card.warning {
            border-left: 4px solid #ffc107 !important;
        }
        .operation-card.info {
            border-left: 4px solid #0dcaf0 !important;
        }
        .operation-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .operation-card .card-body .card-description {
            flex: 1;
        }
        .operation-card .card-body .card-footer-actions {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
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
        .btn-danger-custom {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
            min-width: 100px;
        }
        .btn-danger-custom:hover {
            background-color: #bd2130 !important;
            border-color: #bd2130 !important;
        }
        .btn-warning-custom {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
            min-width: 100px;
        }
        .btn-warning-custom:hover {
            background-color: #e0a800 !important;
            border-color: #e0a800 !important;
        }
        .btn-info-custom {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
            color: #212529 !important;
            min-width: 100px;
        }
        .btn-info-custom:hover {
            background-color: #0bb8d9 !important;
            border-color: #0bb8d9 !important;
        }
        .operation-count {
            font-size: 24px;
            font-weight: 700;
            color: #212529;
        }
        .header-center {
            text-align: center;
            margin-bottom: 40px;
        }
        .header-center h2 {
            font-size: 32px;
            font-weight: 700;
            color: #212529;
        }
        .header-center p {
            color: #6c757d;
            font-size: 16px;
            margin-top: 4px;
        }
        .card-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            flex-shrink: 0;
        }
        .card-icon i {
            font-size: 24px;
        }
        .card-title-area {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }
        .card-title-area h5 {
            margin-bottom: 0;
            font-weight: 700;
        }
        .card-description {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 12px;
            flex: 1;
        }
    </style>
</head>
<body>

    <div class="container mb-5" style="max-width: 1100px; padding-top: 20px;">
        
        <div class="header-center">
            <h2 class="fw-bold">System Operations</h2>
            <p>Manage and clear system data. All operations are irreversible.</p>
        </div>

        <div class="row g-4">
            <?php 
            $appointment_count = count($_SESSION['appointments'] ?? []);
            $booking_count = count($_SESSION['booking_history'] ?? []);
            $chat_count = count($_SESSION['chats'] ?? []);
            $patient_count = count($_SESSION['patient_records'] ?? []);
            $inventory_count = count($_SESSION['inventory'] ?? []);
            ?>

            <!-- Clear Appointments -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 operation-card danger">
                    <div class="card-body">
                        <div class="card-title-area">
                            <div class="card-icon bg-danger bg-opacity-10">
                                <i class="fas fa-calendar-times text-danger"></i>
                            </div>
                            <h5 class="text-danger">Appointments</h5>
                        </div>
                        <p class="card-description">Clear all appointment records including pending, active, and history.</p>
                        <div class="card-footer-actions">
                            <span class="operation-count"><?php echo $appointment_count; ?></span>
                            <button class="btn btn-danger-custom fw-bold" data-bs-toggle="modal" data-bs-target="#clearAppointmentsModal">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear Booking History -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 operation-card danger">
                    <div class="card-body">
                        <div class="card-title-area">
                            <div class="card-icon bg-danger bg-opacity-10">
                                <i class="fas fa-history text-danger"></i>
                            </div>
                            <h5 class="text-danger">Booking History</h5>
                        </div>
                        <p class="card-description">Clear all patient booking history records.</p>
                        <div class="card-footer-actions">
                            <span class="operation-count"><?php echo $booking_count; ?></span>
                            <button class="btn btn-danger-custom fw-bold" data-bs-toggle="modal" data-bs-target="#clearBookingModal">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear Chat History -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 operation-card warning">
                    <div class="card-body">
                        <div class="card-title-area">
                            <div class="card-icon bg-warning bg-opacity-10">
                                <i class="fas fa-comment-slash text-warning"></i>
                            </div>
                            <h5 class="text-warning">Chat History</h5>
                        </div>
                        <p class="card-description">Clear all chat messages between patients and dentist.</p>
                        <div class="card-footer-actions">
                            <span class="operation-count"><?php echo $chat_count; ?></span>
                            <button class="btn btn-warning-custom fw-bold" data-bs-toggle="modal" data-bs-target="#clearChatModal">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear Patient Records -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 operation-card info">
                    <div class="card-body">
                        <div class="card-title-area">
                            <div class="card-icon bg-info bg-opacity-10">
                                <i class="fas fa-users text-info"></i>
                            </div>
                            <h5 class="text-info">Patient Records</h5>
                        </div>
                        <p class="card-description">Clear all patient account records. Login credentials remain intact.</p>
                        <div class="card-footer-actions">
                            <span class="operation-count"><?php echo $patient_count; ?></span>
                            <button class="btn btn-info-custom fw-bold" data-bs-toggle="modal" data-bs-target="#clearPatientsModal">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear Inventory -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 operation-card warning">
                    <div class="card-body">
                        <div class="card-title-area">
                            <div class="card-icon bg-warning bg-opacity-10">
                                <i class="fas fa-boxes text-warning"></i>
                            </div>
                            <h5 class="text-warning">Inventory</h5>
                        </div>
                        <p class="card-description">Clear all inventory items and stock records.</p>
                        <div class="card-footer-actions">
                            <span class="operation-count"><?php echo $inventory_count; ?></span>
                            <button class="btn btn-warning-custom fw-bold" data-bs-toggle="modal" data-bs-target="#clearInventoryModal">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clear Everything -->
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 operation-card danger">
                    <div class="card-body">
                        <div class="card-title-area">
                            <div class="card-icon bg-danger bg-opacity-10">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </div>
                            <h5 class="text-danger">Everything</h5>
                        </div>
                        <p class="card-description">Clear ALL system data including all records above. Login credentials remain intact.</p>
                        <div class="card-footer-actions">
                            <span class="operation-count"><?php echo $appointment_count + $booking_count + $chat_count + $patient_count + $inventory_count; ?></span>
                            <button class="btn btn-danger-custom fw-bold" data-bs-toggle="modal" data-bs-target="#clearEverythingModal">Clear All</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Appointments Modal -->
    <div class="modal fade" id="clearAppointmentsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Appointments</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear all appointments?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=clear_appointments" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Booking Modal -->
    <div class="modal fade" id="clearBookingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Booking History</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear all booking history?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=clear_booking_history" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Chat Modal -->
    <div class="modal fade" id="clearChatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Chat History</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear all chat history?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=clear_chat_history" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Patients Modal -->
    <div class="modal fade" id="clearPatientsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Patient Records</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear all patient records?</p>
                    <p class="text-muted mt-2" style="font-size: 16px;">Patient login credentials will remain intact.</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=clear_patients" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Inventory Modal -->
    <div class="modal fade" id="clearInventoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Inventory</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear all inventory items?</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=clear_inventory" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Everything Modal -->
    <div class="modal fade" id="clearEverythingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Clear Everything</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3 text-center">
                    <p class="fw-bold mb-0 text-dark" style="font-size: 18px;">Are you sure you want to clear ALL system data?</p>
                    <p class="text-muted mt-2" style="font-size: 16px;">This will clear appointments, booking history, chat, patient records, and inventory.</p>
                    <p class="text-muted mt-1" style="font-size: 16px;">Patient login credentials and clinic status will remain intact.</p>
                    <p class="text-danger-modal mt-2" style="font-size: 16px;">This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-2 d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-modal-no px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px;" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=clear_everything" class="btn btn-modal-yes px-4 py-2 fw-bold" style="border-radius: 10px; min-width: 120px; text-decoration: none;">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>