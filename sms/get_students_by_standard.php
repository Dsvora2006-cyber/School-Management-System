<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $standard = isset($_GET['standard']) ? trim($_GET['standard']) : '';

    if (empty($standard)) {
        echo json_encode([
            'success' => false,
            'error' => 'Standard parameter is required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM `students` WHERE `grade` = ? ORDER BY `first_name` ASC, `last_name` ASC");
    if ($stmt) {
        $stmt->bind_param("s", $standard);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'student_id' => $row['student_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'status' => $row['status']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $students
        ]);
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
