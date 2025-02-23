<?php
include "../conn.php"; // Include your database connection file

// Query to check for pending requests
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM stud_info WHERE status = 'pending'");
$data = mysqli_fetch_assoc($result);

// Return the count of pending requests as JSON
echo json_encode(['pending' => $data['total'] > 0]);
