<?php

include "../conn.php";
session_start();

// if (isset($_POST['submit'])) {

//     $id_number = $_POST['id_number'];
//     $first_name = $_POST['first_name'];
//     $last_name = $_POST['last_name'];
//     $middle_name = $_POST['middle_name'];
//     $cnumber = $_POST['cnumber'];
//     $userName = $_POST['username'];
//     $passWord = $_POST['password'];
//     $default_profile = 'profile/default_image.jpeg';


//     $stud_info = array(
//         'id_number' => $id_number,
//         'first_name' => $first_name,
//         'last_name' => $last_name,
//         'middle_name' => $middle_name,
//         'contact_number' => $cnumber,
//     );
//     $qrcode = json_encode($stud_info);

//     // Check if ID number already exists
//     $check_query = "SELECT * FROM `stud_info` WHERE `ID_NUMBER` = '$id_number'";
//     $check_result = mysqli_query($conn, $check_query);

//     if (mysqli_num_rows($check_result) > 0) {
//         echo "<script>alert('ID Number already exists. Please use a different ID Number.');
//         window.location.href='student_register.php';
//         </script>";
//     } else {
//         $insert = "INSERT INTO `stud_info`(`QRCODE`, `ID_NUMBER`, `FIRSTNAME`, `LASTNAME`, `MIDDLENAME`,`CNUMBER`, `PROFILE_IMAGE`, `USERNAME`, `PASSWORD`, `STATUS`) 
//                     VALUES ('$qrcode','$id_number','$first_name','$last_name','$middle_name','$cnumber', '$default_profile','$userName',md5('$passWord'), 'pending')";
//         $result = mysqli_query($conn, $insert);

//         if ($result) {
//             echo "<script>alert('Successfully registered.');
//             window.location.href='student_register.php';
//             </script>";
//         } else {
//             echo "<script>alert('Error');
//             window.location.href='student_register.php';
//             </script>";
//         }
//     }
// }

// if (isset($_POST['submit'])) {

//     $id_number = $_POST['id_number'];
//     $userName = $_POST['username'];
//     $passWord = $_POST['password'];
//     $verfication_picture = $_FILES["verification_picture"]["name"];
//     $ext = pathinfo($verfication_picture, PATHINFO_EXTENSION);
//     $allowedTypes = array("jpg", "png", "jpeg", "gif", "JPG");
//     $tempName = $_FILES["verification_picture"]["tmp_name"];
//     $target_path = "profile/" . $verfication_picture;

//     // Check if ID number already exists


//     if (move_uploaded_file($tempName, $target_path)) {

//         $check_query = "SELECT ID_NUMBER FROM `stud_info` WHERE `ID_NUMBER` = '$id_number'";
//         $check_result = mysqli_query($conn, $check_query);
//         if (mysqli_num_rows($check_result) > 0) {
//             $update_status_to_pending = mysqli_query($conn, "UPDATE `stud_info` SET `USERNAME`='$userName',`PASSWORD`= md5('$passWord'),`VER_PICTURE`='$verfication_picture',`STATUS`='pending' WHERE ID_NUMBER = $id_number");
//         } else {

