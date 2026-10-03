<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $student_id = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';

    if (empty($student_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Student ID is required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $student = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => $student
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Student record not found.'
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database query preparation failed.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
