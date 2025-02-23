<?php
include "../conn.php";
session_start();

if (isset($_SESSION['accounting_status']) && $_SESSION['accounting_status'] === 'login') {
    header("Location: accounting_dashboard.php");
    exit();
} else {
    $_SESSION['accounting_status'] = 'logout';
}

if (isset($_POST['accounting_login'])) {
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    $sql = "SELECT * FROM accounting2 WHERE username = '$admin_username' AND password = '$admin_password'";
    $query = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($query);

    if (mysqli_num_rows($query) > 0) {
        $_SESSION['accounting_status'] = 'login';
        header("location: accounting_dashboard.php");
    } else {
        $_SESSION['accounting_status'] = 'logout';
        $_SESSION['error_msg'] = 'Invalid Credentials';
        // header("location: accounting_login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting Login</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
        }

        /* Login Container */
        .login-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-header img {
            width: 80px;
            margin-bottom: 10px;
        }

        .login-header h3 {
            font-weight: bold;
            color: #333;
        }

        /* Form Inputs */
        .login-form .form-control {
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .login-form button {
            background-color: #347928;
            /* Accounting theme color */
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }

        .login-form button:hover {
            background-color: #285a20;
            /* Darker green on hover */
        }

        /* Footer Section */
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }

        .login-footer p {
            font-size: 14px;
            color: #555;
        }

        .login-footer a {
            text-decoration: none;
            color: #347928;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../final_logo.png" alt="Accounting Logo">
            <h3>Accounting Login</h3>
        </div>
        <form action="" method="POST" class="login-form">
            <?php
            if (isset($_SESSION['error_msg'])) {
                echo "<div class='alert alert-warning' id='error_msg'>" . $_SESSION['error_msg'] . "</div>";
                unset($_SESSION['error_msg']);
            }
            ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <input type="submit" name="accounting_login" value="Login" class="btn btn-success w-100">
        </form>
        <div class="login-footer">
            <a href="../landing_page.php">Back</a>
        </div>
    </div>


    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        setTimeout(() => {
            var error_msg = document.querySelector("#error_msg");
            if (error_msg) {
                error_msg.style.display = 'none';
                window.location.href = "accounting_login.php";
            }
        }, 3000);
    </script>
</body>

</html>