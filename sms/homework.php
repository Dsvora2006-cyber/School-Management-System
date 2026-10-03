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

// Retrieve Homework Assignments list
$homework_list = [];
$sql_hw = "SELECT h.*, c.class_name FROM homework h LEFT JOIN classes c ON h.class_id = c.class_id WHERE h.teacher_id = ? ORDER BY h.due_date DESC";
$stmt_hw = $conn->prepare($sql_hw);
if ($stmt_hw) {
    $stmt_hw->bind_param("s", $teacher_id);
    $stmt_hw->execute();
    $res_hw = $stmt_hw->get_result();
    while ($row_hw = $res_hw->fetch_assoc()) {
        $homework_list[] = $row_hw;
    }
    $stmt_hw->close();
}

// Retrieve Classes Taught by this Teacher for Homework Dropdown
$teacher_classes = [];
$stmt_classes = $conn->prepare("SELECT class_id, class_name, subject, standard FROM classes WHERE teacher_id = ?");
if ($stmt_classes) {
    $stmt_classes->bind_param("s", $teacher_id);
    $stmt_classes->execute();
    $res_classes = $stmt_classes->get_result();
    while ($c_row = $res_classes->fetch_assoc()) {
        $teacher_classes[] = $c_row;
    }
    $stmt_classes->close();
}

