<?php
include "../conn.php";
session_start();

// Get the date filter passed from the URL
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');
$STUD_ID = $_SESSION['stud_id']; // Get the STUD_ID from session

// Validate that the user is logged in
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    header("Location: student_login.php");
    exit();
}

// Set the filename for the export
$filename = 'TransactionHistory-' . date('M-d-Y', strtotime($date_filter)) . '.csv';

// Set headers to prompt file download and specify UTF-8 encoding
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo "\xEF\xBB\xBF"; // Add BOM for UTF-8 compatibility in Excel

// Open the output stream
$file = fopen('php://output', 'w');

// Write the CSV column headers
fputcsv($file, array("From", "Amount", "Date/Time", "Status"));

// Fetch the filtered transaction data
$sql = mysqli_query($conn, "SELECT * FROM deposit WHERE STUD_ID = '$STUD_ID' AND status = 'approved' AND date = '$date_filter'");

while ($row = mysqli_fetch_assoc($sql)) {
    // Get the relevant fields
    $send_to = $row['send_to'];
    $amount = '₱' . number_format($row['amount'], 2); // Format the amount
    $d_t = $row['date'] . " / " . $row['time'];
    $status = $row['status'];

    // Write the row to the CSV file
    fputcsv($file, array($send_to, $amount, $d_t, $status));
}

// Close the file
fclose($file);
exit();
