<?php
session_start();

// ===== DATABASE CONFIGURATION =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dentflow');

function getDB() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

function loadSessionFromDB() {
    $conn = getDB();
    
    // Load dental services
    $result = $conn->query("SELECT * FROM dental_services");
    $_SESSION['dental_services'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['dental_services'][] = $row;
    }
    
    // Load inventory
    $result = $conn->query("SELECT * FROM inventory");
    $_SESSION['inventory'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['inventory'][] = $row;
    }
    
    // Load appointments
    $result = $conn->query("SELECT * FROM appointments ORDER BY id DESC");
    $_SESSION['appointments'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['appointments'][] = $row;
    }
    
    // Load booking history
    $result = $conn->query("SELECT * FROM booking_history ORDER BY id DESC");
    $_SESSION['booking_history'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['booking_history'][] = $row;
    }
    
    // Load chat messages
    $result = $conn->query("SELECT * FROM chat_messages ORDER BY id ASC");
    $_SESSION['chats'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['chats'][] = $row;
    }
    
    // Load patient records
    $result = $conn->query("SELECT id, name, email, created_at FROM patients");
    $_SESSION['patient_records'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['patient_records'][] = $row;
    }
    
    $conn->close();
}

// ===== SESSION INITIALIZATION =====
if (!isset($_SESSION['dentist_status'])) {
    $_SESSION['dentist_status'] = "Open";
}
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = null;
}
if (!isset($_SESSION['user_display_name'])) {
    $_SESSION['user_display_name'] = "";
}
if (!isset($_SESSION['user_email'])) {
    $_SESSION['user_email'] = "";
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
}

// Load data from database to session
loadSessionFromDB();

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

function verify_no_symbols($string) {
    $clean_string = str_replace(' ', '', $string);
    return ctype_alnum($clean_string);
}
?>