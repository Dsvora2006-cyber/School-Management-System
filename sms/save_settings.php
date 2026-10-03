<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$valid_keys = [
    'school_name',
    'school_email',
    'school_phone',
    'school_address',
    'academic_year',
    'current_term',
    'currency',
    'theme_color',
    'density'
];

$success = true;
$error_message = '';

// Start a transaction to ensure all settings update atomically
$conn->begin_transaction();

try {
    foreach ($valid_keys as $key) {
        if (isset($_POST[$key])) {
            $value = trim($_POST[$key]);
            
            // Use prepared statement to insert or update the setting
            $stmt = $conn->prepare("INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `setting_value` = ?");
            if ($stmt) {
                $stmt->bind_param("sss", $key, $value, $value);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute database update for " . $key);
                }
                $stmt->close();
            } else {
                throw new Exception("Failed to prepare update query for " . $key);
            }
        }
    }
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $success = false;
    $error_message = $e->getMessage();
}

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $error_message]);
}
?>
