<?php
include "../conn.php";
session_start();
if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}
$sql = "SELECT * FROM admin";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $admin_id = $row['admin_id'];
    $name = $row['name'];
}

// Check if a date filter has been set
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');  // Default to today's date
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .main {
            height: 100vh;
            display: grid;
            grid-template-rows: 50px 1fr;
        }

        * {
            padding: 0;
            margin: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .navbar .container_fluid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .search {
            grid-column: 2/3;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
    </style>
</head>

<body>
    <div class="main p-5">
        <nav class="navbar navbar-light bg-light">
            <form class="container-fluid" method="GET">
                <div class="buttons">
                    <button class="btn btn-primary me-2" type="button"><a href="admin_dashboard.php" style="text-decoration: none; color: white;">Back</a></button>
                    <button class="btn btn-secondary me-2" type="button" onclick="Export()">EXCEL</button>
                </div>
                <div class="search">
                    <label for="">
                        <h5>Date</h5>
                    </label>
                    <input type="date" name="date_filter" value="<?= $date_filter ?>" style="width: 100%; margin: 0px 10px;" class="form-control" id="date_filter">
                    <input type="submit" class="btn btn-info" value="Filter">
                </div>
            </form>
        </nav>

        <div class="tbl_cashin">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Cash IN</th>
                        <th>Date</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Use the date filter to display records for the selected date
                    $sql = mysqli_query($conn, "SELECT FIRSTNAME, MIDDLENAME, LASTNAME, amount, date, time, deposit.status, send_to
                    FROM deposit JOIN stud_info ON deposit.STUD_ID = stud_info.STUD_ID 
                    WHERE date = '$date_filter' AND deposit.status = 'approved' AND send_to = 'Canteen'");

                    // Initialize grand total
                    $grandTotal = 0;

                    // Loop through the results and display each product
                    while ($row = mysqli_fetch_assoc($sql)) {
                        $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0] . ".";
                        $date = new DateTime($row['date']);
                        $formattedDate = $date->format("M j, Y");
                        $grandTotal += $row['amount']; // Add each row's total to the grand total
                    ?>
                        <tr>
                            <td><?= $fn; ?></td>
                            <td><?= "₱ " . number_format($row['amount'], 2); ?></td>
                            <td><?= $formattedDate; ?></td>
                            <td><?= $row['time']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                        <td><strong><?= "₱ " . number_format($grandTotal, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        function Export() {
            var conf = confirm("Please confirm if you wish to proceed exporting the daily cashin record?");
            if (conf) {
                window.open("daily_cashin_export.php?date_filter=<?= $date_filter ?>", '_blank');
            }
        }
    </script>
</body>

</html>