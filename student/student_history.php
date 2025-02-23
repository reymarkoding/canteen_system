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
    mysqli_query($conn, "DELETE FROM buy WHERE buy_id = $delete_history");
    echo "<script>alert('You deleted one of the history successfully')</script>;
            <script>window.location.href='student_history.php'</script>";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/student_history.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="./css/css/js/bootstrap.bundle.js"></script>
    <style>
        li {
            font-size: 11px;
        }

        .top {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
        }

        .main {
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="main_header">
        <div class="title">
            <center>
                <h4>History</h4>
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
            <button class="btn btn-secondary my-2" type="button" onclick="Export()">
                <i class="fas fa-file-excel me-2"></i> Excel
            </button>
        </div>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    $date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');
                    $sql = mysqli_query($conn, "SELECT * FROM buy WHERE STUD_ID = '$STUD_ID' AND buy_date='$date_filter' LIMIT 10");
                    while ($row = mysqli_fetch_assoc($sql)) {


                    ?>
                        <td><?= $row['product_name'] ?></td>
                        <td><?= "₱" . number_format($row['product_price'], 2); ?></td>
                        <td><?= $row['total_quantity'] ?></td>
                        <td><?= "₱" . number_format($row['total'], 2); ?></td>
                        <td><?= $row['buy_date'] ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

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

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script>
        function Export() {
            var conf = confirm("Please confirm if you wish to proceed exporting your buy history?");
            if (conf) {
                window.open("excel_export_history.php?date_filter=<?= $date_filter ?>", '_blank');
            }
        }

        function confirmDelete(del_ID) {
            console.log("Delete ID: " + del_ID);
            var result = confirm('Are you sure you want to delete this history?');

            if (result) {
                window.location.href = 'student_history.php?delete_history=' + del_ID;
            } else {
                window.location.href = 'student_history.php';
            }
        }
    </script>
</body>

</html>