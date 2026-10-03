<?php
require_once 'admin_header.php';
$page_title = "Overview Workspace";
$page_subtitle = "Welcome back, " . htmlspecialchars($admin_name) . ". Here is what is happening today.";
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';
?>

<!-- Overview Section Content -->
<div id="overview-section" style="display: block;">
    <!-- Stats KPI Cards -->
    <div class="dash-grid">
        <!-- Stat Card 1 -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Total Students</h4>
                <h3>2,548</h3>
                <span class="dash-card-trend trend-up"><i class="fa-solid fa-arrow-up"></i> +12% this term</span>
            </div>
            <div class="dash-card-icon">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Active Teachers</h4>
                <h3>120</h3>
                <span class="dash-card-trend trend-up"><i class="fa-solid fa-arrow-up"></i> +4 new hires</span>
            </div>
            <div class="dash-card-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Daily Attendance</h4>
                <h3>98.6%</h3>
                <span class="dash-card-trend trend-up"><i class="fa-solid fa-arrow-up"></i> +0.4% vs last week</span>
            </div>
            <div class="dash-card-icon" style="color: var(--secondary); background: rgba(6,182,212,0.15);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Unpaid Invoices</h4>
                <h3>$2,450</h3>
                <span class="dash-card-trend trend-down"><i class="fa-solid fa-arrow-down"></i> -15% outstanding</span>
            </div>
            <div class="dash-card-icon" style="color: var(--accent); background: rgba(244,63,94,0.15);">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>

    <!-- Dashboard Split Sections -->
    <div class="dash-sections">
        <!-- Recent Activities Panel -->
        <div class="activity-panel">
            <div class="panel-header">
                <h4>Recent Activities</h4>
                <a href="#" style="font-size: 0.85rem; color: var(--primary); font-weight: 600; text-decoration: none;">View All</a>
            </div>
            <ul class="activity-list" id="activity-feed-list">
                <li class="activity-item">
                    <div class="activity-badge"><i class="fa-solid fa-user-plus"></i></div>
                    <div class="activity-desc">
                        <h5>New Admission Form Registered</h5>
                        <p>Student Liam Anderson registered in Grade 10 Science stream.</p>
                    </div>
                    <div class="activity-time">5 mins ago</div>
                </li>
                <li class="activity-item">
                    <div class="activity-badge" style="color: var(--primary);"><i class="fa-solid fa-file-signature"></i></div>
                    <div class="activity-desc">
                        <h5>Grade Sheets Submitted</h5>
                        <p>Mrs. Sarah Jenkins uploaded the mid-term gradebook for English-A.</p>
                    </div>
                    <div class="activity-time">45 mins ago</div>
                </li>
                <li class="activity-item">
                    <div class="activity-badge" style="color: var(--accent);"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="activity-desc">
                        <h5>System Backup Successful</h5>
                        <p>Weekly database backup successfully stored in Secure AWS Cloud S3 bucket.</p>
                    </div>
                    <div class="activity-time">2 hours ago</div>
                </li>
                <li class="activity-item">
                    <div class="activity-badge" style="color: var(--secondary);"><i class="fa-solid fa-credit-card"></i></div>
                    <div class="activity-desc">
                        <h5>Tuition Fees Paid</h5>
                        <p>Parent Margaret Stone paid invoice #APX-4927 online ($850.00).</p>
                    </div>
                    <div class="activity-time">4 hours ago</div>
                </li>
            </ul>
        </div>

        <!-- Admin Action Panel -->
        <div class="actions-panel">
            <div class="panel-header">
                <h4>Quick Operations</h4>
            </div>
            <div class="action-buttons">
                <a href="student.php?openModal=admission" class="action-btn" id="quick-add-student-btn">
                    <i class="fa-solid fa-user-plus"></i> Add New Student
                </a>
                <a href="teacher.php?openModal=teacher" class="action-btn">
                    <i class="fa-solid fa-user-tie"></i> Appoint Teacher
                </a>
                <a href="class.php?openModal=class" class="action-btn">
                    <i class="fa-solid fa-school"></i> Create Class
                </a>
                <a href="settings.php" class="action-btn">
                    <i class="fa-solid fa-gears"></i> Manage Settings
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'admin_footer.php';
?>
