<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect inputs
    $invoice_number = isset($_POST['invoice_number']) ? trim($_POST['invoice_number']) : '';
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.0;
    $due_date = isset($_POST['due_date']) ? trim($_POST['due_date']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'Unpaid';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : NULL;

    // Verify required inputs
    if (empty($invoice_number) || empty($student_id) || empty($title) || $amount <= 0 || empty($due_date)) {
        echo json_encode([
            'success' => false,
            'error' => 'Please fill in all required fields. Amount must be greater than zero.'
        ]);
        exit;
    }

    // Set payment date if Paid
    $payment_date = NULL;
    if ($status === 'Paid') {
        $payment_date = date('Y-m-d');
        if (empty($payment_method)) {
            $payment_method = 'Cash'; // Default payment method if not specified
        }
    } else {
        $payment_method = NULL;
    }

    // Check duplicate Invoice Number (exact or base prefix)
    $like_pattern = $invoice_number . '-%';
    $check_stmt = $conn->prepare("SELECT id FROM invoices WHERE invoice_number = ? OR invoice_number LIKE ?");
    if ($check_stmt) {
        $check_stmt->bind_param("ss", $invoice_number, $like_pattern);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Invoice number already exists. Please choose a unique number.'
            ]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();
    }

    // Find all students enrolled in this standard grade
    $students_list = [];
    $std_stmt = $conn->prepare("SELECT student_id FROM students WHERE TRIM(grade) = ? OR TRIM(grade) = ?");
    if ($std_stmt) {
        $grade_alt = (strpos($student_id, ' (') !== false) ? str_replace(' (', '(', $student_id) : str_replace('(', ' (', $student_id);
        $std_stmt->bind_param("ss", $student_id, $grade_alt);
        $std_stmt->execute();
        $res_std = $std_stmt->get_result();
        while ($row = $res_std->fetch_assoc()) {
            $students_list[] = $row['student_id'];
        }
        $std_stmt->close();
    }

    // Insert Invoice Records
    if (count($students_list) > 0) {
        $success_count = 0;
        $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, student_id, title, amount, due_date, status, payment_method, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            foreach ($students_list as $s_id) {
                // Generate a unique invoice number by appending student ID
                $unique_inv_no = $invoice_number . '-' . $s_id;
                
                $stmt->bind_param("sssdssss", $unique_inv_no, $s_id, $title, $amount, $due_date, $status, $payment_method, $payment_date);
                if ($stmt->execute()) {
                    $success_count++;
                }
            }
            $stmt->close();
        }
        
        if ($success_count > 0) {
            echo json_encode([
                'success' => true,
                'message' => "Successfully issued {$success_count} invoices for Standard {$student_id}."
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to issue student invoices.'
            ]);
        }
    } else {
        // Fallback: insert one record for the standard itself if no students found
        $stmt = $conn->prepare("INSERT INTO invoices (invoice_number, student_id, title, amount, due_date, status, payment_method, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssdssss", $invoice_number, $student_id, $title, $amount, $due_date, $status, $payment_method, $payment_date);
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'No active students found in this standard. Created default category invoice.'
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
                'error' => 'Database preparation failed: ' . $conn->error
            ]);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request method.'
    ]);
}
?>
