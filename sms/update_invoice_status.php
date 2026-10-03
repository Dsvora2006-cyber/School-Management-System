<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_number = isset($_POST['invoice_number']) ? trim($_POST['invoice_number']) : '';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

    if (empty($invoice_number) || empty($payment_method)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invoice number and payment method are required.'
        ]);
        exit;
    }

    $status = 'Paid';
    $payment_date = date('Y-m-d');

    $stmt = $conn->prepare("UPDATE invoices SET status = ?, payment_method = ?, payment_date = ? WHERE invoice_number = ?");
    if ($stmt) {
        $stmt->bind_param("ssss", $status, $payment_method, $payment_date, $invoice_number);
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'payment_date' => $payment_date
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Database update execution failed: ' . $stmt->error
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
