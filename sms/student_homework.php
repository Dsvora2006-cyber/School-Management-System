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
    <title>My Homework Assignments - <?php echo $school_name; ?></title>
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
                    <li><a href="student_homework.php" class="sidebar-link active" id="tab-student-homework"><i class="fa-solid fa-book"></i> Homework</a></li>
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
            <div class="top-panel">
                <div class="welcome-title">
                    <h2>Homework Assignments</h2>
                    <p>Track pending homework questions, submission instructions, and active due dates.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" style="margin-right: 0.5rem;" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Homework Section -->
            <div class="table-container" id="student-homework-section">
                <div class="table-header-row">
                    <h4>Active Curriculum Assignments</h4>
                </div>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Assignment Name</th>
                            <th>Subject Class</th>
                            <th>Assigned Instructor</th>
                            <th>Submission Deadline</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_h = "SELECT h.*, c.class_name, t.first_name, t.last_name FROM homework h JOIN classes c ON h.class_id = c.class_id JOIN teachers t ON h.teacher_id = t.teacher_id WHERE c.standard = ? ORDER BY h.due_date DESC";
                        $stmt_h = $conn->prepare($sql_h);
                        if ($stmt_h) {
                            $stmt_h->bind_param("s", $grade);
                            $stmt_h->execute();
                            $res_h = $stmt_h->get_result();
                            if ($res_h && $res_h->num_rows > 0) {
                                while ($row_h = $res_h->fetch_assoc()) {
                                    $hwTitle = htmlspecialchars($row_h['title']);
                                    $hwDesc = htmlspecialchars($row_h['description']);
                                    $hwSubject = htmlspecialchars($row_h['subject']);
                                    $hwClass = htmlspecialchars($row_h['class_name']);
                                    $hwDate = htmlspecialchars($row_h['due_date']);
                                    $hwTeacher = htmlspecialchars($row_h['first_name'] . ' ' . $row_h['last_name']);
                                    
                                    // Format deadline date badge
                                    $today = date('Y-m-d');
                                    $badgeClass = ($hwDate < $today) ? 'status-rejected' : 'status-pending';
                                    $badgeIcon = ($hwDate < $today) ? 'fa-triangle-exclamation' : 'fa-hourglass-half';
                                    $badgeText = ($hwDate < $today) ? 'Past Due' : 'Active';

                                    echo '<tr>';
                                    echo '<td style="font-weight: 700; color: var(--text-main);">' . $hwTitle . '</td>';
                                    echo '<td>' . $hwClass . ' (' . $hwSubject . ')</td>';
                                    echo '<td>' . $hwTeacher . '</td>';
                                    echo '<td><span class="status-badge ' . $badgeClass . '"><i class="fa-solid ' . $badgeIcon . '"></i> ' . $hwDate . ' (' . $badgeText . ')</span></td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-secondary view-hw-detail-btn" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; border-radius: 8px;" data-title="' . $hwTitle . '" data-desc="' . $hwDesc . '" data-class="' . $hwClass . '" data-subject="' . $hwSubject . '" data-teacher="' . $hwTeacher . '" data-date="' . $hwDate . '"><i class="fa-solid fa-eye"></i> View details</button>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No active homework assignments registered for your class yet.</td></tr>';
                            }
                            $stmt_h->close();
                        }
                        ?>
                    </tbody>
                </table>
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

            // Homework details view popup
            const homeworkContainer = document.getElementById('student-homework-section');
            if (homeworkContainer) {
                homeworkContainer.addEventListener('click', (e) => {
                    const btn = e.target.closest('.view-hw-detail-btn');
                    if (btn) {
                        const title = btn.getAttribute('data-title');
                        const desc = btn.getAttribute('data-desc');
                        const class_name = btn.getAttribute('data-class');
                        const subject = btn.getAttribute('data-subject');
                        const teacher = btn.getAttribute('data-teacher');
                        const date = btn.getAttribute('data-date');

                        Swal.fire({
                            title: title,
                            html: `
                                <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: var(--text-main); margin-top: 1rem;">
                                    <p style="margin-bottom: 0.5rem;"><strong style="color: var(--primary);">Class Unit:</strong> ${class_name} (${subject})</p>
                                    <p style="margin-bottom: 0.5rem;"><strong style="color: var(--primary);">Assigned By:</strong> ${teacher}</p>
                                    <p style="margin-bottom: 0.5rem;"><strong style="color: var(--primary);">Submission Deadline:</strong> ${date}</p>
                                    <div style="border-top: 1px solid var(--card-border); padding-top: 1rem; margin-top: 1rem;">
                                        <strong style="color: var(--primary);">Instructions:</strong>
                                        <p style="margin-top: 0.5rem; white-space: pre-wrap; color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">${desc}</p>
                                    </div>
                                </div>
                            `,
                            icon: 'info',
                            confirmButtonColor: 'var(--primary)',
                            background: 'var(--swal-bg)',
                            color: 'var(--text-main)',
                            confirmButtonText: 'Understood'
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>
