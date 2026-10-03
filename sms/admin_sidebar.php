<?php
$current_page = basename($_SERVER['PHP_SELF']);
$sidebar_admin_name = $admin_name ?? ($_SESSION['admin_name'] ?? $_SESSION['admin_username'] ?? $_SESSION['username'] ?? 'Admin');
$sidebar_admin_role = $admin_role ?? ($_SESSION['admin_role'] ?? 'System Admin');
$sidebar_admin_initials = $admin_initials ?? (strtoupper(substr($sidebar_admin_name, 0, min(2, strlen($sidebar_admin_name)))));
?>
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div>
                <div class="sidebar-brand">
                    <a href="admin_dashboard.php" class="logo">
                        <img src="images/logo.png" alt="Logo" style="height: 56px; width: auto; border-radius: 6px; -webkit-text-fill-color: initial; margin-right: 0.25rem; vertical-align: middle;"> <span id="sidebar-logo-text"><?php echo $school_name; ?></span>
                    </a>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="admin_dashboard.php" class="sidebar-link <?php if ($current_page === 'admin_dashboard.php') echo 'active'; ?>" id="tab-overview"><i class="fa-solid fa-chart-line"></i> Overview</a></li>
                    <li><a href="student.php" class="sidebar-link <?php if ($current_page === 'student.php') echo 'active'; ?>" id="tab-students"><i class="fa-solid fa-user-graduate"></i> Students</a></li>
                    <li><a href="teacher.php" class="sidebar-link <?php if ($current_page === 'teacher.php') echo 'active'; ?>" id="tab-teachers"><i class="fa-solid fa-chalkboard-user"></i> Teachers</a></li>
                    <li><a href="class.php" class="sidebar-link <?php if ($current_page === 'class.php') echo 'active'; ?>" id="tab-classes"><i class="fa-solid fa-calendar-days"></i> Classes</a></li>
                    <li><a href="marks.php" class="sidebar-link <?php if ($current_page === 'marks.php') echo 'active'; ?>" id="tab-marks"><i class="fa-solid fa-square-poll-vertical"></i> Marks</a></li>
                    <li><a href="cfinance.php" class="sidebar-link <?php if ($current_page === 'cfinance.php') echo 'active'; ?>" id="tab-finance"><i class="fa-solid fa-file-invoice-dollar"></i> Finance</a></li>
                    <li><a href="leave.php" class="sidebar-link <?php if ($current_page === 'leave.php') echo 'active'; ?>" id="tab-leaves"><i class="fa-solid fa-calendar-minus"></i> Leave Requests</a></li>
                    <li><a href="settings.php" class="sidebar-link <?php if ($current_page === 'settings.php') echo 'active'; ?>" id="tab-settings"><i class="fa-solid fa-sliders"></i> Settings</a></li>
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <!-- Admin Name & Profile Card above Log Out button -->
                <div class="admin-profile">
                    <div class="admin-avatar"><?php echo htmlspecialchars($sidebar_admin_initials); ?></div>
                    <div class="admin-details">
                        <h5><?php echo htmlspecialchars($sidebar_admin_name); ?></h5>
                        <span><?php echo htmlspecialchars($sidebar_admin_role); ?></span>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-secondary" style="width: 100%; gap: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-right-from-bracket"></i> Log Out
                </a>
            </div>
        </aside>
