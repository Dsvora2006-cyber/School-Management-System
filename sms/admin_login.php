<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please enter both your Admin username/email and password.'
        ]);
        exit;
    }

    $lower_u = strtolower($username);

    // 1. Specific predefined admin credentials
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
            $_SESSION['admin_name'] = $adm['name']; // "Harshil", "Jeel", "Darvora"
            $_SESSION['admin_email'] = strpos($lower_u, '@') !== false ? $lower_u : $lower_u . '@gmail.com';
            $_SESSION['admin_role'] = $adm['role'];

            echo json_encode([
                'success' => true,
                'name' => $adm['name'],
                'role' => $adm['role']
            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Incorrect password'
            ]);
            exit;
        }
    }

    // 2. Check in database `users` table joined with `admins`
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
                    'name' => $admin_full_name,
                    'role' => 'System Admin'
                ]);
                $stmt->close();
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Incorrect password'
                ]);
                $stmt->close();
                exit;
            }
        }
        $stmt->close();
    }

    echo json_encode([
        'success' => false,
        'error' => 'Invalid administrator username or email.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
