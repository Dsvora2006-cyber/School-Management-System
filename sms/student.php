<?php
$page_title = "Student Management";
$page_subtitle = "Manage, view, and enroll students in the academy.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';
?>

<!-- Students Section Content -->
<div id="students-section" style="display: block;">
    <div class="table-container">
        <div class="table-header-row">
            <h4>Active Student Directory</h4>
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn btn-secondary" id="standard-wise-btn">
                    <i class="fa-solid fa-graduation-cap"></i> Standard-wise List
                </button>
                <button class="btn btn-primary" id="enroll-student-btn">
                    <i class="fa-solid fa-user-plus"></i> Enroll Student
                </button>
            </div>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Enrollment No</th>
                    <th>Standard</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="students-table-body">
                <?php
                $sql_select = "SELECT * FROM `students` ORDER BY `id` DESC";
                $result = $conn->query($sql_select);
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                        $initials = strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1));
                        $studentId = htmlspecialchars($row['student_id']);
                        $grade = htmlspecialchars($row['grade']);
                        $status = htmlspecialchars($row['status']);
                        
                        // Status Badge selection
                        $statusBadge = '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Active</span>';
                        if (strtolower($status) === 'pending') {
                            $statusBadge = '<span class="status-badge status-pending"><i class="fa-solid fa-spinner"></i> Pending</span>';
                        }

                        // Dynamic Avatar Colors
                        $colorChoices = ['var(--primary)', 'var(--secondary)', 'var(--accent)', '#8b5cf6', '#eab308', '#10b981'];
                        $avatarBg = $colorChoices[ord(strtoupper($row['first_name'][0])) % count($colorChoices)];

                        echo '<tr id="row-' . $studentId . '">';
                        echo '<td style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">';
                        echo '<div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background: ' . $avatarBg . ';">' . $initials . '</div>';
                        echo $fullName;
                        echo '</td>';
                        echo '<td>' . $studentId . '</td>';
                        echo '<td>' . $grade . '</td>';
                        echo '<td>' . $statusBadge . '</td>';
                        echo '<td>';
                        echo '<button class="action-icon-btn edit-student-btn" data-id="' . $studentId . '" title="Edit Student"><i class="fa-solid fa-user-pen"></i></button>';
                        echo '<button class="action-icon-btn delete-student-btn" data-id="' . $studentId . '" title="Delete Student" style="color: var(--accent);"><i class="fa-solid fa-trash"></i></button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No students registered in the database directory yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Standard-wise Student List Modal -->
