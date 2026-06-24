<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for error message from session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
if ($error_message) {
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow - Authentication</title>
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
        .text-warning {
            color: #ffc107;
        }
        .auth-screen-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            width: 100%;
            padding-top: 130px;
            padding-bottom: 40px;
            box-sizing: border-box;
        }
        .auth-container-box {
            width: 100%;
            max-width: 540px;
        }
        .card.custom-auth-card {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.20) !important;
        }
        .form-control {
            font-size: 16px !important;
            height: 52px !important;
            padding: 12px 16px !important;
            border-radius: 8px !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-label {
            font-size: 14px !important;
            margin-bottom: 8px;
        }
        .was-validated .form-control:invalid,
        .form-control.is-invalid-custom {
            border-color: #dc3545 !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='M6 8h.01M6 4v2.5' stroke-width='1.5' stroke-linecap='round'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }
        .was-validated .form-control:invalid:focus,
        .form-control.is-invalid-custom:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }
        .was-validated .form-control:valid,
        .form-control.is-valid-custom {
            border-color: #198754 !important;
            padding-right: calc(1.5em + 0.75rem) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right calc(0.375em + 0.1875rem) center !important;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
        }
        .was-validated .form-control:valid:focus,
        .form-control.is-valid-custom:focus {
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25) !important;
        }
        .nav-tabs .nav-link {
            border: none !important;
            font-size: 18px !important;
            color: #6c757d !important;
        }
        .nav-tabs .nav-link.active {
            border-bottom: 4px solid #035270 !important;
            color: #035270 !important;
            background: transparent !important;
        }
        .auth-submit-btn {
            font-size: 18px !important;
            height: 54px !important;
            border-radius: 8px !important;
            border: none !important;
            transition: background-color 0.3s ease;
        }
        .btn-login-custom {
            background-color: #035270 !important;
            color: white !important;
        }
        .btn-login-custom:hover {
            background-color: #023d54 !important;
        }
        .btn-signup-custom {
            background-color: #198754 !important;
            color: white !important;
        }
        .btn-signup-custom:hover {
            background-color: #146c43 !important;
        }
        .alert-danger-custom {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
        }
        .alert-success-custom {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 600;
        }
        .username-validation {
            font-size: 13px;
            margin-top: 6px;
            display: block;
            min-height: 20px;
        }
        .username-validation.valid {
            color: #198754;
        }
        .username-validation.invalid {
            color: #dc3545;
        }
        .username-validation.default {
            color: #6c757d;
        }
        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 6px;
            border: 1px solid #e9ecef;
        }
        .password-requirements .requirement {
            font-size: 13px;
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .password-requirements .requirement .icon {
            font-size: 16px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            font-weight: 700;
        }
        .password-requirements .requirement .msg {
            flex: 1;
        }
        .password-requirements .requirement.valid {
            color: #198754;
        }
        .password-requirements .requirement.valid .icon {
            color: #198754;
        }
        .password-requirements .requirement.invalid {
            color: #dc3545;
        }
        .password-requirements .requirement.invalid .icon {
            color: #dc3545;
        }
        .password-requirements .requirement.default {
            color: #6c757d;
        }
        .password-requirements .requirement.default .icon {
            color: #6c757d;
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

    <div class="auth-screen-wrapper px-3">
        <div class="auth-container-box">
            
            <?php if ($error_message === 'symbols'): ?>
                <div class="alert alert-danger-custom text-center shadow-sm small mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> Error: Special characters or symbols detected! Alphanumeric input values only.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
                <div class="alert alert-danger-custom text-center shadow-sm small mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> Account not found. Please sign up to create an account.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
                <div class="alert alert-danger-custom text-center shadow-sm small mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> Account already exists! Please login instead.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
                <div class="alert alert-danger-custom text-center shadow-sm small mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i> Invalid credentials! Please check your username, email, or password.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['signup']) && $_GET['signup'] === 'success'): ?>
                <div class="alert alert-success-custom text-center shadow-sm small mb-3">
                    <i class="fas fa-check-circle me-2"></i> Account created successfully! Please login.
                </div>
            <?php endif; ?>
            
            <div class="card border-0 custom-auth-card rounded-4 overflow-hidden">
                <div class="card-header bg-white p-0 border-0">
                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold py-3 rounded-0" id="login-tab" data-bs-toggle="tab" data-bs-target="#loginView" type="button" role="tab">Login</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-3 rounded-0" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signupView" type="button" role="tab">Signup</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4 tab-content">
                    
                    <div class="tab-pane fade show active" id="loginView" role="tabpanel">
                        <form method="POST" action="app_process.php" id="loginForm" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="patient_login">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Username</label>
                                <input type="text" id="loginUsername" name="name" class="form-control" placeholder="Username" required pattern="[A-Za-z0-9 ]+" title="Letters, numbers, and spaces only. No symbols.">
                                <div class="username-validation default" id="loginUsernameFeedback">Letters, numbers, and spaces only. No symbols allowed.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required minlength="8">
                                <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                            </div>
                            <button type="submit" class="btn auth-submit-btn btn-login-custom w-100 fw-bold shadow-sm">Login Account</button>
                        </form>
                    </div>
                    
                    <div class="tab-pane fade" id="signupView" role="tabpanel">
                        <form method="POST" action="app_process.php" id="signupForm" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="patient_signup">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Username</label>
                                <input type="text" id="signupUsername" name="name" class="form-control" placeholder="Username" required pattern="[A-Za-z0-9 ]+" title="Letters, numbers, and spaces only. No symbols.">
                                <div class="username-validation default" id="signupUsernameFeedback">Letters, numbers, and spaces only. No symbols allowed.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Password</label>
                                <input type="password" id="signupPassword" name="password" class="form-control" placeholder="Password" required minlength="8">
                                <div class="password-requirements" id="passwordRequirements">
                                    <div class="requirement default" id="reqLength">
                                        <span class="icon">•</span>
                                        <span class="msg">At least 8 characters</span>
                                    </div>
                                    <div class="requirement default" id="reqLetter">
                                        <span class="icon">•</span>
                                        <span class="msg">Must have letters</span>
                                    </div>
                                    <div class="requirement default" id="reqNumber">
                                        <span class="icon">•</span>
                                        <span class="msg">Must have numbers</span>
                                    </div>
                                    <div class="requirement default" id="reqSymbol">
                                        <span class="icon">•</span>
                                        <span class="msg">Must have at least 1 symbol</span>
                                    </div>
                                    <div class="requirement default" id="reqCapital">
                                        <span class="icon">•</span>
                                        <span class="msg">Must have at least 1 capital letter</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary">Confirm Password</label>
                                <input type="password" id="signupConfirmPassword" name="confirm_password" class="form-control" placeholder="Retype Password" required>
                                <div id="passwordMismatchFeedback" class="text-danger small fw-bold mt-1 d-none">Passwords do not match!</div>
                            </div>    
                            <button type="submit" class="btn auth-submit-btn btn-signup-custom w-100 text-white fw-bold shadow-sm">Create Account Profile</button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <div class="modal fade" id="signOutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <h5 class="modal-title fw-bold text-dark">Sign Out</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-2 text-muted">
                    Are you sure you want to sign out?
                </div>
                <div class="modal-footer border-0 pb-4 px-4 pt-3 d-flex gap-2">
                    <button type="button" class="btn btn-light fw-semibold py-2 px-3 flex-grow-1 rounded-3 border text-secondary" data-bs-dismiss="modal">No</button>
                    <a href="app_process.php?action=logout" class="btn btn-danger fw-bold py-2 px-3 flex-grow-1 rounded-3 shadow-sm">Yes</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const signupForm = document.getElementById('signupForm');
            const loginForm = document.getElementById('loginForm');
            const password = document.getElementById('signupPassword');
            const confirmPassword = document.getElementById('signupConfirmPassword');
            const errorFeedback = document.getElementById('passwordMismatchFeedback');
            const forms = document.querySelectorAll('.needs-validation');

            // Username validation for signup
            const signupUsername = document.getElementById('signupUsername');
            const signupUsernameFeedback = document.getElementById('signupUsernameFeedback');

            // Username validation for login
            const loginUsername = document.getElementById('loginUsername');
            const loginUsernameFeedback = document.getElementById('loginUsernameFeedback');

            function validateUsername(input, feedbackElement) {
                const value = input.value;
                if (value === '') {
                    feedbackElement.className = 'username-validation default';
                    feedbackElement.textContent = 'Letters, numbers, and spaces only. No symbols allowed.';
                    input.classList.remove('is-valid-custom', 'is-invalid-custom');
                    return;
                }
                const hasSymbols = /[^A-Za-z0-9 ]/.test(value);
                if (hasSymbols) {
                    feedbackElement.className = 'username-validation invalid';
                    feedbackElement.textContent = '✗ No symbols allowed! Use only letters, numbers, and spaces.';
                    input.classList.remove('is-valid-custom');
                    input.classList.add('is-invalid-custom');
                } else {
                    feedbackElement.className = 'username-validation valid';
                    feedbackElement.textContent = '✓ Valid username';
                    input.classList.remove('is-invalid-custom');
                    input.classList.add('is-valid-custom');
                }
            }

            if (signupUsername) {
                signupUsername.addEventListener('input', function() {
                    validateUsername(this, signupUsernameFeedback);
                });
            }

            if (loginUsername) {
                loginUsername.addEventListener('input', function() {
                    validateUsername(this, loginUsernameFeedback);
                });
            }

            // Password validation with detailed messages
            function validatePassword(passwordValue) {
                const hasLetter = /[a-zA-Z]/.test(passwordValue);
                const hasNumber = /[0-9]/.test(passwordValue);
                const hasSymbol = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(passwordValue);
                const hasCapital = /[A-Z]/.test(passwordValue);
                const hasLength = passwordValue.length >= 8;

                const reqLength = document.getElementById('reqLength');
                const reqLetter = document.getElementById('reqLetter');
                const reqNumber = document.getElementById('reqNumber');
                const reqSymbol = document.getElementById('reqSymbol');
                const reqCapital = document.getElementById('reqCapital');

                // Update each requirement
                updateRequirement(reqLength, hasLength, 'At least 8 characters');
                updateRequirement(reqLetter, hasLetter, 'Must have letters');
                updateRequirement(reqNumber, hasNumber, 'Must have numbers');
                updateRequirement(reqSymbol, hasSymbol, 'Must have at least 1 symbol');
                updateRequirement(reqCapital, hasCapital, 'Must have at least 1 capital letter');

                // Check if all requirements are met
                const allValid = hasLength && hasLetter && hasNumber && hasSymbol && hasCapital;
                if (allValid && passwordValue.length > 0) {
                    password.classList.remove('is-invalid-custom');
                    password.classList.add('is-valid-custom');
                } else if (passwordValue.length > 0) {
                    password.classList.remove('is-valid-custom');
                    password.classList.add('is-invalid-custom');
                } else {
                    password.classList.remove('is-valid-custom', 'is-invalid-custom');
                }

                return allValid;
            }

            function updateRequirement(element, isValid, text) {
                const iconSpan = element.querySelector('.icon');
                const msgSpan = element.querySelector('.msg');
                if (isValid) {
                    element.className = 'requirement valid';
                    iconSpan.textContent = '✓';
                    msgSpan.textContent = text;
                } else {
                    element.className = 'requirement invalid';
                    iconSpan.textContent = '✗';
                    msgSpan.textContent = text;
                }
            }

            // Reset requirements to default when password is empty
            function resetPasswordRequirements() {
                const reqLength = document.getElementById('reqLength');
                const reqLetter = document.getElementById('reqLetter');
                const reqNumber = document.getElementById('reqNumber');
                const reqSymbol = document.getElementById('reqSymbol');
                const reqCapital = document.getElementById('reqCapital');
                
                const elements = [reqLength, reqLetter, reqNumber, reqSymbol, reqCapital];
                elements.forEach(el => {
                    if (el) {
                        el.className = 'requirement default';
                        el.querySelector('.icon').textContent = '•';
                    }
                });
            }

            if (password) {
                password.addEventListener('input', function() {
                    if (this.value === '') {
                        resetPasswordRequirements();
                        this.classList.remove('is-valid-custom', 'is-invalid-custom');
                    } else {
                        validatePassword(this.value);
                    }
                    verifyPasswordsMatch();
                });
            }

            function verifyPasswordsMatch() {
                if (!password || !confirmPassword) return;
                if (password.value !== confirmPassword.value && confirmPassword.value !== '') {
                    confirmPassword.setCustomValidity("Mismatch");
                    confirmPassword.classList.remove('is-valid-custom');
                    confirmPassword.classList.add('is-invalid-custom');
                    errorFeedback.classList.remove('d-none');
                } else if (password.value === confirmPassword.value && confirmPassword.value !== '') {
                    confirmPassword.setCustomValidity("");
                    confirmPassword.classList.remove('is-invalid-custom');
                    confirmPassword.classList.add('is-valid-custom');
                    errorFeedback.classList.add('d-none');
                } else {
                    confirmPassword.setCustomValidity("");
                    confirmPassword.classList.remove('is-invalid-custom', 'is-valid-custom');
                    errorFeedback.classList.add('d-none');
                }
            }

            if (confirmPassword) {
                confirmPassword.addEventListener('input', verifyPasswordsMatch);
            }

            // General form validation - only for signup fields
            document.querySelectorAll('#signupForm .form-control').forEach(input => {
                if (input.id !== 'signupPassword' && input.id !== 'signupConfirmPassword') {
                    input.addEventListener('input', function() {
                        if (this.checkValidity()) {
                            this.classList.remove('is-invalid-custom');
                            this.classList.add('is-valid-custom');
                        } else {
                            this.classList.remove('is-valid-custom');
                            this.classList.add('is-invalid-custom');
                        }
                    });
                }
            });

            // Login form validation
            if (loginForm) {
                loginForm.addEventListener('submit', function(event) {
                    // Let the browser handle validation
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        this.classList.add('was-validated');
                    }
                }, false);
            }

            // Signup form validation
            if (signupForm) {
                signupForm.addEventListener('submit', function(event) {
                    // Validate password requirements before submit
                    const passwordValue = password ? password.value : '';
                    const isPasswordValid = password ? validatePassword(passwordValue) : true;
                    
                    if (confirmPassword) {
                        verifyPasswordsMatch();
                    }
                    
                    if (!this.checkValidity() || !isPasswordValid) {
                        event.preventDefault();
                        event.stopPropagation();
                        
                        this.querySelectorAll('.form-control').forEach(input => {
                            if (input.checkValidity() && !input.classList.contains('is-invalid-custom')) {
                                input.classList.add('is-valid-custom');
                            } else if (!input.checkValidity()) {
                                input.classList.add('is-invalid-custom');
                            }
                        });
                    }
                    this.classList.add('was-validated');
                }, false);
            }
        });
    </script>
</body>
</html>