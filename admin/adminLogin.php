<?php
include "../conn.php";
session_start();

if (isset($_SESSION['admin_status']) && $_SESSION['admin_status'] === 'login') {
    header("Location: admin_dashboard.php");
    exit();
} else {
    $_SESSION['admin_status'] = 'logout';
}

if (isset($_POST['admin_login'])) {
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE username = '$admin_username' AND password = '$admin_password'";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    if (mysqli_num_rows($query) > 0) {
        $_SESSION['admin_status'] = 'login';
        header("location: admin_dashboard.php");
    } else {
        $_SESSION['admin_status'] = 'logout';
        $_SESSION['error_msg'] = 'Invalid Credentials';
        // header("location: adminLogin.php");
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            background: linear-gradient(to bottom right, #2c3e50, #4ca1af);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }

        /* Login Card */
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
        }

        /* Logo Styling */
        .logo {
            width: 100px;
            margin: 0 auto 20px;
            display: block;
        }

        /* Form Styling */
        .login-form .form-control {
            border-radius: 30px;
        }

        .login-form button {
            border-radius: 30px;
        }

        /* Footer Links */
        .footer-links {
            margin-top: 20px;
            font-size: 14px;
            text-align: center;
        }

        .footer-links a {
            text-decoration: none;
            color: #4ca1af;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <img src="../final_logo.png" alt="Admin Logo" class="logo">
        <h3 class="text-center mb-4">Admin Login</h3>
        <form action="" method="POST" class="login-form">
            <?php
            if (isset($_SESSION['error_msg'])) {
                echo "<div class='alert alert-warning' id='error_msg'>" . $_SESSION['error_msg'] . "</div>";
                unset($_SESSION['error_msg']);
            }
            ?>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" name="admin_login" class="btn btn-primary w-100">Login</button>
            <div class="footer-links">
                <a href="../landing_page.php">Back</a>
            </div>
        </form>
    </div>


    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        setTimeout(() => {
            var error_msg = document.querySelector("#error_msg");
            if (error_msg) {
                error_msg.style.display = 'none';
                window.location.href = "adminLogin.php";
            }
        }, 3000);
    </script>
</body>

</html>