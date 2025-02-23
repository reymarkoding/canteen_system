<?php
include "../conn.php";


session_start();

if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}
if (isset($_POST['submit'])) {

    $id_number = $_POST['id_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $cnumber = $_POST['cnumber'];
    // $userName = $_POST['username'];
    // $passWord = $_POST['password'];
    // $default_profile = 'default_image.jpeg';
    $default_profile = 'profile/default_image.jpeg';


    // $stud_info = array(
    //     'id_number' => $id_number,
    //     'first_name' => $first_name,
    //     'last_name' => $last_name,
    //     'middle_name' => $middle_name,
    //     'contact_number' => $cnumber,
    // );
    // $qrcode = json_encode($stud_info);

    // Check if ID number already exists
    $check_query = "SELECT * FROM `stud_info` WHERE `ID_NUMBER` = '$id_number'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('ID Number already exists.');
        window.location.href='students.php';
        </script>";
    } else {
        $insert = "INSERT INTO `stud_info`(`ID_NUMBER`, `FIRSTNAME`, `LASTNAME`, `MIDDLENAME`,`CNUMBER`, `PROFILE_IMAGE`, `STATUS`) 
                    VALUES ('$id_number','$first_name','$last_name','$middle_name','$cnumber', '$default_profile', 'unregistered')";
        $result = mysqli_query($conn, $insert);

        if ($result) {
            echo "<script>alert('Successfully student added.');
            window.location.href='students.php';
            </script>";
        } else {
            echo "<script>alert('Error');
            window.location.href='students.php';
            </script>";
        }
    }
}






?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./admin_css/students.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .notification-dot {
            width: 10px;
            height: 10px;
            display: inline-block;
        }

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
                <div class="menu-item active">
                    <a href="students.php">Students</a>
                </div>
                <!-- <div class="menu-item">
                    <a href="edit_admin.php">Account</a>
                </div> -->
                <!-- <div class="menu-item">
                    <a href="history.php">History</a>
                </div> -->
                <div class="menu-item">
                    <form action="admin_logout.php"><input type="submit" class="btn btn-danger" value="Logout" style="border: none;"></form>
                </div>
            </div>
        </div>
        <div class="main-bar p-2">
            <div class="stud_reg_container p-2">
                <center>
                    <h3>Add Student</h3>
                </center>
                <form action="" method="post">
                    <div class="stud">
                        <input type="text" name="id_number" class="form-control" required placeholder="ID NUMBER">
                    </div>
                    <div class="stud">
                        <input type="text" name="last_name" class="form-control" required placeholder="Last Name">
                    </div>
                    <div class="stud">
                        <input type="text" name="first_name" class="form-control" required placeholder="First Name">
                    </div>
                    <div class="stud">
                        <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                    </div>
                    <div class="stud">
                        <input required maxlength="11" minlength="11" class="form-control" type="tel" name="cnumber" placeholder="Enter Phone Number">
                    </div>
                    <div class="stud">
                        <button class="form-control btn btn-success" type="submit" name="submit">Add Student</button>
                    </div>
                </form>
            </div>
            <form action="import_excel.php" method="POST" enctype="multipart/form-data">
                <label for="excel_file">Upload Excel File:</label>
                <input type="file" name="excel_file" id="excel_file" accept=".csv" required>
                <button type="submit" name="submit">Upload and Import</button>
            </form>
            <div class="stud-table p-2">
                <!-- <div class="top">
                    <div class="search">
                        <input type="text" placeholder="Search" class="form-control">
                    </div>
                    <div class="export">
                        <button class="form-control btn btn-info">EXCEL</button>
                    </div>
                </div> -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 8%;">ID</th>
                            <th style="width: 12%;">Name</th>
                            <th style="width: 8%;">Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $display_stud = mysqli_query($conn, "SELECT * FROM stud_info ORDER BY STATUS, LASTNAME, FIRSTNAME");
                        $previousStatus = null;

                        while ($row = mysqli_fetch_assoc($display_stud)) {
                            $fullName = $row['LASTNAME'] . ', ' . $row['FIRSTNAME'];

                            if (!empty($row['MIDDLENAME'])) {
                                $fullName .= " " . $row['MIDDLENAME'][0] . ".";
                            }

                            // Display the status header only when it changes
                            if ($previousStatus !== $row['STATUS']) {
                                $previousStatus = $row['STATUS'];
                                echo "<tr class='table-warning'>
                        <td colspan='3' style='text-transform: uppercase; text-align: center;'><strong>{$row['STATUS']}</strong></td>
                      </tr>";
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['ID_NUMBER']); ?></td>
                                <td><?= htmlspecialchars($fullName); ?></td>
                                <td><?= htmlspecialchars($row['CNUMBER']); ?></td>
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
</body>

</html>