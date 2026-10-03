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
    die("Database query error.");
}

$first_name = htmlspecialchars($teacher['first_name']);
$last_name = htmlspecialchars($teacher['last_name']);
$specialization = htmlspecialchars($teacher['specialization']);
$standard = htmlspecialchars($teacher['standard']);

// Retrieve Students in teacher's assigned standard
$students_list = [];
$avg_attendance = 100.0;
$stmt_students = $conn->prepare("SELECT * FROM students WHERE grade = ? ORDER BY first_name ASC");
if ($stmt_students) {
    $stmt_students->bind_param("s", $standard);
    $stmt_students->execute();
    $res_s = $stmt_students->get_result();
    $tot_att = 0;
    while ($row_s = $res_s->fetch_assoc()) {
        $students_list[] = $row_s;
        $tot_att += (float)($row_s['attendance'] ?? 100);
    }
    if (count($students_list) > 0) {
        $avg_attendance = number_format($tot_att / count($students_list), 1);
    }
    $stmt_students->close();
}
$students_count = count($students_list);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Class Students - <?php echo $school_name; ?></title>
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

        /* KPI Cards Grid */
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

        /* Table Design */
        .table-container {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-top: 1.5rem;
            overflow-x: auto;
        }

        .table-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header-row h4 {
            font-size: 1.25rem;
            font-weight: 800;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            width: 260px;
        }

        .search-box input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-main);
            font-size: 0.9rem;
            width: 100%;
        }

        .search-box i {
            color: var(--text-muted);
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
            gap: 0.35rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .status-pending {
            background: rgba(244, 63, 94, 0.15);
            color: var(--accent);
        }

        /* Responsive styling */
        @media (max-width: 992px) {
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
                    <li><a href="teacher_dashboard.php" class="sidebar-link"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="teacher_students.php" class="sidebar-link active"><i class="fa-solid fa-user-graduate"></i> My Students</a></li>
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
                    <h2>Class Students Directory</h2>
                    <p>Audit and review student rosters, contact details, attendance averages, and enrollment statuses for Grade <?php echo $standard ?: 'N/A'; ?>.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- KPI Cards Grid -->
            <div class="dash-grid">
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Assigned Grade</h4>
                        <h3>Grade <?php echo $standard ?: 'N/A'; ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-school"></i> Primary Standard</span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--primary); background: rgba(79,70,229,0.15);">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Total Enrolled Students</h4>
                        <h3><?php echo $students_count; ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-users"></i> Class Scholars</span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--secondary); background: rgba(6,182,212,0.15);">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>

                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Class Avg. Attendance</h4>
                        <h3><?php echo $avg_attendance; ?>%</h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-calendar-check"></i> Overall Average</span>
                    </div>
                    <div class="dash-card-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>
            </div>

            <!-- Students Table Container -->
            <div class="table-container">
                <div class="table-header-row">
                    <div>
                        <h4>Students Roster - Grade <?php echo $standard ?: 'N/A'; ?></h4>
                        <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Official grade standard roster</span>
                    </div>
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="students-search" placeholder="Search student name, ID, email...">
                    </div>
                </div>

                <table class="students-table" id="students-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Attendance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students_list)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">No students registered in Grade <?php echo $standard; ?>.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students_list as $row_s): 
                                $sFullName = htmlspecialchars($row_s['first_name'] . ' ' . $row_s['last_name']);
                                $sInitials = strtoupper(substr($row_s['first_name'], 0, 1) . substr($row_s['last_name'], 0, 1));
                                $sId = htmlspecialchars($row_s['student_id']);
                                $sGender = htmlspecialchars($row_s['gender']);
                                $sEmail = htmlspecialchars($row_s['email']);
                                $sMobile = htmlspecialchars($row_s['mobile_number']);
                                $sAttendance = number_format($row_s['attendance'] ?? 100.00, 1) . '%';
                                $sStatus = htmlspecialchars($row_s['status']);
                                
                                $statusBadge = '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Active</span>';
                                if (strtolower($sStatus) === 'pending') {
                                    $statusBadge = '<span class="status-badge status-pending"><i class="fa-solid fa-spinner"></i> Pending</span>';
                                }

                                $colorChoices = ['var(--primary)', 'var(--secondary)', 'var(--accent)', '#8b5cf6', '#eab308', '#10b981'];
                                $avatarBg = $colorChoices[ord(strtoupper($row_s['first_name'][0])) % count($colorChoices)];
                            ?>
                                <tr>
                                    <td style="font-weight: 700; display: flex; align-items: center; gap: 0.65rem;">
                                        <div class="admin-avatar" style="width: 34px; height: 34px; font-size: 0.8rem; background: <?php echo $avatarBg; ?>;"><?php echo $sInitials; ?></div>
                                        <?php echo $sFullName; ?>
                                    </td>
                                    <td><?php echo $sId; ?></td>
                                    <td><?php echo $sGender; ?></td>
                                    <td><?php echo $sEmail; ?></td>
                                    <td><?php echo $sMobile ?: 'N/A'; ?></td>
                                    <td style="font-weight: 700; color: <?php echo (($row_s['attendance'] ?? 100) >= 90 ? '#10b981' : 'var(--accent)'); ?>;"><?php echo $sAttendance; ?></td>
                                    <td><?php echo $statusBadge; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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

            // Real-time table search
            const searchInput = document.getElementById('students-search');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    const query = searchInput.value.toLowerCase();
                    const tableRows = document.querySelectorAll('#students-table tbody tr');
                    tableRows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(query) ? '' : 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>
