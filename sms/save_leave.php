<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify Student Session
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access. Please log in.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'];
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';

    // Verify required inputs
    if (empty($subject) || empty($reason) || empty($start_date) || empty($end_date)) {
        echo json_encode([
            'success' => false,
            'error' => 'All fields (Subject, Reason, Start Date, End Date) are required.'
        ]);
        exit;
    }

    // Verify date validation
    $today = date('Y-m-d');
    if ($start_date < $today) {
        echo json_encode([
            'success' => false,
            'error' => 'Start date cannot be in the past.'
        ]);
        exit;
    }

    if ($end_date < $start_date) {
        echo json_encode([
            'success' => false,
            'error' => 'End date cannot be earlier than start date.'
        ]);
        exit;
    }

    // Generate unique Leave ID (LV-YYYY-XXXX)
    $leave_id = '';
    $is_unique = false;
    $attempts = 0;
    while (!$is_unique && $attempts < 10) {
        $rand_num = sprintf('%04d', rand(1, 9999));
        $temp_id = 'LV-' . date('Y') . '-' . $rand_num;
        
        $check_stmt = $conn->prepare("SELECT id FROM leaves WHERE leave_id = ?");
        $check_stmt->bind_param("s", $temp_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows === 0) {
            $leave_id = $temp_id;
            $is_unique = true;
        }
        $check_stmt->close();
        $attempts++;
    }

    if (empty($leave_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to generate a unique Leave ID. Please try again.'
        ]);
        exit;
    }

    // Insert Leave Request (Default status: Pending)
    $stmt = $conn->prepare("INSERT INTO leaves (leave_id, student_id, subject, reason, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    if ($stmt) {
        $stmt->bind_param("ssssss", $leave_id, $student_id, $subject, $reason, $start_date, $end_date);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'leave_id' => $leave_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to submit leave request: ' . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database query preparation failed: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
