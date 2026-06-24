<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in as admin
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'admin_dashboard.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: " . $redirect);
    exit;
}

// Store the page they were trying to access
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow Admin - Login</title>
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
        .nav-link.text-warning {
            color: #ffc107 !important;
        }
        @media (min-width: 992px) {
            nav {
                padding: 20px 100px !important;
            }
            .nav-link {
                padding: 6px 4px !important;
            }
            .nav-link:hover {
                border-bottom: 4px solid #0dcaf0;
                color: #0dcaf0 !important;
            }
            .nav-link.text-warning:hover {
                border-bottom: 4px solid #ffc107;
                color: #ffc107 !important;
            }
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
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
        .auth-submit-btn {
            font-size: 18px !important;
            height: 54px !important;
            border-radius: 8px !important;
            border: none !important;
            transition: background-color 0.3s ease;
        }
        .btn-login-custom {
            background-color: #212529 !important;
            color: #0dcaf0 !important;
        }
        .btn-login-custom:hover {
            background-color: #1a1d20 !important;
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
            .nav-link.text-warning {
                color: #ffc107 !important;
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
                    <a class="nav-link py-2 py-lg-0" href="admin_tracking.php">Tracking</a>
                    <a class="nav-link py-2 py-lg-0" href="admin_chat.php">Quick Chat</a>
                    <a class="nav-link py-2 py-lg-0" href="admin_dashboard.php">Dashboard</a>
                    <a class="nav-link text-warning fw-bold py-2 py-lg-0 ms-lg-3" href="admin_login.php">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="auth-screen-wrapper px-3">
        <div class="auth-container-box">
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger text-center fw-bold shadow-sm small mb-3">
                    Error: Invalid credentials. Please try again.
                </div>
            <?php endif; ?>
            
            <div class="card border-0 custom-auth-card rounded-4 overflow-hidden">
                <div class="card-header bg-white pt-4 pb-3 border-0 text-center">
                    <h3 class="fw-bold mb-0" style="color: #212529;">Admin Login</h3>
                    <p class="text-muted small mt-1 mb-0">Sign in to manage the DentFlow backend.</p>
                </div>
                
                <div class="card-body p-4 pt-2">
                    <form method="POST" action="app_process.php" class="needs-validation" novalidate>
                        <input type="hidden" name="action" value="admin_login">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Staff Code</label>
                            <input type="text" name="user_code" class="form-control" placeholder="Enter staff code" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn auth-submit-btn btn-login-custom w-100 fw-bold shadow-sm">Login</button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll('.needs-validation');
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.checkValidity()) {
                        this.classList.remove('is-invalid-custom');
                        this.classList.add('is-valid-custom');
                    } else {
                        this.classList.remove('is-valid-custom');
                        this.classList.add('is-invalid-custom');
                    }
                });
            });
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        form.querySelectorAll('.form-control').forEach(input => {
                            if (input.checkValidity() && !input.classList.contains('is-invalid-custom')) {
                                input.classList.add('is-valid-custom');
                            } else if (!input.checkValidity()) {
                                input.classList.add('is-invalid-custom');
                            }
                        });
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
</body>
</html>