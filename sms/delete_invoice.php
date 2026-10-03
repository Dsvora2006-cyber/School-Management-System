<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_number = isset($_POST['invoice_number']) ? trim($_POST['invoice_number']) : '';

    if (empty($invoice_number)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invoice number is required.'
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM invoices WHERE invoice_number = ?");
    if ($stmt) {
        $stmt->bind_param("s", $invoice_number);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Database deletion execution failed: ' . $stmt->error
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
