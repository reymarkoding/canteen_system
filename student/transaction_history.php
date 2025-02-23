<?php

include "../conn.php";
session_start();

$STUD_ID = $_SESSION['stud_id'];
$ID_NUMBER = $_SESSION['ID_NUMBER'];
$fullName = $_SESSION['fullName'];

if (!isset($_SESSION['student_status']) || $_SESSION['student_status'] !== 'login') {
    header("Location: student_login.php");
    exit();
}

if (isset($_GET['delete_history'])) {
    $delete_history = $_GET['delete_history'];
    mysqli_query($conn, "DELETE FROM deposit WHERE deposit_id = $delete_history");
    echo "<script>alert('You deleted one of the history successfully')</script>;
            <script>window.location.href='transaction_history.php'</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction</title>
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/transaction.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="./css/css/js/bootstrap.bundle.js"></script>
    <style>
        .pending {
            background-color: red;
        }

        .approved {
            background-color: green;
        }

        button.btnDLt {
            background-color: transparent;
            grid-row: 3/4;
            border: none;
        }

        button.btnDLt:hover {
            background-color: red;
        }
    </style>

</head>

<body>
    <div class="main_header">
        <div class="title">
            <center>
                <h4>Transaction</h4>
            </center>
        </div>
    </div>
    <div class="main bg-light">
        <div>
            <!-- Date Filter Form -->
            <form method="GET" style="display: flex; align-items: center;">
                <label for="date_filter" class="me-2">Date:</label>
                <input type="date" id="date_filter" name="date_filter" value="<?= isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d'); ?>" class="form-control me-2" style="width: 200px;">
                <button type="submit" class="btn btn-primary ml-1">Filter</button>
            </form>

            <!-- Export to Excel Button -->
            <button class="btn btn-secondary mt-2" type="button" onclick="Export()">
                <i class="fas fa-file-excel me-2"></i> Excel
            </button>
        </div>

        <?php
        // Get the filtered date or default to today
        $date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');

        // Fetch filtered transactions
        $sql = mysqli_query($conn, "SELECT * FROM deposit WHERE STUD_ID = '$STUD_ID' AND status='approved' AND date = '$date_filter' ORDER BY deposit_id DESC LIMIT 10");

        if (mysqli_num_rows($sql) > 0) {
            while ($row = mysqli_fetch_assoc($sql)) {
        ?>
                <div class="history-box">
                    <input type="hidden" name="STUD_ID" value="<?= $row['STUD_ID'] ?>">
                    <h6 style="font-weight: bold; text-shadow: 0px 0px 1px black;">Cash In</h6>
                    <h6 class="send_to">From: <?= $row['send_to'] ?></h6>
                    <h6 class="amount"><?php echo "₱" . number_format($row['amount'], 2); ?></h6>
                    <p class="status"><?= $row['status'] ?></p>
                    <p class="date"><?= $row['date'] . " / " . $row['time'] ?></p>
                </div>
            <?php
            }
        } else {
            ?>
            <p>No transactions found for the selected date.</p>
        <?php } ?>
    </div>


    <div class="footer">
        <div class="top"></div>
        <div class="bottom">
            <div class="menu-icon"><a href="student_dashboard.php"><i class="fas fa-home"></i><span>Home</span></a></div>
            <div class="left">
                <a href="transaction_history.php"><i class="fas fa-history"></i><span>Transaction</span></a>
            </div>
            <div class="circle"><a href="student_profile.php"><i class="fas fa-qrcode"></i><span>QR</span></a></div>
            <div class="menu-icon"><a href="student_cashin.php"><i class="fas fa-money-bill"></i><span>Cash In</span></a></div>
            <?php
            $find_pass = "SELECT PASSWORD FROM stud_info WHERE STUD_ID = '$STUD_ID'";
            $finded = mysqli_query($conn, $find_pass);
            while ($row = mysqli_fetch_assoc($finded)) {
                $pass = $row['PASSWORD'];
            }
            ?>
            <div class="right">
                <form action="student_edit_profile.php" method="post">
                    <button type="submit">
                        <i class="fas fa-user-cog">
                        </i>
                        <span>Profile</span>
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script>
        function Export() {
            var conf = confirm("Please confirm if you wish to proceed exporting your transaction history?");
            if (conf) {
                window.open("excel_export.php?date_filter=<?= $date_filter ?>", '_blank');
            }
        }

        function confirmDelete(del_ID) {
            console.log("Delete ID: " + del_ID);
            var result = confirm('Are you sure you want to delete this history?');

            if (result) {
                window.location.href = 'transaction_history.php?delete_history=' + del_ID;
            } else {
                window.location.href = 'transaction_history.php';
            }
        }
    </script>
</body>

</html>