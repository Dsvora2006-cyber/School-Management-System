<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $teacher_id = isset($_GET['teacher_id']) ? trim($_GET['teacher_id']) : '';

    if (empty($teacher_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Teacher ID is required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $teacher = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => $teacher
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Teacher record not found.'
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
