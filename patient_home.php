<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if patient is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'patient') {
    $_SESSION['redirect_after_login'] = 'patient_home.php';
    header("Location: patient_auth.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow - Home</title>
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
        figure {
            text-align: center;
            display: flex;
            gap: 160px;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            min-height: 100vh;
            padding: 120px 20px 40px;
            box-sizing: border-box;
            max-width: 1200px;
        }
        .big-img {
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            height: auto;
            aspect-ratio: 1 / 1;
            background-color: #035270;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            object-fit: cover;
        }
        .card {
            display: flex;
            flex-direction: column;
            gap: 40px;
            background: transparent;
            border: none;
            text-align: left;
        }
        .hero-title {
            font-size: 48px;
            color: #035270;
        }
        .hero-subtitle {
            font-size: 20px;
            color: #555;
            line-height: 1.6;
            text-align: center;
        }
        .button {
            border-radius: 20px;
            background-color: #035270;
            height: 60px;
            width: 300px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }
        .button a {
            color: white;
            text-decoration: none;
            font-size: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            border-radius: 20px;
        }
        .button:hover {
            background-color: #023d54;
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
            figure {
                flex-direction: column;
                gap: 40px;
                padding-top: 110px;
            }
            .card {
                align-items: center;
                text-align: center;
                gap: 30px;
            }
            .hero-title {
                font-size: 32px;
            }
            .hero-subtitle {
                font-size: 16px;
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
                    <a class="nav-link active py-2 py-lg-0" href="patient_home.php">Home</a>
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

    <figure>
        <img src="images/tooth.webp" alt="DentFlow" class="big-img">
        <div class="card">
            <div class="hero">
                <h1 class="fw-bold border-bottom pb-2 mb-3 hero-title text-center">Welcome to DentFlow Patient Portal</h1>
                <figcaption class="hero-subtitle">Your all-in-one 24/7 platform for effortless dental checkups, instant team consultation, and treatment tracking.</figcaption>
            </div>
            <div class="button">
                <a href="patient_appointment.php">Book an Appointment</a>
            </div>
        </div>
    </figure>

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