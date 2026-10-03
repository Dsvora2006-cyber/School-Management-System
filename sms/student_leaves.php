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
$stmt = $conn->prepare("SELECT first_name, last_name, grade FROM students WHERE student_id = ?");
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
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Leave Requests - <?php echo $school_name; ?></title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 for Premium Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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

        /* Table Design */
        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-top: 1rem;
            overflow-x: auto;
        }

        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .table-header-row h4 {
            font-size: 1.25rem;
            font-weight: 800;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .students-table th {
            padding: 1rem;
            border-bottom: 2px solid var(--card-border);
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.9rem;
        }

        .students-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--card-border);
            font-size: 0.95rem;
        }

        .students-table tr:last-child td {
            border-bottom: none;
        }

        .students-table tbody tr:hover {
            background-color: var(--primary-glow);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-pending {
            background: rgba(234, 179, 8, 0.15);
            color: #eab308;
        }

        .status-approved {
            background: rgba(39, 201, 63, 0.15);
            color: #27c93f;
        }

        .status-rejected {
            background: rgba(244, 63, 94, 0.15);
            color: var(--accent);
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
                    <li><a href="student_leaves.php" class="sidebar-link active" id="tab-student-leaves"><i class="fa-solid fa-calendar-minus"></i> Leave Requests</a></li>
                    <li><a href="student_marks.php" class="sidebar-link" id="tab-student-marks"><i class="fa-solid fa-square-poll-vertical"></i> Marks</a></li>
                    <li><a href="pay_fees.php" class="sidebar-link" id="tab-student-fees"><i class="fa-solid fa-file-invoice-dollar"></i> Fees</a></li>
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

        <!-- Main Workspace -->
        <main class="workspace">
            <div class="top-panel">
                <div class="welcome-title">
                    <h2>Leave Permission Requests</h2>
                    <p>Submit absence applications and view current permission approval status records.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" style="margin-right: 0.5rem;" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Leaves List Section -->
            <div class="table-container">
                <div class="table-header-row">
                    <h4>Leave Application & History</h4>
                    <button class="btn btn-primary" id="apply-leave-btn"><i class="fa-solid fa-calendar-plus"></i> Apply for Leave</button>
                </div>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Leave ID</th>
                            <th>Leave Subject</th>
                            <th>Reason Details</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Date Applied</th>
                            <th>Permission Status</th>
                        </tr>
                    </thead>
                    <tbody id="leaves-table-body">
                        <?php
                        $sql_lv = "SELECT * FROM leaves WHERE student_id = ? ORDER BY id DESC";
                        $stmt_lv = $conn->prepare($sql_lv);
                        if ($stmt_lv) {
                            $stmt_lv->bind_param("s", $student_id);
                            $stmt_lv->execute();
                            $res_lv = $stmt_lv->get_result();
                            if ($res_lv && $res_lv->num_rows > 0) {
                                while ($row_lv = $res_lv->fetch_assoc()) {
                                    $lvId = htmlspecialchars($row_lv['leave_id']);
                                    $lvSub = htmlspecialchars($row_lv['subject']);
                                    $lvReason = htmlspecialchars($row_lv['reason']);
                                    $lvStart = htmlspecialchars($row_lv['start_date']);
                                    $lvEnd = htmlspecialchars($row_lv['end_date']);
                                    $lvCreated = htmlspecialchars(date('Y-m-d', strtotime($row_lv['created_at'])));
                                    $lvStatus = htmlspecialchars($row_lv['status']);

                                    $badgeClass = 'status-pending';
                                    $badgeIcon = 'fa-spinner';
                                    if (strtolower($lvStatus) === 'approved') {
                                        $badgeClass = 'status-approved';
                                        $badgeIcon = 'fa-circle-check';
                                    } elseif (strtolower($lvStatus) === 'rejected') {
                                        $badgeClass = 'status-rejected';
                                        $badgeIcon = 'fa-circle-xmark';
                                    }

                                    echo '<tr>';
                                    echo '<td style="font-weight: 700; color: var(--text-main);">' . $lvId . '</td>';
                                    echo '<td style="font-weight: 700;">' . $lvSub . '</td>';
                                    echo '<td style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="' . $lvReason . '">' . $lvReason . '</td>';
                                    echo '<td>' . $lvStart . '</td>';
                                    echo '<td>' . $lvEnd . '</td>';
                                    echo '<td>' . $lvCreated . '</td>';
                                    echo '<td><span class="status-badge ' . $badgeClass . '"><i class="fa-solid ' . $badgeIcon . '"></i> ' . $lvStatus . '</span></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">No leave request history is registered under your student ID yet.</td></tr>';
                            }
                            $stmt_lv->close();
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Apply Leave Modal Overlay -->
    <div class="modal-overlay" id="leave-modal-overlay">
        <div class="modal-container">
            <button class="modal-close" id="leave-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header">
                <h2>Request Leave Permission</h2>
                <p>Submit your absence details for academic head review approval.</p>
            </div>
            
            <form id="apply-leave-form">
                <div id="leave-error" style="color: var(--accent); font-size: 0.85rem; margin-bottom: 1rem; display: none; text-align: center; font-weight: 600;"></div>
                
                <div class="form-group">
                    <label class="form-label" for="leave-subject">Leave Subject / Topic</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-pen-nib input-icon"></i>
                        <input type="text" id="leave-subject" class="form-control" placeholder="e.g. Dental Treatment Appointment" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="leave-reason">Reason Description</label>
                    <textarea id="leave-reason" class="form-control" placeholder="Provide complete explanation details..." style="padding: 0.85rem 1.25rem; min-height: 100px; font-family: inherit; resize: vertical;" required></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="leave-start-date">Start Date</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-calendar-days input-icon"></i>
                            <input type="date" id="leave-start-date" class="form-control" required min="<?php echo date('Y-m-d'); ?>" style="padding-left: 2.8rem;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="leave-end-date">End Date</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-calendar-days input-icon"></i>
                            <input type="date" id="leave-end-date" class="form-control" required min="<?php echo date('Y-m-d'); ?>" style="padding-left: 2.8rem;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary submit-btn" style="margin-top: 1rem;">
                    Submit Leave Request <i class="fa-solid fa-paper-plane" style="margin-left: 0.25rem;"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const htmlElement = document.documentElement;
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');

            // Apply theme on load
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

            // --- Leave Apply Modal System ---
            const applyLeaveBtn = document.getElementById('apply-leave-btn');
            const leaveModalOverlay = document.getElementById('leave-modal-overlay');
            const leaveModalClose = document.getElementById('leave-modal-close');
            const applyLeaveForm = document.getElementById('apply-leave-form');
            const leaveErrorDiv = document.getElementById('leave-error');

            if (applyLeaveBtn) {
                applyLeaveBtn.addEventListener('click', () => {
                    leaveErrorDiv.style.display = 'none';
                    applyLeaveForm.reset();
                    leaveModalOverlay.classList.add('active');
                });
            }

            if (leaveModalClose) {
                leaveModalClose.addEventListener('click', () => {
                    leaveModalOverlay.classList.remove('active');
                });
            }

            if (leaveModalOverlay) {
                leaveModalOverlay.addEventListener('click', (e) => {
                    if (e.target === leaveModalOverlay) {
                        leaveModalOverlay.classList.remove('active');
                    }
                });
            }

            // Auto open modal on load if URL parameter matches
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'apply') {
                leaveErrorDiv.style.display = 'none';
                applyLeaveForm.reset();
                leaveModalOverlay.classList.add('active');
            }

            // Submit Leave AJAX Form
            if (applyLeaveForm) {
                applyLeaveForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    leaveErrorDiv.style.display = 'none';

                    const subject = document.getElementById('leave-subject').value.trim();
                    const reason = document.getElementById('leave-reason').value.trim();
                    const start_date = document.getElementById('leave-start-date').value;
                    const end_date = document.getElementById('leave-end-date').value;

                    const formData = new FormData();
                    formData.append('subject', subject);
                    formData.append('reason', reason);
                    formData.append('start_date', start_date);
                    formData.append('end_date', end_date);

                    fetch('save_leave.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            leaveModalOverlay.classList.remove('active');
                            Swal.fire({
                                title: 'Leave Applied!',
                                text: `Your leave request (${data.leave_id}) has been submitted successfully for verification review.`,
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.href = 'student_leaves.php';
                            });
                        } else {
                            leaveErrorDiv.textContent = data.error || 'Failed to submit leave request application details.';
                            leaveErrorDiv.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Leave submission error:', err);
                        leaveErrorDiv.textContent = 'Server connection gateway failed. Check database logs.';
                        leaveErrorDiv.style.display = 'block';
                    });
                });
            }
        });
    </script>
</body>
</html>
