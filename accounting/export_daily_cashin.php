<?php
include "../conn.php";

$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');
$filename = 'DailySalesRecord-' . date('M-d-Y', strtotime($date_filter)) . '.csv';
$file = fopen($filename, "w");

// Write header for the first table (individual products)
fputcsv($file, array("Student Name", "Cash-In", "Date", "Time"));
$grandTotal1 = 0;

// Query for the first table (individual products)
$query1 = "SELECT FIRSTNAME, MIDDLENAME, LASTNAME, amount, date, time, deposit_id, deposit.status, send_to
                    FROM deposit JOIN stud_info ON deposit.STUD_ID = stud_info.STUD_ID 
                    WHERE date = '$date_filter' AND deposit.status = 'approved' AND send_to = 'Accounting' ORDER BY time DESC";
$result1 = mysqli_query($conn, $query1);

// Write data for the first table
while ($row = mysqli_fetch_array($result1)) {
    $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0];
    $cashin_amount = $row['amount'];
    $record_date = date('F d, Y', strtotime($row['date']));
    $record_time = $row['time'];

    // Add to grand total for the first table
    $grandTotal1 += $cashin_amount;

    // Write the row to the CSV file
    fputcsv($file, array($fn, $cashin_amount, $record_date, $record_time));
}

// Write the grand total for the first table
fputcsv($file, array('', '', 'Grand Total', $grandTotal1));

// Write a blank line between tables
fputcsv($file, array());

// Set headers to prompt file download
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Type: application/csv;");

// Read the file and prompt download
readfile($filename);

// Delete the file after download
unlink($filename);
exit();
