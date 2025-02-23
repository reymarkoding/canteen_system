<?php

include "../conn.php";
session_start();

$STUD_ID = $_SESSION['stud_id'];
$ID_NUMBER = $_SESSION['ID_NUMBER'];
$fullName = $_SESSION['fullName'];

if (!isset($_SESSION['student_status']) || $_SESSION['student_status'] !== 'login') {
    header("Location: student_login.php");
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/student_dashboard.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="./css/css/js/bootstrap.bundle.js"></script>
    <style>
        li {
            font-size: 11px;
        }

        .top {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
        }
    </style>
</head>

<body>
    <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="instructionsModalLabel">Payment Instructions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol>
                        <li>Go to the school canteen and choose your desired items manually.</li>
                        <li>Proceed to the cashier for the total amount of your purchases.</li>
                        <li>Open your account on the system and locate your QR code.</li>
                        <li>Present your QR code to the cashier for scanning.</li>
                        <li>Receive confirmation of the payment and collect your items.</li>
                    </ol>
                    <p class="mt-3 text-muted">
                        Please ensure your account balance is sufficient before proceeding to the canteen.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="main_header">
        <div class="title">
            <center>
                <h4>Dashboard</h4>
            </center>
        </div>
        <div class="menus">
            <div class="menu-icon">
                <form action="student_logout.php"><input type="submit" class="btn btn-danger" value="Logout" style="border: none;"></form>
            </div>
        </div>
    </div>
    <div class="main bg-light">
        <div class="balance">
            <?php
            $findBal = mysqli_query($conn, "SELECT BALANCE FROM stud_info WHERE STUD_ID = '$STUD_ID'");
            while ($row = mysqli_fetch_assoc($findBal)) {
                $balance = $row['BALANCE'];
            }
            ?>
            <span class="text-dark" style="opacity: 65%;">Balance <i id="eye" class="fas fa-eye" onclick="hideMe()"></i></span>
            <p id="balance"><?php echo "₱ " . number_format($balance, 2); ?></p>
        </div>
        <div class="hero">
            <div class="top">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#instructionsModal">
                    Instructions
                </button>
                <button class="btn btn-info"><a href="student_history.php" class="text-light" style="text-decoration: none;">History</a></button>
            </div>
        </div>

    </div>

    <div class="footer">
        <div class="top"></div>
        <div class="bottom">
            <div class="menu-icon"><a href="student_dashboard.php"><i class="fas fa-home"></i><span>Home</span></a></div>
            <div class="left">
                <a href="transaction_history.php"><i class="fas fa-history"></i><span>Transaction</span></a>
            </div>
            <div class="circle"><a href="student_profile.php"><i class="fas fa-qrcode"></i><span>QR</span></a></div>
            <div class="menu-icon"><a href="student_cashin.php"><i class="fas fa-money-bill"></i><span>Cash In</span></a></div>
            <?php
            $find_pass = "SELECT PASSWORD FROM stud_info WHERE STUD_ID = '$STUD_ID'";
            $finded = mysqli_query($conn, $find_pass);
            while ($row = mysqli_fetch_assoc($finded)) {
                $pass = $row['PASSWORD'];
            }
            ?>
            <div class="right">
                <form action="student_edit_profile.php" method="post">
                    <button type="submit">
                        <i class="fas fa-user-cog">
                        </i>
                        <span>Profile</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script>
        const clickMe = document.querySelector('#clickMe');
        const click = document.querySelector('#click');
        const menuContainer = document.querySelector('.header_menu');

        clickMe.addEventListener('click', () => {
            if (click.classList.contains('fa-bars')) {
                click.classList.remove('fa-bars');
                click.classList.add('fa-close');

                // menu
                menuContainer.classList.add('show');
                menuContainer.classList.remove('hide');
            } else {
                click.classList.add('fa-bars');
                click.classList.remove('fa-close');

                // menu
                menuContainer.classList.remove('show');
                menuContainer.classList.add('hide');
            }
        })

        function hideMe() {
            const eye = document.getElementById("eye");
            const balance = document.getElementById("balance");

            if (balance.innerHTML === '₱....') {
                // Show balance
                balance.innerHTML = '<?php echo "₱ " . number_format($balance, 2); ?>';
                eye.classList.remove('fa-eye-slash'); // Change icon back to eye
                eye.classList.add('fa-eye');
            } else {
                // Hide balance
                balance.innerHTML = '₱....';
                eye.classList.remove('fa-eye'); // Change icon to eye-slash
                eye.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>

</html>