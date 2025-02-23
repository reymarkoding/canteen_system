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

if (isset($_GET['delete'])) {
    $STUD_ID = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM stud_info WHERE STUD_ID = $STUD_ID");
    echo "<script>alert('You delete student successfully')</script>;
            <script>window.location.href='student_list.php'</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
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
                    <th style="width: 8%;">Profile</th>
                    <th style="width: 8%;">Id Number</th>
                    <th style="width: 8%;">Student Name</th>
                    <th style="width: 8%;">Username</th>
                    <th style="width: 5%;">Balance</th>
                    <th style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to group by product_name and sum the quantities and totals
                $sql = mysqli_query($conn, "SELECT * FROM stud_info");

                // Initialize grand total
                $grandTotal = 0;

                // Loop through the results and display each product
                while ($row = mysqli_fetch_assoc($sql)) {
                    $full_name = $row['LASTNAME'] . ", " . $row['FIRSTNAME'];

                    // Check if there is a middle name and include the initial if it exists
                    if (!empty($row['MIDDLENAME'])) {
                        $full_name .= " " . $row['MIDDLENAME'][0] . ".";
                    }

                    $dp = $row['PROFILE_IMAGE'];
                    $path = "../student/" . $dp;
                ?>
                    <tr>
                        <td><?php echo "<center><img src='$path' class='mt-2' style='width:75px;height:75px;'></center>" ?></td>
                        <td><?= $row['ID_NUMBER']; ?></td>
                        <td><?= $full_name; ?></td>
                        <td><?= $row['USERNAME']; ?></td>
                        <td><?= $row['BALANCE']; ?></td>
                        <td>
                            <button <?= $row['BALANCE'] > 0 ? 'disabled' : ''; ?>
                                type="button" onclick="confirmDelete(<?= $row['STUD_ID']; ?>)" class="btn btn-danger">
                                Delete
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
        function confirmDelete(delID) {
            var result = confirm('Are you sure you want to delete this student?');

            if (result) {
                window.location.href = 'student_list.php?delete=' + delID;
            } else {
                window.location.href = 'student_list.php';
            }
        }
    </script>
</body>

</html>