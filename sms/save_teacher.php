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

    // Check duplicate Teacher ID
    $check_stmt = $conn->prepare("SELECT id FROM teachers WHERE teacher_id = ?");
    if ($check_stmt) {
        $check_stmt->bind_param("s", $teacher_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Teacher ID already exists. Please choose a unique ID.'
            ]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
    }

    // Insert Teacher Record (status removed!)
    $stmt = $conn->prepare("INSERT INTO teachers (teacher_id, first_name, last_name, email, mobile_number, specialization, standard) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssssss", $teacher_id, $first_name, $last_name, $email, $mobile_number, $specialization, $standard);
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
