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

// If student is logged in, redirect to student_profile.php
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header('Location: student_profile.php');
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
$email = htmlspecialchars($teacher['email']);
$mobile = htmlspecialchars($teacher['mobile_number']);
$specialization = htmlspecialchars($teacher['specialization']);
$standard = htmlspecialchars($teacher['standard']);

// Retrieve Total Students in Class (using teacher's assigned standard)
$students_count = 0;
$stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM students WHERE grade = ?");
if ($stmt_count) {
    $stmt_count->bind_param("s", $standard);
    $stmt_count->execute();
    $res_count = $stmt_count->get_result()->fetch_assoc();
    $students_count = $res_count['total'] ?? 0;
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
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Faculty Profile - <?php echo $school_name; ?></title>
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

        /* Profile Layout */
        .profile-grid-teacher {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 2rem;
            margin-top: 1rem;
        }

        .profile-card-left {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            height: fit-content;
        }

        .profile-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
        }

        .profile-info-right {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .profile-details-group {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .profile-details-group h4 {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-details-group h4 i {
            color: var(--primary);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .detail-value {
            font-size: 1rem;
            color: var(--text-main);
            font-weight: 700;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Responsive styling */
        @media (max-width: 992px) {
            .profile-grid-teacher {
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
            .details-grid {
                grid-template-columns: 1fr;
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
                    <li><a href="teacher_students.php" class="sidebar-link"><i class="fa-solid fa-user-graduate"></i> My Students</a></li>
                    <li><a href="time_table.php" class="sidebar-link"><i class="fa-solid fa-calendar-days"></i> My Timetable</a></li>
                    <li><a href="homework.php" class="sidebar-link"><i class="fa-solid fa-book"></i> Homework</a></li>
                    <li><a href="student_marks.php" class="sidebar-link"><i class="fa-solid fa-square-poll-vertical"></i> Student Marks</a></li>
                    <li><a href="profile.php" class="sidebar-link active"><i class="fa-solid fa-id-card"></i> My Profile</a></li>
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
                    <h2>Faculty Staff Profile</h2>
                    <p>Review your verified academic staff records, credentials, contact information, and teaching assignments.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Profile Main Content Grid -->
            <div class="profile-grid-teacher">
                <!-- Left Profile Summary Card -->
                <div class="profile-card-left">
                    <div class="profile-avatar-large">
                        <?php echo $first_name[0] . ($last_name[0] ?? ''); ?>
                    </div>
                    <h3 style="font-weight: 800; font-size: 1.4rem; margin-bottom: 0.25rem;"><?php echo $first_name . ' ' . $last_name; ?></h3>
                    <p style="color: var(--primary); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1.5rem;"><?php echo $specialization; ?></p>
                    
                    <div style="width: 100%; border-top: 1px solid var(--card-border); padding-top: 1.5rem; text-align: left; font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Staff ID:</span>
                            <strong style="color: var(--text-main); font-family: monospace; font-size: 0.95rem;"><?php echo $teacher_id; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Standard:</span>
                            <strong style="color: var(--primary);">Grade <?php echo $standard ?: 'N/A'; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Classes Taught:</span>
                            <strong style="color: var(--secondary);"><?php echo $classes_count; ?> Courses</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Class Students:</span>
                            <strong style="color: #10b981;"><?php echo $students_count; ?> Students</strong>
                        </div>
                    </div>
                </div>

                <!-- Right Profile Details Grid -->
                <div class="profile-info-right">
                    <!-- 1. General Info -->
                    <div class="profile-details-group">
                        <h4><i class="fa-solid fa-user-tie"></i> General Faculty Information</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="detail-label">First Name</span>
                                <span class="detail-value"><?php echo $first_name; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Last Name</span>
                                <span class="detail-value"><?php echo $last_name; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Academic Specialization</span>
                                <span class="detail-value"><?php echo $specialization; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Assigned Standard / Grade</span>
                                <span class="detail-value">Grade <?php echo $standard ?: 'Unassigned'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Contact Details -->
                    <div class="profile-details-group">
                        <h4><i class="fa-solid fa-address-book"></i> Contact Information</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Official Email</span>
                                <span class="detail-value"><?php echo $email; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Mobile Number</span>
                                <span class="detail-value"><?php echo $mobile ?: 'N/A'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Portal Credentials & Access -->
                    <div class="profile-details-group">
                        <h4><i class="fa-solid fa-shield-halved"></i> Portal Access & Security</h4>
                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Authorized Role</span>
                                <span class="detail-value" style="color: var(--secondary);">Instructor / Academic Faculty</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Account Status</span>
                                <span class="detail-value" style="color: #27c93f;"><i class="fa-solid fa-circle-check" style="margin-right: 0.25rem;"></i> Active System Access</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Default Password Note</span>
                                <span class="detail-value" style="font-size: 0.85rem; color: var(--text-muted);">Default: teacher123. Contact IT admin to reset credentials.</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Campus Security</span>
                                <span class="detail-value" style="color: #10b981;"><i class="fa-solid fa-lock" style="margin-right: 0.25rem;"></i> Encrypted Session</span>
                            </div>
                        </div>
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
