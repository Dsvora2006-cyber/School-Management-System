<?php
$page_title = "Academic Marks Directory";
$page_subtitle = "View, filter, and analyze student marks by class standard and exam terms.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';

// Fetch distinct exam names from database
$sql_exams = "SELECT DISTINCT `exam_name` FROM `marks` ORDER BY `exam_name` ASC";
$res_exams = $conn->query($sql_exams);
$exams = [];
if ($res_exams && $res_exams->num_rows > 0) {
    while($row = $res_exams->fetch_assoc()) {
        $exams[] = $row['exam_name'];
    }
}
if (empty($exams)) {
    $exams = ["Midterm Exam", "Final Exam"];
}

// Fetch distinct subjects from database
$sql_subjects = "SELECT DISTINCT `subject` FROM `marks` ORDER BY `subject` ASC";
$res_subjects = $conn->query($sql_subjects);
$db_subjects = [];
if ($res_subjects && $res_subjects->num_rows > 0) {
    while($row = $res_subjects->fetch_assoc()) {
        $db_subjects[] = $row['subject'];
    }
}

// Read parameters
$selected_standard = isset($_GET['standard']) ? $_GET['standard'] : '10';
$selected_exam = isset($_GET['exam']) ? $_GET['exam'] : (isset($exams[0]) ? $exams[0] : 'Midterm Exam');
$selected_subject = isset($_GET['subject']) ? $_GET['subject'] : 'All';

// Query performance data
$sql_marks = "SELECT s.first_name, s.last_name, s.student_id, m.subject, m.exam_name, m.marks_obtained, m.max_marks 
              FROM `marks` m 
              JOIN `students` s ON m.student_id = s.student_id 
              WHERE s.grade = ? AND m.exam_name = ?";
if ($selected_subject !== 'All') {
    $sql_marks .= " AND m.subject = ?";
}
$sql_marks .= " ORDER BY s.first_name ASC, s.last_name ASC, m.subject ASC";

$stmt = $conn->prepare($sql_marks);
if ($selected_subject !== 'All') {
    $stmt->bind_param("sss", $selected_standard, $selected_exam, $selected_subject);
} else {
    $stmt->bind_param("ss", $selected_standard, $selected_exam);
}
$stmt->execute();
$result = $stmt->get_result();

$records = [];
$total_obtained = 0;
$total_max = 0;
$passed_count = 0;
$highest_percentage = -1;
$highest_scorer = 'N/A';

while ($row = $result->fetch_assoc()) {
    $records[] = $row;
    $obtained = (float)$row['marks_obtained'];
    $max = (float)$row['max_marks'];
    
    $total_obtained += $obtained;
    $total_max += $max;
    
    $percentage = $max > 0 ? ($obtained / $max) * 100 : 0;
    if ($percentage >= 35.0) {
        $passed_count++;
    }
    
    if ($percentage > $highest_percentage) {
        $highest_percentage = $percentage;
        $highest_scorer = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . " (" . number_format($percentage, 1) . "%)";
    }
}
$total_count = count($records);
$class_average = $total_max > 0 ? ($total_obtained / $total_max) * 100 : 0;
$pass_rate = $total_count > 0 ? ($passed_count / $total_count) * 100 : 0;
?>

