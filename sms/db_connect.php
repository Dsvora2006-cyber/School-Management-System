<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'sms_db';

// Create connection
$conn = new mysqli($host, $user, $password);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql_db = "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql_db) === TRUE) {
    // Select the database
    $conn->select_db($dbname);
} else {
    die("Database creation failed: " . $conn->error);
}

// Create students table if not exists
$sql_table = "CREATE TABLE IF NOT EXISTS `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(50) UNIQUE NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `grade` VARCHAR(50) NOT NULL,
    `dob` DATE NOT NULL,
    `gender` VARCHAR(20) NOT NULL,
    `blood_group` VARCHAR(10) NOT NULL,
    `mobile_number` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL DEFAULT 'student123',
    `address` TEXT NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100) NOT NULL,
    `pin_code` VARCHAR(20) NOT NULL,
    `nationality` VARCHAR(100) NOT NULL,
    `religion` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `father_name` VARCHAR(100) NOT NULL,
    `mother_name` VARCHAR(100) NOT NULL,
    `parent_mobile` VARCHAR(20) NOT NULL,
    `parent_email` VARCHAR(100) NOT NULL,
    `parent_occupation` VARCHAR(100) NOT NULL,
    `attendance` DECIMAL(5,2) DEFAULT 100.00,
    `status` VARCHAR(20) DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_table) !== TRUE) {
    die("Table creation failed: " . $conn->error);
}

// Create teachers table if not exists (status removed!)
$sql_teachers_table = "CREATE TABLE IF NOT EXISTS `teachers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` VARCHAR(50) UNIQUE NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL DEFAULT 'teacher123',
    `mobile_number` VARCHAR(20) NOT NULL,
    `specialization` VARCHAR(100) NOT NULL,
    `standard` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_teachers_table) !== TRUE) {
    die("Teachers table creation failed: " . $conn->error);
}

// Check standard column existence, alter if needed
$check_column = $conn->query("SHOW COLUMNS FROM `teachers` LIKE 'standard'");
if ($check_column && $check_column->num_rows == 0) {
    $conn->query("ALTER TABLE `teachers` ADD COLUMN `standard` VARCHAR(50) NOT NULL DEFAULT '10'");
}

// Check teachers password column existence, alter if needed
$check_teachers_password = $conn->query("SHOW COLUMNS FROM `teachers` LIKE 'password'");
if ($check_teachers_password && $check_teachers_password->num_rows == 0) {
    $conn->query("ALTER TABLE `teachers` ADD COLUMN `password` VARCHAR(255) NOT NULL DEFAULT 'teacher123' AFTER `email`");
}

// Check students password column existence, alter if needed
$check_students_password = $conn->query("SHOW COLUMNS FROM `students` LIKE 'password'");
if ($check_students_password && $check_students_password->num_rows == 0) {
    $conn->query("ALTER TABLE `students` ADD COLUMN `password` VARCHAR(255) NOT NULL DEFAULT 'student123' AFTER `email`");
}

