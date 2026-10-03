<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

// Prevent caching of download response
header("Pragma: no-cache");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);

// Set download headers
$filename = "sms_db_backup_" . date("Y-m-d_H-i-s") . ".sql";
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Transfer-Encoding: binary");

$output = "-- School Management System Database Backup\n";
$output .= "-- Generated: " . date("Y-m-d H:i:s") . "\n";
$output .= "-- Database: `sms_db`\n";
$output .= "-- ------------------------------------------------------\n\n";
$output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Get list of tables
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
}

foreach ($tables as $table) {
    $output .= "-- ------------------------------------------------------\n";
    $output .= "-- Table structure and drop policy for table `$table`\n";
    $output .= "-- ------------------------------------------------------\n\n";
    
    $output .= "DROP TABLE IF EXISTS `$table`;\n";
    
    // Retrieve structure details
    $createResult = $conn->query("SHOW CREATE TABLE `$table`");
    if ($createResult) {
        $createRow = $createResult->fetch_row();
        $output .= $createRow[1] . ";\n\n";
    }
    
    // Retrieve row datasets
    $dataResult = $conn->query("SELECT * FROM `$table`");
    if ($dataResult && $dataResult->num_rows > 0) {
        $output .= "-- Dump data for table `$table`\n";
        while ($row = $dataResult->fetch_assoc()) {
            $insertValues = [];
            foreach ($row as $key => $value) {
                if (is_null($value)) {
                    $insertValues[] = "NULL";
                } else {
                    $insertValues[] = "'" . $conn->real_escape_string($value) . "'";
                }
            }
            $output .= "INSERT INTO `$table` VALUES (" . implode(", ", $insertValues) . ");\n";
        }
    }
    $output .= "\n\n";
}

$output .= "SET FOREIGN_KEY_CHECKS=1;\n";

echo $output;
exit;
?>
