<?php
include "../conn.php";

// Query to check for pending requests
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM deposit WHERE status = 'pending' AND send_to = 'Accounting'");
$data = mysqli_fetch_assoc($result);

// Return the count of pending requests as JSON
echo json_encode(['pending' => $data['total'] > 0]);