// Seed sample students if table is completely empty
$check_empty = "SELECT COUNT(*) as count FROM `students`";
$res_check = $conn->query($check_empty);
if ($res_check) {
    $row_check = $res_check->fetch_assoc();
    if ($row_check['count'] == 0) {
        $sql_seed = "INSERT INTO `students` 
        (`student_id`, `first_name`, `last_name`, `grade`, `dob`, `gender`, `blood_group`, `mobile_number`, `email`, `password`, `address`, `city`, `state`, `pin_code`, `nationality`, `religion`, `category`, `father_name`, `mother_name`, `parent_mobile`, `parent_email`, `parent_occupation`, `attendance`, `status`)
        VALUES 
        ('APX-2026-9481', 'Liam', 'Anderson', '10', '2010-05-14', 'Male', 'A+', '123-456-7890', 'liam@example.com', 'student123', '123 Forest Dr', 'Springfield', 'Illinois', '62701', 'American', 'Christian', 'General', 'Robert Anderson', 'Sarah Anderson', '555-019-2834', 'robert@example.com', 'Engineer', 98.20, 'Active'),
        ('APX-2026-3024', 'Sophia', 'Martinez', '12 (Commerce)', '2008-09-22', 'Female', 'O+', '234-567-8901', 'sophia@example.com', 'student123', '456 Valley View', 'Springfield', 'Illinois', '62702', 'American', 'Christian', 'OBC', 'Miguel Martinez', 'Maria Martinez', '555-020-3849', 'miguel@example.com', 'Doctor', 99.40, 'Active'),
        ('APX-2026-8839', 'Jackson', 'Reed', '11 (Science)', '2009-02-08', 'Male', 'B-', '345-678-9012', 'jackson@example.com', 'student123', '789 Oak Ave', 'Springfield', 'Illinois', '62703', 'American', 'Christian', 'SC/ST', 'David Reed', 'Karen Reed', '555-031-4920', 'david@example.com', 'Teacher', 94.70, 'Active'),
        ('APX-2026-4927', 'Olivia', 'Taylor', '10', '2010-11-30', 'Female', 'AB+', '456-789-0123', 'olivia@example.com', 'student123', '321 Pine St', 'Springfield', 'Illinois', '62704', 'American', 'Christian', 'General', 'Thomas Taylor', 'Linda Taylor', '555-042-5019', 'thomas@example.com', 'Accountant', 92.10, 'Pending')";
        
        $conn->query($sql_seed);
    }
}

// Seed sample teachers if table is completely empty (status removed!)
$check_teachers_empty = "SELECT COUNT(*) as count FROM `teachers`";
$res_t_check = $conn->query($check_teachers_empty);
if ($res_t_check) {
    $row_t_check = $res_t_check->fetch_assoc();
    if ($row_t_check['count'] == 0) {
        $sql_t_seed = "INSERT INTO `teachers` 
        (`teacher_id`, `first_name`, `last_name`, `email`, `mobile_number`, `specialization`, `standard`)
        VALUES 
        ('TCH-2026-4819', 'Sarah', 'Jenkins', 'sarah.j@apexacademy.com', '111-222-3333', 'English & Literature', '10'),
        ('TCH-2026-9281', 'Marcus', 'Vance', 'marcus.v@apexacademy.com', '222-333-4444', 'Physics & Chemistry', '11 (Science)'),
        ('TCH-2026-3049', 'Elena', 'Rostova', 'elena.r@apexacademy.com', '333-444-5555', 'Mathematics', '12 (Science)')";
        
        $conn->query($sql_t_seed);
    }
}

// Create classes table if not exists
$sql_classes_table = "CREATE TABLE IF NOT EXISTS `classes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `class_id` VARCHAR(50) UNIQUE NOT NULL,
    `class_name` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(100) NOT NULL,
    `standard` VARCHAR(50) NOT NULL,
    `teacher_id` VARCHAR(50) DEFAULT NULL,
    `room_number` VARCHAR(50) NOT NULL,
    `schedule` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_classes_table) !== TRUE) {
    die("Classes table creation failed: " . $conn->error);
}

// Check subject column existence, alter if needed
$check_classes_subject = $conn->query("SHOW COLUMNS FROM `classes` LIKE 'subject'");
if ($check_classes_subject && $check_classes_subject->num_rows == 0) {
    $conn->query("ALTER TABLE `classes` ADD COLUMN `subject` VARCHAR(100) NOT NULL DEFAULT 'General' AFTER `class_name`");
}

// Seed sample classes if table is completely empty
$check_classes_empty = "SELECT COUNT(*) as count FROM `classes`";
$res_c_check = $conn->query($check_classes_empty);
if ($res_c_check) {
    $row_c_check = $res_c_check->fetch_assoc();
    if ($row_c_check['count'] == 0) {
        $sql_c_seed = "INSERT INTO `classes`
        (`class_id`, `class_name`, `subject`, `standard`, `teacher_id`, `room_number`, `schedule`)
        VALUES
        ('CLS-2026-101', 'Grade 10 - Division A', 'English & Literature', '10', 'TCH-2026-4819', 'Room 101', 'Mon-Fri 08:00 AM - 01:30 PM'),
        ('CLS-2026-102', 'Grade 11 Science - Div A', 'Physics', '11 (Science)', 'TCH-2026-9281', 'Lab 1', 'Mon-Fri 08:30 AM - 02:00 PM'),
        ('CLS-2026-103', 'Grade 12 Science - Div A', 'Mathematics', '12 (Science)', 'TCH-2026-3049', 'Room 305', 'Mon-Fri 09:00 AM - 02:30 PM')";
        
        $conn->query($sql_c_seed);
    }
}

