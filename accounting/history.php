<?php
include "../conn.php";
session_start();
if (!isset($_SESSION['accounting_status']) || $_SESSION['accounting_status'] !== 'login') {
    header("Location: accounting_login.php");
    exit();
}

$sql = "SELECT * FROM admin";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $admin_id = $row['admin_id'];
    $name = $row['name'];
}

if (isset($_GET['delete'])) {
    $deposit_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM deposit WHERE deposit_id = $deposit_id");
    echo "<script>alert('You deleted one of the history successfully')</script>;
            <script>window.location.href='history.php'</script>";
}
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
                    <button class="btn btn-primary me-2" type="button"><a href="accounting_dashboard.php" style="text-decoration: none; color: white;">Back</a></button>
                </div>
                <h3>Cash In History</h3>
            </form>
        </nav>
        <div class="tbl_history">
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
                    $sql = mysqli_query($conn, "SELECT FIRSTNAME, MIDDLENAME, LASTNAME, amount, date, time, deposit_id, deposit.status, send_to
                        FROM deposit JOIN stud_info ON deposit.STUD_ID = stud_info.STUD_ID 
                        WHERE deposit.status = 'approved' AND send_to = 'Accounting' ORDER BY deposit_id DESC");
                    // Without Limit
                    // Initialize grand total
                    $grandTotal = 0;

                    // Loop through the results and display each product
                    while ($row = mysqli_fetch_assoc($sql)) {
                        $dep_id = $row['deposit_id'];
                        $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0];
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
            </table>
        </div>


    </div>

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        function confirmDelete(delID) {
            console.log("Delete ID: " + delID);
            var result = confirm('Are you sure you want to delete this history?');

            if (result) {
                window.location.href = 'history.php?delete=' + delID;
            } else {
                window.location.href = 'history.php';
            }
        }
    </script>
</body>

</html>