<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser caching of protected admin pages so back-button after logout won't show page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// Redirect students or teachers trying to access admin pages
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header('Location: student_dashboard.php');
    exit;
}
if (isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true) {
    header('Location: teacher_dashboard.php');
    exit;
}

// Verify Admin Session - Redirect to Login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
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

// Fallback default configurations
$school_name = htmlspecialchars($settings['school_name'] ?? 'AWS');
$school_email = htmlspecialchars($settings['school_email'] ?? 'info@apexacademy.com');
$school_phone = htmlspecialchars($settings['school_phone'] ?? '+1 (555) 019-2834');
$school_address = htmlspecialchars($settings['school_address'] ?? '123 Academic Way, Springfield, IL');
$academic_year = htmlspecialchars($settings['academic_year'] ?? '2026-2027');
$current_term = htmlspecialchars($settings['current_term'] ?? 'Term 1');
$currency = htmlspecialchars($settings['currency'] ?? 'USD');
$theme_color = htmlspecialchars($settings['theme_color'] ?? 'indigo');
$density = htmlspecialchars($settings['density'] ?? 'normal');

// Admin Info Configuration
$admin_name = $_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? $_SESSION['username'] ?? $settings['admin_name'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? $settings['admin_email'] ?? 'admin@sms.com';
$admin_role = $_SESSION['admin_role'] ?? $settings['admin_role'] ?? 'System Admin';

// Calculate initials for avatar
$name_parts = preg_split("/[\s_.]+/", trim($admin_name));
if (count($name_parts) >= 2 && !empty($name_parts[0]) && !empty($name_parts[1])) {
    $admin_initials = strtoupper(substr($name_parts[0], 0, 1) . substr($name_parts[1], 0, 1));
} else {
    $admin_initials = strtoupper(substr($admin_name, 0, min(2, strlen($admin_name))));
}
if (empty($admin_initials)) {
    $admin_initials = 'AD';
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $school_name; ?> School Management System - Administrator Dashboard Workspace">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?> - <?php echo $school_name; ?></title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- Dynamic Customization Styles -->
    <style>
        :root {
            <?php if ($theme_color === 'cyan'): ?>
            --primary: #06b6d4;
            --primary-glow: rgba(6, 182, 212, 0.15);
            --secondary: #3b82f6;
            <?php elseif ($theme_color === 'rose'): ?>
            --primary: #f43f5e;
            --primary-glow: rgba(244, 63, 94, 0.15);
            --secondary: #8b5cf6;
            <?php elseif ($theme_color === 'green'): ?>
            --primary: #10b981;
            --primary-glow: rgba(16, 185, 129, 0.15);
            --secondary: #06b6d4;
            <?php elseif ($theme_color === 'amber'): ?>
            --primary: #eab308;
            --primary-glow: rgba(234, 179, 8, 0.15);
            --secondary: #f97316;
            <?php endif; ?>
        }

        <?php if ($density === 'compact'): ?>
        .workspace { padding: 1.25rem; }
        .top-panel { margin-bottom: 1.5rem; }
        .dash-grid { margin-bottom: 1.5rem; gap: 1rem; }
        .dash-card { padding: 1rem; border-radius: 12px; }
        .table-container { padding: 1.25rem; border-radius: 16px; margin-top: 0.5rem; }
        .students-table td, .students-table th { padding: 0.6rem 0.75rem; font-size: 0.85rem; }
        .welcome-title h2 { font-size: 1.4rem; }
        .welcome-title p { font-size: 0.85rem; }
        <?php elseif ($density === 'spacious'): ?>
        .workspace { padding: 3.5rem; }
        .top-panel { margin-bottom: 4rem; }
        .dash-grid { margin-bottom: 4rem; gap: 2rem; }
        .dash-card { padding: 2rem; border-radius: 24px; }
        .table-container { padding: 3rem; border-radius: 32px; margin-top: 2rem; }
        .students-table td, .students-table th { padding: 1.3rem 1.4rem; font-size: 1.05rem; }
        .welcome-title h2 { font-size: 2.2rem; }
        .welcome-title p { font-size: 1.1rem; }
        <?php endif; ?>
    </style>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 for Premium Popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Dashboard Specific Styles -->
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

        /* Main Workspace Design */
        .workspace {
            flex: 1;
            padding: 2.5rem;
            overflow-y: auto;
        }

        /* Top Panel */
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

        .top-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-box input {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: 12px;
            background: var(--input-bg);
            border: 1px solid var(--card-border);
            color: var(--text-main);
            outline: none;
            width: 100%;
        }

        .search-box i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
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
            transform: translateX(4px);
        }

        .action-btn i {
            color: var(--primary);
        }

        /* Students Table Design */
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

        .status-active {
            background: rgba(39, 201, 63, 0.15);
            color: #27c93f;
        }

        .status-pending {
            background: rgba(234, 179, 8, 0.15);
            color: #eab308;
        }

        .action-icon-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.3s ease;
            margin-right: 0.5rem;
        }

        .action-icon-btn:hover {
            color: var(--primary);
        }

        /* Specific select options styling */
        select.form-control option {
            background-color: var(--bg-main);
            color: var(--text-main);
        }

        /* Responsive Breakpoints */
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
            .admin-profile, .sidebar-footer {
                display: none;
            }
            .top-panel {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .search-box {
                max-width: 100%;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