// Create invoices table if not exists
$sql_invoices_table = "CREATE TABLE IF NOT EXISTS `invoices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
    `student_id` VARCHAR(50) NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `due_date` DATE NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'Unpaid',
    `payment_method` VARCHAR(50) DEFAULT NULL,
    `payment_date` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_invoices_table) !== TRUE) {
    die("Invoices table creation failed: " . $conn->error);
}

// Seed sample invoices if table is completely empty
$check_invoices_empty = "SELECT COUNT(*) as count FROM `invoices`";
$res_i_check = $conn->query($check_invoices_empty);
if ($res_i_check) {
    $row_i_check = $res_i_check->fetch_assoc();
    if ($row_i_check['count'] == 0) {
        $sql_i_seed = "INSERT INTO `invoices`
        (`invoice_number`, `student_id`, `title`, `amount`, `due_date`, `status`, `payment_method`, `payment_date`)
        VALUES
        ('INV-2026-0001', 'APX-2026-9481', 'Tuition Fees - Term 1', 850.00, '2026-08-01', 'Paid', 'Online', '2026-07-23'),
        ('INV-2026-0002', 'APX-2026-4927', 'Admission Charges', 600.00, '2026-08-10', 'Unpaid', NULL, NULL),
        ('INV-2026-0003', 'APX-2026-3024', 'Exam Fees - Midterm', 150.00, '2026-08-15', 'Unpaid', NULL, NULL)";
        
        $conn->query($sql_i_seed);
    }
}

// Create homework table if not exists
$sql_homework_table = "CREATE TABLE IF NOT EXISTS `homework` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `homework_id` VARCHAR(50) UNIQUE NOT NULL,
    `class_id` VARCHAR(50) NOT NULL,
    `teacher_id` VARCHAR(50) NOT NULL,
    `subject` VARCHAR(100) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `due_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_homework_table) !== TRUE) {
    die("Homework table creation failed: " . $conn->error);
}

// Seed sample homework if table is completely empty
$check_homework_empty = "SELECT COUNT(*) as count FROM `homework`";
$res_hw_check = $conn->query($check_homework_empty);
if ($res_hw_check) {
    $row_hw_check = $res_hw_check->fetch_assoc();
    if ($row_hw_check['count'] == 0) {
        $sql_hw_seed = "INSERT INTO `homework`
        (`homework_id`, `class_id`, `teacher_id`, `subject`, `title`, `description`, `due_date`)
        VALUES
        ('HW-2026-0001', 'CLS-2026-101', 'TCH-2026-4819', 'English & Literature', 'Essay on Macbeth Act I', 'Write a 500-word analysis of Macbeth\'s internal conflict in Act I, Scene 7. Focus on his ambition versus his moral integrity.', '2026-08-05'),
        ('HW-2026-0002', 'CLS-2026-102', 'TCH-2026-9281', 'Physics', 'Electromagnetism Problem Set', 'Complete problems 1 to 10 on page 142 of the textbook. Show all work and formulas used.', '2026-08-07')";
        
        $conn->query($sql_hw_seed);
    }
}

// Create leaves table if not exists
$sql_leaves_table = "CREATE TABLE IF NOT EXISTS `leaves` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `leave_id` VARCHAR(50) UNIQUE NOT NULL,
    `student_id` VARCHAR(50) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `reason` TEXT NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` VARCHAR(20) DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_leaves_table) !== TRUE) {
    die("Leaves table creation failed: " . $conn->error);
}

