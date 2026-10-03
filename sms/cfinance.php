<?php
$page_title = "Finance Workspace";
$page_subtitle = "Track student invoices, fees collection, and academic revenue.";
require_once 'admin_header.php';
require_once 'admin_sidebar.php';
require_once 'admin_top_panel.php';

// Compute Stats
$collected_sql = "SELECT SUM(amount) AS total FROM invoices WHERE status = 'Paid'";
$collected_res = $conn->query($collected_sql);
$collected_row = $collected_res ? $collected_res->fetch_assoc() : [];
$total_collected = number_format($collected_row['total'] ?? 0.0, 2);

$outstanding_sql = "SELECT SUM(amount) AS total FROM invoices WHERE status = 'Unpaid'";
$outstanding_res = $conn->query($outstanding_sql);
$outstanding_row = $outstanding_res ? $outstanding_res->fetch_assoc() : [];
$total_outstanding = number_format($outstanding_row['total'] ?? 0.0, 2);

$paid_count_sql = "SELECT COUNT(*) AS count FROM invoices WHERE status = 'Paid'";
$paid_count_res = $conn->query($paid_count_sql);
$paid_count_row = $paid_count_res ? $paid_count_res->fetch_assoc() : [];
$paid_count = $paid_count_row['count'] ?? 0;

$unpaid_count_sql = "SELECT COUNT(*) AS count FROM invoices WHERE status = 'Unpaid'";
$unpaid_count_res = $conn->query($unpaid_count_sql);
$unpaid_count_row = $unpaid_count_res ? $unpaid_count_res->fetch_assoc() : [];
$unpaid_count = $unpaid_count_row['count'] ?? 0;
?>

<!-- Finance Section Content -->
<div id="finance-section" style="display: block;">
    <!-- Stats KPI Cards -->
    <div class="dash-grid" id="finance-stats-grid">
        <!-- Stat Card 1: Total Collected -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Total Collected</h4>
                <h3 id="stat-total-collected">₹<?php echo $total_collected; ?></h3>
                <span class="dash-card-trend trend-up"><i class="fa-solid fa-circle-check"></i> <?php echo $paid_count; ?> Payments</span>
            </div>
            <div class="dash-card-icon" style="color: #10b981; background: rgba(16, 185, 129, 0.15);">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <!-- Stat Card 2: Total Outstanding -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Total Outstanding</h4>
                <h3 id="stat-total-outstanding">₹<?php echo $total_outstanding; ?></h3>
                <span class="dash-card-trend trend-down" style="color: var(--accent);"><i class="fa-solid fa-clock"></i> <?php echo $unpaid_count; ?> Pending</span>
            </div>
            <div class="dash-card-icon" style="color: var(--accent); background: rgba(244, 63, 94, 0.15);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <!-- Stat Card 3: Paid Invoices -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Paid Bills</h4>
                <h3 id="stat-paid-count"><?php echo $paid_count; ?></h3>
                <span class="dash-card-trend trend-up"><i class="fa-solid fa-arrow-up"></i> Cleared registry</span>
            </div>
            <div class="dash-card-icon" style="color: var(--secondary); background: rgba(6, 182, 212, 0.15);">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <!-- Stat Card 4: Pending Invoices -->
        <div class="dash-card">
            <div class="dash-card-info">
                <h4>Pending Bills</h4>
                <h3 id="stat-unpaid-count"><?php echo $unpaid_count; ?></h3>
                <span class="dash-card-trend trend-down" style="color: #eab308;"><i class="fa-solid fa-hourglass-half"></i> Waiting action</span>
            </div>
            <div class="dash-card-icon" style="color: #eab308; background: rgba(234, 179, 8, 0.15);">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header-row">
            <h4>Invoices Directory</h4>
            <div style="display: flex; gap: 0.5rem;">
                <button class="btn btn-primary" id="collect-fee-btn">
                    <i class="fa-solid fa-plus"></i> Create Fees
                </button>
                <button class="btn btn-secondary" id="fees-structure-btn" style="background: var(--card-bg); border: 1px solid var(--card-border); color: var(--text-main);">
                    <i class="fa-solid fa-table-list"></i> Fees Structure
                </button>
            </div>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Student Name</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Payment Detail</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="invoices-table-body">
                <?php
                $sql_i_select = "SELECT i.*, s.first_name, s.last_name FROM `invoices` i LEFT JOIN `students` s ON i.student_id = s.student_id ORDER BY i.id DESC";
                $res_i = $conn->query($sql_i_select);
                if ($res_i && $res_i->num_rows > 0) {
                    while ($row_i = $res_i->fetch_assoc()) {
                        $invNo = htmlspecialchars($row_i['invoice_number']);
                        $invTitle = htmlspecialchars($row_i['title']);
                        $invAmount = number_format($row_i['amount'], 2);
                        $invDueDate = htmlspecialchars($row_i['due_date']);
                        $invStatus = htmlspecialchars($row_i['status']);
                        $invMethod = htmlspecialchars($row_i['payment_method'] ?? '');
                        $invDate = htmlspecialchars($row_i['payment_date'] ?? '');
                        
                        // Student/Standard Name
                        if (!empty($row_i['first_name'])) {
                            $sName = htmlspecialchars($row_i['first_name'] . ' ' . $row_i['last_name']);
                        } else {
                            $stdVal = htmlspecialchars($row_i['student_id']);
                            $sName = (strpos($stdVal, 'APX-') === 0) 
                                ? '<span style="color: var(--text-muted); font-style: italic;">Unknown ('.$stdVal.')</span>' 
                                : 'Standard ' . $stdVal;
                        }

                        // Status Badge
                        if (strtolower($invStatus) === 'paid') {
                            $statusBadge = '<span class="status-badge status-active"><i class="fa-solid fa-circle-check"></i> Paid</span>';
                            $payDetail = '<div style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.2;">' . $invMethod . '<br><span style="font-size: 0.75rem;">' . $invDate . '</span></div>';
                            $actionBtn = '';
                        } else {
                            $statusBadge = '<span class="status-badge status-pending" style="background: rgba(244,63,94,0.15); color: var(--accent);"><i class="fa-solid fa-clock"></i> Unpaid</span>';
                            $payDetail = '<span style="color: var(--text-muted); font-style: italic;">-</span>';
                            $actionBtn = '<button class="action-icon-btn pay-invoice-btn" data-id="' . $invNo . '" title="Mark as Paid" style="color: #10b981;"><i class="fa-solid fa-circle-check"></i></button>';
                        }

                        echo '<tr id="irow-' . $invNo . '">';
                        echo '<td style="font-weight: 700;">' . $invNo . '</td>';
                        echo '<td>' . $sName . '</td>';
                        echo '<td>' . $invTitle . '</td>';
                        echo '<td style="font-weight: 700; color: var(--text-main);">₹' . $invAmount . '</td>';
                        echo '<td>' . $invDueDate . '</td>';
                        echo '<td>' . $statusBadge . '</td>';
                        echo '<td>' . $payDetail . '</td>';
                        echo '<td>';
                        echo $actionBtn;
                        echo '<button class="action-icon-btn delete-invoice-btn" data-id="' . $invNo . '" title="Delete Invoice" style="color: var(--accent);"><i class="fa-solid fa-trash"></i></button>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">No invoices generated in the database directory yet.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'admin_footer.php';
?>
