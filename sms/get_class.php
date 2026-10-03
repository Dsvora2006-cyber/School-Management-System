<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $class_id = isset($_GET['class_id']) ? trim($_GET['class_id']) : '';

    if (empty($class_id)) {
        echo json_encode([
            'success' => false,
            'error' => 'Class ID is required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $class_data = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => $class_data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Class record not found.'
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
