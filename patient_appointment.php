<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if patient is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'patient') {
    $_SESSION['redirect_after_login'] = 'patient_appointment.php';
    header("Location: patient_auth.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow - Book Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
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
        .btn-primary:hover, .btn-primary:focus {
            background-color: #023d54 !important;
            border-color: #023d54 !important;
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
        .btn-modal-yes-navy {
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
        .btn-modal-yes-navy:hover {
            background-color: #023d54 !important;
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
        @media (min-width: 576px) {
            .modal-dialog-centered {
                min-height: calc(100% - 3.5rem) !important;
            }
        }
        .form-control, .form-select {
            font-size: 16px !important;
            padding: 10px 14px !important;
        }
        .form-label {
            font-size: 14px !important;
            margin-bottom: 6px;
        }
        .was-validated .form-control[type="date"],
        .was-validated .form-control[type="time"] {
            background-image: none !important;
            padding-right: 14px !important;
        }
        .name-row {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .name-row .form-group {
            flex: 1;
            min-width: 100px;
        }
        .urgency-urgent {
            color: #dc3545 !important;
            font-weight: 700 !important;
        }
        .urgency-not-urgent {
            color: #6c757d !important;
            font-weight: 400 !important;
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
            }
            .nav-link.active {
                background-color: rgba(255, 255, 255, 0.15);
                border-radius: 5px;
                padding-left: 10px !important;
            }
            .name-row .form-group {
                min-width: 80px;
            }
        }
    </style>
</head>
<body class="bg-light" style="padding-top: 115px;">

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
                    <a class="nav-link active py-2 py-lg-0" href="patient_appointment.php">Appointment Form</a>
                    <a class="nav-link py-2 py-lg-0" href="patient_chat.php">Quick Chat</a>
                    <a class="nav-link py-2 py-lg-0" href="patient_booking_history.php">Booking History</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'patient'): ?>
                        <button type="button" class="nav-link text-warning fw-bold py-2 py-lg-0 ms-lg-3" data-bs-toggle="modal" data-bs-target="#signOutModal">Logout</button>
                    <?php else: ?>
                        <a class="nav-link text-warning py-2 py-lg-0 ms-lg-3" href="patient_auth.php">Sign In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mb-5 px-3 px-sm-0" style="max-width: 750px;">
        <?php if (isset($_SESSION['dentist_status']) && $_SESSION['dentist_status'] == "Open"): ?>
            <div class="alert alert-success border-success text-center fw-bold shadow-sm mb-4">🟢 Clinic Status: Open</div>
        <?php else: ?>
            <div class="alert alert-danger border-danger text-center fw-bold shadow-sm mb-4">🔴 Clinic Status: Closed</div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h2 class="text-primary fw-bold border-bottom pb-2 mb-4 text-center">Book Appointment</h2>
                
                <form id="appointmentForm" method="POST" action="app_process.php" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="process_appointment">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Full Name</label>
                        <div class="name-row">
                            <div class="form-group">
                                <input type="text" name="last_name" class="form-control" placeholder="Last Name" pattern="[A-Za-z\s\-\.]+" required>
                                <div class="invalid-feedback">Letters only.</div>
                            </div>
                            <div class="form-group">
                                <input type="text" name="first_name" class="form-control" placeholder="First Name" pattern="[A-Za-z\s\-\.]+" required>
                                <div class="invalid-feedback">Letters only.</div>
                            </div>
                            <div class="form-group">
                                <input type="text" name="middle_name" class="form-control" placeholder="Middle Name" pattern="[A-Za-z\s\-\.]+">
                            </div>
                            <div class="form-group" style="flex: 0.6; min-width: 70px;">
                                <input type="text" name="suffix" class="form-control" placeholder="Suffix" pattern="[A-Za-z\s\-\.]+">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" placeholder="yourname@example.com" required>
                        <div class="invalid-feedback">Please provide a valid email address.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Contact Number</label>
                        <input type="text" name="contact" class="form-control" placeholder="09XXXXXXXXX" pattern="^09\d{9}$" maxlength="11" required>
                        <div class="invalid-feedback">Must start with 09 and contain exactly 11 digits.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Select Dental Service</label>
                        <select name="service" id="service_select" class="form-select" required>
                            <option value="">-- Select a Service --</option>
                            <?php foreach ($_SESSION['dental_services'] as $service): ?>
                                <option value="<?php echo htmlspecialchars($service['name']); ?>" data-price="<?php echo $service['price']; ?>">
                                    <?php echo htmlspecialchars($service['name']); ?> - ₱<?php echo number_format($service['price'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a dental service.</div>
                    </div>

                    <div class="card p-3 mb-3 bg-white border rounded shadow-sm">
                        <label class="form-label fw-bold text-secondary small mb-2">Patient Medical History</label>
                        <div class="row mb-2">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="history[]" value="Hypertension" id="hyper">
                                    <label class="form-check-label text-dark small" for="hyper">Hypertension</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="history[]" value="Diabetes" id="diabetes">
                                    <label class="form-check-label text-dark small" for="diabetes">Diabetes</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="history[]" value="Drug Allergies" id="allergies">
                                    <label class="form-check-label text-dark small" for="allergies">Drug Allergies</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="history[]" value="Bleeding Disorders" id="bleeding">
                                    <label class="form-check-label text-dark small" for="bleeding">Bleeding Disorders</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold text-secondary small">Other Illnesses or Medications:</label>
                            <textarea name="other_meds" class="form-control" rows="2" placeholder="List any other relevant health notes here..."></textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">Appointment Date</label>
                            <input type="date" name="appt_date" class="form-control" required>
                            <div class="invalid-feedback">Please select a valid date.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">Appointment Time</label>
                            <input type="time" name="appt_time" class="form-control" required>
                            <div class="invalid-feedback">Please select a valid time slot.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">Age Category</label>
                            <select name="age_group" class="form-select" required>
                                <option value="Adult">Adult</option>
                                <option value="Child">Child</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">Payment Method</label>
                            <select name="payment_method" id="payment_method_select" class="form-select" required>
                                <option value="Cash">Cash</option>
                                <option value="Gcash">Gcash</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Urgency Status</label>
                        <select name="urgency_status" id="urgency_status_select" class="form-select border-warning shadow-sm" required>
                            <option value="Not Urgent" class="urgency-not-urgent">Not Urgent</option>
                            <option value="Urgent" class="urgency-urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label id="message_label" class="form-label fw-bold text-secondary small">Message / Symptoms</label>
                        <textarea name="message" id="message_textarea" class="form-control" rows="3" placeholder="Describe symptoms or reasons for booking..."></textarea>
                        <div id="urgent_requirement_alert" class="alert alert-danger border-danger mt-2 py-2 px-3 small fw-bold d-none">
                            Urgent status selected! You must provide details regarding your emergency symptoms below before submitting.
                        </div>
                    </div>

                    <input type="hidden" name="service_price" id="service_price" value="0">

                    <button type="button" id="submitTriggerBtn" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">Submit Appointment</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">Confirm Details</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <p class="fs-5 mb-1 fw-medium text-secondary">Are you sure all your inputs are correct?</p>
                    <small class="text-muted">Please review your information carefully before finalizing.</small>
                </div>
                <div class="modal-footer border-0 bg-light d-flex justify-content-center gap-3">
                    <button type="button" id="confirmedSubmitBtn" class="btn-modal-yes-navy px-4 fw-bold">Yes</button>
                    <button type="button" class="btn-modal-no px-4 fw-bold" data-bs-dismiss="modal">No</button>
                </div>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById('appointmentForm');
            const submitTriggerBtn = document.getElementById('submitTriggerBtn');
            const confirmedSubmitBtn = document.getElementById('confirmedSubmitBtn');
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            // Function to capitalize each word (Proper Case)
            function toProperCase(str) {
                if (!str) return str;
                return str.replace(/\w\S*/g, function(word) {
                    return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
                });
            }

            // Auto-capitalize name fields on blur
            document.querySelectorAll('input[name="last_name"], input[name="first_name"], input[name="middle_name"], input[name="suffix"]').forEach(function(input) {
                input.addEventListener('blur', function() {
                    if (this.value) {
                        this.value = toProperCase(this.value);
                    }
                });
            });

            submitTriggerBtn.addEventListener('click', function() {
                // Capitalize all name fields before validation
                document.querySelectorAll('input[name="last_name"], input[name="first_name"], input[name="middle_name"], input[name="suffix"]').forEach(function(input) {
                    if (input.value) {
                        input.value = toProperCase(input.value);
                    }
                });

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                } else {
                    confirmModal.show();
                }
            });

            confirmedSubmitBtn.addEventListener('click', function() {
                // Capitalize all name fields one more time before submit
                document.querySelectorAll('input[name="last_name"], input[name="first_name"], input[name="middle_name"], input[name="suffix"]').forEach(function(input) {
                    if (input.value) {
                        input.value = toProperCase(input.value);
                    }
                });
                form.submit();
            });

            const urgencySelect = document.getElementById('urgency_status_select');
            const messageLabel = document.getElementById('message_label');
            const messageTextarea = document.getElementById('message_textarea');
            const urgentAlert = document.getElementById('urgent_requirement_alert');
            const serviceSelect = document.getElementById('service_select');
            const servicePriceInput = document.getElementById('service_price');

            urgencySelect.addEventListener('change', function() {
                if (this.value === 'Urgent') {
                    messageLabel.classList.remove('text-secondary');
                    messageLabel.classList.add('text-danger');
                    messageLabel.innerHTML = 'Urgent Message Details (Required)';
                    messageTextarea.setAttribute('required', 'true');
                    messageTextarea.placeholder = "Please explain the emergency symptoms clearly here...";
                    urgentAlert.classList.remove('d-none');
                    urgencySelect.classList.add('border-danger');
                } else {
                    messageLabel.classList.remove('text-danger');
                    messageLabel.classList.add('text-secondary');
                    messageLabel.innerHTML = 'Message / Symptoms';
                    messageTextarea.removeAttribute('required');
                    messageTextarea.placeholder = "Describe symptoms or reasons for booking...";
                    urgentAlert.classList.add('d-none');
                    urgencySelect.classList.remove('border-danger');
                }
            });

            serviceSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const price = selected.getAttribute('data-price');
                if (price) {
                    servicePriceInput.value = price;
                }
            });
        });
    </script>
</body>
</html>