<?php
$page_title = "Teacher Management";
$page_subtitle = "Manage, view, and appoint teachers in the academy.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';
?>

<!-- Teachers Section Content -->
<div id="teachers-section" style="display: block;">
    <div class="table-container">
        <div class="table-header-row">
            <h4>Active Teacher Directory</h4>
            <button class="btn btn-primary" id="appoint-teacher-btn">
                <i class="fa-solid fa-user-plus"></i> Appoint Teacher
            </button>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Teacher Name</th>
                    <th>Teacher ID</th>
                    <th>Specialization</th>
                    <th>Standard</th>
                    <th>Email</th>
                    <th>Mobile Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="teachers-table-body">
                <?php
                $sql_t_select = "SELECT * FROM `teachers` ORDER BY `id` DESC";
                $res_t = $conn->query($sql_t_select);
                if ($res_t && $res_t->num_rows > 0) {
                    while ($row_t = $res_t->fetch_assoc()) {
                        $tFullName = htmlspecialchars($row_t['first_name'] . ' ' . $row_t['last_name']);
                        $tInitials = strtoupper(substr($row_t['first_name'], 0, 1) . substr($row_t['last_name'], 0, 1));
                        $tId = htmlspecialchars($row_t['teacher_id']);
                        $tSpecial = htmlspecialchars($row_t['specialization']);
                        $tStandard = htmlspecialchars($row_t['standard']);
                        $tEmail = htmlspecialchars($row_t['email']);
                        $tMobile = htmlspecialchars($row_t['mobile_number']);

                        // Dynamic Avatar Colors
                        $tColorChoices = ['#8b5cf6', '#06b6d4', '#f43f5e', '#10b981', '#6366f1', '#eab308'];
                        $tAvatarBg = $tColorChoices[ord(strtoupper($row_t['first_name'][0])) % count($tColorChoices)];

                        echo '<tr id="trow-' . $tId . '">';
                        echo '<td style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">';
                        echo '<div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background: ' . $tAvatarBg . ';">' . $tInitials . '</div>';
                        echo $tFullName;
                        echo '</td>';
                        echo '<td>' . $tId . '</td>';
                        echo '<td>' . $tSpecial . '</td>';
                        echo '<td>' . $tStandard . '</td>';
                        echo '<td>' . $tEmail . '</td>';
                        echo '<td>' . $tMobile . '</td>';
                        echo '<td>';
                        echo '<button class="action-icon-btn edit-teacher-btn" data-id="' . $tId . '" title="Edit Teacher"><i class="fa-solid fa-user-pen"></i></button>';
                        echo '<button class="action-icon-btn delete-teacher-btn" data-id="' . $tId . '" title="Delete Teacher" style="color: var(--accent);"><i class="fa-solid fa-trash"></i></button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">No teachers registered in the database directory yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'admin_footer.php';
?>
