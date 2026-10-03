<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Verify Teacher Session
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
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

$teacher_id = $_SESSION['teacher_id'];

// Retrieve Teacher Profile Details
$stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
if ($stmt) {
    $stmt->bind_param("s", $teacher_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $teacher = $res->fetch_assoc();
    } else {
        session_destroy();
        header('Location: index.php');
        exit;
    }
    $stmt->close();
} else {
    die("Database query error: teacher lookup preparation failed.");
}

$first_name = htmlspecialchars($teacher['first_name']);
$last_name = htmlspecialchars($teacher['last_name']);
$email = htmlspecialchars($teacher['email']);
$mobile = htmlspecialchars($teacher['mobile_number']);
$specialization = htmlspecialchars($teacher['specialization']);
$standard = htmlspecialchars($teacher['standard']);

// Retrieve Total Students in Class (using teacher's assigned standard)
$students_count = 0;
$avg_attendance = 100.0;
$stmt_count = $conn->prepare("SELECT COUNT(*) as total, AVG(attendance) as avg_att FROM students WHERE grade = ?");
if ($stmt_count) {
    $stmt_count->bind_param("s", $standard);
    $stmt_count->execute();
    $res_count = $stmt_count->get_result()->fetch_assoc();
    $students_count = $res_count['total'] ?? 0;
    $avg_attendance = number_format($res_count['avg_att'] ?? 100.00, 1);
    $stmt_count->close();
}

// Retrieve Number of classes taught by this teacher
$classes_count = 0;
$stmt_c_count = $conn->prepare("SELECT COUNT(*) as total FROM classes WHERE teacher_id = ?");
if ($stmt_c_count) {
    $stmt_c_count->bind_param("s", $teacher_id);
    $stmt_c_count->execute();
    $res_c_count = $stmt_c_count->get_result()->fetch_assoc();
    $classes_count = $res_c_count['total'] ?? 0;
    $stmt_c_count->close();
}

