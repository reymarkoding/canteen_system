<?php
include "../conn.php";
session_start();

if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}

$sql_today = "SELECT SUM(total) as total_today FROM buy WHERE buy_date = CURDATE()";
$result_today = mysqli_query($conn, $sql_today);
$row_today = mysqli_fetch_assoc($result_today);
$total_today = $row_today['total_today'] ? $row_today['total_today'] : 0;

// Fetch sales data for yesterday
$sql_yesterday = "SELECT SUM(total) as total_yesterday FROM buy WHERE buy_date = CURDATE() - INTERVAL 1 DAY";
$result_yesterday = mysqli_query($conn, $sql_yesterday);
$row_yesterday = mysqli_fetch_assoc($result_yesterday);
$total_yesterday = $row_yesterday['total_yesterday'] ? $row_yesterday['total_yesterday'] : 0;

$conn = new mysqli('localhost', 'root', '', 'canteenfinalsystem');

if (isset($_POST['export'])) {
    $tables = [
        'accounting2',
        'admin',
        'buy',
        'cart',
        'cash_input',
        'categories',
        'damage_product',
        'deposit',
        'products',
        'stud_info'
    ];

    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="canteenfinalsystem.sql"');

    $sql = '';

    foreach ($tables as $table) {
        // Check if table exists
        $checkTableQuery = "SHOW TABLES LIKE '$table'";
        $checkResult = $conn->query($checkTableQuery);
        if ($checkResult->num_rows === 0) {
            continue; // Skip non-existing table
        }

        // Generate CREATE TABLE statement
        $createQuery = "SHOW CREATE TABLE $table";
        $createResult = $conn->query($createQuery);
        if (!$createResult) {
            die("Error retrieving table structure for $table: " . $conn->error);
        }
        $createRow = $createResult->fetch_assoc();
        $sql .= "\n\n" . $createRow['Create Table'] . ";\n\n";

        // Generate INSERT INTO statements
        $selectQuery = "SELECT * FROM $table";
        $result = $conn->query($selectQuery);
        if (!$result) {
            die("Error retrieving data for $table: " . $conn->error);
        }

        while ($row = $result->fetch_assoc()) {
            $columns = array_keys($row);
            $values = array_map(function ($value) use ($conn) {
                return $value === null ? "NULL" : "'" . $conn->real_escape_string($value) . "'";
            }, array_values($row));

            $sql .= "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
        }

        $sql .= "\n\n";
    }

    echo $sql;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./admin_css/admin_dashboard.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .notification-dot {
            width: 10px;
            height: 10px;
            display: inline-block;
        }

        .notification-dot2 {
            width: 10px;
            height: 10px;
            display: inline-block;
        }

        .header-logo {
            background-image: url(../final_logo.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: #dcdcdc;
            /* Light Gray */
            z-index: -2;
        }

        /* .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(52, 121, 40, 0.3), rgba(50, 50, 50, 0.9)),
                url('../bg.png') no-repeat center center / cover;

            z-index: -2;
        } */
    </style>
</head>

<body>
    <div class="background">

    </div>
    <div class="main">
        <div class="side-bar">
            <div class="header-logo">
            </div>
            <div class="menu">
                <span>MAIN MENU</span>
                <div class="menu-item active">
                    <a href="admin_dashboard.php">Dashboard</a>
                </div>
                <div class="menu-item">
                    <a href="admin_category.php">Category</a>
                </div>
                <div class="menu-item">
                    <a href="admin_purchase.php">Purchase</a>
                </div>
                <div class="menu-item">
                    <a href="admin_product.php">Product</a>
                </div>
                <div class="menu-item">
                    <a href="admin_sales.php">Sales</a>
                </div>
                <div class="menu-item">
                    <a href="students.php">Students</a>
                </div>
                <!-- <div class="menu-item">
                    <a href="edit_admin.php">Account</a>
                </div> -->
                <!-- <div class="menu-item">
                    <a href="history.php">History</a>
                </div> -->
                <div class="menu-item">
                    <form action="admin_logout.php"><input type="submit" class="btn btn-danger" value="Logout" style="border: none;"></form>
                </div>
            </div>
        </div>
        <div class="main-bar p-2">
            <div class="total-students px-1 py-2">
                <div class="left">
                    <i class="fas fa-user-graduate"></i>
                    <?php
                    $find_stud = mysqli_query($conn, "SELECT * FROM stud_info");

                    if ($total_students = mysqli_num_rows($find_stud)) {
                        echo "<h4 class='text-dark'>" . $total_students . "</h4>";
                    } else {
                        echo "<h4>0</h4>";
                    }
                    ?>
                </div>
                <div class="right">
                    <h4 class="text-dark">STUDENTS</h4>
                    <a href="student_list.php" style="text-decoration: none;">View all</a>
                </div>
            </div>
            <div class="total-category px-1 py-2">
                <div class="left">
                    <i class="fas fa-stream"></i>
                    <?php
                    $find_cate = mysqli_query($conn, "SELECT * FROM categories");

                    if ($total_category = mysqli_num_rows($find_cate)) {
                        echo "<h4 class='text-light'>" . $total_category . "</h4>";
                    } else {
                        echo "<h4 class='text-light'>0</h4>";
                    }
                    ?>
                </div>
                <div class="right">
                    <h4 class="text-light">CATEGORY</h4>
                    <a href="admin_category.php" class="text-light" style="text-decoration: none;">View all</a>
                </div>
            </div>
            <div class="total-products px-1 py-2">
                <div class="left">
                    <i class="fas fa-box-open"></i>
                    <?php
                    $find_prod = mysqli_query($conn, "SELECT * FROM products");

                    if ($total_products = mysqli_num_rows($find_prod)) {
                        echo "<h4 class='text-light'>" . $total_products . "</h4>";
                    } else {
                        echo "<h4>0</h4>";
                    }
                    ?>
                </div>
                <div class="right">
                    <h4 class="text-light">PRODUCTS</h4>
                    <a href="products.php" style="text-decoration: none;">View all</a>
                </div>
            </div>
            <div class="daily-sales">
                <div class="left">
                    <i class="fas fa-dollar"></i>
                    <a href="daily_sales.php" style="text-decoration: none;">View all</a>
                </div>
                <div class="right">
                    <h4>DAILY SALES</h4>
                    <?php
                    // Query to get the sum of today's sales
                    $find_prod = mysqli_query($conn, "SELECT SUM(total) as total_sum FROM buy WHERE buy_date = CURDATE()");
                    $row = mysqli_fetch_array($find_prod);

                    // Check if there's a result and assign the value, otherwise default to 0
                    $daily_sales = $row['total_sum'] ? $row['total_sum'] : 0;  // Default to 0 if no sales

                    // Display the result
                    echo "<h4>₱" . number_format($daily_sales, 2) . "</h4>";
                    ?>
                </div>
            </div>
            <div class="daily-sales">
                <div class="left">
                    <i class="fas fa-dollar"></i>
                    <a href="daily_sales_cashin.php" style="text-decoration: none;">View all</a>
                </div>
                <div class="right">
                    <h4>Cashin Sales</h4>
                    <?php
                    // Query to get the sum of today's sales
                    $find_prod = mysqli_query($conn, "SELECT SUM(amount) as total_sum FROM deposit WHERE date = CURDATE() AND status='approved' AND send_to='Canteen'");
                    $row = mysqli_fetch_array($find_prod);

                    // Check if there's a result and assign the value, otherwise default to 0
                    $daily_sales = $row['total_sum'] ? $row['total_sum'] : 0;  // Default to 0 if no sales

                    // Display the result
                    echo "<h4>₱" . number_format($daily_sales, 2) . "</h4>";
                    ?>
                </div>
            </div>
            <div class="daily-sales">
                <div class="left">
                    <i class="fas fa-history"></i>
                    <a href="buy_history.php" style="text-decoration: none;">View all</a>
                </div>
                <div class="right">
                    <h4>Buy History</h4>
                    <!-- <?php
                            // Query to get the sum of today's sales
                            $find_history = mysqli_query($conn, "SELECT * FROM buy WHERE status = 'paid'");

                            if ($buy_history = mysqli_num_rows($find_history)) {
                                echo "<h4 class='text-dark'>" . $buy_history . "</h4>";
                            } else {
                                echo "<h4>0</h4>";
                            }
                            ?> -->
                </div>
            </div>

            <!-- <div class="daily-sales">
                <div class="left">
                    <i class="fas fa-dollar"></i>

                    <a href="">View all</a>
                </div>
                <div class="right">
                    <center>
                        <h5>MONTHLY SALES</h5>
                    </center>
                    <?php
                    $find_prod = mysqli_query($conn, "SELECT *, (SELECT SUM(total) FROM buy WHERE MONTH(buy_date) = MONTH(CURDATE()) AND YEAR(buy_date) = YEAR(CURDATE())) as total_sum 
                                FROM buy");
                    $row = mysqli_fetch_array($find_prod);
                    $daily_sales = $row['total_sum'];

                    if ($row) {
                        // echo "<h1>" . $daily_sales . "</h1>";
                        echo "<h4>₱" . number_format($daily_sales, 2) . "</h4>";
                    } else {
                        echo "<h1>₱0.00</h1>";
                    }
                    ?>
                </div>
            </div>
            <div class="daily-sales">
                <div class="left">
                    <i class="fas fa-dollar"></i>

                    <a href="">View all</a>
                </div>
                <div class="right">
                    <center>
                        <h5>ANNUAL SALES</h5>
                    </center>
                    <?php
                    $find_prod = mysqli_query($conn, "SELECT *, (SELECT SUM(total) FROM buy WHERE buy_date = CURDATE()) as total_sum 
                                FROM buy");
                    $row = mysqli_fetch_array($find_prod);
                    $daily_sales = $row['total_sum'];

                    if ($row) {
                        // echo "<h1>" . $daily_sales . "</h1>";
                        echo "<h4>₱" . number_format($daily_sales, 2) . "</h4>";
                    } else {
                        echo "<h1>₱0.00</h1>";
                    }
                    ?>
                </div>
            </div> -->
            <div class="graph">
                <div class="header-notif">
                    <form method="post" action="">
                        <button type="submit" name="export" class="btn btn-info">Back-up Database</button>
                    </form>
                    <button class="btn btn-secondary position-relative">
                        <a href="registration_approval.php" class="text-light text-decoration-none">Registration Approval</a>
                        <span id="regNotifDot" class="notification-dot2 position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle d-none"></span>
                    </button>
                    <!-- <button class="btn btn-warning"><a href="student_cash_in.php" class="text-dark text-decoration-none">Cash In Approval</a></button> -->
                    <button class="btn btn-warning position-relative">
                        <a href="student_cash_in.php" class="text-dark text-decoration-none">Cash In Approval</a>
                        <span id="cashInNotifDot" class="notification-dot position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle d-none"></span>
                    </button>
                </div>
                <div class="graph-sales">
                    <canvas id="salesComparisonGraph" width="1000px" height="auto"></canvas>
                </div>
            </div>
        </div>
    </div>


    <script src="js/chart.min.js"></script>
    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        // Get the total sales data from PHP variables
        var totalToday = <?php echo $total_today; ?>;
        var totalYesterday = <?php echo $total_yesterday; ?>;

        // Line chart for comparing today's and yesterday's sales
        var ctx = document.getElementById('salesComparisonGraph').getContext('2d');
        var salesComparisonChart = new Chart(ctx, {
            type: 'line', // Line chart
            data: {
                labels: ['Yesterday', 'Today'], // Labels for each day
                datasets: [{
                    label: 'Sales Comparison (₱)',
                    data: [totalYesterday, totalToday], // Sales data for yesterday and today
                    borderColor: 'rgba(75, 192, 192, 1)', // Line color for the chart
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Fill color for the line
                    tension: 0.1, // Line smoothness
                    fill: true // Fill under the line
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true, // Start the y-axis from 0
                        title: {
                            display: true,
                            text: 'Sales (₱)'
                        }
                    }
                }
            }
        });

        // red dots
        $(document).ready(function() {
            function checkCashInRequests() {
                $.ajax({
                    url: 'admin_notif.php', // PHP script to check for pending requests
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.pending) {
                            // Show the notification dot if there are pending requests
                            $('#cashInNotifDot').removeClass('d-none');
                        } else {
                            // Hide the notification dot if no pending requests
                            $('#cashInNotifDot').addClass('d-none');
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch cash-in request status.');
                    }
                });
            }

            // Run the check every 5 seconds (5000 ms)
            setInterval(checkCashInRequests, 5000);

            // Initial check on page load
            checkCashInRequests();
        });

        // red dot register
        $(document).ready(function() {
            function checkRegRequests() {
                $.ajax({
                    url: 'admin_reg_notif.php', // PHP script to check for pending requests
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.pending) {
                            // Show the notification dot if there are pending requests
                            $('#regNotifDot').removeClass('d-none');
                        } else {
                            // Hide the notification dot if no pending requests
                            $('#regNotifDot').addClass('d-none');
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch cash-in request status.');
                    }
                });
            }

            // Run the check every 5 seconds (5000 ms)
            setInterval(checkRegRequests, 5000);

            // Initial check on page load
            checkRegRequests();
        });
    </script>
</body>

</html>