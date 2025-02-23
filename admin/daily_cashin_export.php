<?php
include "../conn.php";

$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');
$filename = 'DailyCashInRecord-' . date('M-d-Y', strtotime($date_filter)) . '.csv';
$file = fopen($filename, "w");

// Set UTF-8 BOM for proper character encoding
fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Write header for the table
fputcsv($file, array("Student Name", "Cash In", "Date", "Time"));
$grandTotal = 0;

// Query for the cash-in records
$query = "SELECT FIRSTNAME, MIDDLENAME, LASTNAME, amount, date, time, deposit.status, send_to
          FROM deposit 
          JOIN stud_info ON deposit.STUD_ID = stud_info.STUD_ID 
          WHERE date = '$date_filter' AND deposit.status = 'approved' AND send_to = 'Canteen'";
$result = mysqli_query($conn, $query);

// Write data for the table
while ($row = mysqli_fetch_array($result)) {
    $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0] . ".";
    $amount = "₱ " . number_format($row['amount'], 2);
    $date = new DateTime($row['date']);
    $formattedDate = $date->format("M j, Y");
    $grandTotal += $row['amount'];

    // Write the row to the CSV file
    fputcsv($file, array($fn, $amount, $formattedDate, $row['time']));
}

// Write the grand total to the CSV file
fputcsv($file, array('', '', 'Grand Total', "₱ " . number_format($grandTotal, 2)));

// Close the file
fclose($file);

// Set headers to prompt file download
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Type: application/csv;");

// Read the file and prompt download
readfile($filename);

// Delete the file after download
unlink($filename);
exit();
