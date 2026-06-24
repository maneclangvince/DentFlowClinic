<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentFlow - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .portal-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-height: 320px;
        }
        .portal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
        .portal-card .card-body {
            padding: 2rem !important;
        }
        .portal-card .btn {
            padding: 14px !important;
            font-size: 18px !important;
        }
        .portal-card .icon-wrapper {
            margin-bottom: 1rem;
        }
        .portal-card .icon-wrapper svg {
            width: 72px;
            height: 72px;
        }
        .portal-card h2 {
            font-size: 24px !important;
            margin-bottom: 12px !important;
        }
        .portal-card p {
            font-size: 15px !important;
            margin-bottom: 24px !important;
            min-height: 44px;
        }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-dark">Welcome to <span class="text-info">DentFlow</span></h1>
            <p class="text-muted fs-5">Please choose your portal below to continue.</p>
        </div>

        <div class="row g-4 justify-content-center" style="max-width: 1100px; margin: 0 auto;">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-0 text-center h-100 portal-card bg-white">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-wrapper text-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                </svg>
                            </div>
                            <h2 class="h4 fw-bold text-dark mb-2">Patient Portal</h2>
                            <p class="text-muted">Book appointments, check your place in line, and message your clinic.</p>
                        </div>
                        <a href="patient_home.php" class="btn btn-info text-white fw-bold w-100 rounded-3 shadow-sm">Go to Patient UI</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-0 text-center h-100 portal-card bg-white">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="icon-wrapper text-dark">
                                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                                    <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.117.738 7.503 2.147 9.433.882 1.205 1.849 1.96 2.682 2.357.16.077.34.077.501 0 .833-.398 1.8-1.152 2.682-2.357 1.409-1.93 2.701-5.316 2.147-9.433a.488.488 0 0 0-.328-.39 61.44 61.44 0 0 0-2.837-.855C9.552 1.29 8.631 1.054 8 1.054c-.63 0-1.552.236-2.662.536zM8 0c.656 0 1.63.26 2.813.579a60.2 60.2 0 0 1 2.772.815c.428.13.731.472.78 1.074.554 4.346-.92 8.16-2.531 10.374a11.148 11.148 0 0 1-2.9 2.766a.874.874 0 0 1-.866 0 11.148 11.148 0 0 1-2.9-2.766C1.92 10.013.446 6.2 1 1.854c.048-.602.351-.944.78-1.074A60.2 60.2 0 0 1 5.188.58 1.55 1.55 0 0 1 8 0z"/>
                                    <path d="M9.5 6.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zM8 7.405c.238 0 .412.188.412.425v1.27c0 .237-.174.425-.412.425s-.412-.188-.412-.425v-1.27c0-.237.174-.425.412-.425z"/>
                                </svg>
                            </div>
                            <h2 class="h4 fw-bold text-dark mb-2">Dentist Portal</h2>
                            <p class="text-muted">Manage appointments, review queues, and view clinic operations.</p>
                        </div>
                        <a href="admin_login.php" class="btn btn-dark fw-bold w-100 rounded-3 shadow-sm">Go to Dentist UI</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>