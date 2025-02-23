<?php
include "../conn.php";
session_start();

// if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
//     header("Location: adminLogin.php");
//     exit();
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="./admin_css/admin_dashboard.css"> -->
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


        /* .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(52, 121, 40, 0.3), rgba(50, 50, 50, 0.9)),
                url('../bg.png') no-repeat center center / cover;

            z-index: -2;
        } */

        .edit-container {
            width: 85%;
            margin: 30px auto;
            padding: 20px 30px;
            background-color: #ffffff;
            /* White background for contrast */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow for depth */
            font-family: Arial, sans-serif;
            color: #343a40;
            /* Dark gray text */
        }

        .edit-container h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #347928;
            /* Primary green */
            font-weight: bold;
        }

        .edit-container .form-group {
            margin-bottom: 20px;
        }

        .edit-container .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .edit-container .form-control {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .edit-container .form-control:focus {
            outline: none;
            border-color: #347928;
            /* Primary green for focus */
            box-shadow: 0 0 5px rgba(52, 121, 40, 0.3);
        }

        .edit-container button.btn {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            background-color: #347928;
            /* Primary green */
            color: #ffffff;
            /* White text */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-container button.btn:hover {
            background-color: #2a6022;
            /* Darker green */
        }

        .main-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    </style>
</head>

<body>
    <div class="background">

    </div>
    <div class="main">
        <div class="main-bar p-2">
            <div class="edit-container">
                <h2>Profile</h2>
                <?php
                $sql = mysqli_query($conn, "SELECT * FROM admin");
                $row = mysqli_fetch_assoc($sql);
                ?>
                <form action="" method="POST">
                    <!-- Change Username -->
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" value="<?= $row['username'] ?>" name="username" class="form-control" readonly>
                    </div>

                    <!-- Change Password -->
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <div class="password-container" style="position: relative;">
                            <input type="password" id="password" value="<?= $row['password'] ?>" name="password" class="form-control" readonly>
                            <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fas fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="edit-container">
                <h2>Edit Profile</h2>
                <form action="update_profile.php" method="POST">
                    <!-- Change Username -->
                    <div class="form-group">
                        <label for="username">Change Username:</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter new username" required>
                    </div>

                    <!-- Change Password -->
                    <div class="form-group">
                        <label for="password">Change Password:</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="admin_dashboard.php" class="btn btn-danger mt-2">Back</a>
                </form>
            </div>
        </div>
    </div>


    <script src="js/chart.min.js"></script>
    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const icon = this.querySelector('i');
            // Toggle password visibility
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>

</html>