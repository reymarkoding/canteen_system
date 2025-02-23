<?php

include "../conn.php";
session_start();

$ID_NUMBER = $_SESSION['ID_NUMBER'];
$STUD_ID = $_SESSION['stud_id'];
$fullName = $_SESSION['fullName'];
$balance = $_SESSION['balance'];

if (!isset($_SESSION['student_status']) || $_SESSION['student_status'] !== 'login') {
    header("Location: student_login.php");
    exit();
}

if (isset($_POST['new_profile'])) {
    $display_info = mysqli_query($conn, "SELECT * FROM stud_info WHERE STUD_ID = '$STUD_ID'");
    while ($stud_info = mysqli_fetch_assoc($display_info)) {
        $ID = $stud_info['STUD_ID'];
    }
    $new_username = isset($_POST['new_username']) ? $_POST['new_username'] : null;
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : null;
    $new_contact_number = isset($_POST['new_contact_number']) ? $_POST['new_contact_number'] : null;
    $targetDir = "profile/";
    $targetFile = isset($_FILES["new_image"]["name"]) ? $targetDir . basename($_FILES["new_image"]["name"]) : null;

    // Update profile picture if provided
    if (!empty($_FILES["new_image"]["tmp_name"])) {
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $targetFile)) {
            $updateImage = mysqli_query($conn, "UPDATE `stud_info` SET `PROFILE_IMAGE`='$targetFile' WHERE STUD_ID = '$STUD_ID'");
            if ($updateImage) {
                echo "<script>alert('Profile picture updated')</script>";
            }
        } else {
            echo "<script>alert('Failed to upload profile picture')</script>";
        }
    }

    // Update username if provided
    if (!empty($new_username)) {
        $updateUsername = mysqli_query($conn, "UPDATE `stud_info` SET `USERNAME`='$new_username' WHERE STUD_ID = '$STUD_ID'");
    }

    // Update password if provided
    if (!empty($new_password)) {
        $hashedPassword = md5($new_password); // Use md5 only if it's a requirement; otherwise, use password_hash
        $updatePassword = mysqli_query($conn, "UPDATE `stud_info` SET `PASSWORD`='$hashedPassword' WHERE STUD_ID = '$STUD_ID'");
    }

    // Update contact number if provided (optional based on your logic)
    if (!empty($new_contact_number)) {
        $updateContact = mysqli_query($conn, "UPDATE `stud_info` SET `CNUMBER`='$new_contact_number' WHERE STUD_ID = '$STUD_ID'");
    }
    $_SESSION['success'] = 'Update complete';
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/student_edit_profile.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="./css/css/js/bootstrap.bundle.js"></script>
    <style>
        img {
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.4);
        }

        label {
            color: #f8f9fa;
        }

        .alert {
            position: fixed;
            top: 5%;
            left: 5%;
            animation-name: goleft;
            animation-duration: 3500ms;
        }

        @keyframes goleft {
            0% {
                left: -20%;
            }

            25% {
                left: 75px;
            }

            50% {
                left: 75px;
            }

            75% {
                left: 75px;
            }

            100% {
                left: -60%;
            }
        }
    </style>
</head>

<body>
    <?php
    if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-info' id='success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <div class="main">
        <div class="edit_container p-2">
            <a href="student_dashboard.php" class="text-dark" style="text-decoration: none;">Back</a>
            <?php
            $find = mysqli_query($conn, "SELECT * FROM stud_info WHERE STUD_ID = '$STUD_ID'");
            while ($row = mysqli_fetch_assoc($find)) {
                $pro_pic = $row['PROFILE_IMAGE'];
                $id_num = $row['ID_NUMBER'];
                $bal = $row['BALANCE'];
                $ln = $row['LASTNAME'];
                $fn = $row['FIRSTNAME'];
                $mn = $row['MIDDLENAME'];
                $cn = $row['CNUMBER'];
                $un = $row['USERNAME'];
                $pw = $row['PASSWORD'];
            }
            ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="top">
                    <div class="image mb-2">
                        <?= "<center><img src='$pro_pic' class='p-2' style='width:80%;height:50%;border: 1px solid #347928;'></center>" ?>
                    </div>
                    <div class="input-group mb-2">
                        <input class="form-control" style="cursor: pointer;" type="file" name="new_image" accept="image/*" id="new_image">
                    </div>
                    <label for="" class="text-dark">Balance: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" readonly type="text" value="<?= $bal; ?>">
                    </div>
                    <label for="" class="text-dark">ID Number: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" readonly type="text" value="<?= $id_num; ?>">
                    </div>
                    <label for="" class="text-dark">Last Name: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" readonly type="text" value="<?= $ln; ?>">
                    </div>
                    <label for="" class="text-dark">First Name: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" readonly type="text" value="<?= $fn; ?>">
                    </div>
                    <label for="" class="text-dark">Middle Name: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" readonly type="text" value="<?= $mn; ?>">
                    </div>
                    <label for="" class="text-dark">Contact Number: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" name="new_contact_number" type="text" value="<?= $cn; ?>">
                    </div>
                    <label for="" class="text-dark">Username: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" readonly type="text" value="<?= $un; ?>">
                    </div>
                    <label for="" class="text-dark">New username: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" name="new_username" type="text" value="<?= $un; ?>">
                    </div>
                    <label for="" class=" text-dark">New password: </label>
                    <div class="input-group mb-2">
                        <input class="form-control" name="new_password" type="password" placeholder="Enter new password">
                    </div>
                    <div class="input-group mb-2">
                        <input class="form-control btn btn-success" type="submit" name="new_profile" value="UPDATE PROFILE">
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    setTimeout(() => {
        var success = document.querySelector("#success");
        if (success) {
            success.style.display = 'none';
            window.location.href = "student_edit_profile.php";
        }
    }, 3500);
</script>

</html>