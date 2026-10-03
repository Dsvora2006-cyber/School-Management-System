<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify Session
if (!isset($_SESSION['student_logged_in']) || $_SESSION['student_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Handle Logout Action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
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
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
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
    die("Database query error: student retrieval failed.");
}

$first_name = htmlspecialchars($student['first_name']);
$last_name = htmlspecialchars($student['last_name']);
$grade = htmlspecialchars($student['grade']);

// KPI Stats Computation
// 1. Submitted Leaves Count
$leaves_count = 0;
$stmt_lv = $conn->prepare("SELECT COUNT(*) as cnt FROM leaves WHERE student_id = ?");
if ($stmt_lv) {
    $stmt_lv->bind_param("s", $student_id);
    $stmt_lv->execute();
    $res_lv = $stmt_lv->get_result()->fetch_assoc();
    $leaves_count = $res_lv['cnt'] ?? 0;
    $stmt_lv->close();
}

// 2. Active Homework
$homework_count = 0;
$stmt_hw = $conn->prepare("SELECT COUNT(*) as cnt FROM homework h JOIN classes c ON h.class_id = c.class_id WHERE c.standard = ?");
if ($stmt_hw) {
    $stmt_hw->bind_param("s", $grade);
    $stmt_hw->execute();
    $res_hw = $stmt_hw->get_result()->fetch_assoc();
    $homework_count = $res_hw['cnt'] ?? 0;
    $stmt_hw->close();
}

// 3. Pending Unpaid Fees Total
$due_amount = 0.00;
$stmt_fees = $conn->prepare("SELECT SUM(amount) as total FROM invoices WHERE student_id = ? AND status = 'Unpaid'");
if ($stmt_fees) {
    $stmt_fees->bind_param("s", $student_id);
    $stmt_fees->execute();
    $res_fees = $stmt_fees->get_result()->fetch_assoc();
    $due_amount = $res_fees['total'] ?? 0.00;
    $stmt_fees->close();
}

// 4. Academic Performance Average
$average_marks = 0.0;
$stmt_marks = $conn->prepare("SELECT AVG(marks_obtained) as avg_marks FROM marks WHERE student_id = ?");
if ($stmt_marks) {
    $stmt_marks->bind_param("s", $student_id);
    $stmt_marks->execute();
    $res_marks = $stmt_marks->get_result()->fetch_assoc();
    $average_marks = number_format($res_marks['avg_marks'] ?? 0.0, 1);
    $stmt_marks->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AWS - Student Dashboard Portal Workspace">
    <title>Student Dashboard - <?php echo $school_name; ?></title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Premium Icons -->
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
            margin-bottom: 2rem;
            gap: 2rem;
        }



        /* Stats Dashboard Grid */
        .dash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .dash-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            border-color: rgba(79, 70, 229, 0.25);
        }

        .dash-card-info h4 {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .dash-card-info h3 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .dash-card-icon {
            width: 54px;
            height: 54px;
            background: var(--primary-glow);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .dash-card-trend {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .trend-up { color: #27c93f; }
        .trend-down { color: var(--accent); }

        /* Dashboard Sections Layout */
        .dash-sections {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .activity-panel, .actions-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 1rem;
        }

        .panel-header h4 {
            font-size: 1.2rem;
            font-weight: 800;
        }

        /* Activity Items */
        .activity-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .activity-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            transition: all 0.2s ease;
            border-radius: 12px;
            padding: 0.5rem;
        }

        .activity-item:hover {
            background: rgba(255, 255, 255, 0.02);
            transform: translateX(4px);
        }

        .activity-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--input-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
            color: var(--secondary);
        }

        .activity-desc h5 {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
            color: var(--text-main);
        }

        .activity-desc p {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .activity-time {
            margin-left: auto;
            font-size: 0.75rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Quick Action Buttons Grid */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            border-color: var(--primary);
            background: var(--primary-glow);
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .action-btn i {
            color: var(--primary);
        }

        @media (max-width: 992px) {
            .dash-sections {
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
                    <li><a href="student_dashboard.php" class="sidebar-link active" id="tab-student-overview"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="student_schedule.php" class="sidebar-link" id="tab-student-schedule"><i class="fa-solid fa-calendar-days"></i> Timetable</a></li>
                    <li><a href="student_homework.php" class="sidebar-link" id="tab-student-homework"><i class="fa-solid fa-book"></i> Homework</a></li>
                    <li><a href="student_leaves.php" class="sidebar-link" id="tab-student-leaves"><i class="fa-solid fa-calendar-minus"></i> Leave Requests</a></li>
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
            <!-- Academic Portal Workspace Banner -->
            <div class="welcome-banner" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(6, 182, 212, 0.08)); border: 1px solid var(--card-border); border-radius: 24px; padding: 2rem 2.5rem; margin-bottom: 2.5rem; display: flex; align-items: center; justify-content: space-between; gap: 2rem; box-shadow: var(--shadow); position: relative; overflow: hidden; margin-top: 1rem;">
                <div>
                    <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        Academic Portal Workspace <i class="fa-solid fa-graduation-cap" style="color: var(--primary);"></i>
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; max-width: 700px; margin: 0;">
                        Track your studies, grades, bills, and class schedules at a glance. Manage your school activities efficiently.
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
                    <button class="theme-toggle" id="theme-toggle" style="width: 44px; height: 44px; border-radius: 12px; border: 1px solid var(--card-border); background: var(--card-bg); color: var(--text-main); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>


            <!-- Workspace Section Containers -->
            <div class="student-workspace-contents">

                <!-- Tab 1: Overview Section -->
                <div id="student-overview-section" style="display: block;">
                    <!-- KPI Cards Grid -->
                    <div class="dash-grid">
                        <!-- Card 1: Active Homework -->
                        <div class="dash-card">
                            <div class="dash-card-info">
                                <h4>Active Homework</h4>
                                <h3><?php echo $homework_count; ?> Tasks</h3>
                                <span class="dash-card-trend trend-up"><i class="fa-solid fa-book-open"></i> Pending submissions</span>
                            </div>
                            <div class="dash-card-icon" style="color: var(--secondary); background: rgba(6, 182, 212, 0.15);">
                                <i class="fa-solid fa-book"></i>
                            </div>
                        </div>

                        <!-- Card 2: Leaves Count -->
                        <div class="dash-card">
                            <div class="dash-card-info">
                                <h4>Absence Leaves</h4>
                                <h3><?php echo $leaves_count; ?> Request<?php echo $leaves_count === 1 ? '' : 's'; ?></h3>
                                <span class="dash-card-trend trend-up"><i class="fa-solid fa-circle-question"></i> Permitted absence applications</span>
                            </div>
                            <div class="dash-card-icon" style="color: #eab308; background: rgba(234, 179, 8, 0.15);">
                                <i class="fa-solid fa-calendar-minus"></i>
                            </div>
                        </div>

                        <!-- Card 3: Pending Fees -->
                        <div class="dash-card">
                            <div class="dash-card-info">
                                <h4>Pending Fees Due</h4>
                                <h3>$<?php echo number_format($due_amount, 2); ?></h3>
                                <span class="dash-card-trend trend-down" style="color: <?php echo $due_amount > 0 ? 'var(--accent)' : '#27c93f'; ?>;">
                                    <i class="fa-solid <?php echo $due_amount > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check'; ?>"></i> 
                                    <?php echo $due_amount > 0 ? 'Outstanding balance' : 'No pending dues'; ?>
                                </span>
                            </div>
                            <div class="dash-card-icon" style="color: #f43f5e; background: rgba(244, 63, 94, 0.15);">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                        </div>

                        <!-- Card 4: Academic Performance -->
                        <div class="dash-card">
                            <div class="dash-card-info">
                                <h4>Academic Avg. Marks</h4>
                                <h3><?php echo $average_marks; ?>%</h3>
                                <span class="dash-card-trend trend-up"><i class="fa-solid fa-graduation-cap"></i> Overall grading marks</span>
                            </div>
                            <div class="dash-card-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">
                                <i class="fa-solid fa-square-poll-vertical"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Split View Grid -->
                    <div class="dash-sections">
                        <!-- Announcements Box -->
                        <div class="activity-panel">
                            <div class="panel-header">
                                <h4>Academic Alerts & Announcements</h4>
                            </div>
                            <ul class="activity-list">
                                <?php
                                $sql_inv_alert = "SELECT invoice_number, title, amount, due_date FROM invoices WHERE student_id = ? AND status = 'Unpaid' ORDER BY id DESC";
                                $stmt_inv_alert = $conn->prepare($sql_inv_alert);
                                if ($stmt_inv_alert) {
                                    $stmt_inv_alert->bind_param("s", $student_id);
                                    $stmt_inv_alert->execute();
                                    $res_inv_alert = $stmt_inv_alert->get_result();
                                    while ($row_inv_alert = $res_inv_alert->fetch_assoc()) {
                                        $alertTitle = htmlspecialchars($row_inv_alert['title']);
                                        $alertNo = htmlspecialchars($row_inv_alert['invoice_number']);
                                        $alertAmount = number_format($row_inv_alert['amount'], 2);
                                        $alertDue = htmlspecialchars($row_inv_alert['due_date']);
                                        ?>
                                        <li class="activity-item">
                                            <div class="activity-badge" style="color: var(--accent);"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                            <div class="activity-desc">
                                                <h5 style="color: var(--accent);">Outstanding Invoice Issued</h5>
                                                <p>New fee invoice <strong><?php echo $alertNo; ?></strong> (<?php echo $alertTitle; ?>) of <strong>$<?php echo $alertAmount; ?></strong> has been generated. Due Date: <strong><?php echo $alertDue; ?></strong>.</p>
                                            </div>
                                            <div class="activity-time" style="color: var(--accent); font-weight: 700;">Unpaid</div>
                                        </li>
                                        <?php
                                    }
                                    $stmt_inv_alert->close();
                                }
                                ?>
                                <li class="activity-item">
                                    <div class="activity-badge" style="color: var(--primary);"><i class="fa-solid fa-bullhorn"></i></div>
                                    <div class="activity-desc">
                                        <h5>Mid-Term Exam Schedule Released</h5>
                                        <p>Please check the Timetable tab to review the scheduled assessment timeline for Grade <?php echo $grade; ?> classes.</p>
                                    </div>
                                    <div class="activity-time">2 hours ago</div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-badge" style="color: var(--secondary);"><i class="fa-solid fa-shield-halved"></i></div>
                                    <div class="activity-desc">
                                        <h5>Student Portal Upgraded</h5>
                                        <p>You can now check pending term fees balance online and apply for leave permissions directly using the leaves tab request panel.</p>
                                    </div>
                                    <div class="activity-time">Yesterday</div>
                                </li>
                            </ul>
                        </div>

                        <!-- Quick Links Box -->
                        <div class="actions-panel">
                            <div class="panel-header">
                                <h4>Quick Navigation Links</h4>
                            </div>
                            <div class="action-buttons">
                                <a href="student_leaves.php?action=apply" class="action-btn">
                                    <i class="fa-solid fa-calendar-plus"></i> Request Leave Permission
                                </a>
                                <a href="student_schedule.php" class="action-btn">
                                    <i class="fa-solid fa-calendar-days"></i> Check Class Timetable
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
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
        });
    </script>
</body>
</html>