// Retrieve Homework count
$homework_count = 0;
$stmt_hw_c = $conn->prepare("SELECT COUNT(*) as total FROM homework WHERE teacher_id = ?");
if ($stmt_hw_c) {
    $stmt_hw_c->bind_param("s", $teacher_id);
    $stmt_hw_c->execute();
    $res_hw_c = $stmt_hw_c->get_result()->fetch_assoc();
    $homework_count = $res_hw_c['total'] ?? 0;
    $stmt_hw_c->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal Dashboard - <?php echo $school_name; ?></title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 for Alerts -->
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
            background: var(--primary-glow);
            color: var(--primary);
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 24px;
        }

        .sidebar-footer {
            border-top: 1px solid var(--card-border);
            padding-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem;
        }

        .admin-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .admin-details h5 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
        }

        .admin-details span {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Workspace Main Layout */
        .workspace {
            flex: 1;
            padding: 2.5rem 3.5rem;
            overflow-y: auto;
        }

        .top-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
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

        .top-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .theme-toggle {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            color: #eab308;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: rotate(15deg) scale(1.05);
        }

        /* Stats Dashboard Grid */
        .dash-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
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
            transition: all 0.3s ease;
        }

        .dash-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
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
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .dash-card-trend {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .trend-up { color: #27c93f; }

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
        }

        .activity-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .activity-desc h5 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
            color: var(--text-main);
        }

        .activity-desc p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
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
            gap: 0.85rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 1rem 1.25rem;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            border-color: var(--primary);
            background: var(--primary-glow);
            transform: translateX(4px);
        }

        .action-btn i {
            color: var(--primary);
            font-size: 1.1rem;
            width: 20px;
        }

        /* Responsive styling */
        @media (max-width: 992px) {
            .dash-sections {
                grid-template-columns: 1fr;
            }
            .workspace {
                padding: 1.5rem;
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
            .top-panel {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
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
                    <a href="teacher_dashboard.php" class="logo" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                        <img src="images/logo.png" alt="Logo" style="height: 56px; width: auto; border-radius: 6px; margin-right: 0.5rem;"> <span id="sidebar-logo-text" style="font-weight: 800; font-size: 1.3rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo $school_name; ?></span>
                    </a>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="teacher_dashboard.php" class="sidebar-link active"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="teacher_students.php" class="sidebar-link"><i class="fa-solid fa-user-graduate"></i> My Students</a></li>
                    <li><a href="time_table.php" class="sidebar-link"><i class="fa-solid fa-calendar-days"></i> My Timetable</a></li>
                    <li><a href="homework.php" class="sidebar-link"><i class="fa-solid fa-book"></i> Homework</a></li>
                    <li><a href="student_marks.php" class="sidebar-link"><i class="fa-solid fa-square-poll-vertical"></i> Student Marks</a></li>
                    <li><a href="profile.php" class="sidebar-link"><i class="fa-solid fa-id-card"></i> My Profile</a></li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <?php echo $first_name[0] . ($last_name[0] ?? ''); ?>
                    </div>
                    <div class="admin-details">
                        <h5><?php echo $first_name . ' ' . $last_name; ?></h5>
                        <span>Teacher Profile</span>
                    </div>
                </div>
                <a href="teacher_dashboard.php?action=logout" class="sidebar-link" style="color: var(--accent); padding-left: 0.75rem;"><i class="fa-solid fa-power-off"></i> Sign Out</a>
            </div>
        </aside>

        <!-- Main Workspace Contents -->
        <main class="workspace">
            <!-- Header bar -->
            <div class="top-panel">
                <div class="welcome-title">
                    <h2>Welcome Back, <?php echo $first_name; ?></h2>
                    <p>Access your classes, student rosters, weekly schedules, homework tasks, and faculty records.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- KPI cards grid -->
            <div class="dash-grid">
                <!-- Card 1: Assigned Standard -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>My Standard Class</h4>
                        <h3>Grade <?php echo $standard ?: 'N/A'; ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-circle-check"></i> Primary Instructor</span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--primary); background: rgba(79,70,229,0.15);">
                        <i class="fa-solid fa-school"></i>
                    </div>
                </div>

                <!-- Card 2: Students Count -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Total Class Students</h4>
                        <h3><?php echo $students_count; ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-user-graduate"></i> Enrolled in Grade</span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--secondary); background: rgba(6,182,212,0.15);">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>

                <!-- Card 3: Class Average Attendance -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Avg. Attendance Rate</h4>
                        <h3><?php echo $avg_attendance; ?>%</h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-calendar-check"></i> Overall Average</span>
                    </div>
                    <div class="dash-card-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>

                <!-- Card 4: Classes Taught -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>My Weekly Classes</h4>
                        <h3><?php echo $classes_count; ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-clock"></i> Active schedule</span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--accent); background: rgba(244,63,94,0.15);">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                </div>
            </div>

            <!-- Split view visual layout -->
            <div class="dash-sections">
                <!-- Announcement Box -->
                <div class="activity-panel">
                    <div class="panel-header">
                        <h4><i class="fa-solid fa-bullhorn" style="color: var(--primary); margin-right: 0.5rem;"></i> Academic Alerts & Announcements</h4>
                    </div>
                    <ul class="activity-list">
                        <li class="activity-item">
                            <div class="activity-badge" style="color: var(--primary);"><i class="fa-solid fa-file-signature"></i></div>
                            <div class="activity-desc">
                                <h5>Mid-Term Assessment Entry</h5>
                                <p>Please enter and publish mid-term assessment marks for your assigned grade standard using the Student Marks portal.</p>
                            </div>
                            <div class="activity-time">Active</div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-badge" style="color: var(--secondary);"><i class="fa-solid fa-book-open"></i></div>
                            <div class="activity-desc">
                                <h5>Homework Task Distribution</h5>
                                <p>Ensure weekly study assignments and problem sets are published to classes before the Friday review window.</p>
                            </div>
                            <div class="activity-time">Weekly</div>
                        </li>
                        <li class="activity-item">
                            <div class="activity-badge" style="color: #10b981;"><i class="fa-solid fa-shield-halved"></i></div>
                            <div class="activity-desc">
                                <h5>Faculty Portal Synchronized</h5>
                                <p>Independent portals for Timetable, Homework, Marks, and Profiles are now live with instant navigation.</p>
                            </div>
                            <div class="activity-time">System</div>
                        </li>
                    </ul>
                </div>

                <!-- Quick actions -->
                <div class="actions-panel">
                    <div class="panel-header">
                        <h4><i class="fa-solid fa-bolt" style="color: #eab308; margin-right: 0.5rem;"></i> Quick Navigation</h4>
                    </div>
                    <div class="action-buttons">
                        <a href="teacher_students.php" class="action-btn">
                            <i class="fa-solid fa-user-graduate"></i> View Class Students
                        </a>
                        <a href="time_table.php" class="action-btn">
                            <i class="fa-solid fa-calendar-days"></i> Check Timetable Schedule
                        </a>
                        <a href="homework.php" class="action-btn">
                            <i class="fa-solid fa-book"></i> Assign & Manage Homework
                        </a>
                        <a href="student_marks.php" class="action-btn">
                            <i class="fa-solid fa-square-poll-vertical"></i> Enter Student Marks
                        </a>
                        <a href="profile.php" class="action-btn">
                            <i class="fa-solid fa-id-card"></i> View Faculty Profile
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JS Logic -->
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