$today = date('Y-m-d');
$active_count = 0;
foreach ($homework_list as $hw) {
    if ($hw['due_date'] >= $today) {
        $active_count++;
    }
}
$overdue_count = count($homework_list) - $active_count;
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework Management - <?php echo $school_name; ?></title>
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
        .trend-down { color: var(--accent); }

        /* Homework Cards Grid */
        .homework-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .homework-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            gap: 1.25rem;
            transition: all 0.3s ease;
        }

        .homework-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.75rem;
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

        /* Modal Design */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: var(--swal-bg, #111827);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            transform: scale(0.95) translateY(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .modal-overlay.active .modal-container {
            transform: scale(1) translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: var(--input-bg);
            color: var(--text-main);
        }

        .modal-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .modal-header h2 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .modal-header h2 span {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .modal-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            color: var(--text-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1.25rem 0.85rem 3rem;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .submit-btn {
            width: 100%;
            padding: 0.9rem;
            font-size: 1rem;
            font-weight: 700;
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
                    <li><a href="teacher_students.php" class="sidebar-link"><i class="fa-solid fa-user-graduate"></i> My Students</a></li>
                    <li><a href="time_table.php" class="sidebar-link"><i class="fa-solid fa-calendar-days"></i> My Timetable</a></li>
                    <li><a href="homework.php" class="sidebar-link active"><i class="fa-solid fa-book"></i> Homework</a></li>
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
                    <h2>Homework Management</h2>
                    <p>Assign tasks, monitor active deadlines, and manage homework materials for your students.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- KPI Cards Grid -->
            <div class="dash-grid">
                <!-- Card 1: Total Homework -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Total Assigned</h4>
                        <h3 id="hw-count-total"><?php echo count($homework_list); ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-folder-open"></i> Homework tasks</span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--primary); background: rgba(79,70,229,0.15);">
                        <i class="fa-solid fa-book"></i>
                    </div>
                </div>

                <!-- Card 2: Active Homework -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Active Tasks</h4>
                        <h3 id="hw-count-active"><?php echo $active_count; ?></h3>
                        <span class="dash-card-trend trend-up"><i class="fa-solid fa-hourglass-half"></i> Awaiting submissions</span>
                    </div>
                    <div class="dash-card-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>

                <!-- Card 3: Overdue Homework -->
                <div class="dash-card">
                    <div class="dash-card-info">
                        <h4>Overdue / Past Due</h4>
                        <h3 id="hw-count-overdue"><?php echo $overdue_count; ?></h3>
                        <span class="dash-card-trend trend-down" style="color: <?php echo $overdue_count > 0 ? 'var(--accent)' : '#10b981'; ?>;">
                            <i class="fa-solid fa-circle-exclamation"></i> Passed deadline
                        </span>
                    </div>
                    <div class="dash-card-icon" style="color: var(--accent); background: rgba(244,63,94,0.15);">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>

            <!-- Actions Panel -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <h4 style="font-size: 1.25rem; font-weight: 800;">Assigned Homework Registry</h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">Manage study questions, essays, problem sets, and class assignments.</p>
                </div>
                <button class="btn btn-primary" id="assign-homework-btn" style="gap: 0.5rem; font-size: 0.95rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);">
                    <i class="fa-solid fa-plus"></i> Assign New Homework
                </button>
            </div>

            <!-- Homework Grid / List -->
            <div class="homework-grid" id="homework-list-container">
                <?php if (empty($homework_list)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 24px; color: var(--text-muted);">
                        <i class="fa-solid fa-book-open" style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--primary); opacity: 0.6;"></i>
                        <h5 style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">No Homework Assigned Yet</h5>
                        <p style="font-size: 0.9rem;">Assign readings, essays, problem sets, and tracking tasks to your classes.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($homework_list as $hw): 
                        $hwId = htmlspecialchars($hw['homework_id']);
                        $hwTitle = htmlspecialchars($hw['title']);
                        $hwDesc = htmlspecialchars($hw['description']);
                        $hwClass = htmlspecialchars($hw['class_name'] ?? 'General Class');
                        $hwSubject = htmlspecialchars($hw['subject']);
                        $hwDueDate = htmlspecialchars($hw['due_date']);
                        $isOverdue = ($hw['due_date'] < $today);
                        $statusBadge = $isOverdue 
                            ? '<span class="status-badge status-pending"><i class="fa-solid fa-circle-exclamation"></i> Overdue</span>'
                            : '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Active</span>';
                    ?>
                        <div class="homework-card" id="hw-card-<?php echo $hwId; ?>">
                            <div>
                                <!-- Card Header -->
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.75rem;">
                                    <div>
                                        <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;"><?php echo $hwSubject; ?></span>
                                        <h5 style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-top: 0.1rem;"><?php echo $hwClass; ?></h5>
                                    </div>
                                    <?php echo $statusBadge; ?>
                                </div>
                                
                                <!-- Homework Details -->
                                <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text-main); line-height: 1.3;"><?php echo $hwTitle; ?></h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 1rem;"><?php echo $hwDesc; ?></p>
                            </div>
                            
                            <!-- Card Actions Footer -->
                            <div style="border-top: 1px solid var(--card-border); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: <?php echo $isOverdue ? 'var(--accent)' : 'var(--text-muted)'; ?>;"><i class="fa-solid fa-calendar-days" style="margin-right: 0.25rem;"></i> Due: <?php echo $hwDueDate; ?></span>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button class="btn btn-secondary view-hw-btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 8px; background: var(--input-bg); border: 1px solid var(--card-border); color: var(--text-main);" data-title="<?php echo $hwTitle; ?>" data-desc="<?php echo $hwDesc; ?>" data-class="<?php echo $hwClass; ?>" data-subject="<?php echo $hwSubject; ?>" data-date="<?php echo $hwDueDate; ?>">Details</button>
                                    <button class="btn btn-secondary delete-hw-btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 8px; color: var(--accent); border: 1px solid rgba(244,63,94,0.2); background: rgba(244,63,94,0.05);" data-id="<?php echo $hwId; ?>">Delete</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Assign Homework Modal -->
    <div class="modal-overlay" id="homework-modal-overlay">
        <div class="modal-container">
            <button class="modal-close" id="homework-modal-close" aria-label="Close modal">&times;</button>
            <div class="modal-header">
                <h2>Assign <span>Homework</span></h2>
                <p>Distribute study tasks, assignments, and essays to classes.</p>
            </div>
            <form id="assign-homework-form">
                <div id="hw-error" style="color: var(--accent); font-size: 0.85rem; margin-bottom: 1rem; display: none; text-align: center; font-weight: 600;"></div>
                
                <div class="form-group">
                    <label class="form-label" for="hw-class">Target Class</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-school input-icon" style="top: 50%;"></i>
                        <select id="hw-class" class="form-control" style="appearance: none; -webkit-appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=%22%239ca3af%22 height=%2224%22 viewBox=%220 0 24 24%22 width=%2224%22 xmlns=%22http://www.w3.org/2000/svg%22><path d=%22M7 10l5 5 5-5z%22/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5rem;" required>
                            <option value="" disabled selected>Select class instruction unit</option>
                            <?php if (empty($teacher_classes)): ?>
                                <option value="" disabled>No classes assigned to you</option>
                            <?php else: ?>
                                <?php foreach ($teacher_classes as $tc): ?>
                                    <option value="<?php echo htmlspecialchars($tc['class_id']); ?>">
                                        <?php echo htmlspecialchars($tc['class_name'] . ' (' . $tc['subject'] . ' - Grade ' . $tc['standard'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="hw-title">Homework Title</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-heading input-icon"></i>
                        <input type="text" id="hw-title" class="form-control" placeholder="e.g. Macbeth Scene Analysis" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="hw-desc">Instructions & Description</label>
                    <textarea id="hw-desc" class="form-control" placeholder="Describe the assignment instructions, expectations, and grading details..." style="padding: 0.85rem 1.25rem; min-height: 120px; font-family: inherit; resize: vertical;" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="hw-due-date">Submission Due Date</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-calendar-days input-icon"></i>
                        <input type="date" id="hw-due-date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary submit-btn" style="margin-top: 1rem;">
                    Publish Assignment <i class="fa-solid fa-paper-plane" style="margin-left: 0.25rem;"></i>
                </button>
            </form>
        </div>
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

            // --- Homework Modal & Logic ---
            const assignHomeworkBtn = document.getElementById('assign-homework-btn');
            const homeworkModalOverlay = document.getElementById('homework-modal-overlay');
            const homeworkModalClose = document.getElementById('homework-modal-close');
            const assignHomeworkForm = document.getElementById('assign-homework-form');
            const hwErrorDiv = document.getElementById('hw-error');

            // Open Modal
            if (assignHomeworkBtn) {
                assignHomeworkBtn.addEventListener('click', () => {
                    hwErrorDiv.style.display = 'none';
                    assignHomeworkForm.reset();
                    homeworkModalOverlay.classList.add('active');
                });
            }

            // Close Modal (x button)
            if (homeworkModalClose) {
                homeworkModalClose.addEventListener('click', () => {
                    homeworkModalOverlay.classList.remove('active');
                });
            }

            // Close Modal (clicking outside container)
            if (homeworkModalOverlay) {
                homeworkModalOverlay.addEventListener('click', (e) => {
                    if (e.target === homeworkModalOverlay) {
                        homeworkModalOverlay.classList.remove('active');
                    }
                });
            }

            // Submit Homework Form
            if (assignHomeworkForm) {
                assignHomeworkForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    hwErrorDiv.style.display = 'none';

                    const class_id = document.getElementById('hw-class').value;
                    const title = document.getElementById('hw-title').value;
                    const description = document.getElementById('hw-desc').value;
                    const due_date = document.getElementById('hw-due-date').value;

                    const formData = new FormData();
                    formData.append('class_id', class_id);
                    formData.append('title', title);
                    formData.append('description', description);
                    formData.append('due_date', due_date);

                    fetch('save_homework.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            homeworkModalOverlay.classList.remove('active');
                            Swal.fire({
                                title: 'Success!',
                                text: 'Homework assignment published successfully.',
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            hwErrorDiv.textContent = data.error || 'Failed to save homework assignment.';
                            hwErrorDiv.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        console.error('Error saving homework:', err);
                        hwErrorDiv.textContent = 'An unexpected connection error occurred.';
                        hwErrorDiv.style.display = 'block';
                    });
                });
            }

            // Delete & View Homework Handler
            const hwContainer = document.getElementById('homework-list-container');
            if (hwContainer) {
                hwContainer.addEventListener('click', (e) => {
                    const deleteBtn = e.target.closest('.delete-hw-btn');
                    if (deleteBtn) {
                        const homeworkId = deleteBtn.getAttribute('data-id');
                        
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'You won\'t be able to revert this homework assignment!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--primary)',
                            cancelButtonColor: 'var(--accent)',
                            confirmButtonText: 'Yes, delete it!',
                            background: 'var(--swal-bg)',
                            color: 'var(--text-main)'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const formData = new FormData();
                                formData.append('homework_id', homeworkId);

                                fetch('delete_homework.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Homework assignment has been removed.',
                                            icon: 'success',
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.error || 'Failed to delete homework assignment.',
                                            icon: 'error',
                                            confirmButtonColor: 'var(--primary)'
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error('Error deleting homework:', err);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'A connection error occurred.',
                                        icon: 'error',
                                        confirmButtonColor: 'var(--primary)'
                                    });
                                });
                            }
                        });
                    }

                    // View Details Event Listener
                    const viewBtn = e.target.closest('.view-hw-btn');
                    if (viewBtn) {
                        const title = viewBtn.getAttribute('data-title');
                        const desc = viewBtn.getAttribute('data-desc');
                        const class_name = viewBtn.getAttribute('data-class');
                        const subject = viewBtn.getAttribute('data-subject');
                        const due_date = viewBtn.getAttribute('data-date');

                        Swal.fire({
                            title: title,
                            html: `
                                <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: var(--text-main); margin-top: 1rem;">
                                    <p style="margin-bottom: 0.75rem;"><strong style="color: var(--primary);">Class Unit:</strong> ${class_name} (${subject})</p>
                                    <p style="margin-bottom: 0.75rem;"><strong style="color: var(--primary);">Submission Due:</strong> ${due_date}</p>
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
