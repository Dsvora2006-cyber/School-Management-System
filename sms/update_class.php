<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect inputs
    $class_id = isset($_POST['class_id']) ? trim($_POST['class_id']) : '';
    $class_name = isset($_POST['class_name']) ? trim($_POST['class_name']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $standard = isset($_POST['standard']) ? trim($_POST['standard']) : '';
    $teacher_id = isset($_POST['teacher_id']) ? trim($_POST['teacher_id']) : null;
    $room_number = isset($_POST['room_number']) ? trim($_POST['room_number']) : '';
    $schedule = isset($_POST['schedule']) ? trim($_POST['schedule']) : '';

    // If teacher_id is empty string, set to null
    if ($teacher_id === '') {
        $teacher_id = null;
    }

    // Verify required inputs
    if (empty($class_id) || empty($class_name) || empty($subject) || empty($standard) || empty($room_number) || empty($schedule)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please fill in all required fields.'
        ]);
        exit;
    }

    // Update Class Record
    $stmt = $conn->prepare("UPDATE classes SET class_name = ?, subject = ?, standard = ?, teacher_id = ?, room_number = ?, schedule = ? WHERE class_id = ?");
    if ($stmt) {
        $stmt->bind_param("sssssss", $class_name, $subject, $standard, $teacher_id, $room_number, $schedule, $class_id);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Database execution failed: ' . $stmt->error
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database preparation failed: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
