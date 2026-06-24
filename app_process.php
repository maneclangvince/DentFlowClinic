<?php
include('config.php');

$action = isset($_REQUEST['action']) ? sanitize_input($_REQUEST['action']) : '';

// Add debug for modify_item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'modify_item') {
    error_log("=== MODIFY ITEM DEBUG ===");
    error_log("POST data: " . print_r($_POST, true));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($action === 'patient_signup') {
        $name = sanitize_input($_POST['name']);
        $email = sanitize_input($_POST['email']);
        $pass = $_POST['password'];
        $conf = $_POST['confirm_password'];
        
        // Only check username for symbols, password can have symbols
        if (!verify_no_symbols($name)) {
            $_SESSION['error_message'] = 'symbols';
            header("Location: patient_auth.php");
            exit;
        }
        
        $conn = getDB();
        
        // Check if account already exists
        $stmt = $conn->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $conn->close();
            header("Location: patient_auth.php?error=exists");
            exit;
        }
        $stmt->close();
        
        if ($pass === $conf && !empty($name) && !empty($email)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO patients (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $name_formatted = ucwords(strtolower($name));
            $stmt->bind_param("sss", $name_formatted, $email, $pass);
            $stmt->execute();
            $patient_id = $conn->insert_id;
            $stmt->close();
            
            $_SESSION['user_role'] = 'patient';
            $_SESSION['user_display_name'] = $name_formatted;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $patient_id;
            
            $conn->close();
            
            // Check if there's a redirect URL
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'patient_home.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        }
        $conn->close();
    }

    if ($action === 'patient_login') {
        $name = sanitize_input($_POST['name']);
        $email = sanitize_input($_POST['email']);
        $pass = $_POST['password'];
        
        // Only check username for symbols, password can have symbols
        if (!verify_no_symbols($name)) {
            $_SESSION['error_message'] = 'symbols';
            header("Location: patient_auth.php");
            exit;
        }
        
        $conn = getDB();
        
        // Check if user exists with matching password
        $stmt = $conn->prepare("SELECT id, name, email, password FROM patients WHERE LOWER(email) = LOWER(?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $user_exists = false;
        $user_data = null;
        
        while ($row = $result->fetch_assoc()) {
            if (strtolower($row['name']) === strtolower($name) && $row['password'] === $pass) {
                $user_exists = true;
                $user_data = $row;
                break;
            }
        }
        $stmt->close();
        $conn->close();
        
        if (!empty($name) && !empty($email) && !empty($pass) && $user_exists) {
            $_SESSION['user_role'] = 'patient';
            $_SESSION['user_display_name'] = $user_data['name'];
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $user_data['id'];
            
            // Check if there's a redirect URL
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'patient_home.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        } else {
            header("Location: patient_auth.php?error=invalid");
            exit;
        }
    }

    // ========== ADMIN LOGIN (replaces dentist_login) ==========
    if ($action === 'admin_login') {
        $user_code = sanitize_input($_POST['user_code']);
        $password = $_POST['password'];
        
        if (strcmp($user_code, '#admin') === 0 && $password === '123') {
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_display_name'] = "Admin";
            
            // Check if there's a redirect URL
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'admin_dashboard.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        } else {
            header("Location: admin_login.php?error=auth");
            exit;
        }
    }

    if ($action === 'process_appointment') {
        $last_name = sanitize_input($_POST['last_name'] ?? '');
        $first_name = sanitize_input($_POST['first_name'] ?? '');
        $middle_name = sanitize_input($_POST['middle_name'] ?? '');
        $suffix = sanitize_input($_POST['suffix'] ?? '');
        $email = sanitize_input($_POST['email']);
        $contact = sanitize_input($_POST['contact']);
        $date = sanitize_input($_POST['appt_date']);
        $time = sanitize_input($_POST['appt_time']);
        $age_group = sanitize_input($_POST['age_group']);
        $payment = sanitize_input($_POST['payment_method']);
        $urgency = sanitize_input($_POST['urgency_status']);
        $msg = sanitize_input($_POST['message']);
        $service = sanitize_input($_POST['service']);
        $service_price = floatval($_POST['service_price']);
        
        $med_alerts = isset($_POST['history']) ? implode(', ', $_POST['history']) : 'No input entries';
        
        $full_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name . ' ' . $suffix);
        if (empty($full_name)) {
            $full_name = $_SESSION['user_display_name'] ?? 'Patient';
        }
        
        $target_status = 'Pending';
        if ($urgency === 'Urgent') {
            $target_status = 'Operational';
        }
        
        $conn = getDB();
        
        // Get patient_id
        $patient_id = $_SESSION['user_id'] ?? null;
        if (!$patient_id) {
            // Try to find patient by email
            $stmt = $conn->prepare("SELECT id FROM patients WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $patient_id = $row['id'];
            }
            $stmt->close();
        }
        
        // Insert appointment
        $stmt = $conn->prepare("INSERT INTO appointments 
            (patient_id, first_name, middle_name, last_name, suffix, email, contact, appt_date, appt_time, age_group, payment_method, urgency, medical_history, message, service, service_price, status, booked_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("issssssssssssssds", 
            $patient_id, $first_name, $middle_name, $last_name, $suffix, $email, $contact, 
            $date, $time, $age_group, $payment, $urgency, $med_alerts, $msg, $service, $service_price, $target_status
        );
        $stmt->execute();
        $appointment_id = $conn->insert_id;
        $stmt->close();
        
        // Insert into booking history
        $stmt = $conn->prepare("INSERT INTO booking_history 
            (appointment_id, patient_id, first_name, middle_name, last_name, suffix, email, contact, appt_date, appt_time, age_group, payment_method, urgency, medical_history, message, service, service_price, status, booked_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissssssssssssssds", 
            $appointment_id, $patient_id, $first_name, $middle_name, $last_name, $suffix, $email, $contact, 
            $date, $time, $age_group, $payment, $urgency, $med_alerts, $msg, $service, $service_price, $target_status
        );
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: patient_booking_history.php");
        exit;
    }

    if ($action === 'patient_send_message') {
        $text = sanitize_input($_POST['message_text']);
        if (!empty($text)) {
            $conn = getDB();
            $stmt = $conn->prepare("INSERT INTO chat_messages (sender, sender_email, sender_name, message_text, timestamp) VALUES ('patient', ?, ?, ?, NOW())");
            $sender_email = $_SESSION['user_email'];
            $sender_name = $_SESSION['user_display_name'];
            $stmt->bind_param("sss", $sender_email, $sender_name, $text);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            // Reload session data
            loadSessionFromDB();
        }
        header("Location: patient_chat.php");
        exit;
    }

    // ========== ADMIN SEND MESSAGE (replaces dentist_send_message) ==========
    if ($action === 'admin_send_message') {
        $text = sanitize_input($_POST['message_text']);
        $recipient = sanitize_input($_POST['recipient_email']);
        if (!empty($text)) {
            $conn = getDB();
            $stmt = $conn->prepare("INSERT INTO chat_messages (sender, sender_email, sender_name, recipient, message_text, timestamp) VALUES ('admin', 'admin@dentflow.com', 'Admin', ?, ?, NOW())");
            $stmt->bind_param("ss", $recipient, $text);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            // Reload session data
            loadSessionFromDB();
        }
        header("Location: admin_chat.php");
        exit;
    }

    if ($action === 'update_booking') {
        $id = intval($_POST['id']);
        $contact = sanitize_input($_POST['contact']);
        $date = sanitize_input($_POST['appt_date']);
        $time = sanitize_input($_POST['appt_time']);
        $age_group = sanitize_input($_POST['age_group']);
        $payment = sanitize_input($_POST['payment_method']);
        $urgency = sanitize_input($_POST['urgency_status']);
        $msg = sanitize_input($_POST['message']);
        $service = sanitize_input($_POST['service']);
        $service_price = floatval($_POST['service_price']);
        
        $med_alerts = isset($_POST['history']) ? implode(', ', $_POST['history']) : 'No input entries';
        
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET contact = ?, appt_date = ?, appt_time = ?, age_group = ?, payment_method = ?, urgency = ?, medical_history = ?, message = ?, service = ?, service_price = ? WHERE id = ?");
        $stmt->bind_param("sssssssssdi", $contact, $date, $time, $age_group, $payment, $urgency, $med_alerts, $msg, $service, $service_price, $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET contact = ?, appt_date = ?, appt_time = ?, age_group = ?, payment_method = ?, urgency = ?, medical_history = ?, message = ?, service = ?, service_price = ? WHERE appointment_id = ?");
        $stmt->bind_param("sssssssssdi", $contact, $date, $time, $age_group, $payment, $urgency, $med_alerts, $msg, $service, $service_price, $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: patient_booking_history.php");
        exit;
    }

    if ($action === 'toggle_clinic') {
        $_SESSION['dentist_status'] = sanitize_input($_POST['is_open']);
        header("Location: admin_dashboard.php");
        exit;
    }

    if ($action === 'process_payment') {
        $id = intval($_POST['id']);
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET payment_status = 'Paid' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET payment_status = 'Paid' WHERE appointment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_dashboard.php");
        exit;
    }

    // Unprocess payment - revert paid status back to unpaid
    if ($action === 'unprocess_payment') {
        $id = intval($_POST['id']);
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET payment_status = NULL WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET payment_status = NULL WHERE appointment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_dashboard.php");
        exit;
    }

    // MODIFY ITEM - FIXED
    if ($action === 'modify_item') {
        $id = intval($_POST['id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);
        
        if ($id == 0) {
            header("Location: admin_dashboard.php?error=invalid_id");
            exit;
        }
        
        $conn = getDB();
        
        // Get the existing item first to get low_stock_limit
        $stmt = $conn->prepare("SELECT low_stock_limit FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $low_limit = $row['low_stock_limit'] ?? 10;
        $stmt->close();
        
        // Determine status based on quantity
        if ($quantity == 0) {
            $status = 'Out of Stock';
        } elseif ($quantity <= $low_limit) {
            $status = 'Low Stock';
        } else {
            $status = 'In Stock';
        }
        
        // Update inventory
        $stmt = $conn->prepare("UPDATE inventory SET quantity = ?, price = ?, status = ? WHERE id = ?");
        $stmt->bind_param("idsi", $quantity, $price, $status, $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            // Reload session data
            loadSessionFromDB();
            header("Location: admin_dashboard.php?success=updated");
            exit;
        } else {
            $error = $stmt->error;
            $stmt->close();
            $conn->close();
            header("Location: admin_dashboard.php?error=" . urlencode($error));
            exit;
        }
    }

    if ($action === 'add_inventory_item') {
        $item = sanitize_input($_POST['item']);
        
        if (empty($item)) {
            header("Location: admin_dashboard.php?error=empty_item");
            exit;
        }
        
        $conn = getDB();
        $stmt = $conn->prepare("INSERT INTO inventory (item, status, price, quantity, low_stock_limit) VALUES (?, 'Out of Stock', 0.00, 0, 10)");
        $stmt->bind_param("s", $item);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_dashboard.php?success=added");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // ========== LOGOUT - Redirect to patient_auth.php ==========
    if ($action === 'logout') {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Redirect to patient login page
        header("Location: patient_auth.php");
        exit;
    }

    if ($action === 'set_status') {
        $_SESSION['dentist_status'] = $_GET['status'] === 'Open' ? 'Open' : 'Closed';
        header("Location: admin_tracking.php");
        exit;
    }

    if ($action === 'accept_appt') {
        $id = intval($_GET['id']);
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Operational' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET status = 'Operational' WHERE appointment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_tracking.php");
        exit;
    }

    if ($action === 'decline_appt') {
        $id = intval($_GET['id']);
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Declined' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET status = 'Declined' WHERE appointment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_tracking.php");
        exit;
    }

    if ($action === 'done_appt') {
        $id = intval($_GET['id']);
        date_default_timezone_set('Asia/Manila');
        $today_date = date('Y-m-d');
        
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Completed', completed_date = ? WHERE id = ?");
        $stmt->bind_param("si", $today_date, $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET status = 'Completed', completed_date = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $today_date, $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_tracking.php");
        exit;
    }

    if ($action === 'cancel_appt') {
        $id = intval($_GET['id']);
        $conn = getDB();
        
        // Update appointments
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Update booking_history
        $stmt = $conn->prepare("UPDATE booking_history SET status = 'Cancelled' WHERE appointment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: admin_tracking.php");
        exit;
    }

    if ($action === 'delete_booking') {
        $id = intval($_GET['id']);
        $conn = getDB();
        
        // Delete from appointments
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        
        // Delete from booking_history
        $stmt = $conn->prepare("DELETE FROM booking_history WHERE appointment_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: patient_booking_history.php");
        exit;
    }

    if ($action === 'delete_inventory_item') {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id == 0) {
            header("Location: admin_dashboard.php?error=invalid_id");
            exit;
        }
        
        $conn = getDB();
        $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            // Reload session data
            loadSessionFromDB();
            header("Location: admin_dashboard.php?success=deleted");
            exit;
        } else {
            $error = $stmt->error;
            $stmt->close();
            $conn->close();
            header("Location: admin_dashboard.php?error=" . urlencode($error));
            exit;
        }
    }

    // ========== CLEAR CHAT - Clear only selected patient (for admin) ==========
    if ($action === 'clear_chat') {
        $patient_email = isset($_GET['patient']) ? sanitize_input($_GET['patient']) : '';
        
        $conn = getDB();
        
        if (!empty($patient_email)) {
            // Delete messages from this patient AND admin replies to this patient
            $stmt = $conn->prepare("DELETE FROM chat_messages WHERE (sender = 'patient' AND sender_email = ?) OR (sender = 'admin' AND recipient = ?)");
            $stmt->bind_param("ss", $patient_email, $patient_email);
            $stmt->execute();
            $stmt->close();
        } else {
            // Fallback: delete all if no patient specified
            $conn->query("DELETE FROM chat_messages");
        }
        
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        // Redirect back to admin_chat.php with the patient parameter
        if (!empty($patient_email)) {
            header("Location: admin_chat.php?patient=" . urlencode($patient_email));
        } else {
            header("Location: admin_chat.php");
        }
        exit;
    }

    // ========== CLEAR PATIENT CHAT - Clear only current patient's chat (for patient) ==========
    if ($action === 'clear_patient_chat') {
        $patient_email = isset($_GET['patient']) ? sanitize_input($_GET['patient']) : '';
        
        if (!empty($patient_email)) {
            $conn = getDB();
            
            // Delete messages from this patient AND admin replies to this patient
            $stmt = $conn->prepare("DELETE FROM chat_messages WHERE (sender = 'patient' AND sender_email = ?) OR (sender = 'admin' AND recipient = ?)");
            $stmt->bind_param("ss", $patient_email, $patient_email);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            
            // Reload session data
            loadSessionFromDB();
            
            // Redirect back to patient_chat.php
            header("Location: patient_chat.php");
            exit;
        } else {
            header("Location: patient_chat.php");
            exit;
        }
    }

    // ========== CLEAR OPERATIONS ==========
    if ($action === 'clear_appointments') {
        $conn = getDB();
        $conn->query("DELETE FROM appointments");
        $conn->query("DELETE FROM booking_history");
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: operations.php");
        exit;
    }

    if ($action === 'clear_booking_history') {
        $conn = getDB();
        $conn->query("DELETE FROM booking_history");
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: operations.php");
        exit;
    }

    if ($action === 'clear_chat_history') {
        $conn = getDB();
        $conn->query("DELETE FROM chat_messages");
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: operations.php");
        exit;
    }

    if ($action === 'clear_patients') {
        $conn = getDB();
        $conn->query("DELETE FROM patients");
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: operations.php");
        exit;
    }

    if ($action === 'clear_inventory') {
        $conn = getDB();
        $conn->query("DELETE FROM inventory");
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: operations.php");
        exit;
    }

    if ($action === 'clear_everything') {
        $conn = getDB();
        $conn->query("DELETE FROM appointments");
        $conn->query("DELETE FROM booking_history");
        $conn->query("DELETE FROM chat_messages");
        $conn->query("DELETE FROM patients");
        $conn->query("DELETE FROM inventory");
        $conn->close();
        
        // Reload session data
        loadSessionFromDB();
        
        header("Location: operations.php");
        exit;
    }
}
?>