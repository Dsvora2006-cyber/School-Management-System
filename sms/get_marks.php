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
$class_id = isset($_GET['class_id']) ? trim($_GET['class_id']) : '';
$exam_name = isset($_GET['exam_name']) ? trim($_GET['exam_name']) : '';
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';

if (empty($class_id) || empty($exam_name) || empty($subject)) {
    echo json_encode(['success' => false, 'error' => 'Class, Exam Name, and Subject are required parameters.']);
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

// 2. Fetch all students matching the standard / grade
$student_list = [];
$stmt_students = $conn->prepare("SELECT student_id, first_name, last_name FROM students WHERE grade = ? AND status = 'Active' ORDER BY first_name ASC");
if (!$stmt_students) {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare student query.']);
    exit;
}
$stmt_students->bind_param("s", $standard);
$stmt_students->execute();
$res_students = $stmt_students->get_result();

while ($row = $res_students->fetch_assoc()) {
    $student_list[] = $row;
}
$stmt_students->close();

if (empty($student_list)) {
    echo json_encode(['success' => true, 'subject' => $subject, 'students' => []]);
    exit;
}

// Map standard grades to subjects list
$standard_subjects_map = [
    '10' => ['English', 'Gujarati', 'Mathematics', 'Science', 'Social Science', 'Hindi'],
    '11 (Science)' => ['Physics', 'Chemistry', 'Mathematics', 'Biology', 'English', 'Gujarati'],
    '12 (Science)' => ['Physics', 'Chemistry', 'Mathematics', 'Biology', 'English', 'Gujarati'],
    '11 (Commerce)' => ['Account', 'Statistics', 'BO', 'Economics', 'SPCC', 'Gujarati', 'English'],
    '12 (Commerce)' => ['Account', 'Statistics', 'BO', 'Economics', 'SPCC', 'Gujarati', 'English'],
    '11' => ['English', 'Gujarati', 'Mathematics', 'Science', 'Social Science'],
    '12' => ['English', 'Gujarati', 'Mathematics', 'Science', 'Social Science']
];

$subjects_to_load = ($subject === 'All') 
    ? (isset($standard_subjects_map[$standard]) ? $standard_subjects_map[$standard] : ['English', 'Gujarati', 'Mathematics', 'Science'])
    : [$subject];

// Build flat array of student-subject rows
$rows_data = [];
foreach ($student_list as $student) {
    foreach ($subjects_to_load as $subj) {
        $key = $student['student_id'] . '_' . $subj;
        $rows_data[$key] = [
            'student_id' => $student['student_id'],
            'full_name' => $student['first_name'] . ' ' . $student['last_name'],
            'subject' => $subj,
            'marks_obtained' => null,
            'max_marks' => 100.00
        ];
    }
}

// 3. Fetch any existing marks for these subjects and exam name
$student_ids = array_unique(array_column($student_list, 'student_id'));
$placeholders = implode(',', array_fill(0, count($student_ids), '?'));
$subj_placeholders = implode(',', array_fill(0, count($subjects_to_load), '?'));

$sql_marks = "SELECT student_id, subject, marks_obtained, max_marks FROM marks WHERE exam_name = ? AND subject IN ($subj_placeholders) AND student_id IN ($placeholders)";

$stmt_marks = $conn->prepare($sql_marks);
if ($stmt_marks) {
    $types = 's' . str_repeat('s', count($subjects_to_load)) . str_repeat('s', count($student_ids));
    $bind_params = array_merge([$exam_name], $subjects_to_load, $student_ids);
    
    $stmt_marks->bind_param($types, ...$bind_params);
    $stmt_marks->execute();
    $res_marks = $stmt_marks->get_result();
    
    while ($row = $res_marks->fetch_assoc()) {
        $key = $row['student_id'] . '_' . $row['subject'];
        if (isset($rows_data[$key])) {
            $rows_data[$key]['marks_obtained'] = (float)$row['marks_obtained'];
            $rows_data[$key]['max_marks'] = (float)$row['max_marks'];
        }
    }
    $stmt_marks->close();
}

echo json_encode([
    'success' => true,
    'subject' => $subject,
    'students' => array_values($rows_data)
]);
?>
