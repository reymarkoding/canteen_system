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

if (isset($_GET['approveID'])) {
    $STUD_ID = $_GET['approveID'];
    mysqli_query($conn, "UPDATE `stud_info` SET `STATUS`='approved' WHERE STUD_ID = '$STUD_ID'");
    echo "<script>alert('Approved the request.')</script>;
            <script>window.location.href='registration_approval.php'</script>";
}

if (isset($_GET['delete'])) {
    $STUD_ID = $_GET['delete'];
    mysqli_query($conn, "UPDATE `stud_info` SET `STATUS`='unregistered' WHERE STUD_ID = '$STUD_ID'");
    echo "<script>alert('You successfully rejected the request.')</script>;
            <script>window.location.href='registration_approval.php'</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval</title>
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

        table {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="main p-5">
        <nav class="navbar navbar-light bg-light">
            <form class="container-fluid justify-content-start">
                <button class="btn btn-primary me-2" type="button"><a href="admin_dashboard.php" style="text-decoration: none; color: white;">Back</a></button>
            </form>
        </nav>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 12%;">Verification Photo</th>
                    <th style="width: 8%;">Id Number</th>
                    <th style="width: 10%;">Student Name</th>
                    <th style="width: 6%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to group by product_name and sum the quantities and totals
                $sql = mysqli_query($conn, "SELECT * FROM stud_info WHERE STATUS = 'pending'");

                // Loop through the results and display each product
                while ($row = mysqli_fetch_assoc($sql)) {
                    $full_name = $row['LASTNAME'] . ", " . $row['FIRSTNAME'];

                    // Check if there is a middle name and include the initial if it exists
                    if (!empty($row['MIDDLENAME'])) {
                        $full_name .= " " . $row['MIDDLENAME'][0] . ".";
                    }

                    $dp = $row['VER_PICTURE'];
                    $path = "uploads/" . $dp;

                ?>
                    <tr>
                        <td><?php echo "<center><img src='$path' class='mt-2' style='width:125px;height:125px;'></center>" ?></td>
                        <td><?= $row['ID_NUMBER']; ?></td>
                        <td><?= $full_name; ?></td>
                        <td>
                            <button
                                type="button" onclick="approve(<?= $row['STUD_ID']; ?>)" class="btn btn-success">
                                Accept
                            </button>
                            <button
                                type="button" onclick="confirmDelete(<?= $row['STUD_ID']; ?>)" class="btn btn-danger">
                                Reject
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>



    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        function approve(approveID) {
            var a = confirm('Are you sure you want approve?');

            if (a) {
                window.location.href = 'registration_approval.php?approveID=' + approveID;
            } else {
                window.location.href = 'registration_approval.php';
            }
        }

        function confirmDelete(delID) {
            var result = confirm('Are you sure you want to reject this request?');

            if (result) {
                window.location.href = 'registration_approval.php?delete=' + delID;
            } else {
                window.location.href = 'registration_approval.php';
            }
        }
    </script>
</body>

</html>