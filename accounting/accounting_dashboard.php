<?php
include "../conn.php";
session_start();

if (!isset($_SESSION['accounting_status']) || $_SESSION['accounting_status'] !== 'login') {
    header("Location: accounting_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACCOUNTING</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./acc_css/acc_dashboard.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
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
    </style>
</head>

<body>
    <div class="background"></div>
    <div class="main">
        <div class="side-bar">
            <div class="header-logo">

            </div>
            <div class="menu">
                <span>MAIN MENU</span>
                <div class="menu-item">
                    <a href="accounting_dashboard.php">Dashboard</a>
                </div>
                <div class="menu-item">
                    <a href="students.php">Students</a>
                </div>
                <div class="menu-item">
                    <a href="acc_request_deposit.php">Deposit</a>
                </div>
                <div class="menu-item">
                    <a href="history.php">History</a>
                </div>
                <div class="menu-item">
                    <form action="accounting_logout.php"><input type="submit" class="btn btn-danger" value="Logout" style="border: none;"></form>
                </div>
            </div>
        </div>
        <div class="main-bar">
            <div class="top">
                <div class="left">
                    <div class="total-students bg-light px-1 py-2">
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
                            <a href="students.php" style="text-decoration: none;">View all</a>
                        </div>
                    </div>
                    <div class="daily-sales bg-light">
                        <div class="left">
                            <i class="fas fa-dollar"></i>
                            <a href="daily_sales_cashin.php" style="text-decoration: none;">View</a>
                        </div>
                        <div class="right">
                            <h5>CASHIN SALES</h5>
                            <?php
                            // Query to get the sum of today's sales
                            $find_prod = mysqli_query($conn, "SELECT SUM(amount) as total_sum FROM deposit WHERE date = CURDATE() AND status='approved' AND send_to='Accounting'");
                            $row = mysqli_fetch_array($find_prod);

                            // Check if there's a result and assign the value, otherwise default to 0
                            $daily_sales = $row['total_sum'] ? $row['total_sum'] : 0;  // Default to 0 if no sales

                            // Display the result
                            echo "<h4>₱" . number_format($daily_sales, 2) . "</h4>";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="right">
                    <button class="btn btn-warning position-relative">
                        <a href="acc_request_deposit.php" class="text-dark text-decoration-none">Cash In Approval</a>
                        <span id="cashInNotifDot" class="notification-dot position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle d-none"></span>
                    </button>
                </div>
            </div>
            <div class="bottom"></div>
        </div>
    </div>

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        // red dots
        $(document).ready(function() {
            function checkCashInRequests() {
                $.ajax({
                    url: 'acc_notif.php',
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
            checkCashInRequests();
        });
    </script>
</body>

</html>