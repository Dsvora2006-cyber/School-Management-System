<?php
require_once 'db_connect.php';

// Handle AJAX search request to fetch student invoices
if (isset($_GET['action']) && $_GET['action'] === 'search_student') {
    header('Content-Type: application/json');
    $student_id = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';

    if (empty($student_id)) {
        echo json_encode(['success' => false, 'error' => 'Please enter a student enrollment number.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT first_name, last_name, grade FROM students WHERE student_id = ?");
    if ($stmt) {
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res && $res->num_rows > 0) {
            $student = $res->fetch_assoc();
            
            // Get unpaid invoices
            $invoices = [];
            $inv_stmt = $conn->prepare("SELECT invoice_number, title, amount, due_date FROM invoices WHERE student_id = ? AND status = 'Unpaid' ORDER BY id ASC");
            if ($inv_stmt) {
                $inv_stmt->bind_param("s", $student_id);
                $inv_stmt->execute();
                $res_inv = $inv_stmt->get_result();
                while ($row = $res_inv->fetch_assoc()) {
                    $invoices[] = [
                        'invoice_number' => $row['invoice_number'],
                        'title' => $row['title'],
                        'amount' => (float)$row['amount'],
                        'due_date' => $row['due_date']
                    ];
                }
                $inv_stmt->close();
            }

            echo json_encode([
                'success' => true,
                'student_name' => $student['first_name'] . ' ' . $student['last_name'],
                'grade' => $student['grade'],
                'invoices' => $invoices
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No student found with this enrollment number.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database search query failed.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Fees Payment Portal - AWS</title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .portal-container {
            max-width: 900px;
            margin: 5rem auto;
            padding: 0 1.5rem;
        }

        .portal-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .portal-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .portal-header h1 span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .portal-header p {
            color: var(--text-muted);
            font-size: 1.05rem;
        }

        .search-card, .pay-grid-container {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            backdrop-filter: blur(var(--glass-blur));
        }

        .search-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .search-input {
            flex: 1;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary);
        }

        /* Split view */
        .pay-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .checkout-box-title {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-main);
        }

        .pay-form-group {
            margin-bottom: 1.25rem;
        }

        .pay-form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .checkout-select-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
        }

        .card-entry-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed var(--card-border);
            padding: 1.25rem;
            border-radius: 12px;
            margin-top: 1rem;
        }

        .student-profile-summary {
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            padding: 1.25rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .summary-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .summary-info h4 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .summary-info p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        .invoice-bill-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .invoice-bill-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            padding: 0.85rem 1.15rem;
            border-radius: 10px;
        }

        .bill-amount {
            font-weight: 800;
            color: var(--accent);
        }

        .back-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .pay-grid {
                grid-template-columns: 1fr;
            }
            .search-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="portal-container">
        <div class="portal-header">
            <a href="index.php" style="text-decoration: none; display: inline-flex; align-items: center; margin-bottom: 1rem; font-size: 1.75rem; font-weight: 800; color: var(--text-main); gap: 0.5rem;">
                <img src="images/logo.png" alt="Logo" style="height: 56px; width: auto; border-radius: 6px; -webkit-text-fill-color: initial; vertical-align: middle;"> AWS
            </a>
            <h1><span>Fees Payment</span> Portal</h1>
            <p>Access school term bills and settle tuition fees online without logging in.</p>
        </div>

        <!-- State 1: Search Form -->
        <div id="search-section" class="search-card">
            <h3 style="font-size: 1.2rem; font-weight: 800;"><i class="fa-solid fa-magnifying-glass" style="margin-right: 0.5rem; color: var(--primary);"></i> Search Student Account</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Enter the student's unique enrollment ID to fetch pending unpaid invoices (e.g. APX-2026-9481).</p>
            
            <form id="search-portal-form">
                <div class="search-group">
                    <input type="text" id="portal-student-id" class="search-input" placeholder="Enter Enrollment No (e.g. APX-2026-9481)" required>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px; padding: 0.85rem 1.75rem;"><i class="fa-solid fa-cloud-arrow-down"></i> Search Invoices</button>
                </div>
            </form>
        </div>

        <!-- State 2: Checkout Form (Hidden by Default) -->
        <div id="checkout-section" class="pay-grid-container" style="display: none;">
            <div class="back-nav" id="back-to-search-btn"><i class="fa-solid fa-arrow-left"></i> Back to Search</div>
            
            <div class="student-profile-summary">
                <div class="summary-avatar" id="student-avatar-badge">LA</div>
                <div class="summary-info">
                    <h4 id="student-name-text">Liam Anderson</h4>
                    <p id="student-meta-text">Enrollment: APX-2026-9481 • Standard: Grade 10</p>
                </div>
            </div>

            <div class="pay-grid">
                <!-- Left checkout form -->
                <div class="checkout-actions">
                    <div class="checkout-box-title">
                        <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i> Settle Payment
                    </div>
                    <form id="checkout-portal-form">
                        <!-- Invoice select -->
                        <div class="pay-form-group">
                            <label for="invoice-select">Select Outstanding Invoice</label>
                            <select id="invoice-select" class="checkout-select-input" required></select>
                        </div>

                        <!-- Method select -->
                        <div class="pay-form-group">
                            <label for="method-select">Payment Method</label>
                            <select id="method-select" class="checkout-select-input" required>
                                <option value="GPay">Google Pay (GPay)</option>
                                <option value="UPI">Unified Payments Interface (UPI)</option>
                                <option value="RTGS">RTGS / Direct Bank Transfer</option>
                            </select>
                        </div>

                        <!-- 1. Google Pay (GPay) Input block -->
                        <div id="gpay-fields" class="card-entry-box" style="display: block;">
                            <div class="pay-form-group">
                                <label for="gpay-mobile">Google Pay Registered Mobile Number / VPA ID</label>
                                <input type="text" id="gpay-mobile" class="checkout-select-input" placeholder="e.g. +1 555-019-2834 or liam@okaxis" style="background: var(--bg-main);">
                            </div>
                        </div>

                        <!-- 2. UPI Input Block -->
                        <div id="upi-fields" class="card-entry-box" style="display: none;">
                            <div class="pay-form-group">
                                <label for="upi-id">UPI Virtual Payment Address (VPA)</label>
                                <input type="text" id="upi-id" class="checkout-select-input" placeholder="e.g. liam@okaxis" style="background: var(--bg-main);">
                            </div>
                        </div>

                        <!-- 3. RTGS Transfer Block -->
                        <div id="rtgs-fields" class="card-entry-box" style="display: none; background: rgba(6, 182, 212, 0.03);">
                            <div style="font-size: 0.85rem; line-height: 1.6; color: var(--text-main);">
                                <strong style="color: var(--secondary); display: block; font-size: 0.95rem; margin-bottom: 0.5rem;"><i class="fa-solid fa-building-columns"></i> AWS Corporate RTGS Dues</strong>
                                <span style="display: block; margin-bottom: 0.25rem;">Bank Name: <strong>AWS Development Bank</strong></span>
                                <span style="display: block; margin-bottom: 0.25rem;">Account Number: <strong>9876-5432-1098</strong></span>
                                <span style="display: block; margin-bottom: 0.25rem;">RTGS / IFSC / Routing Code: <strong>APEX0009988</strong></span>
                                <span style="display: block; margin-bottom: 0.75rem;">Account Type: <strong>Checking / Corporate</strong></span>
                                <p style="font-size: 0.75rem; color: var(--text-muted); border-top: 1px dashed var(--card-border); padding-top: 0.5rem;">Please transfer the exact invoice amount via RTGS. Click the authorize button below once the transaction completes to update dashboard records.</p>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 12px; padding: 0.9rem; font-weight: 700; margin-top: 1.5rem; gap: 0.5rem;">
                            <i class="fa-solid fa-shield-check"></i> Settle Billing Invoice
                        </button>
                    </form>
                </div>

                <!-- Right list overview -->
                <div class="checkout-overview">
                    <div class="checkout-box-title">
                        <i class="fa-solid fa-file-invoice" style="color: var(--secondary);"></i> Pending Bills
                    </div>
                    <ul id="pending-bills-list" class="invoice-bill-list"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Portal Controller -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchSection = document.getElementById('search-section');
            const checkoutSection = document.getElementById('checkout-section');
            
            const searchForm = document.getElementById('search-portal-form');
            const checkoutForm = document.getElementById('checkout-portal-form');
            const backToSearchBtn = document.getElementById('back-to-search-btn');

            let currentStudentId = '';

            // Handle student invoice search
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const studentId = document.getElementById('portal-student-id').value.trim();

                Swal.fire({
                    title: 'Retrieving Invoices...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`fees_payment_portal.php?action=search_student&student_id=${encodeURIComponent(studentId)}`)
                .then(res => res.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        currentStudentId = studentId;
                        renderCheckoutPage(data);
                    } else {
                        Swal.fire({
                            title: 'Search Failed',
                            text: data.error || 'Student not found.',
                            icon: 'error',
                            confirmButtonColor: 'var(--accent)'
                        });
                    }
                })
                .catch(err => {
                    Swal.close();
                    console.error('Search error:', err);
                    Swal.fire({
                        title: 'Server Error',
                        text: 'Connection failed. Verify database is running.',
                        icon: 'error',
                        confirmButtonColor: 'var(--accent)'
                    });
                });
            });

            // Render checkout state
            function renderCheckoutPage(data) {
                document.getElementById('student-name-text').textContent = data.student_name;
                document.getElementById('student-meta-text').textContent = `Enrollment: ${currentStudentId} • Standard: Grade ${data.grade}`;
                document.getElementById('student-avatar-badge').textContent = data.student_name.split(' ').map(n => n[0]).join('');

                const invoiceSelect = document.getElementById('invoice-select');
                const billsList = document.getElementById('pending-bills-list');
                
                invoiceSelect.innerHTML = '';
                billsList.innerHTML = '';

                if (data.invoices.length === 0) {
                    Swal.fire({
                        title: 'All Cleared!',
                        text: 'No outstanding bills are recorded for this student.',
                        icon: 'success',
                        confirmButtonColor: 'var(--primary)'
                    });
                    return;
                }

                data.invoices.forEach(inv => {
                    // Populate select
                    const opt = document.createElement('option');
                    opt.value = inv.invoice_number;
                    opt.textContent = `${inv.invoice_number} - ${inv.title} ($${inv.amount.toFixed(2)})`;
                    invoiceSelect.appendChild(opt);

                    // Populate summary list
                    const li = document.createElement('li');
                    li.className = 'invoice-bill-item';
                    li.innerHTML = `
                        <div>
                            <div style="font-weight:700; color:var(--text-main);">${inv.title}</div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.15rem;">Invoice: ${inv.invoice_number} • Due: ${inv.due_date}</div>
                        </div>
                        <div class="bill-amount">$${inv.amount.toFixed(2)}</div>
                    `;
                    billsList.appendChild(li);
                });

                searchSection.style.display = 'none';
                checkoutSection.style.display = 'block';
            }

            // Back button
            backToSearchBtn.addEventListener('click', () => {
                checkoutSection.style.display = 'none';
                searchSection.style.display = 'block';
                document.getElementById('portal-student-id').value = '';
            });

            // Toggle payment fields
            const methodSelect = document.getElementById('method-select');
            const gpayFields = document.getElementById('gpay-fields');
            const upiFields = document.getElementById('upi-fields');
            const rtgsFields = document.getElementById('rtgs-fields');

            methodSelect.addEventListener('change', () => {
                gpayFields.style.display = 'none';
                upiFields.style.display = 'none';
                rtgsFields.style.display = 'none';

                if (methodSelect.value === 'GPay') {
                    gpayFields.style.display = 'block';
                } else if (methodSelect.value === 'UPI') {
                    upiFields.style.display = 'block';
                } else if (methodSelect.value === 'RTGS') {
                    rtgsFields.style.display = 'block';
                }
            });

            // Handle invoice payment
            checkoutForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const invoiceNum = document.getElementById('invoice-select').value;
                const payMethod = methodSelect.value;

                if (payMethod === 'GPay') {
                    const gpayMobile = document.getElementById('gpay-mobile').value.trim();
                    if (!gpayMobile) {
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please input Google Pay Mobile Number or UPI ID.',
                            icon: 'warning',
                            confirmButtonColor: 'var(--primary)'
                        });
                        return;
                    }
                } else if (payMethod === 'UPI') {
                    const upiId = document.getElementById('upi-id').value.trim();
                    if (!upiId) {
                        Swal.fire({
                            title: 'Validation Error',
                            text: 'Please input UPI Virtual Payment Address (VPA).',
                            icon: 'warning',
                            confirmButtonColor: 'var(--primary)'
                        });
                        return;
                    }
                }

                Swal.fire({
                    title: 'Authorizing Payment...',
                    text: 'Connecting to gateway network.',
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
                            title: 'Transaction Successful!',
                            text: `Invoice ${invoiceNum} has been paid and cleared successfully.`,
                            icon: 'success',
                            confirmButtonColor: 'var(--primary)'
                        }).then(() => {
                            // Reload search trigger to refresh invoice balances
                            searchForm.dispatchEvent(new Event('submit'));
                        });
                    } else {
                        Swal.fire({
                            title: 'Payment Failed',
                            text: data.error || 'Transaction could not be cleared.',
                            icon: 'error',
                            confirmButtonColor: 'var(--accent)'
                        });
                    }
                })
                .catch(err => {
                    Swal.close();
                    console.error('Portal checkout error:', err);
                    Swal.fire({
                        title: 'Gateway Failure',
                        text: 'Connection gateway failed. Try again later.',
                        icon: 'error',
                        confirmButtonColor: 'var(--accent)'
                    });
                });
            });
        });
    </script>
</body>
</html>
