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
$filename = 'BuyHistory-' . date('M-d-Y', strtotime($date_filter)) . '.csv';

// Set headers to prompt file download and specify UTF-8 encoding
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo "\xEF\xBB\xBF"; // Add BOM for UTF-8 compatibility in Excel

// Open the output stream
$file = fopen('php://output', 'w');

// Write the CSV column headers
fputcsv($file, array("Product", "Price", "Quantity", "Total", "Date"));

// Fetch the filtered transaction data
$sql = mysqli_query($conn, "SELECT * FROM buy WHERE STUD_ID = '$STUD_ID' AND buy_date='$date_filter'");

while ($row = mysqli_fetch_assoc($sql)) {
    // Get the relevant fields
    $product_name = $row['product_name'];
    $t_quan = $row['total_quantity'];
    $price = '₱' . number_format($row['product_price'], 2); // Format the amount
    $total = $row['total'];
    $date = $row['buy_date'];

    // Write the row to the CSV file
    fputcsv($file, array($product_name, $price, $t_quan, $total, $date));
}

// Close the file
fclose($file);
exit();