<div class="modal-overlay" id="standard-wise-modal-overlay">
    <div class="modal-container" id="standard-wise-modal-container" style="max-width: 800px; padding: 2.25rem; width: 90%;">
        <button class="modal-close" id="standard-wise-modal-close" aria-label="Close modal">&times;</button>
        <div class="modal-header" style="margin-bottom: 1.5rem;">
            <h2>Standard-wise <span>Student Directory</span></h2>
            <p>Select a class standard to view its enrolled students.</p>
        </div>
        
        <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: center; justify-content: center;">
            <label class="form-label" for="filter-standard-select" style="margin-bottom: 0; font-weight: 700; white-space: nowrap;">
                <i class="fa-solid fa-graduation-cap" style="color: var(--primary);"></i> Select Standard:
            </label>
            <div class="input-wrapper" style="flex: 1; max-width: 300px;">
                <select id="filter-standard-select" class="form-control" style="padding-left: 1.5rem; appearance: none; -webkit-appearance: none;">
                    <option value="" disabled selected>-- Select Standard --</option>
                    <option value="1">Standard 1</option>
                    <option value="2">Standard 2</option>
                    <option value="3">Standard 3</option>
                    <option value="4">Standard 4</option>
                    <option value="5">Standard 5</option>
                    <option value="6">Standard 6</option>
                    <option value="7">Standard 7</option>
                    <option value="8">Standard 8</option>
                    <option value="9">Standard 9</option>
                    <option value="10">Standard 10</option>
                    <option value="11 (Commerce)">Standard 11 (Commerce)</option>
                    <option value="11 (Science)">Standard 11 (Science)</option>
                    <option value="12 (Commerce)">Standard 12 (Commerce)</option>
                    <option value="12 (Science)">Standard 12 (Science)</option>
                </select>
            </div>
        </div>

        <div class="table-container" style="box-shadow: none; padding: 0; background: transparent; border: none; max-height: 400px; overflow-y: auto;">
            <table class="students-table" style="width: 100%;">
                <thead>
                    <tr style="background: var(--primary-glow);">
                        <th>Student Name</th>
                        <th>Enrollment No</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="standard-wise-table-body">
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            Please select a standard from the dropdown above to view students.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
            <button type="button" class="btn btn-secondary" id="standard-wise-modal-close-btn">Close Window</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const standardWiseBtn = document.getElementById('standard-wise-btn');
    const standardWiseOverlay = document.getElementById('standard-wise-modal-overlay');
    const standardWiseClose = document.getElementById('standard-wise-modal-close');
    const standardWiseCloseBtn = document.getElementById('standard-wise-modal-close-btn');
    const filterStandardSelect = document.getElementById('filter-standard-select');
    const standardWiseTableBody = document.getElementById('standard-wise-table-body');

    if (standardWiseBtn && standardWiseOverlay) {
        standardWiseBtn.addEventListener('click', () => {
            standardWiseOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            standardWiseOverlay.classList.remove('active');
            document.body.style.overflow = '';
        };

        if (standardWiseClose) standardWiseClose.addEventListener('click', closeModal);
        if (standardWiseCloseBtn) standardWiseCloseBtn.addEventListener('click', closeModal);
        standardWiseOverlay.addEventListener('click', (e) => {
            if (e.target === standardWiseOverlay) {
                closeModal();
            }
        });

        if (filterStandardSelect) {
            filterStandardSelect.addEventListener('change', () => {
                const standardVal = filterStandardSelect.value;
                if (!standardVal) return;

                standardWiseTableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 2rem;"><i class="fa-solid fa-spinner fa-spin"></i> Loading students...</td></tr>';

                fetch(`get_students_by_standard.php?standard=${encodeURIComponent(standardVal)}`)
                    .then(res => res.json())
                    .then(resData => {
                        if (resData.success) {
                            const students = resData.data;
                            if (students.length === 0) {
                                standardWiseTableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-muted);">No students enrolled in this standard.</td></tr>';
                            } else {
                                let html = '';
                                students.forEach(student => {
                                    const fullName = student.first_name + ' ' + student.last_name;
                                    const initials = ((student.first_name[0] || '') + (student.last_name[0] || '')).toUpperCase();
                                    
                                    const colorChoices = ['var(--primary)', 'var(--secondary)', 'var(--accent)', '#8b5cf6', '#eab308', '#10b981'];
                                    const charCode = student.first_name.charCodeAt(0) || 0;
                                    const avatarBg = colorChoices[charCode % colorChoices.length];

                                    const statusBadge = student.status.toLowerCase() === 'active' 
                                        ? '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Active</span>'
                                        : '<span class="status-badge status-pending"><i class="fa-solid fa-spinner"></i> Pending</span>';

                                    html += `<tr>
                                        <td style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                                            <div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background: ${avatarBg}; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;">${initials}</div>
                                            ${fullName}
                                        </td>
                                        <td>${student.student_id}</td>
                                        <td>${statusBadge}</td>
                                    </tr>`;
                                });
                                standardWiseTableBody.innerHTML = html;
                            }
                        } else {
                            standardWiseTableBody.innerHTML = `<tr><td colspan="3" style="text-align: center; padding: 2rem; color: var(--accent);">${resData.error || 'Failed to load students.'}</td></tr>`;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        standardWiseTableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 2rem; color: var(--accent);">Network error occurred.</td></tr>';
                    });
            });
        }
    }
});
</script>

<?php
require_once 'admin_footer.php';
?>
