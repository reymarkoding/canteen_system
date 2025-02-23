<?php
include "../conn.php";

$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');
$filename = 'DailySalesRecord-' . date('M-d-Y', strtotime($date_filter)) . '.csv';
$file = fopen($filename, "w");

$formatted_date = date('F d, Y', strtotime($date_filter));
// Write header for the first table (individual products)
fputcsv($file, array("Daily Sales Record: $formatted_date"));
fputcsv($file, array());
fputcsv($file, array("Product Name", "Quantity", "Price", "Total"));
$grandTotal1 = 0;

// Query for the first table (individual products)
$query1 = "SELECT 
                        product_name, 
                        total_quantity, 
                        product_price, 
                        total
                    FROM buy 
                    WHERE buy_date = '$date_filter' AND status = 'paid' ORDER BY product_name ASC";
$result1 = mysqli_query($conn, $query1);

// Write data for the first table
while ($row = mysqli_fetch_array($result1)) {
    $p_name = $row['product_name'];
    $quantity = $row['total_quantity'];
    $price = $row['product_price'];
    $total = $row['total'];

    // Add to grand total for the first table
    $grandTotal1 += $total;

    // Write the row to the CSV file
    fputcsv($file, array($p_name, $quantity, $price, $total));
}

// Write the grand total for the first table
fputcsv($file, array('', '', 'Grand Total', $grandTotal1));

// Write a blank line between tables
fputcsv($file, array());
fputcsv($file, array("PRODUCT SALES SUMMARY"));
fputcsv($file, array());

// Write header for the second table (summed products)
fputcsv($file, array("Product Name", "Quantity", "Price", "Total"));
$grandTotal2 = 0;

// Query for the second table (summed products)
$query2 = "SELECT 
                        product_name,
                        SUM(total_quantity) AS total_quantity,
                        product_price,
                        SUM(total) AS total
                    FROM buy WHERE buy_date = '$date_filter' AND status = 'paid'
                    GROUP BY product_name, product_price";
$result2 = mysqli_query($conn, $query2);

// Write data for the second table
while ($row = mysqli_fetch_array($result2)) {
    $p_name = $row['product_name'];
    $quantity = $row['total_quantity'];
    $price = $row['product_price'];
    $total = $row['total'];

    // Add to grand total for the second table
    $grandTotal2 += $total;

    // Write the row to the CSV file
    fputcsv($file, array($p_name, $quantity, $price, $total));
}

// Write the grand total for the second table
fputcsv($file, array('', '', 'Grand Total', $grandTotal2));

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
