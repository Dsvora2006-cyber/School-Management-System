<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please enter both your Teacher ID and password.'
        ]);
        exit;
    }

    // Query database for matching teacher by teacher_id or email (case-insensitive)
    $stmt = $conn->prepare("SELECT teacher_id, first_name, last_name, password FROM teachers WHERE LOWER(TRIM(teacher_id)) = LOWER(TRIM(?)) OR LOWER(TRIM(email)) = LOWER(TRIM(?))");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_teacher_id, $first_name, $last_name, $db_password);
            $stmt->fetch();

            if (trim($db_password) === $password || (function_exists('password_verify') && password_verify($password, $db_password))) {
                // Start secure session
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['teacher_logged_in'] = true;
                $_SESSION['teacher_id'] = $db_teacher_id;
                $_SESSION['teacher_name'] = $first_name . ' ' . $last_name;

                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Incorrect password'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid Teacher ID'
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: preparation failed.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
