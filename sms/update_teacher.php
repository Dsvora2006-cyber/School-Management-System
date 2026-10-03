<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect inputs
    $teacher_id = isset($_POST['teacher_id']) ? trim($_POST['teacher_id']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mobile_number = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';
    $specialization = isset($_POST['specialization']) ? trim($_POST['specialization']) : '';
    $standard = isset($_POST['standard']) ? trim($_POST['standard']) : '';

    // Verify required inputs
    if (empty($teacher_id) || empty($first_name) || empty($last_name) || empty($email) || empty($specialization) || empty($standard)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please fill in all required fields.'
        ]);
        exit;
    }

    // Update Teacher Record (status removed!)
    $stmt = $conn->prepare("UPDATE teachers SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, specialization = ?, standard = ? WHERE teacher_id = ?");
    if ($stmt) {
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $mobile_number, $specialization, $standard, $teacher_id);
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
