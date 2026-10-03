<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify Teacher Session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access. Please log in.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = $_SESSION['teacher_id'];
    $class_id = isset($_POST['class_id']) ? trim($_POST['class_id']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $due_date = isset($_POST['due_date']) ? trim($_POST['due_date']) : '';

    // Verify required inputs
    if (empty($class_id) || empty($title) || empty($description) || empty($due_date)) {
        echo json_encode([
            'success' => false,
            'error' => 'All fields (Class, Title, Description, Due Date) are required.'
        ]);
        exit;
    }

    // Verify teacher teaches this class and fetch the class subject
    $stmt_class = $conn->prepare("SELECT subject FROM classes WHERE class_id = ? AND teacher_id = ?");
    if (!$stmt_class) {
        echo json_encode([
            'success' => false,
            'error' => 'Database preparation error: ' . $conn->error
        ]);
        exit;
    }
    
    $stmt_class->bind_param("ss", $class_id, $teacher_id);
    $stmt_class->execute();
    $res_class = $stmt_class->get_result();
    
    if ($res_class->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'You are not assigned to instruct the selected class.'
        ]);
        $stmt_class->close();
        exit;
    }
    
    $class_data = $res_class->fetch_assoc();
    $subject = $class_data['subject'];
    $stmt_class->close();

    // Generate unique Homework ID (HW-YYYY-XXXX)
    $homework_id = '';
    $is_unique = false;
    $attempts = 0;
    while (!$is_unique && $attempts < 10) {
        $rand_num = sprintf('%04d', rand(1, 9999));
        $temp_id = 'HW-' . date('Y') . '-' . $rand_num;
        
        $check_stmt = $conn->prepare("SELECT id FROM homework WHERE homework_id = ?");
        $check_stmt->bind_param("s", $temp_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows === 0) {
            $homework_id = $temp_id;
            $is_unique = true;
        }
        $check_stmt->close();
        $attempts++;
    }

    if (empty($homework_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to generate a unique Homework ID. Please try again.'
        ]);
        exit;
    }

    // Insert Homework Record
    $stmt = $conn->prepare("INSERT INTO homework (homework_id, class_id, teacher_id, subject, title, description, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssss", $homework_id, $class_id, $teacher_id, $subject, $title, $description, $due_date);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'homework_id' => $homework_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to insert homework record: ' . $stmt->error
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
