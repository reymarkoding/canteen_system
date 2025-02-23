<?php
include "../conn.php";
session_start();

if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}
// $sql = "SELECT * FROM admin";
// $result = mysqli_query($conn, $sql);

// if ($result) {
//     $row = mysqli_fetch_assoc($result);
//     $admin_id = $row['admin_id'];
//     $name = $row['name'];
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
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
            background: #dcdcdc;
            /* Light Gray */
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
                <div class="menu-item active">
                    <a href="admin_sales.php">Sales</a>
                </div>
            </div>
        </div>

        <div class="main-bar p-2">
            <div class="search">
                <form method="GET" action="">
                    <div class="from">
                        <label for="from_date">
                            <h5 style="text-shadow: 0px 0px 1px black;">From:</h5>
                        </label>
                        <input type="date" name="from_date" class="form-control" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
                    </div>
                    <div class="to">
                        <label for="to_date">
                            <h5 style="text-shadow: 0px 0px 1px black;">To:</h5>
                        </label>
                        <input type="date" name="to_date" class="form-control" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                    </div>
                    <div class="filter">
                        <input type="submit" value="Filter" class="btn btn-primary me-2">
                    </div>
                </form>
            </div>
            <div class="tbl_stud p-2">
                <div class="header p-2">
                    <h3 class="text-light">Sales of E-Payment</h3>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch filtered data for E-Payment
                        $from_date = $_GET['from_date'] ?? null;
                        $to_date = $_GET['to_date'] ?? null;

                        $query_epayment = "
                    SELECT ID_NUMBER, product_name, product_price, total_quantity, total
                    FROM stud_info 
                    JOIN buy ON stud_info.STUD_ID = buy.STUD_ID 
                    WHERE buy.status = 'approve'
                ";

                        // Add date range filter
                        if ($from_date && $to_date) {
                            $query_epayment .= " AND buy.buy_date BETWEEN '$from_date' AND '$to_date'";
                        } else {
                            $query_epayment .= " AND buy.buy_date = CURDATE()";
                        }

                        $result_epayment = mysqli_query($conn, $query_epayment);

                        while ($row = mysqli_fetch_assoc($result_epayment)) {
                        ?>
                            <tr>
                                <td><?= $row['ID_NUMBER']; ?></td>
                                <td><?= $row['product_name']; ?></td>
                                <td><?= $row['product_price']; ?></td>
                                <td><?= $row['total_quantity']; ?></td>
                                <td><?= $row['total']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php
                            $total_result_epayment = mysqli_query($conn, "
                        SELECT SUM(total) AS grand_total 
                        FROM buy 
                        WHERE status = 'approve'
                    " . ($from_date && $to_date ? " AND buy_date BETWEEN '$from_date' AND '$to_date'" : " AND buy_date = CURDATE()"));

                            $total_row_epayment = mysqli_fetch_assoc($total_result_epayment);
                            $grand_total_epayment = $total_row_epayment['grand_total'];
                            ?>
                            <td colspan="4">
                                <h3>Total:</h3>
                            </td>
                            <td>
                                <h3>₱<?= number_format($grand_total_epayment, 2); ?></h3>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Sales of Cash Input Table -->
            <div class="tbl_cashInput p-2">
                <div class="header p-2">
                    <h3 class="text-light">Sales of Cash-Input</h3>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
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

                        // Add date range filter
                        if ($from_date && $to_date) {
                            $query_cash .= " AND buy_date BETWEEN '$from_date' AND '$to_date'";
                        } else {
                            $query_cash .= " AND buy_date = CURDATE()";
                        }

                        $result_cash = mysqli_query($conn, $query_cash);

                        while ($row2 = mysqli_fetch_assoc($result_cash)) {
                        ?>
                            <tr>
                                <td><?= $row2['buy_id']; ?></td>
                                <td><?= $row2['product_name']; ?></td>
                                <td><?= $row2['product_price']; ?></td>
                                <td><?= $row2['total_quantity']; ?></td>
                                <td><?= $row2['total']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <?php
                            $total_result_cash = mysqli_query($conn, "
                        SELECT SUM(total) AS grand_total 
                        FROM buy 
                        WHERE status = 'paid'
                    " . ($from_date && $to_date ? " AND buy_date BETWEEN '$from_date' AND '$to_date'" : " AND buy_date = CURDATE()"));

                            $total_row_cash = mysqli_fetch_assoc($total_result_cash);
                            $grand_total_cash = $total_row_cash['grand_total'];
                            ?>
                            <td colspan="4">
                                <h3>Total:</h3>
                            </td>
                            <td>
                                <h3>₱<?= number_format($grand_total_cash, 2); ?></h3>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>



    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
</body>

</html>