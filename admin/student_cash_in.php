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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Cash In</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .main {
            height: 100vh;
            display: grid;
            grid-template-rows: 50px 1fr;
        }

        .hero {

            box-shadow: 0px 0px 3px rgb(0, 0, 0, 0.6);
        }

        * {
            padding: 0;
            margin: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
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
    <?php
    if (isset($_SESSION['delete_deposit'])) {
        echo "<div class='alert alert-warning' id='delete_deposit'>" . $_SESSION['delete_deposit'] . "</div>";
        unset($_SESSION['delete_deposit']);
    }
    ?>
    <div class="main p-5">
        <div class="header">
            <h1 style="text-shadow: 0px 0px 3px black;">DEPOSIT</h1>
        </div>
        <div class="hero p-2">

            <a href="admin_dashboard.php" class="btn btn-danger mb-2">Back</a>
            <form action="admin_approve_deposit.php" method="post">
                <table class="table table-striped">
                    <thead>
                        <th class="bg-warning">ID</th>
                        <th class="bg-warning">Name</th>
                        <th class="bg-warning">Deposit</th>
                        <th class="bg-warning">Date</th>
                        <th class="bg-warning">Time</th>
                        <th class="bg-warning">Action</th>
                    </thead>
                    <tbody>
                        <?php
                        $find = mysqli_query($conn, "SELECT deposit.amount, deposit.deposit_id, deposit.STUD_ID, deposit.status, deposit.date, deposit.time, stud_info.ID_NUMBER,
                       stud_info.FIRSTNAME, stud_info.LASTNAME, stud_info.MIDDLENAME
                            FROM deposit 
                            JOIN stud_info ON deposit.STUD_ID = stud_info.STUD_ID 
                            WHERE deposit.status='pending' AND deposit.send_to = 'Canteen' 
                            ORDER BY deposit.time DESC");

                        while ($row = mysqli_fetch_assoc($find)) {
                            $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0] . ".";
                            $date = new DateTime($row['date']);
                            $formattedDate = $date->format("M j, Y");


                        ?>
                            <tr>
                                <td><?= $row['ID_NUMBER']; ?></td>
                                <td><?= $fn; ?></td>
                                <td><?= $row['amount']; ?></td>
                                <td><?= $formattedDate; ?></td>
                                <td><?= $row['time']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary"
                                        onclick="confirmApprove(event, '<?= $fn; ?>', <?= $row['deposit_id']; ?>)">
                                        Approve
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <a onclick="confirmDelete(event)" href="admin_approve_deposit.php?reject_id=<?= $row['deposit_id']; ?>" style="color: white; text-decoration: none;">Reject</a>
                                    </button>
                                </td>
                            </tr>
                        <?php }; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    </div>



    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        function confirmApprove(event, studentName, depositId) {
            if (confirm(`Do you want to approve the cash-in of '${studentName}'?`)) {
                // Redirect to the approval page if confirmed
                window.location.href = `admin_approve_deposit.php?approve_id=${depositId}`;
            } else {
                // Prevent any default action if the confirmation is canceled
                event.preventDefault();
            }
        }

        function confirmDelete(event) {
            if (!confirm("Are you sure you want to reject?")) {
                // if the cancel clicked, then prevent the form submission
                event.preventDefault();
            }
        }

        setTimeout(() => {
            var delete_deposit = document.querySelector("#delete_deposit");
            if (delete_deposit) {
                delete_deposit.style.display = 'none';
                window.location.href = "student_cash_in.php";
            }
        }, 3500);
    </script>
</body>

</html>