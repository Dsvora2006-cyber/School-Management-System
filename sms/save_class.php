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

    // Check duplicate Class ID
    $check_stmt = $conn->prepare("SELECT id FROM classes WHERE class_id = ?");
    if ($check_stmt) {
        $check_stmt->bind_param("s", $class_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Class ID already exists. Please choose a unique ID.'
            ]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
    }

    // Insert Class Record
    $stmt = $conn->prepare("INSERT INTO classes (class_id, class_name, subject, standard, teacher_id, room_number, schedule) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssss", $class_id, $class_name, $subject, $standard, $teacher_id, $room_number, $schedule);
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
