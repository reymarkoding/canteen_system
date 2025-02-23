<?php

include "../conn.php";
session_start();

$ID_NUMBER = $_SESSION['ID_NUMBER'];
$STUD_ID = $_SESSION['stud_id'];
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
    <title>CashIn</title>
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/bootstrap.css">
    <link rel="stylesheet" href="./css/student_dashboard.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script src="./css/css/js/bootstrap.bundle.js"></script>
    <style>
        .deposit-box {
            height: 60vh;
            width: auto;
            border-radius: 10px;
            box-shadow: var(--box-shadow);
        }

        .deposit-box form {
            height: auto;
            display: grid;
            grid-template-rows: 50px 100px 1fr 50px;
        }

        .deposit-box form :nth-child(3) {
            display: grid;
            grid-template-rows: 1fr 1fr;
            grid-template-columns: 1fr 1fr;
            gap: 5%;
        }
    </style>
</head>

<body>
    <div class="main_header">
        <div class="title">
            <center>
                <h4>Cash In</h4>
            </center>
        </div>

    </div>
    <div class="main px-2">
        <div class="deposit-box p-2">
            <?php
            if (isset($_SESSION['success_deposit'])) {
                echo "<div class='alert alert-primary' id='success_deposit'>" . $_SESSION['success_deposit'] . "</div>";
                unset($_SESSION['success_deposit']);
            }
            if (isset($_SESSION['no_amount'])) {
                echo "<div class='alert alert-danger' id='success_deposit'>" . $_SESSION['no_amount'] . "</div>";
                unset($_SESSION['no_amount']);
            }
            ?>
            <small id="amountAlert" style="color: red; display: none;">Amount must be greater than or equal to ₱100.</small>
            <form action="cashin.php" method="post" onsubmit="confirmSubmission(event)">
                <div class="deposit-item">
                    <input type="hidden" name="STUD_ID" value="<?= $STUD_ID; ?>">
                    <center><input readonly type="text" name="id_number" style="border: none; width: auto;" class="form-control" value="<?= $ID_NUMBER; ?>"></center>
                </div>
                <div class="deposit-item">
                    <center><label for="">
                            <h5>Specify Amount for Cash Deposit</h5>
                        </label></center>
                    <div class="input-container" style="position: relative;">
                        <input type="number" class="form-control" name="amount" placeholder="Enter amount" id="depositAmount" oninput="checkAmount()">
                    </div>
                </div>
                <div class="deposit-item p-2">
                    <input type="button" value="100" onclick="setAmount(100)">
                    <input type="button" value="200" onclick="setAmount(200)">
                    <input type="button" value="500" onclick="setAmount(500)">
                    <input type="button" value="1000" onclick="setAmount(1000)">
                </div>
                <div class="send_to">
                    <select required name="send_to" id="" class="form-control">
                        <option value="">Send To:</option>
                        <option value="Accounting">Accounting</option>
                        <option value="Canteen">Canteen</option>
                    </select>
                </div>
                <div class="deposit-item">
                    <button type="submit" onclick="confirmDeposit()" name="deposit" style="width: 100%;" class="btn btn-success">Send</button>
                </div>
            </form>
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

    <script>
        function checkAmount() {
            const amountInput = document.getElementById('depositAmount');
            const alertMessage = document.getElementById('amountAlert');

            // Check if the input value is empty or less than 100
            if (amountInput.value === "" || amountInput.value < 100) {
                alertMessage.style.display = 'inline'; // Show alert if value is less than 100 or empty
            } else {
                alertMessage.style.display = 'none'; // Hide alert if value is 100 or greater
            }
        }


        function setAmount(value) {
            document.getElementById('depositAmount').value = value;
        }

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

        setTimeout(() => {
            var success_deposit = document.querySelector("#success_deposit");
            if (success_deposit) {
                success_deposit.style.display = 'none';
                window.location.href = "student_cashin.php";
            }
        }, 3500);

        function confirmSubmission(event) {
            if (!confirm("Are you sure you want to deposit the cash?")) {
                // if the cancel clicked, then prevent the form submission
                event.preventDefault();
            }
        }
    </script>
</body>

</html>