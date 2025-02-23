<?php
include "../conn.php";
session_start();

$sql = "SELECT * FROM admin";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $admin_id = $row['admin_id'];
    $name = $row['name'];
}

if (isset($_GET['delete_payment'])) {
    $buy_id_e_payment = $_GET['delete_payment'];
    mysqli_query($conn, "DELETE FROM buy WHERE buy_id = $buy_id_e_payment");
    echo "<script>alert('You deleted one of the history successfully')</script>;
            <script>window.location.href='history.php'</script>";
}

if (isset($_GET['delete_direct_payment'])) {
    $delete_direct_payment = $_GET['delete_direct_payment'];
    mysqli_query($conn, "DELETE FROM buy WHERE buy_id = $delete_direct_payment");
    echo "<script>alert('You deleted one of the history successfully')</script>;
            <script>window.location.href='history.php'</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./admin_css/admin_sales.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: url("../bg.png");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            filter: blur(2px);
            z-index: -2;
        }

        .header-logo {
            background-image: url(../final_logo.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }
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
                <div class="menu-item">
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
                    <a href="">Users</a>
                </div>
                <div class="menu-item active">
                    <a href="history.php">History</a>
                </div>
                <div class="menu-item">
                    <a href="">Logout</a>
                </div>
            </div>
        </div>

        <div class="main-bar p-2">
            <div class="search">
                <h1 style="background-color: green; color: white; text-align: center;">HISTORY</h1>
            </div>
            <div class="tbl_stud p-2">
                <div class="header p-2">
                    <h3 class="text-light">E-Payment</h3>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $query_epayment = "SELECT ID_NUMBER, product_name,buy_id, product_price, total_quantity, total
                    FROM stud_info 
                    JOIN buy ON stud_info.STUD_ID = buy.STUD_ID 
                    WHERE buy.status = 'approve'
                ";

                        $result_epayment = mysqli_query($conn, $query_epayment);

                        while ($row = mysqli_fetch_assoc($result_epayment)) {
                        ?>
                            <tr>
                                <td><?= $row['ID_NUMBER']; ?></td>
                                <td><?= $row['product_name']; ?></td>
                                <td><?= $row['product_price']; ?></td>
                                <td><?= $row['total_quantity']; ?></td>
                                <td><?= $row['total']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $row['buy_id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Sales of Cash Input Table -->
            <div class="tbl_cashInput p-2">
                <div class="header p-2">
                    <h3 class="text-light">Cash-Input</h3>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch filtered data for Cash Input
                        $query_cash = "
                    SELECT buy_id, product_name, product_price, total_quantity, total
                    FROM buy 
                    WHERE status = 'paid'
                ";

                        $result_cash = mysqli_query($conn, $query_cash);

                        while ($row2 = mysqli_fetch_assoc($result_cash)) {
                        ?>
                            <tr>
                                <td><?= $row2['buy_id']; ?></td>
                                <td><?= $row2['product_name']; ?></td>
                                <td><?= $row2['product_price']; ?></td>
                                <td><?= $row2['total_quantity']; ?></td>
                                <td><?= $row2['total']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete_Direct(<?= $row2['buy_id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        function confirmDelete(del_ID) {
            console.log("Delete ID: " + del_ID);
            var result = confirm('Are you sure you want to delete this history?');

            if (result) {
                window.location.href = 'history.php?delete_payment=' + del_ID;
            } else {
                window.location.href = 'history.php';
            }
        }

        function confirmDelete_Direct(del_ID_D) {
            console.log("Delete ID: " + del_ID_D);
            var result = confirm('Are you sure you want to delete this history?');

            if (result) {
                window.location.href = 'history.php?delete_direct_payment=' + del_ID_D;
            } else {
                window.location.href = 'history.php';
            }
        }
    </script>
</body>

</html>