<!-- Marks Filters Panel -->
<div class="table-container" style="margin-bottom: 2rem;">
    <form method="GET" action="marks.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-standard"><i class="fa-solid fa-graduation-cap" style="color: var(--primary);"></i> Select Standard</label>
            <div class="input-wrapper">
                <select id="filter-standard" name="standard" class="form-control" style="appearance: none; -webkit-appearance: none;">
                    <?php
                    $standard_options = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11 (Commerce)", "11 (Science)", "12 (Commerce)", "12 (Science)"];
                    foreach ($standard_options as $opt) {
                        $selected = $opt === $selected_standard ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($opt) . '" ' . $selected . '>Standard ' . htmlspecialchars($opt) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-exam"><i class="fa-solid fa-file-invoice" style="color: var(--secondary);"></i> Exam Term</label>
            <div class="input-wrapper">
                <select id="filter-exam" name="exam" class="form-control" style="appearance: none; -webkit-appearance: none;">
                    <?php
                    foreach ($exams as $ex) {
                        $selected = $ex === $selected_exam ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($ex) . '" ' . $selected . '>' . htmlspecialchars($ex) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="filter-subject"><i class="fa-solid fa-book" style="color: var(--accent);"></i> Subject</label>
            <div class="input-wrapper">
                <select id="filter-subject" name="subject" class="form-control" style="appearance: none; -webkit-appearance: none;">
                    <option value="All" <?php echo $selected_subject === 'All' ? 'selected' : ''; ?>>All Subjects</option>
                    <?php
                    foreach ($db_subjects as $sub) {
                        $selected = $sub === $selected_subject ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($sub) . '" ' . $selected . '>' . htmlspecialchars($sub) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="width: 100%; height: 45px;">
                <i class="fa-solid fa-filter"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- Performance Metrics Panel -->
<div class="dash-grid">
    <!-- Class Average Card -->
    <div class="dash-card">
        <div class="dash-card-info">
            <h4>Class Average</h4>
            <h3><?php echo number_format($class_average, 1); ?>%</h3>
            <span class="dash-card-trend trend-up"><i class="fa-solid fa-chart-line"></i> Overall Performance</span>
        </div>
        <div class="dash-card-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--primary); width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-calculator"></i>
        </div>
    </div>

    <!-- Pass Rate Card -->
    <div class="dash-card">
        <div class="dash-card-info">
            <h4>Pass Rate</h4>
            <h3><?php echo number_format($pass_rate, 1); ?>%</h3>
            <span class="dash-card-trend trend-up"><i class="fa-solid fa-circle-check"></i> Standard Benchmark (35%+)</span>
        </div>
        <div class="dash-card-icon" style="background: rgba(6, 182, 212, 0.15); color: var(--secondary); width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
    </div>

    <!-- Top Scorer Card -->
    <div class="dash-card">
        <div class="dash-card-info">
            <h4>Top Scorer</h4>
            <h3 style="font-size: 1.1rem; margin-top: 0.5rem; word-break: break-all;"><?php echo $highest_scorer; ?></h3>
            <span class="dash-card-trend trend-up"><i class="fa-solid fa-trophy" style="color: #eab308;"></i> Highest Achieved</span>
        </div>
        <div class="dash-card-icon" style="background: rgba(244, 63, 94, 0.15); color: var(--accent); width: 54px; height: 54px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fa-solid fa-award"></i>
        </div>
    </div>
</div>

<!-- Marks Results Directory -->
<div class="table-container">
    <div class="table-header-row">
        <h4>Marks Registry for Standard <?php echo htmlspecialchars($selected_standard); ?></h4>
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="marks-search-input" placeholder="Search name or ID...">
        </div>
    </div>
    
    <table class="students-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Subject</th>
                <th>Exam Term</th>
                <th>Marks Obtained</th>
                <th>Max Marks</th>
                <th>Percentage</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="marks-table-body">
            <?php
            if ($total_count > 0) {
                foreach ($records as $row) {
                    $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                    $initials = strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1));
                    $studentId = htmlspecialchars($row['student_id']);
                    $subjectName = htmlspecialchars($row['subject']);
                    $examName = htmlspecialchars($row['exam_name']);
                    $obtained = (float)$row['marks_obtained'];
                    $max = (float)$row['max_marks'];
                    $percentage = $max > 0 ? ($obtained / $max) * 100 : 0;
                    
                    if ($percentage >= 35.0) {
                        $statusBadge = '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Pass</span>';
                    } else {
                        $statusBadge = '<span class="status-badge status-pending" style="background: rgba(244, 63, 94, 0.15); color: var(--accent);"><i class="fa-solid fa-circle-xmark"></i> Fail</span>';
                    }
                    
                    $colorChoices = ['var(--primary)', 'var(--secondary)', 'var(--accent)', '#8b5cf6', '#eab308', '#10b981'];
                    $avatarBg = $colorChoices[ord(strtoupper($row['first_name'][0])) % count($colorChoices)];
                    
                    echo '<tr>';
                    echo '<td style="font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">';
                    echo '<div class="admin-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; background: ' . $avatarBg . '; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white;">' . $initials . '</div>';
                    echo $fullName;
                    echo '</td>';
                    echo '<td>' . $studentId . '</td>';
                    echo '<td>' . $subjectName . '</td>';
                    echo '<td>' . $examName . '</td>';
                    echo '<td style="font-weight: 600;">' . number_format($obtained, 2) . '</td>';
                    echo '<td>' . number_format($max, 2) . '</td>';
                    echo '<td style="font-weight: 700; color: ' . ($percentage >= 35.0 ? 'var(--secondary)' : 'var(--accent)') . ';">' . number_format($percentage, 1) . '%</td>';
                    echo '<td>' . $statusBadge . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);"><i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; color: var(--card-border);"></i> No marks records found for this standard and exam term.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('marks-search-input');
    const tableBody = document.getElementById('marks-table-body');
    
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            const rows = tableBody.getElementsByTagName('tr');
            
            for (let row of rows) {
                if (row.cells.length === 1) continue;
                
                const studentName = row.cells[0].textContent.toLowerCase();
                const studentId = row.cells[1].textContent.toLowerCase();
                const subject = row.cells[2].textContent.toLowerCase();
                
                if (studentName.includes(query) || studentId.includes(query) || subject.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
});
</script>

<?php
require_once 'admin_footer.php';
?>
