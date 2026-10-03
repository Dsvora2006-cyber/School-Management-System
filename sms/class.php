<?php
$page_title = "Class Management";
$page_subtitle = "Manage, view, and organize academy classes and timetables.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';
?>

<!-- Classes Section Content -->
<div id="classes-section" style="display: block;">
    <div class="table-container">
        <div class="table-header-row">
            <h4>Active Class Registry</h4>
            <button class="btn btn-primary" id="create-class-btn">
                <i class="fa-solid fa-plus"></i> Create Class
            </button>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Subject</th>
                    <th>Class ID</th>
                    <th>Standard</th>
                    <th>Class Teacher</th>
                    <th>Room Number</th>
                    <th>Schedule</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="classes-table-body">
                <?php
                $sql_c_select = "SELECT c.*, t.first_name, t.last_name FROM `classes` c LEFT JOIN `teachers` t ON c.teacher_id = t.teacher_id ORDER BY c.id DESC";
                $res_c = $conn->query($sql_c_select);
                if ($res_c && $res_c->num_rows > 0) {
                    while ($row_c = $res_c->fetch_assoc()) {
                        $cName = htmlspecialchars($row_c['class_name']);
                        $cSubject = htmlspecialchars($row_c['subject']);
                        $cId = htmlspecialchars($row_c['class_id']);
                        $cStandard = htmlspecialchars($row_c['standard']);
                        $cRoom = htmlspecialchars($row_c['room_number']);
                        $cSchedule = htmlspecialchars($row_c['schedule']);
                        
                        // Generate initials/abbreviation from class name for avatar
                        $words = explode(' ', preg_replace('/[^A-Za-z0-9 ]/', '', $cName));
                        $cInitials = '';
                        foreach ($words as $w) {
                            if (strlen($w) > 0) {
                                if (is_numeric($w)) {
                                    $cInitials .= $w;
                                } else {
                                    $cInitials .= strtoupper($w[0]);
                                }
                            }
                        }
                        $cInitials = substr($cInitials, 0, 3);
                        if (empty($cInitials)) {
                            $cInitials = 'CLS';
                        }

                        // Class Teacher Name
                        if (!empty($row_c['first_name'])) {
                            $tName = htmlspecialchars($row_c['first_name'] . ' ' . $row_c['last_name']);
                        } else {
                            $tName = '<span style="color: var(--text-muted); font-style: italic;">Unassigned</span>';
                        }

                        // Dynamic Avatar Colors
                        $cColorChoices = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
                        $cAvatarBg = $cColorChoices[ord(strtoupper($cName[0] ?? 'A')) % count($cColorChoices)];

                        echo '<tr id="crow-' . $cId . '">';
                        echo '<td style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">';
                        echo '<div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background: ' . $cAvatarBg . ';">' . $cInitials . '</div>';
                        echo $cName;
                        echo '</td>';
                        echo '<td>' . $cSubject . '</td>';
                        echo '<td>' . $cId . '</td>';
                        echo '<td>' . $cStandard . '</td>';
                        echo '<td>' . $tName . '</td>';
                        echo '<td>' . $cRoom . '</td>';
                        echo '<td>' . $cSchedule . '</td>';
                        echo '<td>';
                        echo '<button class="action-icon-btn edit-class-btn" data-id="' . $cId . '" title="Edit Class"><i class="fa-solid fa-user-pen"></i></button>';
                        echo '<button class="action-icon-btn delete-class-btn" data-id="' . $cId . '" title="Delete Class" style="color: var(--accent);"><i class="fa-solid fa-trash"></i></button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">No classes registered in the database directory yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'admin_footer.php';
?>
