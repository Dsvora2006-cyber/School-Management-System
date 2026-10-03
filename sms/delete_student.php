<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';

    if (empty($student_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Student ID is required.'
        ]);
        exit;
    }

    // Delete record from database
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $student_id);
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
            'error' => 'Database statement preparation failed: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