//             echo "<script>alert('Error');
//             window.location.href='student_register.php';
//             </script>";
//         }
//     }
// }
if (isset($_POST['submit'])) {
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $userName = mysqli_real_escape_string($conn, $_POST['username']);
    $passWord = mysqli_real_escape_string($conn, $_POST['password']);
    $verfication_picture = $_FILES["verification_picture"]["name"];
    $ext = pathinfo($verfication_picture, PATHINFO_EXTENSION);
    $allowedTypes = array("jpg", "png", "jpeg", "JPG");
    $tempName = $_FILES["verification_picture"]["tmp_name"];
    $target_path = "../admin/uploads/" . $verfication_picture;

    if (empty($id_number) || empty($userName) || empty($passWord) || empty($verfication_picture)) {
        echo "<script>alert('All fields are required'); window.location.href='student_register.php';</script>";
        exit;
    }

    if (!in_array($ext, $allowedTypes)) {
        echo "<script>alert('Invalid file type. Allowed types are: jpg, png, jpeg, gif'); window.location.href='student_register.php';</script>";
        exit;
    }

    if (move_uploaded_file($tempName, $target_path)) {
        $check_query = "SELECT ID_NUMBER FROM `stud_info` WHERE `ID_NUMBER` = '$id_number' AND STATUS = 'unregistered'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $update_status_to_pending = mysqli_query($conn, "UPDATE `stud_info` SET `USERNAME`='$userName',`PASSWORD`= md5('$passWord'),`VER_PICTURE`='$verfication_picture',`STATUS`='pending' WHERE ID_NUMBER = '$id_number'");
            if ($update_status_to_pending) {
                echo "<script>alert('Register successfully. Please wait for the admin to approve your registration request.'); window.location.href='student_register.php';</script>";
            } else {
                echo "<script>alert('Error updating record'); window.location.href='student_register.php';</script>";
            }
        } else {
            echo "<script>alert('ID number not found'); window.location.href='student_register.php';</script>";
        }
    } else {
        echo "<script>alert('Error uploading file'); window.location.href='student_register.php';</script>";
    }
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- <link rel="stylesheet" href="./css/student_register.css"> -->
    <script type="text/javascript" src="../js/adapter.min.js"></script>
    <script type="text/javascript" src="../js/vue.min.js"></script>
    <script type="text/javascript" src="../js/instascan.min.js"></script>
    <script type="text/javascript" src="../js/html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: url('your-background-image.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .password-container {
            position: relative;
        }

        #togglePassword {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .registration-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            height: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .registration-container h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #347928;
        }

        .registration-container .form-group {
            margin-bottom: 15px;
        }

        .registration-container input,
        .registration-container button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
        }

        .registration-container button {
            background-color: #347928;
            color: #fff;
            cursor: pointer;
            font-size: 1.1rem;
            border: none;
        }

        .registration-container button:hover {
            background-color: #285a20;
        }

        .registration-container .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .registration-container .footer a {
            color: #ff6f61;
            text-decoration: none;
        }

        .registration-container .footer a:hover {
            text-decoration: underline;
        }

        .registration-container input::placeholder {
            color: #777;
        }

        .registration-container label {
            font-weight: bold;
        }

        .registration-container p {
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="registration-container">
        <h2>Student Registration</h2>
        <p>*Please fill out the information regarding your study load</p>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="id_number">ID Number:</label>
                <input required class="input" id="id_number" type="text" name="id_number" placeholder="ID Number ex. C-0000-0000">
            </div>
            <div class="form-group">
                <label for="username">Attach selfie with School ID:</label>
                <input required class="input" type="file" name="verification_picture">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input required class="input" type="text" name="username" placeholder="Create username">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <div class="password-container">
                    <input required class="input" id="password" type="password" name="password" placeholder="Create password">
                    <i id="togglePassword" class="fas fa-eye-slash"></i> <!-- Eye Icon -->
                </div>
            </div>
            <div class="form-group">
                <button class="submit" type="submit" name="submit">Register</button>
            </div>
        </form>

        <div class="footer">
            <p>Already have an account? <a href="student_login.php">Login here!</a></p>
        </div>
    </div>


    <!-- <div class="main">
        <div class="header">
            <span>Registration Form</span>
        </div>
        <div class="registration-body">
            <form action="" method="post">
                <p style="font-size: 11px; color: white; opacity: 50%;">*Please fill out the information regarding your study load</p>
                <div class="box-form">
                    <label for="">ID Number:</label>
                    <input required class="input" id="id_number" type="text" name="id_number" placeholder="ID Number ex. C-0000-0000">
                </div>
                <div class="box-form">
                    <label for="">First Name:</label>
                    <input required class="input" id="firstName" type="text" name="first_name" placeholder="First Name">
                </div>
                <div class="box-form">
                    <label for="">Last Name:</label>
                    <input required class="input" id="lastName" type="text" name="last_name" placeholder="Last Name">
                </div>
                <div class="box-form">
                    <label for="">Middle Name:</label>
                    <input required class="input" id="middleName" type="text" name="middle_name" placeholder="Middle Name">
                </div>
                <div class="box-form">
                    <label for="">Contact Number:</label>
                    <input required maxlength="11" minlength="11" class="input" type="tel" name="cnumber" placeholder="Enter Phone Number">
                </div>
                <div class="box-form">
                    <label for="">Username:</label>
                    <input required class="input" type="text" name="username" placeholder="Enter username">
                </div>
                <div class="box-form">
                    <label for="">Password:</label>
                    <input required class="input" type="password" name="password" placeholder="Enter password">
                </div>
                <div class="box-form">
                    <button class="submit" type="submit" name="submit">Register</button>
                </div>
                <div class="box-form">
                    <a href="student_login.php">Already have an account</a>
                </div>
            </form>
        </div>
    </div> -->


    <!-- HTML5QRCODE GAMIT ANI -->
    <!-- <script>
        function onScanSuccess(qrMessage) {
            try {
                var bal = JSON.parse(qrMessage);
                document.getElementById('qrcode').value = qrMessage;
                document.getElementById('stud_id').value = bal.ID;
                document.getElementById('fullName').value = bal.StudentName;
                document.getElementById('stud_course').value = bal.Program;
            } catch (e) {
                console.error('Error parsing QR code content:', e);
            }
        }

        function onScanError(errorMessage) {
            console.error('Scan error:', errorMessage);
        }

        // Initialize the QR Code scanner
        let html5QrCode = new Html5Qrcode("preview");

        // Start the QR Code scanning
        html5QrCode.start(
            { facingMode: "environment" }, // Use back camera
            {
                fps: 10, // Set the scanning frequency
                qrbox: {width: 250, height:250}, // Set the scanning box size
                aspectRatio: 1.0
            },
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error("Error starting the QR Code scanner:", err);
        });
    </script> -->




    <!-- IKADUHA NI NA JS -->

    <!-- MAO NI GAMITON NGA SCANNER PARA SA SCANNING OF PAYMENT -->
    <!-- <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                // Default to the first camera
                let selectedCamera = cameras[0];
                
                // Try to select the back camera if it exists
                cameras.forEach(camera => {
                    if (camera.name.toLowerCase().includes('back')) {
                        selectedCamera = camera;
                    }
                });

                scanner.start(selectedCamera);
            } else {
                console.error('No cameras found');
            }
        }).catch(function (e) {
            console.error(e);
        });

        scanner.addListener('scan', function (content) {
            try {
                let data = JSON.parse(content);
                document.getElementById('qrcode').value = content;
                document.getElementById('stud_id').value = data.ID;
                document.getElementById('fullName').value = data.StudentName;
                document.getElementById('stud_course').value = data.Program;
            } catch (e) {
                alert('Student ID only');
            }
        });
    </script> -->

    <script>
        function validatePhoneNumber(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/\D/g, '');

            // Limit the length to 11 characters
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }

        // Toggle password visibility
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function() {
            // Toggle the password input type
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;

            // Toggle the eye icon class
            if (passwordInput.type === "password") {
                this.classList.add("fa-eye-slash");
                this.classList.remove("fa-eye");
            } else {
                this.classList.add("fa-eye");
                this.classList.remove("fa-eye-slash");
            }
        });
    </script>
</body>

</html>