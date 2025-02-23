<?php
include "../conn.php";

$filename = 'ProductInventory.csv';
$file = fopen($filename, "w");

// Set the encoding to UTF-8
fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8

// Add the current date as a label
$current_date = date("F d, Y"); // Format: January 06, 2025
fputcsv($file, array("Export Date: $current_date"));

// Write header for the CSV
fputcsv($file, array("Product Name", "Category", "Qty Limit", "Stock In", "Stock Out", "Stocks", "Price"));

// Query to fetch product data
$query1 = "SELECT category, product_name, stock_limit, product_quantity, remaining_quantity, product_price 
           FROM products 
           JOIN categories ON products.category_id = categories.category_id 
           ORDER BY product_name ASC";
$result1 = mysqli_query($conn, $query1);

// Write data rows
while ($row = mysqli_fetch_assoc($result1)) {
    $p_name = $row['product_name'];
    $p_category = $row['category'];
    $p_limit = $row['stock_limit'];
    $p_stock_in = $row['product_quantity'];
    $p_stock = $row['remaining_quantity'];
    $stock_out = $row['product_quantity'] - $row['remaining_quantity'];
    $p_price = "₱ " . number_format($row['product_price'], 2);

    // Write the row to the CSV file
    fputcsv($file, array($p_name, $p_category, $p_limit, $p_stock_in, $stock_out, $p_stock, $p_price));
}

// Close the file
fclose($file);

// Set headers for the file download
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$filename");
header("Content-Type: application/csv;");

// Read the file and trigger download
readfile($filename);

// Delete the file from the server after download
unlink($filename);
exit();
