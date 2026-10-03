<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Verify session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access. Please log in first.']);
    exit;
}

require_once 'db_connect.php';

$teacher_id = $_SESSION['teacher_id'];
$class_id = isset($_POST['class_id']) ? trim($_POST['class_id']) : '';
$exam_name = isset($_POST['exam_name']) ? trim($_POST['exam_name']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$marks_json = isset($_POST['marks_data']) ? $_POST['marks_data'] : '';

if (empty($class_id) || empty($exam_name) || empty($subject) || empty($marks_json)) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters.']);
    exit;
}

$marks_list = json_decode($marks_json, true);
if (!is_array($marks_list)) {
    echo json_encode(['success' => false, 'error' => 'Invalid marks data format.']);
    exit;
}

// 1. Verify class belongs to teacher and retrieve standard
$stmt_class = $conn->prepare("SELECT standard FROM classes WHERE class_id = ? AND teacher_id = ?");
if (!$stmt_class) {
    echo json_encode(['success' => false, 'error' => 'Database query preparation failed.']);
    exit;
}
$stmt_class->bind_param("ss", $class_id, $teacher_id);
$stmt_class->execute();
$res_class = $stmt_class->get_result();

if ($res_class->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Assigned class not found.']);
    $stmt_class->close();
    exit;
}

$class_info = $res_class->fetch_assoc();
$standard = $class_info['standard'];
$stmt_class->close();

// 2. Fetch all valid student IDs in this class standard to ensure we only save valid records
$valid_students = [];
$stmt_students = $conn->prepare("SELECT student_id FROM students WHERE grade = ? AND status = 'Active'");
if ($stmt_students) {
    $stmt_students->bind_param("s", $standard);
    $stmt_students->execute();
    $res_students = $stmt_students->get_result();
    while ($row = $res_students->fetch_assoc()) {
        $valid_students[] = $row['student_id'];
    }
    $stmt_students->close();
}

// Begin transaction
$conn->begin_transaction();

try {
    $stmt_save = $conn->prepare("INSERT INTO marks (student_id, subject, exam_name, marks_obtained, max_marks) 
        VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), max_marks = VALUES(max_marks)");
        
    if (!$stmt_save) {
        throw new Exception("Failed to prepare database insertion statement.");
    }

    foreach ($marks_list as $item) {
        $s_id = isset($item['student_id']) ? trim($item['student_id']) : '';
        $item_subject = isset($item['subject']) ? trim($item['subject']) : $subject;
        $obtained = isset($item['marks_obtained']) ? (float)$item['marks_obtained'] : 0.00;
        $max = isset($item['max_marks']) ? (float)$item['max_marks'] : 100.00;

        // Skip records not belonging to active class students
        if (!in_array($s_id, $valid_students)) {
            continue;
        }

        // Validate values
        if ($obtained < 0 || $max <= 0 || $obtained > $max) {
            throw new Exception("Invalid marks input for student ID: $s_id. Marks obtained ($obtained) cannot exceed maximum marks ($max) or fall below zero.");
        }

        $stmt_save->bind_param("sssdd", $s_id, $item_subject, $exam_name, $obtained, $max);
        $stmt_save->execute();
    }
    
    $stmt_save->close();
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Student marks successfully published and saved.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
