<?php

include "../conn.php";
session_start();

if (isset($_SESSION['student_status']) && $_SESSION['student_status'] === 'login') {
    header("Location: student_dashboard.php");
    exit();
} else {
    $_SESSION['student_status'] = 'logout';
}

if (isset($_POST['login'])) {
    $userName = $_POST['username'];
    $passWord = $_POST['password'];

    $sql = "SELECT * FROM stud_info WHERE USERNAME='$userName' AND PASSWORD=md5('$passWord')";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($query);

    if (mysqli_num_rows($query) > 0) {
        $stat = $row['STATUS'];
        if ($stat === 'approved') {
            $fullname = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0] . ".";
            $_SESSION['student_status'] = 'login';
            $_SESSION['stud_id'] = $row['STUD_ID'];
            $_SESSION['ID_NUMBER'] = $row['ID_NUMBER'];
            $_SESSION['fullName'] = $fullname;
            $_SESSION['balance'] = $row['BALANCE'];
            $_SESSION['password'] = $passWord;

            header("location:student_dashboard.php");
        } else {
            $_SESSION['status'] = 'logout';
            $_SESSION['error_msg'] = 'Wait for approval.';
        }
    } else {
        $_SESSION['status'] = 'logout';
        $_SESSION['error_msg'] = 'Invalid Credentials';
        // header("location:student_login.php");

    }
}




?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- <link rel="stylesheet" href="./css/student_register.css"> -->
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <script type="text/javascript" src="../js/adapter.min.js"></script>
    <script type="text/javascript" src="../js/vue.min.js"></script>
    <script type="text/javascript" src="../js/instascan.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: url('../bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .login-container {
            background: #fff;
            /* Change the background color to white */
            padding: 30px;
            border-radius: 10px;
            max-width: 330px;
            width: 100%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            color: #347928;
        }

        .login-container .form-group {
            margin-bottom: 15px;
        }

        .login-container input,
        .login-container button {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
        }

        .login-container button {
            background-color: #347928;
            color: #fff;
            cursor: pointer;
            font-size: 1.1rem;
            border: none;
        }

        .login-container button:hover {
            background-color: #285a20;
        }

        .login-container .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .login-container .footer a {
            color: #ff6f61;
            text-decoration: none;
        }

        .login-container .footer a:hover {
            text-decoration: underline;
        }

        .login-container input::placeholder,
        .login-container button::placeholder {
            color: #777;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Student Login</h2>
        <form action="" method="POST">
            <?php
            if (isset($_SESSION['error_msg'])) {
                echo "<div class='alert alert-warning' id='error_msg'>" . $_SESSION['error_msg'] . "</div>";
                unset($_SESSION['error_msg']);
            }
            ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>
            <div class="form-group">
                <button class="submit" type="submit" name="login">Login</button>
            </div>
        </form>

        <div class="footer">
            <p>Don't have account? <a href="student_register.php">Register here!</a></p>
            <a href="../landing_page.php">Back</a>
        </div>
    </div>



    <!-- <div class="main">
        <div class="header">
            <span>Login Form</span>
        </div>
        <div class="registration-body">
            <form action="" method="post">
                <div class="box-form">
                    <label for="">Username:</label>
                    <input required class="input" type="text" name="username" placeholder="Enter username">
                </div>
                <div class="box-form">
                    <label for="">Password:</label>
                    <input required class="input" type="password" name="password" placeholder="Enter password">
                </div>
                <div class="box-form">
                    <button class="submit" type="submit" name="login">Login</button>
                </div>
                <div class="box-form">
                    <a href="student_register.php">Registration</a>
                </div>
            </form>
        </div>
    </div> -->


    <!-- <script>

        // const balue = document.getElementById('qrcode');
        // let name = document.queryelector('#fullName');
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview')});
        Instascan.Camera.getCameras().then(function(cameras){
            if(cameras.length > 0){
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found');
            }
        }).catch(function(e){
            console.error(e);
        });
        scanner.addListener('scan', function(content){
            var bal = JSON.parse(content)
            document.getElementById('qrcode').value=content;
            document.getElementById('stud_id').value=bal.ID;
            document.getElementById('fullName').value=bal.StudentName;
            document.getElementById('stud_course').value=bal.Program;
            // document.forms[0].submit();
        });


    </script>  -->
    <script>
        setTimeout(() => {
            var error_msg = document.querySelector("#error_msg");
            if (error_msg) {
                error_msg.style.display = 'none';
                window.location.href = "student_login.php";
            }
        }, 3000);
    </script>
</body>

</html>