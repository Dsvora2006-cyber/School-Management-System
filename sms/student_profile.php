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
    die("Database query error.");
}

$first_name = htmlspecialchars($student['first_name']);
$last_name = htmlspecialchars($student['last_name']);
$grade = htmlspecialchars($student['grade']);
$email = htmlspecialchars($student['email']);
$mobile = htmlspecialchars($student['mobile_number']);
$dob = htmlspecialchars($student['dob']);
$gender = htmlspecialchars($student['gender']);
$blood_group = htmlspecialchars($student['blood_group']);
$address = htmlspecialchars($student['address']);
$city = htmlspecialchars($student['city']);
$state = htmlspecialchars($student['state']);
$pin_code = htmlspecialchars($student['pin_code']);
$nationality = htmlspecialchars($student['nationality']);
$religion = htmlspecialchars($student['religion']);
$category = htmlspecialchars($student['category']);
$father_name = htmlspecialchars($student['father_name']);
$mother_name = htmlspecialchars($student['mother_name']);
$parent_mobile = htmlspecialchars($student['parent_mobile']);
$parent_email = htmlspecialchars($student['parent_email']);
$parent_occupation = htmlspecialchars($student['parent_occupation']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Student Profile - <?php echo $school_name; ?></title>
    <!-- Base Stylesheet -->
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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

        /* Profile Layout Style Rules */
        .profile-grid-student {
            display: grid;
            grid-template-columns: 1fr 2.5fr;
            gap: 2rem;
            align-items: start;
        }

        .profile-card-left {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .profile-card-left:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .profile-info-right {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .profile-details-group {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .profile-details-group:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .detail-item {
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            background: rgba(255, 255, 255, 0.02) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            transform: translateX(4px);
        }

        @media (max-width: 992px) {
            .profile-grid-student {
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
                    <li><a href="student_dashboard.php" class="sidebar-link" id="tab-student-overview"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="student_schedule.php" class="sidebar-link" id="tab-student-schedule"><i class="fa-solid fa-calendar-days"></i> Timetable</a></li>
                    <li><a href="student_homework.php" class="sidebar-link" id="tab-student-homework"><i class="fa-solid fa-book"></i> Homework</a></li>
                    <li><a href="student_leaves.php" class="sidebar-link" id="tab-student-leaves"><i class="fa-solid fa-calendar-minus"></i> Leave Requests</a></li>
                    <li><a href="student_marks.php" class="sidebar-link" id="tab-student-marks"><i class="fa-solid fa-square-poll-vertical"></i> Marks</a></li>
                    <li><a href="pay_fees.php" class="sidebar-link" id="tab-student-fees"><i class="fa-solid fa-file-invoice-dollar"></i> Fees</a></li>
                    <li><a href="student_profile.php" class="sidebar-link active" id="tab-student-profile"><i class="fa-solid fa-id-card"></i> My Profile</a></li>
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
                    <h2>My Student Profile</h2>
                    <p>Review your official enrollment, parental contact, and residential address details.</p>
                </div>
                <div class="top-actions">
                    <button class="theme-toggle" id="theme-toggle" style="margin-right: 0.5rem;" aria-label="Toggle theme">
                        <i class="fa-solid fa-sun" id="theme-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Profile Info Section -->
            <div class="profile-grid-student">
                <!-- Profile Card Left -->
                <div class="profile-card-left">
                    <div class="profile-avatar" style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 800; font-size: 2.2rem; margin: 0 auto 1.5rem; border: 4px solid var(--card-border); box-shadow: 0 0 15px rgba(79, 70, 229, 0.4); text-transform: uppercase;">
                        <?php echo $first_name[0] . ($last_name[0] ?? ''); ?>
                    </div>
                    <h3 style="font-size: 1.4rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;"><?php echo $first_name . ' ' . $last_name; ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0.25rem 0 1.5rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Grade <?php echo $grade; ?> Student</p>
                    
                    <div style="border-top: 1px solid var(--card-border); padding-top: 1.5rem; text-align: left; display: flex; flex-direction: column; gap: 0.85rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                            <span style="color: var(--text-muted); font-weight: 600;"><i class="fa-solid fa-hashtag" style="margin-right: 0.35rem;"></i> ID Number</span>
                            <strong style="color: var(--text-main);"><?php echo $student_id; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; align-items: center;">
                            <span style="color: var(--text-muted); font-weight: 600;"><i class="fa-solid fa-shield-halved" style="margin-right: 0.35rem;"></i> Account Status</span>
                            <span class="status-badge status-active" style="padding: 0.2rem 0.6rem; font-size: 0.75rem;"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($student['status'] ?: 'Active'); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                            <span style="color: var(--text-muted); font-weight: 600;"><i class="fa-solid fa-calendar-check" style="margin-right: 0.35rem;"></i> Join Date</span>
                            <strong style="color: var(--text-main);"><?php echo date('Y-m-d', strtotime($student['created_at'])); ?></strong>
                        </div>

                    </div>
                </div>

                <!-- Profile Info Right -->
                <div class="profile-info-right">
                    <!-- Group 1: General Info -->
                    <div class="profile-details-group" style="border-left: 4px solid var(--primary);">
                        <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                            <i class="fa-solid fa-address-card" style="color: var(--primary);"></i> General & Academic Information
                        </h4>
                        <div class="details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">First Name</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-user" style="color: var(--primary); margin-right: 0.5rem;"></i> <?php echo $first_name; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Last Name</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-user" style="color: var(--primary); margin-right: 0.5rem;"></i> <?php echo $last_name; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Grade Standard</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-graduation-cap" style="color: var(--primary); margin-right: 0.5rem;"></i> Grade <?php echo $grade; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Date of Birth</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-calendar" style="color: var(--primary); margin-right: 0.5rem;"></i> <?php echo $dob; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Gender</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-venus-mars" style="color: var(--primary); margin-right: 0.5rem;"></i> <?php echo $gender; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Blood Group</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--accent);"><i class="fa-solid fa-droplet" style="color: var(--accent); margin-right: 0.5rem;"></i> <?php echo $blood_group ?: '—'; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Nationality</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-flag" style="color: var(--primary); margin-right: 0.5rem;"></i> <?php echo $nationality; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Category Group</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-users" style="color: var(--primary); margin-right: 0.5rem;"></i> <?php echo $category ?: '—'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Contact Info -->
                    <div class="profile-details-group" style="border-left: 4px solid var(--secondary);">
                        <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                            <i class="fa-solid fa-envelope-open-text" style="color: var(--secondary);"></i> Contact & Residential details
                        </h4>
                        <div class="details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Official Email Address</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-envelope" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $email; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Contact Number</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-phone" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $mobile; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem; grid-column: span 2;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Residential Address</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-map-location-dot" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $address; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">City</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-city" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $city; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">State</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-map" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $state; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">ZIP / Pin Code</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-envelopes-bulk" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $pin_code; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Religion / Belief</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-hands-praying" style="color: var(--secondary); margin-right: 0.5rem;"></i> <?php echo $religion ?: '—'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Group 3: Family Contacts -->
                    <div class="profile-details-group" style="border-left: 4px solid #10b981;">
                        <h4 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main);">
                            <i class="fa-solid fa-people-roof" style="color: #10b981;"></i> Parental Contacts & Family info
                        </h4>
                        <div class="details-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Father's Name</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-user-tie" style="color: #10b981; margin-right: 0.5rem;"></i> <?php echo $father_name; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Mother's Name</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-user-tie" style="color: #10b981; margin-right: 0.5rem;"></i> <?php echo $mother_name; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Parent Phone Number</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-phone-volume" style="color: #10b981; margin-right: 0.5rem;"></i> <?php echo $parent_mobile; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Parent Email Address</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-at" style="color: #10b981; margin-right: 0.5rem;"></i> <?php echo $parent_email ?: '—'; ?></span>
                            </div>
                            <div class="detail-item" style="background: rgba(255,255,255,0.01); border: 1px solid var(--card-border); border-radius: 14px; padding: 1rem 1.25rem; grid-column: span 2;">
                                <span class="detail-label" style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; margin-bottom: 0.25rem; text-transform: uppercase;">Parent / Father Occupation</span>
                                <span class="detail-value" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-briefcase" style="color: #10b981; margin-right: 0.5rem;"></i> <?php echo $parent_occupation ?: '—'; ?></span>
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
