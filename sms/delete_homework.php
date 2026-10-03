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
    $homework_id = isset($_POST['homework_id']) ? trim($_POST['homework_id']) : '';

    if (empty($homework_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Homework ID is required.'
        ]);
        exit;
    }

    // Delete the homework assignment (ensure it belongs to the logged-in teacher)
    $stmt = $conn->prepare("DELETE FROM homework WHERE homework_id = ? AND teacher_id = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $homework_id, $teacher_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Homework not found or you do not have permission to delete it.'
                ]);
            }
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
