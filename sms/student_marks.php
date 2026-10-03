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

$is_teacher = isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true;
$is_student = isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true;

if (!$is_teacher && !$is_student) {
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

// ==========================================
// 1. TEACHER VIEW SECTION
// ==========================================
if ($is_teacher) {
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

    // Retrieve Classes Taught by this Teacher for Class Dropdown
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
    ?>
    <!DOCTYPE html>
    <html lang="en" data-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Marks Management - <?php echo $school_name; ?></title>
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

            /* Filter & Panels */
            .activity-panel {
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
                font-size: 1.25rem;
                font-weight: 800;
            }

            .form-group {
                margin-bottom: 1rem;
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

            .filter-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1.25rem;
                margin-bottom: 1.5rem;
            }

            /* Responsive styling */
            @media (max-width: 992px) {
                .filter-grid {
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
                        <li><a href="teacher_dashboard.php" class="sidebar-link"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                        <li><a href="teacher_students.php" class="sidebar-link"><i class="fa-solid fa-user-graduate"></i> My Students</a></li>
                        <li><a href="time_table.php" class="sidebar-link"><i class="fa-solid fa-calendar-days"></i> My Timetable</a></li>
                        <li><a href="homework.php" class="sidebar-link"><i class="fa-solid fa-book"></i> Homework</a></li>
                        <li><a href="student_marks.php" class="sidebar-link active"><i class="fa-solid fa-square-poll-vertical"></i> Student Marks</a></li>
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
                        <h2>Student Marks & Assessment Registry</h2>
                        <p>Enter, modify, settle, and generate printable grading reports for your student classes.</p>
                    </div>
                    <div class="top-actions">
                        <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                            <i class="fa-solid fa-sun" id="theme-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Marks Control Panel -->
                <div class="activity-panel">
                    <div class="panel-header">
                        <h4><i class="fa-solid fa-square-poll-vertical" style="color: var(--primary); margin-right: 0.5rem;"></i> Marks Assessment Selector</h4>
                    </div>
                    
                    <!-- Filter Controls Grid -->
                    <div class="filter-grid">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="marks-class-select">Select Class Unit</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-school input-icon" style="top: 50%;"></i>
                                <select id="marks-class-select" class="form-control" style="appearance: none; -webkit-appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=%22%239ca3af%22 height=%2224%22 viewBox=%220 0 24 24%22 width=%2224%22 xmlns=%22http://www.w3.org/2000/svg%22><path d=%22M7 10l5 5 5-5z%22/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5rem;" required>
                                    <option value="" disabled selected>Select class unit</option>
                                    <?php foreach ($teacher_classes as $tc): ?>
                                        <option value="<?php echo htmlspecialchars($tc['class_id']); ?>" data-standard="<?php echo htmlspecialchars($tc['standard']); ?>">
                                            <?php echo htmlspecialchars($tc['class_name'] . ' (Grade ' . $tc['standard'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="marks-subject-select">Select Subject</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-book-bookmark input-icon" style="top: 50%;"></i>
                                <select id="marks-subject-select" class="form-control" style="appearance: none; -webkit-appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=%22%239ca3af%22 height=%2224%22 viewBox=%220 0 24 24%22 width=%2224%22 xmlns=%22http://www.w3.org/2000/svg%22><path d=%22M7 10l5 5 5-5z%22/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5rem;" required>
                                    <option value="" disabled selected>Select class first</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="marks-exam-name">Exam Name / Title</label>
                            <div class="input-wrapper">
                                <i class="fa-solid fa-file-signature input-icon"></i>
                                <input type="text" id="marks-exam-name" class="form-control" placeholder="e.g. Midterm Exam, Class Test 1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end; gap: 1rem; flex-wrap: wrap;">
                        <button class="btn btn-secondary" id="show-students-marks-btn" style="gap: 0.5rem; font-size: 0.95rem; border-radius: 12px; display: inline-flex; align-items: center; background: var(--secondary); border: 1px solid var(--secondary); color: #fff;">
                            <i class="fa-solid fa-eye"></i> Show Marks Report
                        </button>
                        <button class="btn btn-primary" id="load-students-marks-btn" style="gap: 0.5rem; font-size: 0.95rem; border-radius: 12px; display: inline-flex; align-items: center;">
                            <i class="fa-solid fa-cloud-arrow-down"></i> Load Marks Registry
                        </button>
                    </div>
                </div>

                <!-- Marks Entry Grid Container (Hidden until loaded) -->
                <div id="marks-entry-container" style="display: none;">
                    <form id="save-students-marks-form">
                        <div class="table-container">
                            <div class="table-header-row">
                                <div>
                                    <h4>Marks Entry Sheets - <span id="marks-class-title" style="color: var(--primary);">Grade</span></h4>
                                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Subject: <strong id="marks-subject-name" style="color: var(--text-main);">General</strong></span>
                                </div>
                                <span style="font-size: 0.85rem; color: #10b981; font-weight: 700;"><i class="fa-solid fa-pen-to-square"></i> Live Input Active</span>
                            </div>
                            <table class="students-table">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Student ID</th>
                                        <th style="width: 180px;">Maximum Marks</th>
                                        <th style="width: 180px;">Marks Obtained</th>
                                    </tr>
                                </thead>
                                <tbody id="marks-table-tbody">
                                    <!-- Populated dynamically via JS -->
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 1.5rem; text-align: right;">
                            <button type="submit" class="btn btn-primary" style="gap: 0.5rem; border-radius: 12px; font-size: 0.95rem; display: inline-flex; align-items: center; padding: 0.85rem 2rem; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);">
                                <i class="fa-solid fa-circle-check"></i> Settle & Publish Marks
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Marks Report Container (Hidden until loaded) -->
                <div id="marks-report-container" style="display: none; margin-top: 1.5rem;">
                    <div class="table-container">
                        <div class="table-header-row" style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h4>Marks Grading Report - <span id="report-class-title" style="color: var(--primary);">Grade</span></h4>
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Subject: <strong id="report-subject-name" style="color: var(--text-main);">General</strong></span>
                            </div>
                            <button class="btn btn-secondary" id="print-marks-report-btn" style="gap: 0.5rem; font-size: 0.85rem; border-radius: 8px; display: inline-flex; align-items: center; background: var(--input-bg); border: 1px solid var(--card-border); color: var(--text-main);">
                                <i class="fa-solid fa-print"></i> Print Report
                            </button>
                        </div>
                        <table class="students-table">
                            <thead id="report-table-thead">
                                <!-- Dynamic headers -->
                            </thead>
                            <tbody id="report-table-tbody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- JS Logic for Teacher Marks -->
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

                // Marks Functionality
                const loadMarksBtn = document.getElementById('load-students-marks-btn');
                const marksClassSelect = document.getElementById('marks-class-select');
                const marksSubjectSelect = document.getElementById('marks-subject-select');
                const marksExamName = document.getElementById('marks-exam-name');
                const marksEntryContainer = document.getElementById('marks-entry-container');
                const marksTableTbody = document.getElementById('marks-table-tbody');
                const marksClassTitle = document.getElementById('marks-class-title');
                const marksSubjectName = document.getElementById('marks-subject-name');
                const saveMarksForm = document.getElementById('save-students-marks-form');

                // Map standard grades to subjects
                const standardSubjectsMap = {
                    '10': ['English', 'Gujarati', 'Mathematics', 'Science', 'Social Science', 'Hindi'],
                    '11 (Science)': ['Physics', 'Chemistry', 'Mathematics', 'Biology', 'English', 'Gujarati'],
                    '12 (Science)': ['Physics', 'Chemistry', 'Mathematics', 'Biology', 'English', 'Gujarati'],
                    '11 (Commerce)': ['Account', 'Statistics', 'BO', 'Economics', 'SPCC', 'Gujarati', 'English'],
                    '12 (Commerce)': ['Account', 'Statistics', 'BO', 'Economics', 'SPCC', 'Gujarati', 'English'],
                    '11': ['English', 'Gujarati', 'Mathematics', 'Science', 'Social Science'],
                    '12': ['English', 'Gujarati', 'Mathematics', 'Science', 'Social Science']
                };

                const defaultSubjects = ['English', 'Gujarati', 'Mathematics', 'Science', 'Account', 'Statistics', 'BO', 'Economics', 'SPCC'];

                if (marksClassSelect) {
                    marksClassSelect.addEventListener('change', () => {
                        const selectedOpt = marksClassSelect.options[marksClassSelect.selectedIndex];
                        const standard = selectedOpt.getAttribute('data-standard');
                        
                        marksSubjectSelect.innerHTML = '<option value="" disabled selected>Select subject</option>';
                        
                        const subjects = standardSubjectsMap[standard] || defaultSubjects;
                        subjects.forEach(subj => {
                            const opt = document.createElement('option');
                            opt.value = subj;
                            opt.textContent = subj;
                            marksSubjectSelect.appendChild(opt);
                        });
                        
                        const allOpt = document.createElement('option');
                        allOpt.value = 'All';
                        allOpt.textContent = 'All';
                        marksSubjectSelect.appendChild(allOpt);
                    });
                }

                if (loadMarksBtn) {
                    loadMarksBtn.addEventListener('click', () => {
                        const classId = marksClassSelect.value;
                        const subject = marksSubjectSelect.value;
                        const examName = marksExamName.value.trim();

                        if (!classId) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'Please select a Class Unit.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }

                        if (!subject) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'Please select a Subject.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }

                        if (!examName) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'Please enter an Exam Name.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Loading Marks Sheets...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        marksReportContainer.style.display = 'none';

                        fetch(`get_marks.php?class_id=${encodeURIComponent(classId)}&exam_name=${encodeURIComponent(examName)}&subject=${encodeURIComponent(subject)}`)
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                marksClassTitle.textContent = marksClassSelect.options[marksClassSelect.selectedIndex].text.split(' (Grade')[0];
                                marksSubjectName.textContent = data.subject;
                                marksTableTbody.innerHTML = '';

                                const showSubjectCol = (subject === 'All');
                                const thead = marksTableTbody.closest('table').querySelector('thead');
                                if (showSubjectCol) {
                                    thead.innerHTML = `
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th>Subject</th>
                                            <th style="width: 180px;">Maximum Marks</th>
                                            <th style="width: 180px;">Marks Obtained</th>
                                        </tr>
                                    `;
                                } else {
                                    thead.innerHTML = `
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th style="width: 180px;">Maximum Marks</th>
                                            <th style="width: 180px;">Marks Obtained</th>
                                        </tr>
                                    `;
                                }

                                if (data.students.length === 0) {
                                    marksTableTbody.innerHTML = `<tr><td colspan="${showSubjectCol ? 5 : 4}" style="text-align: center; padding: 2rem; color: var(--text-muted);">No active students registered in this grade.</td></tr>`;
                                } else {
                                    data.students.forEach(student => {
                                        const tr = document.createElement('tr');
                                        if (showSubjectCol) {
                                            tr.innerHTML = `
                                                <td style="font-weight: 700;">${student.full_name}</td>
                                                <td>${student.student_id}</td>
                                                <td style="font-weight: 600; color: var(--secondary);">${student.subject}</td>
                                                <td>
                                                    <input type="number" class="form-control max-marks-input" style="width: 120px; background: var(--input-bg); border: 1px solid var(--card-border); color: var(--text-main);" step="0.5" min="1" value="${student.max_marks}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control marks-obtained-input" style="width: 120px; background: var(--input-bg); border: 1px solid var(--card-border); color: var(--text-main);" step="0.5" min="0" max="${student.max_marks}" value="${student.marks_obtained !== null ? student.marks_obtained : ''}" placeholder="Pending" required data-id="${student.student_id}" data-subject="${student.subject}">
                                                </td>
                                            `;
                                        } else {
                                            tr.innerHTML = `
                                                <td style="font-weight: 700;">${student.full_name}</td>
                                                <td>${student.student_id}</td>
                                                <td>
                                                    <input type="number" class="form-control max-marks-input" style="width: 120px; background: var(--input-bg); border: 1px solid var(--card-border); color: var(--text-main);" step="0.5" min="1" value="${student.max_marks}" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control marks-obtained-input" style="width: 120px; background: var(--input-bg); border: 1px solid var(--card-border); color: var(--text-main);" step="0.5" min="0" max="${student.max_marks}" value="${student.marks_obtained !== null ? student.marks_obtained : ''}" placeholder="Pending" required data-id="${student.student_id}">
                                                </td>
                                            `;
                                        }
                                        marksTableTbody.appendChild(tr);

                                        const maxInput = tr.querySelector('.max-marks-input');
                                        const obtainedInput = tr.querySelector('.marks-obtained-input');

                                        maxInput.addEventListener('input', () => {
                                            obtainedInput.max = maxInput.value;
                                        });
                                    });
                                }

                                marksEntryContainer.style.display = 'block';
                            } else {
                                Swal.fire({
                                    title: 'Failed to load',
                                    text: data.error || 'Could not fetch student records.',
                                    icon: 'error',
                                    confirmButtonColor: 'var(--accent)'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            console.error('Error loading marks:', err);
                            Swal.fire({
                                title: 'Server Error',
                                text: 'Failed to connect to backend server.',
                                icon: 'error',
                                confirmButtonColor: 'var(--accent)'
                            });
                        });
                    });
                }

                if (saveMarksForm) {
                    saveMarksForm.addEventListener('submit', (e) => {
                        e.preventDefault();

                        const classId = marksClassSelect.value;
                        const subject = marksSubjectSelect.value;
                        const examName = marksExamName.value.trim();
                        const marksData = [];

                        const rows = marksTableTbody.querySelectorAll('tr');
                        let isValid = true;
                        let validationMsg = '';

                        rows.forEach(row => {
                            const maxInput = row.querySelector('.max-marks-input');
                            const obtainedInput = row.querySelector('.marks-obtained-input');

                            if (maxInput && obtainedInput) {
                                const studentId = obtainedInput.getAttribute('data-id');
                                const rowSubject = obtainedInput.getAttribute('data-subject') || subject;
                                const maxVal = parseFloat(maxInput.value);
                                const obtainedVal = parseFloat(obtainedInput.value);

                                if (isNaN(obtainedVal) || obtainedVal < 0) {
                                    isValid = false;
                                    validationMsg = 'Marks obtained must be a positive number.';
                                } else if (obtainedVal > maxVal) {
                                    isValid = false;
                                    validationMsg = `Marks obtained cannot exceed max marks (${maxVal}) for student ${studentId}.`;
                                }

                                marksData.push({
                                    student_id: studentId,
                                    subject: rowSubject,
                                    marks_obtained: obtainedVal,
                                    max_marks: maxVal
                                });
                            }
                        });

                        if (!isValid) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: validationMsg,
                                icon: 'warning',
                                confirmButtonColor: 'var(--accent)'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Publishing Student Marks...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        const postData = new FormData();
                        postData.append('class_id', classId);
                        postData.append('subject', subject);
                        postData.append('exam_name', examName);
                        postData.append('marks_data', JSON.stringify(marksData));

                        fetch('save_marks.php', {
                            method: 'POST',
                            body: postData
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    title: 'Marks Published!',
                                    text: data.message || 'Student marks successfully settled and published.',
                                    icon: 'success',
                                    confirmButtonColor: 'var(--primary)'
                                }).then(() => {
                                    loadMarksBtn.click();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Failed to publish marks',
                                    text: data.error || 'Server rejected inputs.',
                                    icon: 'error',
                                    confirmButtonColor: 'var(--accent)'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            console.error('Error saving marks:', err);
                            Swal.fire({
                                title: 'Connection Failed',
                                text: 'Failed to submit marks to database.',
                                icon: 'error',
                                confirmButtonColor: 'var(--accent)'
                            });
                        });
                    });
                }

                const showMarksBtn = document.getElementById('show-students-marks-btn');
                const marksReportContainer = document.getElementById('marks-report-container');
                const reportTableTbody = document.getElementById('report-table-tbody');
                const reportTableThead = document.getElementById('report-table-thead');
                const reportClassTitle = document.getElementById('report-class-title');
                const reportSubjectName = document.getElementById('report-subject-name');
                const printReportBtn = document.getElementById('print-marks-report-btn');

                if (showMarksBtn) {
                    showMarksBtn.addEventListener('click', () => {
                        const classId = marksClassSelect.value;
                        const subject = marksSubjectSelect.value;
                        const examName = marksExamName.value.trim();

                        if (!classId) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'Please select a Class Unit.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }

                        if (!subject) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'Please select a Subject.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }

                        if (!examName) {
                            Swal.fire({
                                title: 'Validation Error',
                                text: 'Please enter an Exam Name.',
                                icon: 'warning',
                                confirmButtonColor: 'var(--primary)'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Generating Report Sheet...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`get_marks.php?class_id=${encodeURIComponent(classId)}&exam_name=${encodeURIComponent(examName)}&subject=${encodeURIComponent(subject)}`)
                        .then(res => res.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                reportClassTitle.textContent = marksClassSelect.options[marksClassSelect.selectedIndex].text.split(' (Grade')[0];
                                reportSubjectName.textContent = data.subject;
                                reportTableTbody.innerHTML = '';

                                const showSubjectCol = (subject === 'All');
                                
                                if (showSubjectCol) {
                                    reportTableThead.innerHTML = `
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th>Subject</th>
                                            <th>Max Marks</th>
                                            <th>Marks Obtained</th>
                                            <th>Percentage</th>
                                            <th>Result Status</th>
                                        </tr>
                                    `;
                                } else {
                                    reportTableThead.innerHTML = `
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student ID</th>
                                            <th>Max Marks</th>
                                            <th>Marks Obtained</th>
                                            <th>Percentage</th>
                                            <th>Result Status</th>
                                        </tr>
                                    `;
                                }

                                if (data.students.length === 0) {
                                    reportTableTbody.innerHTML = `<tr><td colspan="${showSubjectCol ? 7 : 6}" style="text-align: center; padding: 2rem; color: var(--text-muted);">No marks recorded yet for this exam configuration.</td></tr>`;
                                } else {
                                    data.students.forEach(student => {
                                        const tr = document.createElement('tr');
                                        
                                        const maxVal = parseFloat(student.max_marks) || 100;
                                        const obtainedVal = student.marks_obtained !== null ? parseFloat(student.marks_obtained) : null;
                                        
                                        let percentText = 'N/A';
                                        let statusText = '<span style="color: var(--text-muted);">Pending</span>';
                                        
                                        if (obtainedVal !== null) {
                                            const pct = (obtainedVal / maxVal) * 100;
                                            percentText = pct.toFixed(1) + '%';
                                            
                                            if (pct >= 40) {
                                                statusText = '<span class="status-pass" style="color: #27c93f; font-weight: 700;"><i class="fa-solid fa-circle-check"></i> Passed</span>';
                                            } else {
                                                statusText = '<span class="status-fail" style="color: var(--accent); font-weight: 700;"><i class="fa-solid fa-circle-xmark"></i> Failed</span>';
                                            }
                                        }
                                        
                                        const obtainedText = obtainedVal !== null ? obtainedVal.toFixed(1) : '<em>Not Entered</em>';

                                        if (showSubjectCol) {
                                            tr.innerHTML = `
                                                <td style="font-weight: 700;">${student.full_name}</td>
                                                <td>${student.student_id}</td>
                                                <td style="font-weight: 600; color: var(--secondary);">${student.subject}</td>
                                                <td>${maxVal.toFixed(1)}</td>
                                                <td style="font-weight: 700; color: var(--primary);">${obtainedText}</td>
                                                <td style="font-weight: 600;">${percentText}</td>
                                                <td>${statusText}</td>
                                            `;
                                        } else {
                                            tr.innerHTML = `
                                                <td style="font-weight: 700;">${student.full_name}</td>
                                                <td>${student.student_id}</td>
                                                <td>${maxVal.toFixed(1)}</td>
                                                <td style="font-weight: 700; color: var(--primary);">${obtainedText}</td>
                                                <td style="font-weight: 600;">${percentText}</td>
                                                <td>${statusText}</td>
                                            `;
                                        }
                                        reportTableTbody.appendChild(tr);
                                    });
                                }

                                marksEntryContainer.style.display = 'none';
                                marksReportContainer.style.display = 'block';
                            } else {
                                Swal.fire({
                                    title: 'Failed to load',
                                    text: data.error || 'Could not fetch student records.',
                                    icon: 'error',
                                    confirmButtonColor: 'var(--accent)'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            console.error('Error loading report:', err);
                            Swal.fire({
                                title: 'Server Error',
                                text: 'Failed to connect to backend server.',
                                icon: 'error',
                                confirmButtonColor: 'var(--accent)'
                            });
                        });
                    });
                }

                if (printReportBtn) {
                    printReportBtn.addEventListener('click', () => {
                        const printContents = document.getElementById('marks-report-container').innerHTML;
                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(`
                            <html>
                            <head>
                                <title>Marks Grading Report</title>
                                <style>
                                    body { font-family: 'Segoe UI', sans-serif; padding: 2rem; color: #1f2937; }
                                    h4 { margin: 0 0 0.5rem 0; font-size: 1.5rem; color: #4f46e5; }
                                    span { font-size: 0.9rem; color: #4b5563; display: block; margin-bottom: 1.5rem; }
                                    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
                                    th, td { border: 1px solid #e5e7eb; padding: 0.75rem 1rem; text-align: left; }
                                    th { background-color: #f3f4f6; font-weight: 700; }
                                    td { font-size: 0.95rem; }
                                    .status-pass { color: #10b981; font-weight: 700; }
                                    .status-fail { color: #ef4444; font-weight: 700; }
                                    #print-marks-report-btn { display: none; }
                                </style>
                            </head>
                            <body>
                                ${printContents}
                            </body>
                            </html>
                        `);
                        printWindow.document.close();
                        printWindow.focus();
                        printWindow.print();
                        printWindow.close();
                    });
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

// ==========================================
// 2. STUDENT VIEW SECTION
// ==========================================
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

// Query marks data for this student
$sql_st_marks = "SELECT * FROM `marks` WHERE `student_id` = ? ORDER BY `exam_name` ASC, `subject` ASC";
$stmt_st_marks = $conn->prepare($sql_st_marks);
$student_records = [];
$st_total_obtained = 0;
$st_total_max = 0;
$st_passed_count = 0;

if ($stmt_st_marks) {
    $stmt_st_marks->bind_param("s", $student_id);
    $stmt_st_marks->execute();
    $res_st_marks = $stmt_st_marks->get_result();
    while ($row_st = $res_st_marks->fetch_assoc()) {
        $student_records[] = $row_st;
        $obtained = (float)$row_st['marks_obtained'];
        $max = (float)$row_st['max_marks'];
        $st_total_obtained += $obtained;
        $st_total_max += $max;
        
        $percentage = $max > 0 ? ($obtained / $max) * 100 : 0;
        if ($percentage >= 35.0) {
            $st_passed_count++;
        }
    }
    $stmt_st_marks->close();
}
$st_total_count = count($student_records);
$st_gpa = $st_total_max > 0 ? ($st_total_obtained / $st_total_max) * 100 : 0;

// Fetch distinct exam names for this student
$sql_st_exams = "SELECT DISTINCT `exam_name` FROM `marks` WHERE `student_id` = ? ORDER BY `exam_name` ASC";
$stmt_st_exams = $conn->prepare($sql_st_exams);
$st_exams = [];
if ($stmt_st_exams) {
    $stmt_st_exams->bind_param("s", $student_id);
    $stmt_st_exams->execute();
    $res_st_exams = $stmt_st_exams->get_result();
    while ($row = $res_st_exams->fetch_assoc()) {
        $st_exams[] = $row['exam_name'];
    }
    $stmt_st_exams->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Marks Directory - <?php echo $school_name; ?></title>
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
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Workspace Content */
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

        /* Table Styling */
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
            flex-wrap: wrap;
            gap: 1rem;
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
                    <a href="student_dashboard.php" class="logo" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                        <img src="images/logo.png" alt="Logo" style="height: 56px; width: auto; border-radius: 6px; margin-right: 0.5rem;"> <span id="sidebar-logo-text" style="font-weight: 800; font-size: 1.3rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo $school_name; ?></span>
                    </a>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="student_dashboard.php" class="sidebar-link" id="tab-student-overview"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="student_schedule.php" class="sidebar-link" id="tab-student-schedule"><i class="fa-solid fa-calendar-days"></i> Timetable</a></li>
                    <li><a href="student_homework.php" class="sidebar-link" id="tab-student-homework"><i class="fa-solid fa-book"></i> Homework</a></li>
                    <li><a href="student_leaves.php" class="sidebar-link" id="tab-student-leaves"><i class="fa-solid fa-calendar-minus"></i> Leave Requests</a></li>
                    <li><a href="student_marks.php" class="sidebar-link active" id="tab-student-marks"><i class="fa-solid fa-square-poll-vertical"></i> Marks</a></li>
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
                        <span>Grade <?php echo $grade; ?> Scholar</span>
                    </div>
                </div>
                <a href="student_dashboard.php?action=logout" class="sidebar-link" style="color: var(--accent); padding-left: 0.75rem;"><i class="fa-solid fa-power-off"></i> Sign Out</a>
            </div>
        </aside>

        <!-- Main Workspace Contents -->
        <main class="workspace">
            <div class="top-panel">
                <div class="welcome-title">
                    <h2>Academic Marks & Grade Report</h2>
                    <p>Select or enter your examination name to search and generate your scorecard.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Exam Name Input / Search Card -->
            <div class="table-container" style="margin-top: 0; margin-bottom: 2rem; padding: 1.75rem 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary-glow); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 1.15rem; font-weight: 800; margin: 0;">Search Examination Marks</h4>
                        <span style="font-size: 0.85rem; color: var(--text-muted);">Choose an exam from the list or type your exam name below</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)) auto auto; gap: 1rem; align-items: flex-end;">
                    <!-- Select Dropdown -->
                    <div>
                        <label for="student-exam-select" style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.4rem;">
                            <i class="fa-solid fa-list-check" style="color: var(--primary); margin-right: 0.3rem;"></i> Available Exams
                        </label>
                        <select id="student-exam-select" class="form-control" style="width: 100%; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.95rem;">
                            <option value="">-- Choose Examination --</option>
                            <?php foreach ($st_exams as $ex): ?>
                                <option value="<?php echo htmlspecialchars($ex); ?>"><?php echo htmlspecialchars($ex); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Manual Input Field with Datalist -->
                    <div>
                        <label for="custom-exam-name" style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.4rem;">
                            <i class="fa-solid fa-pen-to-square" style="color: var(--secondary); margin-right: 0.3rem;"></i> Or Type Exam Name
                        </label>
                        <input type="text" id="custom-exam-name" list="exam-datalist" class="form-control" placeholder="e.g. Mid-Term Examination 2026" style="width: 100%; padding: 0.75rem 1rem; border-radius: 12px; font-size: 0.95rem;">
                        <datalist id="exam-datalist">
                            <?php foreach ($st_exams as $ex): ?>
                                <option value="<?php echo htmlspecialchars($ex); ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <!-- View Marks Button -->
                    <div>
                        <button type="button" id="btn-show-student-marks" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; height: 46px; white-space: nowrap; cursor: pointer;">
                            <i class="fa-solid fa-square-poll-vertical"></i> View Marks
                        </button>
                    </div>

                    <!-- Reset Button -->
                    <div>
                        <button type="button" id="btn-reset-marks" class="btn btn-secondary" style="padding: 0.75rem 1.25rem; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 0.4rem; height: 46px; white-space: nowrap; cursor: pointer;">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Initial Placeholder (Shown when no exam has been searched yet) -->
            <div id="marks-initial-placeholder" style="text-align: center; padding: 4.5rem 2rem; background: var(--card-bg); border: 1px dashed var(--card-border); border-radius: 24px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-glow); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.5rem;">
                    <i class="fa-solid fa-file-circle-question"></i>
                </div>
                <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 0.5rem;">No Examination Selected</h3>
                <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto; font-size: 0.95rem; line-height: 1.6;">
                    Please select an exam name from the dropdown or type it in the input field above, then click <strong>"View Marks"</strong> to display your results and report card.
                </p>
            </div>

            <!-- Marks Results Container (Hidden by default until student clicks 'View Marks') -->
            <div id="marks-results-container" style="display: none;">
                <!-- Result Exam Header Banner -->
                <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(6, 182, 212, 0.15)); border: 1px solid var(--card-border); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 800; color: var(--primary);">Evaluation Report Card</span>
                        <h3 id="result-exam-title" style="font-size: 1.6rem; font-weight: 800; margin: 0.2rem 0; color: var(--text-main);">Exam Name</h3>
                        <span style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600;">
                            Student: <strong><?php echo $first_name . ' ' . $last_name; ?></strong> (ID: <code><?php echo $student_id; ?></code>) | Grade: <strong><?php echo $grade; ?></strong>
                        </span>
                    </div>
                    <div>
                        <button type="button" id="btn-print-scorecard" class="btn btn-secondary" style="padding: 0.65rem 1.25rem; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <i class="fa-solid fa-print"></i> Print Report Card
                        </button>
                    </div>
                </div>

                <!-- Exam-Specific KPI Summary Cards -->
                <div class="dash-grid" id="exam-kpi-grid">
                    <div class="dash-card">
                        <div class="dash-card-info">
                            <h4>Exam Percentage</h4>
                            <h3 id="kpi-exam-percentage">0.0%</h3>
                            <span class="dash-card-trend trend-up" id="kpi-exam-badge"><i class="fa-solid fa-award"></i> Grade Standing</span>
                        </div>
                        <div class="dash-card-icon" style="color: var(--primary); background: rgba(99, 102, 241, 0.15);">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-info">
                            <h4>Subjects Passed</h4>
                            <h3 id="kpi-exam-passed">0 / 0</h3>
                            <span class="dash-card-trend trend-up" id="kpi-passed-rate"><i class="fa-solid fa-circle-check"></i> Standard Pass: >=35%</span>
                        </div>
                        <div class="dash-card-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                    </div>

                    <div class="dash-card">
                        <div class="dash-card-info">
                            <h4>Total Marks Gained</h4>
                            <h3 id="kpi-exam-total">0 / 0</h3>
                            <span class="dash-card-trend trend-up"><i class="fa-solid fa-star"></i> Aggregate Score</span>
                        </div>
                        <div class="dash-card-icon" style="color: var(--secondary); background: rgba(6, 182, 212, 0.15);">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                        </div>
                    </div>
                </div>

                <!-- Detailed Marks Table Container -->
                <div class="table-container">
                    <div class="table-header-row">
                        <div>
                            <h4 id="table-card-heading">Subject Performance Breakdown</h4>
                            <span id="table-card-subheading" style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Verified examination scores</span>
                        </div>
                        <div id="exam-status-pill"></div>
                    </div>

                    <table class="students-table" id="student-marks-table">
                        <thead>
                            <tr>
                                <th>Subject Course</th>
                                <th>Max Marks</th>
                                <th>Marks Obtained</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Result Status</th>
                            </tr>
                        </thead>
                        <tbody id="student-marks-tbody">
                            <!-- Dynamically generated rows -->
                        </tbody>
                        <tfoot id="student-marks-tfoot" style="border-top: 2px solid var(--card-border); font-weight: 700;">
                            <!-- Dynamically generated totals -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- JS Logic for Student Marks -->
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

            // Raw student marks data from database
            const allMarksRecords = <?php echo json_encode($student_records); ?>;
            const studentInfo = {
                id: <?php echo json_encode($student_id); ?>,
                fullName: <?php echo json_encode($first_name . ' ' . $last_name); ?>,
                grade: <?php echo json_encode($grade); ?>,
                schoolName: <?php echo json_encode($school_name); ?>
            };

            const examSelect = document.getElementById('student-exam-select');
            const customExamInput = document.getElementById('custom-exam-name');
            const btnShowMarks = document.getElementById('btn-show-student-marks');
            const btnResetMarks = document.getElementById('btn-reset-marks');
            const initialPlaceholder = document.getElementById('marks-initial-placeholder');
            const resultsContainer = document.getElementById('marks-results-container');
            const resultExamTitle = document.getElementById('result-exam-title');
            const marksTbody = document.getElementById('student-marks-tbody');
            const marksTfoot = document.getElementById('student-marks-tfoot');
            const kpiExamPercentage = document.getElementById('kpi-exam-percentage');
            const kpiExamPassed = document.getElementById('kpi-exam-passed');
            const kpiExamTotal = document.getElementById('kpi-exam-total');
            const kpiExamBadge = document.getElementById('kpi-exam-badge');
            const examStatusPill = document.getElementById('exam-status-pill');
            const btnPrintScorecard = document.getElementById('btn-print-scorecard');

            let currentFilteredRecords = [];
            let currentExamName = '';

            // Sync select with input
            if (examSelect) {
                examSelect.addEventListener('change', () => {
                    if (examSelect.value) {
                        customExamInput.value = examSelect.value;
                    }
                });
            }

            if (customExamInput) {
                customExamInput.addEventListener('input', () => {
                    const typed = customExamInput.value.trim().toLowerCase();
                    let matched = false;
                    for (let i = 0; i < examSelect.options.length; i++) {
                        if (examSelect.options[i].value.toLowerCase() === typed) {
                            examSelect.selectedIndex = i;
                            matched = true;
                            break;
                        }
                    }
                    if (!matched) {
                        examSelect.value = '';
                    }
                });

                // Allow pressing Enter in input field
                customExamInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        btnShowMarks.click();
                    }
                });
            }

            // View Marks Button Click Handler
            if (btnShowMarks) {
                btnShowMarks.addEventListener('click', () => {
                    const searchExam = (customExamInput.value || examSelect.value || '').trim();

                    if (!searchExam) {
                        Swal.fire({
                            title: 'Exam Name Required',
                            text: 'Please select an exam from the list or type your examination name.',
                            icon: 'warning',
                            confirmButtonColor: 'var(--primary)'
                        });
                        return;
                    }

                    // Filter records matching the exam name (case-insensitive)
                    const searchLower = searchExam.toLowerCase();
                    const filtered = allMarksRecords.filter(rec => {
                        return (rec.exam_name || '').trim().toLowerCase() === searchLower;
                    });

                    if (filtered.length === 0) {
                        Swal.fire({
                            title: 'No Marks Found',
                            text: `No marks records found for examination "${searchExam}". Please verify the exam name or check if results have been published.`,
                            icon: 'info',
                            confirmButtonColor: 'var(--primary)'
                        });
                        return;
                    }

                    currentFilteredRecords = filtered;
                    currentExamName = filtered[0].exam_name || searchExam;

                    renderExamResults(currentExamName, currentFilteredRecords);
                });
            }

            // Calculate Letter Grade based on percentage
            function getLetterGrade(pct) {
                if (pct >= 90) return { grade: 'A+', color: '#10b981' };
                if (pct >= 80) return { grade: 'A', color: '#10b981' };
                if (pct >= 70) return { grade: 'B+', color: '#3b82f6' };
                if (pct >= 60) return { grade: 'B', color: '#3b82f6' };
                if (pct >= 50) return { grade: 'C+', color: '#eab308' };
                if (pct >= 40) return { grade: 'C', color: '#eab308' };
                if (pct >= 35) return { grade: 'D', color: '#f97316' };
                return { grade: 'F', color: 'var(--accent)' };
            }

            // Render Results into UI
            function renderExamResults(examName, records) {
                resultExamTitle.textContent = examName;

                let totalObtained = 0;
                let totalMax = 0;
                let passedCount = 0;

                marksTbody.innerHTML = '';

                records.forEach(rec => {
                    const subject = rec.subject;
                    const maxMarks = parseFloat(rec.max_marks) || 0;
                    const marksObtained = parseFloat(rec.marks_obtained) || 0;
                    totalObtained += marksObtained;
                    totalMax += maxMarks;

                    const pct = maxMarks > 0 ? (marksObtained / maxMarks) * 100 : 0;
                    const isPass = pct >= 35.0;
                    if (isPass) passedCount++;

                    const letterInfo = getLetterGrade(pct);
                    const statusBadge = isPass 
                        ? '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Passed</span>'
                        : '<span class="status-badge status-pending"><i class="fa-solid fa-circle-xmark"></i> Failed</span>';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td style="font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-book-open" style="color: var(--secondary); margin-right: 0.5rem;"></i>${escapeHtml(subject)}</td>
                        <td>${maxMarks.toFixed(1)}</td>
                        <td style="font-weight: 700; color: var(--primary); font-size: 1rem;">${marksObtained.toFixed(1)}</td>
                        <td style="font-weight: 600; color: ${isPass ? '#10b981' : 'var(--accent)'};">${pct.toFixed(1)}%</td>
                        <td><span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 800; font-size: 0.85rem; background: rgba(255,255,255,0.06); color: ${letterInfo.color};">${letterInfo.grade}</span></td>
                        <td>${statusBadge}</td>
                    `;
                    marksTbody.appendChild(tr);
                });

                // Calculate Totals and KPIs
                const overallPct = totalMax > 0 ? (totalObtained / totalMax) * 100 : 0;
                const overallPass = (passedCount === records.length) && (overallPct >= 35.0);
                const overallGradeInfo = getLetterGrade(overallPct);

                kpiExamPercentage.textContent = overallPct.toFixed(1) + '%';
                kpiExamPassed.textContent = `${passedCount} / ${records.length}`;
                kpiExamTotal.textContent = `${totalObtained.toFixed(1)} / ${totalMax.toFixed(1)}`;

                kpiExamBadge.innerHTML = `<i class="fa-solid fa-award"></i> Grade: <strong>${overallGradeInfo.grade}</strong> (${overallPass ? 'Passed' : 'Needs Improvement'})`;

                if (overallPass) {
                    examStatusPill.innerHTML = '<span class="status-badge status-active" style="font-size: 0.85rem; padding: 0.35rem 0.9rem;"><i class="fa-solid fa-circle-check"></i> Examination Cleared</span>';
                } else {
                    examStatusPill.innerHTML = '<span class="status-badge status-pending" style="font-size: 0.85rem; padding: 0.35rem 0.9rem;"><i class="fa-solid fa-triangle-exclamation"></i> Supplementary Required</span>';
                }

                // Render Footer Totals
                marksTfoot.innerHTML = `
                    <tr style="background: rgba(255, 255, 255, 0.03);">
                        <td style="color: var(--text-main); font-size: 1rem;">Aggregate Total</td>
                        <td style="color: var(--text-main);">${totalMax.toFixed(1)}</td>
                        <td style="color: var(--primary); font-size: 1.05rem;">${totalObtained.toFixed(1)}</td>
                        <td style="color: ${overallPass ? '#10b981' : 'var(--accent)'}; font-size: 1.05rem;">${overallPct.toFixed(1)}%</td>
                        <td><span style="font-weight: 800; color: ${overallGradeInfo.color};">${overallGradeInfo.grade}</span></td>
                        <td>${overallPass ? '<span style="color: #10b981; font-weight: 700;">PASSED</span>' : '<span style="color: var(--accent); font-weight: 700;">FAILED</span>'}</td>
                    </tr>
                `;

                // Show Results Section and Hide Placeholder
                initialPlaceholder.style.display = 'none';
                resultsContainer.style.display = 'block';

                // Smooth scroll down to results
                resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Reset Button Handler
            if (btnResetMarks) {
                btnResetMarks.addEventListener('click', () => {
                    if (examSelect) examSelect.value = '';
                    if (customExamInput) customExamInput.value = '';
                    resultsContainer.style.display = 'none';
                    initialPlaceholder.style.display = 'block';
                    currentFilteredRecords = [];
                    currentExamName = '';
                });
            }

            // Print Scorecard Handler
            if (btnPrintScorecard) {
                btnPrintScorecard.addEventListener('click', () => {
                    if (!currentFilteredRecords || currentFilteredRecords.length === 0) return;

                    let totalObtained = 0;
                    let totalMax = 0;
                    let passedCount = 0;
                    let rowsHtml = '';

                    currentFilteredRecords.forEach(rec => {
                        const subject = rec.subject;
                        const maxMarks = parseFloat(rec.max_marks) || 0;
                        const marksObtained = parseFloat(rec.marks_obtained) || 0;
                        totalObtained += marksObtained;
                        totalMax += maxMarks;

                        const pct = maxMarks > 0 ? (marksObtained / maxMarks) * 100 : 0;
                        const isPass = pct >= 35.0;
                        if (isPass) passedCount++;
                        const letterInfo = getLetterGrade(pct);

                        rowsHtml += `
                            <tr>
                                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; font-weight: 600;">${escapeHtml(subject)}</td>
                                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; text-align: center;">${maxMarks.toFixed(1)}</td>
                                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; text-align: center; font-weight: 700; color: #4338ca;">${marksObtained.toFixed(1)}</td>
                                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; text-align: center;">${pct.toFixed(1)}%</td>
                                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; text-align: center; font-weight: 700;">${letterInfo.grade}</td>
                                <td style="padding: 10px 14px; border-bottom: 1px solid #e5e7eb; text-align: center; font-weight: 700; color: ${isPass ? '#15803d' : '#b91c1c'};">${isPass ? 'PASS' : 'FAIL'}</td>
                            </tr>
                        `;
                    });

                    const overallPct = totalMax > 0 ? (totalObtained / totalMax) * 100 : 0;
                    const overallPass = (passedCount === currentFilteredRecords.length) && (overallPct >= 35.0);
                    const overallGradeInfo = getLetterGrade(overallPct);
                    const todayDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>${escapeHtml(currentExamName)} - Report Card - ${escapeHtml(studentInfo.fullName)}</title>
                            <style>
                                @page { size: A4; margin: 20mm; }
                                body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; margin: 0; padding: 20px; }
                                .header-box { border-bottom: 3px double #4f46e5; padding-bottom: 15px; margin-bottom: 20px; text-align: center; }
                                .school-name { font-size: 26px; font-weight: 800; color: #4338ca; margin: 0; text-transform: uppercase; }
                                .report-title { font-size: 16px; font-weight: 600; color: #6b7280; margin-top: 4px; }
                                .meta-grid { display: flex; justify-content: space-between; margin-bottom: 25px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px 20px; font-size: 14px; }
                                .meta-col { line-height: 1.8; }
                                table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
                                th { background-color: #f3f4f6; color: #374151; font-weight: 700; padding: 10px 14px; border-bottom: 2px solid #d1d5db; text-align: left; }
                                th.center { text-align: center; }
                                .summary-box { display: flex; justify-content: space-between; margin-top: 25px; padding: 15px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb; font-size: 14px; }
                                .stamp-box { margin-top: 50px; display: flex; justify-content: space-between; padding: 0 30px; font-size: 13px; color: #4b5563; }
                                .sig-line { border-top: 1px solid #9ca3af; width: 180px; text-align: center; padding-top: 6px; }
                            </style>
                        </head>
                        <body>
                            <div class="header-box">
                                <h1 class="school-name">${escapeHtml(studentInfo.schoolName)}</h1>
                                <div class="report-title">OFFICIAL STUDENT ACADEMIC SCORECARD</div>
                            </div>

                            <div class="meta-grid">
                                <div class="meta-col">
                                    <div><strong>Student Name:</strong> ${escapeHtml(studentInfo.fullName)}</div>
                                    <div><strong>Student ID:</strong> ${escapeHtml(studentInfo.id)}</div>
                                    <div><strong>Grade / Class:</strong> ${escapeHtml(studentInfo.grade)}</div>
                                </div>
                                <div class="meta-col" style="text-align: right;">
                                    <div><strong>Examination:</strong> ${escapeHtml(currentExamName)}</div>
                                    <div><strong>Issue Date:</strong> ${todayDate}</div>
                                    <div><strong>Overall Result:</strong> <strong style="color: ${overallPass ? '#15803d' : '#b91c1c'};">${overallPass ? 'PASSED' : 'FAILED'}</strong></div>
                                </div>
                            </div>

                            <table>
                                <thead>
                                    <tr>
                                        <th>Subject Course</th>
                                        <th class="center">Maximum Marks</th>
                                        <th class="center">Marks Obtained</th>
                                        <th class="center">Percentage</th>
                                        <th class="center">Grade</th>
                                        <th class="center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rowsHtml}
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f3f4f6; font-weight: 700;">
                                        <td style="padding: 10px 14px; border-top: 2px solid #d1d5db;">Aggregate Total</td>
                                        <td style="padding: 10px 14px; border-top: 2px solid #d1d5db; text-align: center;">${totalMax.toFixed(1)}</td>
                                        <td style="padding: 10px 14px; border-top: 2px solid #d1d5db; text-align: center; color: #4338ca;">${totalObtained.toFixed(1)}</td>
                                        <td style="padding: 10px 14px; border-top: 2px solid #d1d5db; text-align: center;">${overallPct.toFixed(1)}%</td>
                                        <td style="padding: 10px 14px; border-top: 2px solid #d1d5db; text-align: center;">${overallGradeInfo.grade}</td>
                                        <td style="padding: 10px 14px; border-top: 2px solid #d1d5db; text-align: center; color: ${overallPass ? '#15803d' : '#b91c1c'};">${overallPass ? 'PASSED' : 'FAILED'}</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="summary-box">
                                <div><strong>Total Percentage:</strong> ${overallPct.toFixed(1)}%</div>
                                <div><strong>Cumulative Grade:</strong> ${overallGradeInfo.grade}</div>
                                <div><strong>Subjects Passed:</strong> ${passedCount} of ${currentFilteredRecords.length}</div>
                            </div>

                            <div class="stamp-box">
                                <div class="sig-line">Class Teacher's Signature</div>
                                <div class="sig-line">Principal / Controller of Exams</div>
                            </div>
                        </body>
                        </html>
                    `);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => {
                        printWindow.print();
                    }, 300);
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.toString().replace(/[&<>"']/g, m => map[m]);
            }
        });
    </script>
</body>
</html>
