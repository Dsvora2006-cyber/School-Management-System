<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = isset($_POST['teacher_id']) ? trim($_POST['teacher_id']) : '';

    if (empty($teacher_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Teacher ID is required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM teachers WHERE teacher_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $teacher_id);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Database deletion execution failed.'
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database statement preparation failed.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
