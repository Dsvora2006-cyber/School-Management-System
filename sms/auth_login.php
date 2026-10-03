<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please enter both your Username / ID / Email and password.'
        ]);
        exit;
    }

    $lower_u = strtolower($username);

    // -------------------------------------------------------------------------
    // 1. Check Predefined Static Admins
    // -------------------------------------------------------------------------
    $static_admins = [
        'harshil@gmail.com'    => ['pass' => '123', 'name' => 'Harshil', 'username' => 'harshil', 'role' => 'System Admin'],
        'harshil'              => ['pass' => '123', 'name' => 'Harshil', 'username' => 'harshil', 'role' => 'System Admin'],
        'jeel@gmail.com'       => ['pass' => '123', 'name' => 'Jeel', 'username' => 'jeel', 'role' => 'System Admin'],
        'jeel'                 => ['pass' => '123', 'name' => 'Jeel', 'username' => 'jeel', 'role' => 'System Admin'],
        'darvora575@gmail.com' => ['pass' => '2642006', 'name' => 'Darvora', 'username' => 'darvora575', 'role' => 'Super Administrator'],
        'darvora575'           => ['pass' => '2642006', 'name' => 'Darvora', 'username' => 'darvora575', 'role' => 'Super Administrator']
    ];

    if (isset($static_admins[$lower_u])) {
        $adm = $static_admins[$lower_u];
        if ($adm['pass'] === $password) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $adm['username'];
            $_SESSION['admin_username'] = $adm['username'];
            $_SESSION['admin_name'] = $adm['name'];
            $_SESSION['admin_email'] = strpos($lower_u, '@') !== false ? $lower_u : $lower_u . '@gmail.com';
            $_SESSION['admin_role'] = $adm['role'];

            echo json_encode([
                'success' => true,
                'role' => 'admin',
                'redirect' => 'admin_dashboard.php',
                'name' => $adm['name']
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Incorrect password.'
            ]);
            exit;
        }
    }

    // -------------------------------------------------------------------------
    // 2. Check Database Admins (users table)
    // -------------------------------------------------------------------------
    $stmt = $conn->prepare("SELECT u.id, u.email, u.password_hash, u.role, a.fname, a.lname FROM users u LEFT JOIN admins a ON u.id = a.id WHERE (LOWER(TRIM(u.email)) = LOWER(TRIM(?)) OR LOWER(TRIM(u.id)) = LOWER(TRIM(?))) AND u.role = 'admin'");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_id, $db_email, $db_password, $db_role, $fname, $lname);
            $stmt->fetch();

            if (trim($db_password) === $password || (function_exists('password_verify') && password_verify($password, $db_password))) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $admin_full_name = trim(($fname ?? '') . ' ' . ($lname ?? ''));
                if (empty($admin_full_name)) {
                    $admin_full_name = ucfirst(explode('@', $db_email)[0]);
                }

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $db_id;
                $_SESSION['admin_username'] = explode('@', $db_email)[0];
                $_SESSION['admin_name'] = $admin_full_name;
                $_SESSION['admin_email'] = $db_email;
                $_SESSION['admin_role'] = 'System Admin';

                echo json_encode([
                    'success' => true,
                    'role' => 'admin',
                    'redirect' => 'admin_dashboard.php',
                    'name' => $admin_full_name
                ]);
                $stmt->close();
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Incorrect password.'
                ]);
                $stmt->close();
                exit;
            }
        }
        $stmt->close();
    }

    // -------------------------------------------------------------------------
    // 3. Check Teachers Table
    // -------------------------------------------------------------------------
    $stmtT = $conn->prepare("SELECT teacher_id, first_name, last_name, password FROM teachers WHERE LOWER(TRIM(teacher_id)) = LOWER(TRIM(?)) OR LOWER(TRIM(email)) = LOWER(TRIM(?))");
    if ($stmtT) {
        $stmtT->bind_param("ss", $username, $username);
        $stmtT->execute();
        $stmtT->store_result();

        if ($stmtT->num_rows > 0) {
            $stmtT->bind_result($db_teacher_id, $first_name, $last_name, $db_password);
            $stmtT->fetch();

            if (trim($db_password) === $password || (function_exists('password_verify') && password_verify($password, $db_password))) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $teacher_full_name = trim($first_name . ' ' . $last_name);

                $_SESSION['teacher_logged_in'] = true;
                $_SESSION['teacher_id'] = $db_teacher_id;
                $_SESSION['teacher_name'] = $teacher_full_name;

                echo json_encode([
                    'success' => true,
                    'role' => 'teacher',
                    'redirect' => 'teacher_dashboard.php',
                    'name' => $teacher_full_name
                ]);
                $stmtT->close();
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Incorrect password.'
                ]);
                $stmtT->close();
                exit;
            }
        }
        $stmtT->close();
    }

    // -------------------------------------------------------------------------
    // 4. Check Students Table
    // -------------------------------------------------------------------------
    $stmtS = $conn->prepare("SELECT student_id, first_name, last_name, password FROM students WHERE LOWER(TRIM(student_id)) = LOWER(TRIM(?)) OR LOWER(TRIM(email)) = LOWER(TRIM(?))");
    if ($stmtS) {
        $stmtS->bind_param("ss", $username, $username);
        $stmtS->execute();
        $stmtS->store_result();

        if ($stmtS->num_rows > 0) {
            $stmtS->bind_result($db_student_id, $first_name, $last_name, $db_password);
            $stmtS->fetch();

            if (trim($db_password) === $password || (function_exists('password_verify') && password_verify($password, $db_password))) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $student_full_name = trim($first_name . ' ' . $last_name);

                $_SESSION['student_logged_in'] = true;
                $_SESSION['student_id'] = $db_student_id;
                $_SESSION['student_name'] = $student_full_name;

                echo json_encode([
                    'success' => true,
                    'role' => 'student',
                    'redirect' => 'student_dashboard.php',
                    'name' => $student_full_name
                ]);
                $stmtS->close();
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Incorrect password.'
                ]);
                $stmtS->close();
                exit;
            }
        }
        $stmtS->close();
    }

    // -------------------------------------------------------------------------
    // 5. No Match Found
    // -------------------------------------------------------------------------
    echo json_encode([
        'success' => false,
        'error' => 'No account found matching this Username, ID, or Email.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