// Seed sample leaves if table is completely empty
$check_leaves_empty = "SELECT COUNT(*) as count FROM `leaves`";
$res_lv_check = $conn->query($check_leaves_empty);
if ($res_lv_check) {
    $row_lv_check = $res_lv_check->fetch_assoc();
    if ($row_lv_check['count'] == 0) {
        $sql_lv_seed = "INSERT INTO `leaves`
        (`leave_id`, `student_id`, `subject`, `reason`, `start_date`, `end_date`, `status`)
        VALUES
        ('LV-2026-0001', 'APX-2026-9481', 'Family Wedding Ceremony', 'I request leave to attend my sister\'s wedding ceremony out of state. I will make sure to catch up on any missed classwork.', '2026-08-10', '2026-08-12', 'Approved'),
        ('LV-2026-0002', 'APX-2026-9481', 'Dental Checkup & Surgery', 'I have a scheduled wisdom tooth extraction and dentist follow-up appointment.', '2026-08-20', '2026-08-20', 'Pending')";
        
        $conn->query($sql_lv_seed);
    }
}

// Create settings table if not exists
$sql_settings_table = "CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_settings_table) !== TRUE) {
    die("Settings table creation failed: " . $conn->error);
}

// Seed sample settings if table is empty
$check_settings_empty = "SELECT COUNT(*) as count FROM `settings`";
$res_set_check = $conn->query($check_settings_empty);
if ($res_set_check) {
    $row_set_check = $res_set_check->fetch_assoc();
    if ($row_set_check['count'] == 0) {
        $sql_set_seed = "INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
        ('school_name', 'AWS'),
        ('school_email', 'info@apexacademy.com'),
        ('school_phone', '+1 (555) 019-2834'),
        ('school_address', '123 Academic Way, Springfield, IL'),
        ('academic_year', '2026-2027'),
        ('current_term', 'Term 1'),
        ('currency', 'USD'),
        ('theme_color', 'indigo'),
        ('density', 'normal')";
        $conn->query($sql_set_seed);
    }
}

// Create marks table if not exists
$sql_marks_table = "CREATE TABLE IF NOT EXISTS `marks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` VARCHAR(50) NOT NULL,
    `subject` VARCHAR(100) NOT NULL,
    `exam_name` VARCHAR(100) NOT NULL,
    `marks_obtained` DECIMAL(5,2) NOT NULL,
    `max_marks` DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `student_exam_subject` (`student_id`, `subject`, `exam_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_marks_table) !== TRUE) {
    die("Marks table creation failed: " . $conn->error);
}

// Seed sample marks if table is completely empty
$check_marks_empty = "SELECT COUNT(*) as count FROM `marks`";
$res_m_check = $conn->query($check_marks_empty);
if ($res_m_check) {
    $row_m_check = $res_m_check->fetch_assoc();
    if ($row_m_check['count'] == 0) {
        $sql_m_seed = "INSERT INTO `marks`
        (`student_id`, `subject`, `exam_name`, `marks_obtained`, `max_marks`)
        VALUES
        ('APX-2026-9481', 'English & Literature', 'Midterm Exam', 88.50, 100.00),
        ('APX-2026-9481', 'English & Literature', 'Final Exam', 92.00, 100.00),
        ('APX-2026-4927', 'English & Literature', 'Midterm Exam', 76.00, 100.00)";
        $conn->query($sql_m_seed);
    }
}

// Create admins table if not exists
$sql_admins_table = "CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) UNIQUE NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) DEFAULT 'System Admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql_admins_table) !== TRUE) {
    die("Admins table creation failed: " . $conn->error);
}

// Seed sample admins if table is empty
$check_admins_empty = "SELECT COUNT(*) as count FROM `admins`";
$res_adm_check = $conn->query($check_admins_empty);
if ($res_adm_check) {
    $row_adm_check = $res_adm_check->fetch_assoc();
    if ($row_adm_check['count'] == 0) {
        $sql_adm_seed = "INSERT INTO `admins`
        (`username`, `name`, `email`, `password`, `role`)
        VALUES
        ('darvora575', 'Darvora', 'darvora575@gmail.com', '2642006', 'Super Administrator'),
        ('harshil', 'Harshil', 'harshil@gmail.com', '123', 'System Admin'),
        ('jeel', 'Jeel', 'jeel@gmail.com', '123', 'System Admin')";
        $conn->query($sql_adm_seed);
    }
}
?>

