<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';
    $dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $blood_group = isset($_POST['blood_group']) ? trim($_POST['blood_group']) : '';
    $mobile_number = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $state = isset($_POST['state']) ? trim($_POST['state']) : '';
    $pin_code = isset($_POST['pin_code']) ? trim($_POST['pin_code']) : '';
    $nationality = isset($_POST['nationality']) ? trim($_POST['nationality']) : '';
    $religion = isset($_POST['religion']) ? trim($_POST['religion']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $father_name = isset($_POST['father_name']) ? trim($_POST['father_name']) : '';
    $mother_name = isset($_POST['mother_name']) ? trim($_POST['mother_name']) : '';
    $parent_mobile = isset($_POST['parent_mobile']) ? trim($_POST['parent_mobile']) : '';
    $parent_email = isset($_POST['parent_email']) ? trim($_POST['parent_email']) : '';
    $parent_occupation = isset($_POST['parent_occupation']) ? trim($_POST['parent_occupation']) : '';

    // Check required fields
    if (empty($student_id) || empty($first_name) || empty($last_name) || empty($grade) || empty($dob) || empty($gender) || empty($email)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please fill in all required fields.'
        ]);
        exit;
    }

    // Check if Student ID already exists
    $check_stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
    $check_stmt->bind_param("s", $student_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Student ID / Enrollment Number already exists.'
        ]);
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Prepare SQL Statement (22 columns now!)
    $stmt = $conn->prepare("INSERT INTO students 
        (student_id, first_name, last_name, grade, dob, gender, blood_group, mobile_number, email, password, address, city, state, pin_code, nationality, religion, category, father_name, mother_name, parent_mobile, parent_email, parent_occupation) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $default_password = 'student123';
        $stmt->bind_param("ssssssssssssssssssssss", 
            $student_id, 
            $first_name, 
            $last_name, 
            $grade,
            $dob, 
            $gender, 
            $blood_group, 
            $mobile_number, 
            $email, 
            $default_password,
            $address, 
            $city, 
            $state, 
            $pin_code, 
            $nationality, 
            $religion, 
            $category, 
            $father_name, 
            $mother_name, 
            $parent_mobile, 
            $parent_email, 
            $parent_occupation
        );

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
            'error' => 'Database statement preparation failed: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
