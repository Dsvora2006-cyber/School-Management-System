<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $leave_id = isset($_POST['leave_id']) ? trim($_POST['leave_id']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if (empty($leave_id) || empty($status)) {
        echo json_encode([
            'success' => false,
            'error' => 'Missing Leave ID or status payload.'
        ]);
        exit;
    }

    // Restrict status values
    $allowed_statuses = ['Approved', 'Rejected'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid status change value specified.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE leave_id = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $status, $leave_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'Leave Request already has the requested status or ID does not exist.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Database update error: ' . $stmt->error
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
