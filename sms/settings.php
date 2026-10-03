<?php
$page_title = "Branding & Customization Settings";
$page_subtitle = "Personalize school details, active term sessions, color accent palettes, densities, and backups.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';
?>

<!-- Settings Section Content -->
<div id="settings-section" style="display: block;">
    <form id="settings-form" method="POST">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem;">
            
            <!-- Column 1: School Profile & Theme Customization -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- School Profile Card -->
                <div class="activity-panel">
                    <div class="panel-header" style="margin-bottom: 1.5rem;">
                        <h4><i class="fa-solid fa-school" style="color: var(--primary); margin-right: 0.5rem;"></i> School Profile</h4>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="setting-school-name">School Name</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-graduation-cap input-icon"></i>
                            <input type="text" id="setting-school-name" name="school_name" class="form-control" placeholder="e.g. AWS" value="<?php echo $school_name; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="setting-school-email">Contact Email</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="setting-school-email" name="school_email" class="form-control" placeholder="e.g. info@apexacademy.com" value="<?php echo $school_email; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="setting-school-phone">Contact Phone</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="tel" id="setting-school-phone" name="school_phone" class="form-control" placeholder="e.g. +1 (555) 019-2834" value="<?php echo $school_phone; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="setting-school-address">School Address</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-location-dot input-icon"></i>
                            <input type="text" id="setting-school-address" name="school_address" class="form-control" placeholder="e.g. 123 Academic Way" value="<?php echo $school_address; ?>" required>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Column 2: Administrator Profile -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Administrator Profile Details -->
                <div class="activity-panel">
                    <div class="panel-header" style="margin-bottom: 1.5rem;">
                        <h4><i class="fa-solid fa-user-shield" style="color: var(--primary); margin-right: 0.5rem;"></i> Admin Account Info</h4>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1.25rem; margin-bottom: 1.5rem; background: var(--input-bg); padding: 1rem; border-radius: 12px; border: 1px solid var(--card-border);">
                        <div class="admin-avatar" style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: #ffffff;"><?php echo htmlspecialchars($admin_initials); ?></div>
                        <div>
                            <h5 style="font-size: 1.05rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem;"><?php echo htmlspecialchars($admin_name); ?></h5>
                            <span class="status-badge status-active" style="padding: 0.15rem 0.5rem; font-size: 0.7rem;"><i class="fa-solid fa-shield"></i> <?php echo htmlspecialchars($admin_role); ?></span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; font-size: 0.9rem; margin-bottom: 1.25rem;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--card-border); padding-bottom: 0.5rem;">
                            <span style="color: var(--text-muted); font-weight: 500;">Email / Username</span>
                            <span style="color: var(--text-main); font-weight: 600;"><?php echo htmlspecialchars($admin_email); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--card-border); padding-bottom: 0.5rem;">
                            <span style="color: var(--text-muted); font-weight: 500;">Access Role</span>
                            <span style="color: var(--text-main); font-weight: 600;"><?php echo htmlspecialchars($admin_role); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--card-border); padding-bottom: 0.5rem;">
                            <span style="color: var(--text-muted); font-weight: 500;">Authentication</span>
                            <span style="color: var(--text-main); font-weight: 600;">Active Authenticated Session</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted); font-weight: 500;">Security Status</span>
                            <span style="color: #27c93f; font-weight: 600; display: flex; align-items: center; gap: 0.25rem;"><i class="fa-solid fa-circle-check"></i> SSL Secured</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="justify-content: center; gap: 0.5rem; width: 100%;">
                        <i class="fa-solid fa-circle-check"></i> Save Settings Configuration
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<?php
require_once 'admin_footer.php';
?>
