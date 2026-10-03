<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? trim($_POST['action']) : 'verify';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';

    if (empty($username)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please enter your Username or ID.'
        ]);
        exit;
    }

    if ($action === 'update') {
        if (empty($new_password)) {
            echo json_encode([
                'success' => false,
                'error' => 'Please enter a new password.'
            ]);
            exit;
        }

        // 1. Check & update in Students
        $stmt_s = $conn->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
        if ($stmt_s) {
            $stmt_s->bind_param("ss", $username, $username);
            $stmt_s->execute();
            $stmt_s->store_result();
            if ($stmt_s->num_rows > 0) {
                $stmt_s->close();
                $up = $conn->prepare("UPDATE students SET password = ? WHERE student_id = ? OR email = ?");
                $up->bind_param("sss", $new_password, $username, $username);
                if ($up->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to update database.']);
                }
                $up->close();
                exit;
            }
            $stmt_s->close();
        }

        // 2. Check & update in Teachers
        $stmt_t = $conn->prepare("SELECT id FROM teachers WHERE teacher_id = ? OR email = ?");
        if ($stmt_t) {
            $stmt_t->bind_param("ss", $username, $username);
            $stmt_t->execute();
            $stmt_t->store_result();
            if ($stmt_t->num_rows > 0) {
                $stmt_t->close();
                $up = $conn->prepare("UPDATE teachers SET password = ? WHERE teacher_id = ? OR email = ?");
                $up->bind_param("sss", $new_password, $username, $username);
                if ($up->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Password updated successfully!']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to update database.']);
                }
                $up->close();
                exit;
            }
            $stmt_t->close();
        }

        // 3. Check & update in Admins
        $stmt_a = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        if ($stmt_a) {
            $stmt_a->bind_param("ss", $username, $username);
            $stmt_a->execute();
            $stmt_a->store_result();
            if ($stmt_a->num_rows > 0) {
                $stmt_a->close();
                $up = $conn->prepare("UPDATE admins SET password = ? WHERE username = ? OR email = ?");
                $up->bind_param("sss", $new_password, $username, $username);
                if ($up->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Admin password updated successfully!']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to update database.']);
                }
                $up->close();
                exit;
            }
            $stmt_a->close();
        }

        $admin_accounts = ['darvora575@gmail.com', 'harshil@gmail.com', 'jeel@gmail.com', 'darvora575', 'harshil', 'jeel'];
        if (in_array(strtolower($username), $admin_accounts)) {
            echo json_encode(['success' => true, 'message' => 'Admin password reset successfully!']);
            exit;
        }

        echo json_encode(['success' => false, 'error' => 'Account not found.']);
        exit;
    }

    // Default Action: 'verify'
    // 1. Check if identifier belongs to a Student
    $stmt_student = $conn->prepare("SELECT first_name, last_name FROM students WHERE student_id = ? OR email = ?");
    if ($stmt_student) {
        $stmt_student->bind_param("ss", $username, $username);
        $stmt_student->execute();
        $stmt_student->store_result();

        if ($stmt_student->num_rows > 0) {
            $stmt_student->bind_result($first_name, $last_name);
            $stmt_student->fetch();
            $stmt_student->close();

            echo json_encode([
                'success' => true,
                'user_type' => 'Student',
                'user_name' => $first_name . ' ' . $last_name,
                'message' => "Account ($first_name $last_name) verified. Please enter your new password below."
            ]);
            exit;
        }
        $stmt_student->close();
    }

    // 2. Check if identifier belongs to a Teacher
    $stmt_teacher = $conn->prepare("SELECT first_name, last_name FROM teachers WHERE teacher_id = ? OR email = ?");
    if ($stmt_teacher) {
        $stmt_teacher->bind_param("ss", $username, $username);
        $stmt_teacher->execute();
        $stmt_teacher->store_result();

        if ($stmt_teacher->num_rows > 0) {
            $stmt_teacher->bind_result($first_name, $last_name);
            $stmt_teacher->fetch();
            $stmt_teacher->close();

            echo json_encode([
                'success' => true,
                'user_type' => 'Teacher',
                'user_name' => $first_name . ' ' . $last_name,
                'message' => "Account ($first_name $last_name) verified. Please enter your new password below."
            ]);
            exit;
        }
        $stmt_teacher->close();
    }

    // 3. Check if Administrator
    $admin_accounts = [
        'darvora575@gmail.com' => 'Darvora',
        'harshil@gmail.com' => 'Harshil',
        'jeel@gmail.com' => 'Jeel'
    ];

    if (array_key_exists(strtolower($username), $admin_accounts)) {
        $admin_name = $admin_accounts[strtolower($username)];
        echo json_encode([
            'success' => true,
            'user_type' => 'Admin',
            'user_name' => "Administrator ($admin_name)",
            'message' => "Administrator ($admin_name) account verified. Please enter your new password below."
        ]);
        exit;
    }

    // If not found anywhere
    echo json_encode([
        'success' => false,
        'error' => 'No account found matching this Username / ID. Please check and try again.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
