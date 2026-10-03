<?php
$page_title = "Leave Requests Approval";
$page_subtitle = "Review, approve, and manage student leave applications and status reports.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';

$sql_l_select = "SELECT l.*, s.first_name, s.last_name, s.grade FROM `leaves` l LEFT JOIN `students` s ON l.student_id = s.student_id ORDER BY l.id DESC";
$res_l = $conn->query($sql_l_select);

$total_l = 0;
$approved_l = 0;
$pending_l = 0;
$rejected_l = 0;
$leaves_data = [];

if ($res_l && $res_l->num_rows > 0) {
    while ($row_l = $res_l->fetch_assoc()) {
        $leaves_data[] = $row_l;
        $total_l++;
        $status = strtolower($row_l['status']);
        if ($status === 'approved') $approved_l++;
        elseif ($status === 'rejected') $rejected_l++;
        else $pending_l++;
    }
}
?>

<!-- Leave Requests Section -->
<div id="leaves-section" style="display: block;">
    <!-- Stats KPI Cards -->
    <div class="dash-grid">
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Total Requests</h4>
                <h3><?php echo $total_l; ?></h3>
                <span class="dash-card-trend trend-up"><i class="fa-solid fa-folder-open"></i> Leaves registry</span>
            </div>
            <div class="dash-card-icon" style="color: var(--primary); background: rgba(79, 70, 229, 0.15);">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Pending Review</h4>
                <h3 id="leaves-pending-count"><?php echo $pending_l; ?></h3>
                <span class="dash-card-trend trend-up" style="color: #eab308;"><i class="fa-solid fa-hourglass-half"></i> Awaiting decisions</span>
            </div>
            <div class="dash-card-icon" style="color: #eab308; background: rgba(234, 179, 8, 0.15);">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Approved</h4>
                <h3><?php echo $approved_l; ?></h3>
                <span class="dash-card-trend trend-up" style="color: #27c93f;"><i class="fa-solid fa-circle-check"></i> Leaves permitted</span>
            </div>
            <div class="dash-card-icon" style="color: #27c93f; background: rgba(39, 201, 63, 0.15);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Rejected</h4>
                <h3><?php echo $rejected_l; ?></h3>
                <span class="dash-card-trend trend-down" style="color: var(--accent);"><i class="fa-solid fa-circle-xmark"></i> Requests denied</span>
            </div>
            <div class="dash-card-icon" style="color: var(--accent); background: rgba(244, 63, 94, 0.15);">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Directory Table -->
    <div class="table-container">
        <div class="table-header-row">
            <h4>Leave Requests Directory</h4>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Subject</th>
                    <th>Dates Range</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody id="admin-leaves-table-body">
                <?php if (empty($leaves_data)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No student leave requests registered.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leaves_data as $lv): 
                        $lvId = htmlspecialchars($lv['leave_id']);
                        $sFname = htmlspecialchars($lv['first_name'] ?? 'Unknown');
                        $sLname = htmlspecialchars($lv['last_name'] ?? '');
                        $sFullName = trim($sFname . ' ' . $sLname);
                        $sId = htmlspecialchars($lv['student_id']);
                        $grade = htmlspecialchars($lv['grade'] ?? 'N/A');
                        $lvSubject = htmlspecialchars($lv['subject']);
                        $lvReason = htmlspecialchars($lv['reason']);
                        $lvStart = htmlspecialchars($lv['start_date']);
                        $lvEnd = htmlspecialchars($lv['end_date']);
                        $lvStatus = htmlspecialchars($lv['status']);
                        
                        // Initials & Avatar
                        $initials = strtoupper(substr($sFname, 0, 1) . substr($sLname, 0, 1));
                        $colorChoices = ['var(--primary)', 'var(--secondary)', 'var(--accent)', '#8b5cf6', '#eab308', '#10b981'];
                        $avatarBg = $colorChoices[ord(strtoupper($sFname[0])) % count($colorChoices)];

                        // Status Badge
                        if (strtolower($lvStatus) === 'approved') {
                            $statusBadge = '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Approved</span>';
                            $actions = '';
                        } else if (strtolower($lvStatus) === 'rejected') {
                            $statusBadge = '<span class="status-badge status-pending" style="background: rgba(244,63,94,0.15); color: var(--accent);"><i class="fa-solid fa-circle-xmark"></i> Rejected</span>';
                            $actions = '';
                        } else {
                            $statusBadge = '<span class="status-badge status-pending"><i class="fa-solid fa-spinner"></i> Pending</span>';
                            $actions = '
                                <button class="action-icon-btn approve-leave-btn" data-id="'.$lvId.'" title="Approve Request" style="color: #27c93f;"><i class="fa-solid fa-circle-check"></i></button>
                                <button class="action-icon-btn reject-leave-btn" data-id="'.$lvId.'" title="Reject Request" style="color: var(--accent);"><i class="fa-solid fa-circle-xmark"></i></button>
                            ';
                        }
                    ?>
                        <tr id="lvrow-<?php echo $lvId; ?>">
                            <td style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                                <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background: <?php echo $avatarBg; ?>;"><?php echo $initials; ?></div>
                                <div>
                                    <?php echo $sFullName; ?>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); display: block; font-weight: 500;">Grade <?php echo $grade; ?></span>
                                </div>
                            </td>
                            <td><?php echo $sId; ?></td>
                            <td style="font-weight: 600;"><?php echo $lvSubject; ?></td>
                            <td><?php echo $lvStart . ' to ' . $lvEnd; ?></td>
                            <td class="lv-status-col"><?php echo $statusBadge; ?></td>
                            <td style="text-align: right;">
                                <button class="action-icon-btn view-leave-btn" data-id="<?php echo $lvId; ?>" data-name="<?php echo $sFullName; ?>" data-subject="<?php echo $lvSubject; ?>" data-reason="<?php echo $lvReason; ?>" data-start="<?php echo $lvStart; ?>" data-end="<?php echo $lvEnd; ?>" data-status="<?php echo $lvStatus; ?>" title="View Application Details"><i class="fa-solid fa-eye"></i></button>
                                <span class="lv-actions-col"><?php echo $actions; ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'admin_footer.php';
?>
