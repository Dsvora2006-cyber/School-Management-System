<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify Session
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require_once 'db_connect.php';

// Fetch settings from database
$settings = [];
$settings_sql = "SELECT `setting_key`, `setting_value` FROM `settings`";
$settings_res = $conn->query($settings_sql);
if ($settings_res && $settings_res->num_rows > 0) {
    while ($row = $settings_res->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
$school_name = htmlspecialchars($settings['school_name'] ?? 'AWS');

$student_id = $_SESSION['student_id'];

// Retrieve Student Profile Details
$stmt = $conn->prepare("SELECT first_name, last_name, grade, email FROM students WHERE student_id = ?");
if ($stmt) {
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $student = $res->fetch_assoc();
    } else {
        session_destroy();
        header('Location: index.php');
        exit;
    }
    $stmt->close();
} else {
    die("Database query error.");
}

$first_name = htmlspecialchars($student['first_name']);
$last_name = htmlspecialchars($student['last_name']);
$grade = htmlspecialchars($student['grade']);

// Retrieve unpaid invoices list
$unpaid_invoices = [];
$unpaid_sql = "SELECT invoice_number, title, amount, due_date FROM invoices WHERE student_id = ? AND status = 'Unpaid' ORDER BY id ASC";
$stmt_unpaid = $conn->prepare($unpaid_sql);
if ($stmt_unpaid) {
    $stmt_unpaid->bind_param("s", $student_id);
    $stmt_unpaid->execute();
    $res_unpaid = $stmt_unpaid->get_result();
    while ($row_unpaid = $res_unpaid->fetch_assoc()) {
        $unpaid_invoices[] = $row_unpaid;
    }
    $stmt_unpaid->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fees Checkout - <?php echo $school_name; ?></title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 for Premium Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Dashboard layout CSS rules -->
    <style>
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Design */
        .sidebar {
            width: 280px;
            background: var(--card-bg);
            border-right: 1px solid var(--card-border);
            padding: 2.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: sticky;
            top: 0;
            height: 100vh;
            backdrop-filter: blur(var(--glass-blur));
            -webkit-backdrop-filter: blur(var(--glass-blur));
        }

        .sidebar-brand {
            margin-bottom: 3rem;
            padding-left: 0.75rem;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: var(--text-main);
            background: var(--primary-glow);
        }

        .sidebar-link.active {
            border-left: 3px solid var(--primary);
            border-radius: 0 12px 12px 0;
            padding-left: calc(1rem - 3px);
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-footer {
            border-top: 1px solid var(--card-border);
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .admin-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 700;
            border: 2px solid var(--card-border);
        }

        .admin-details h5 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .admin-details span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Workspace */
        .workspace {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
        }

        .top-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            gap: 2rem;
        }

        .welcome-title h2 {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .welcome-title p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Two columns checkout split */
        .checkout-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        .checkout-card, .invoices-summary-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }

        .card-header-title {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-main);
        }

        .checkout-form-group {
            margin-bottom: 1.25rem;
        }

        .checkout-form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .checkout-input-select {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .checkout-input-select:focus {
            border-color: var(--primary);
        }

        .card-details-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed var(--card-border);
            padding: 1.5rem;
            border-radius: 14px;
            margin-top: 1.5rem;
        }

        /* Invoices Summary List */
        .invoice-summary-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            list-style: none;
        }

        .invoice-summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            padding: 1rem 1.25rem;
            border-radius: 12px;
        }

        .invoice-info-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-main);
        }

        .invoice-info-meta {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        .invoice-amount-tag {
            font-weight: 800;
            color: var(--accent);
            font-size: 1.1rem;
        }

        .no-outstanding-box {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-muted);
        }

        .no-outstanding-box i {
            font-size: 3rem;
            color: #27c93f;
            margin-bottom: 1.25rem;
        }

        .no-outstanding-box h4 {
            color: var(--text-main);
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 992px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--card-border);
                padding: 1.5rem;
            }
            .sidebar-brand {
                margin-bottom: 1.5rem;
            }
            .sidebar-menu {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 0.5rem;
                margin-bottom: 1rem;
            }
            .sidebar-link {
                white-space: nowrap;
            }
            .sidebar-footer {
                display: none;
            }
            .workspace {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div>
                <div class="sidebar-brand">
                    <a href="student_dashboard.php" class="logo">
                        <img src="images/logo.png" alt="Logo" style="height: 56px; width: auto; border-radius: 6px; -webkit-text-fill-color: initial; margin-right: 0.25rem; vertical-align: middle;"> <span id="sidebar-logo-text"><?php echo $school_name; ?></span>
                    </a>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="student_dashboard.php" class="sidebar-link" id="tab-student-overview"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="student_schedule.php" class="sidebar-link" id="tab-student-schedule"><i class="fa-solid fa-calendar-days"></i> Timetable</a></li>
                    <li><a href="student_homework.php" class="sidebar-link" id="tab-student-homework"><i class="fa-solid fa-book"></i> Homework</a></li>
                    <li><a href="student_leaves.php" class="sidebar-link" id="tab-student-leaves"><i class="fa-solid fa-calendar-minus"></i> Leave Requests</a></li>
                    <li><a href="student_marks.php" class="sidebar-link" id="tab-student-marks"><i class="fa-solid fa-square-poll-vertical"></i> Marks</a></li>
                    <li><a href="pay_fees.php" class="sidebar-link active" id="tab-student-fees"><i class="fa-solid fa-file-invoice-dollar"></i> Fees</a></li>
                    <li><a href="student_profile.php" class="sidebar-link" id="tab-student-profile"><i class="fa-solid fa-id-card"></i> My Profile</a></li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?php echo $first_name[0] . ($last_name[0] ?? ''); ?>
                    </div>
                    <div class="admin-details">
                        <h5><?php echo $first_name . ' ' . $last_name; ?></h5>
                        <span>Student Profile</span>
                    </div>
                </div>
                <a href="student_dashboard.php?action=logout" class="sidebar-link" style="color: var(--accent); padding-left: 0.75rem;"><i class="fa-solid fa-power-off"></i> Sign Out</a>
            </div>
        </aside>

        <!-- Main Checkout Workspace -->
        <main class="workspace">
            <div class="top-panel">
                <div class="welcome-title">
                    <h2>Student Information Form</h2>
                    <p>Review student enrollment profile details and process fee payments securely.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" style="margin-right: 0.5rem;" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <?php if (count($unpaid_invoices) > 0): ?>
                <div class="alert-banner" style="background: rgba(244, 63, 94, 0.1); border: 1px solid var(--accent); border-radius: 16px; padding: 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                    <div style="font-size: 1.5rem; color: var(--accent);"><i class="fa-solid fa-bell"></i></div>
                    <div style="font-size: 0.9rem; line-height: 1.5; color: var(--text-main);">
                        <strong>New Fees Alert:</strong> The administration department has issued new outstanding billing dues for your student account. Please review details in the form below and settle them.
                    </div>
                </div>
            <?php endif; ?>

            <div class="checkout-grid">
                <!-- Checkout Card Form -->
                <div class="checkout-card">
                    <div class="card-header-title">
                        <i class="fa-solid fa-id-card" style="color: var(--primary);"></i> Student Information Form
                    </div>
                    <form id="fees-payment-checkout-form">
                        <!-- Enrollment Number Input (Enabled) -->
                        <div class="checkout-form-group">
                            <label for="checkout-enrollment-no">Enrollment Number</label>
                            <input type="text" id="checkout-enrollment-no" class="checkout-input-select" value="<?php echo htmlspecialchars($student_id); ?>" placeholder="Enter Enrollment ID" required>
                        </div>

                        <!-- Student Full Name (Readonly) -->
                        <div class="checkout-form-group">
                            <label for="checkout-student-name">Student Full Name</label>
                            <input type="text" id="checkout-student-name" class="checkout-input-select" value="<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>" readonly style="background: var(--input-bg); cursor: not-allowed; opacity: 0.85;">
                        </div>

                        <!-- Class Standard (Readonly) -->
                        <div class="checkout-form-group">
                            <label for="checkout-student-grade">Class Standard</label>
                            <input type="text" id="checkout-student-grade" class="checkout-input-select" value="Grade <?php echo htmlspecialchars($student['grade']); ?>" readonly style="background: var(--input-bg); cursor: not-allowed; opacity: 0.85;">
                        </div>

                        <!-- Student Email Address (Readonly) -->
                        <div class="checkout-form-group">
                            <label for="checkout-student-email">Email Address</label>
                            <input type="text" id="checkout-student-email" class="checkout-input-select" value="<?php echo htmlspecialchars($student['email']); ?>" readonly style="background: var(--input-bg); cursor: not-allowed; opacity: 0.85;">
                        </div>

                        <!-- Select Pending Invoice -->
                        <div class="checkout-form-group">
                            <label for="checkout-invoice">Select Unpaid Invoice</label>
                            <select id="checkout-invoice" class="checkout-input-select" required>
                                <?php if (count($unpaid_invoices) > 0): ?>
                                    <?php foreach ($unpaid_invoices as $inv): ?>
                                        <option value="<?php echo htmlspecialchars($inv['invoice_number']); ?>" data-amount="<?php echo $inv['amount']; ?>">
                                            <?php echo htmlspecialchars($inv['invoice_number'] . ' - ' . $inv['title'] . ' ($' . number_format($inv['amount'], 2) . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled selected>No outstanding invoices</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Payment Method Selector -->
                        <div class="checkout-form-group">
                            <label for="checkout-method">Select Payment Method</label>
                            <select id="checkout-method" class="checkout-input-select" required <?php echo (count($unpaid_invoices) == 0) ? 'disabled' : ''; ?>>
                                <option value="GPay">Google Pay (GPay)</option>
                                <option value="UPI">Unified Payments Interface (UPI)</option>
                                <option value="RTGS">RTGS / Direct Bank Transfer</option>
                            </select>
                        </div>

                         <!-- 1. Google Pay (GPay) Input block -->
                         <div id="gpay-fields-container" class="card-details-box" style="display: <?php echo (count($unpaid_invoices) > 0) ? 'block' : 'none'; ?>;">
                             <div class="checkout-form-group">
                                 <label for="gpay-mobile">Google Pay Registered Mobile Number / VPA ID</label>
                                 <input type="text" id="gpay-mobile" class="checkout-input-select" placeholder="e.g. +1 555-019-2834 or liam@okaxis" style="background: var(--input-bg);">
                             </div>
                         </div>

                         <!-- 2. UPI Input Block -->
                         <div id="upi-fields-container" class="card-details-box" style="display: none;">
                             <div class="checkout-form-group">
                                 <label for="upi-id">UPI Virtual Payment Address (VPA)</label>
                                 <input type="text" id="upi-id" class="checkout-input-select" placeholder="e.g. liam@okaxis" style="background: var(--input-bg);">
                             </div>
                         </div>

                        <!-- 3. RTGS Transfer Block -->
                        <div id="rtgs-fields-container" class="card-details-box" style="display: none; background: rgba(6, 182, 212, 0.03);">
                            <div style="font-size: 0.85rem; line-height: 1.6; color: var(--text-main);">
                                <strong style="color: var(--secondary); display: block; font-size: 0.95rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-building-columns"></i> <?php echo $school_name; ?> Corporate RTGS Dues</strong>
                                <span style="display: block; margin-bottom: 0.25rem;">Bank Name: <strong>AWS Development Bank</strong></span>
                                <span style="display: block; margin-bottom: 0.25rem;">Account Number: <strong>9876-5432-1098</strong></span>
                                <span style="display: block; margin-bottom: 0.25rem;">RTGS / IFSC / Routing Code: <strong>APEX0009988</strong></span>
                                <span style="display: block; margin-bottom: 0.75rem;">Account Type: <strong>Checking / Corporate</strong></span>
                                <p style="font-size: 0.75rem; color: var(--text-muted); border-top: 1px dashed var(--card-border); padding-top: 0.5rem;">Please transfer the exact invoice amount via RTGS. Click the authorize button below once the transaction completes to update dashboard records.</p>
                            </div>
                        </div>

                        <!-- Payment submission button -->
                        <?php if (count($unpaid_invoices) > 0): ?>
                            <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 12px; padding: 0.9rem; font-weight: 700; margin-top: 2rem; gap: 0.5rem;">
                                <i class="fa-solid fa-lock"></i> Authorize & Settle Fees Dues
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn" style="width: 100%; border-radius: 12px; padding: 0.9rem; font-weight: 700; margin-top: 2rem; gap: 0.5rem; background: #27c93f; border-color: #27c93f; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-circle-check"></i> Account Fully Cleared
                            </button>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Invoices list on the right -->
                <div class="invoices-summary-card">
                    <div class="card-header-title">
                        <i class="fa-solid fa-receipt" style="color: var(--secondary);"></i> Outstanding Invoices
                    </div>
                    <ul class="invoice-summary-list">
                        <?php if (count($unpaid_invoices) > 0): ?>
                            <?php foreach ($unpaid_invoices as $inv): ?>
                                <li class="invoice-summary-item">
                                    <div>
                                        <div class="invoice-info-title"><?php echo htmlspecialchars($inv['title']); ?></div>
                                        <div class="invoice-info-meta">Invoice: <?php echo htmlspecialchars($inv['invoice_number']); ?> • Due: <?php echo htmlspecialchars($inv['due_date']); ?></div>
                                    </div>
                                    <div class="invoice-amount-tag">$<?php echo number_format($inv['amount'], 2); ?></div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li style="text-align: center; padding: 2rem 0; color: var(--text-muted); list-style: none;">
                                <i class="fa-solid fa-circle-check" style="font-size: 2.2rem; color: #10b981; margin-bottom: 1rem; display: block;"></i>
                                <strong>No Unpaid Invoices</strong>
                                <p style="font-size: 0.8rem; margin-top: 0.25rem;">No pending tuition or curriculum fees are recorded.</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <!-- Script triggers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Theme toggle
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const htmlElement = document.documentElement;

            const savedTheme = localStorage.getItem('theme') || 'dark';
            setTheme(savedTheme);

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });

            function setTheme(theme) {
                 htmlElement.setAttribute('data-theme', theme);
                 localStorage.setItem('theme', theme);
                 if (theme === 'dark') {
                    themeIcon.className = 'fa-solid fa-sun';
                    themeToggleBtn.style.color = '#eab308';
                } else {
                    themeIcon.className = 'fa-solid fa-moon';
                    themeToggleBtn.style.color = '#4f46e5';
                }
            }

            // Sidebar Navigation redirection helper
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href && href.indexOf('student_dashboard.php') !== -1) {
                    link.addEventListener('click', () => {
                        localStorage.setItem('activeStudentTab', link.id);
                    });
                }
            });

            // Payment Form interactions
            const checkoutMethod = document.getElementById('checkout-method');
            const gpayFields = document.getElementById('gpay-fields-container');
            const upiFields = document.getElementById('upi-fields-container');
            const rtgsFields = document.getElementById('rtgs-fields-container');
            
            const paymentForm = document.getElementById('fees-payment-checkout-form');

            if (checkoutMethod) {
                checkoutMethod.addEventListener('change', () => {
                    const method = checkoutMethod.value;
                    gpayFields.style.display = 'none';
                    upiFields.style.display = 'none';
                    rtgsFields.style.display = 'none';

                    if (method === 'GPay') {
                        gpayFields.style.display = 'block';
                    } else if (method === 'UPI') {
                        upiFields.style.display = 'block';
                    } else if (method === 'RTGS') {
                        rtgsFields.style.display = 'block';
                    }
                });
            }

            if (paymentForm) {
                paymentForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const invoiceNum = document.getElementById('checkout-invoice').value;
                    if (!invoiceNum) {
                        Swal.fire({
                            title: 'Account Fully Cleared!',
                            text: 'No outstanding bills are registered on your account.',
                            icon: 'success',
                            confirmButtonColor: 'var(--primary)'
                        });
                        return;
                    }
                    const payMethod = document.getElementById('checkout-method').value;

                    // Form Validation for payment fields
                    if (payMethod === 'GPay') {
                        const gpayMobile = document.getElementById('gpay-mobile').value.trim();
                        if (!gpayMobile) {
                            Swal.fire({
                                title: 'Missing Information',
                                text: 'Please fill out your Google Pay Mobile Number or UPI ID.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }
                    } else if (payMethod === 'UPI') {
                        const upiId = document.getElementById('upi-id').value.trim();
                        if (!upiId) {
                            Swal.fire({
                                title: 'Missing UPI Address',
                                text: 'Please input your UPI Virtual Payment Address (VPA).',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }
                    }

                    // Process Payment gateway load
                    Swal.fire({
                        title: 'Authorizing Transaction...',
                        text: 'Verifying payment with bank network gateway.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const payData = new FormData();
                    payData.append('invoice_number', invoiceNum);
                    payData.append('payment_method', payMethod);

                    fetch('update_invoice_status.php', {
                        method: 'POST',
                        body: payData
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            Swal.fire({
                                title: 'Payment Succeeded!',
                                text: `Your payment for Invoice ${invoiceNum} has been processed successfully.`,
                                icon: 'success',
                                confirmButtonColor: 'var(--primary)'
                            }).then(() => {
                                // Redirect back to Dashboard's Overview Tab
                                localStorage.setItem('activeStudentTab', 'tab-student-overview');
                                window.location.href = 'student_dashboard.php';
                            });
                        } else {
                            Swal.fire({
                                title: 'Transaction Declined',
                                text: data.error || 'Transaction could not be completed.',
                                icon: 'error',
                                confirmButtonColor: 'var(--accent)'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        console.error('Payment checkout error:', err);
                        Swal.fire({
                            title: 'Gateway Connection Failed',
                            text: 'Ensure the local server is running.',
                            icon: 'error',
                            confirmButtonColor: 'var(--accent)'
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>
