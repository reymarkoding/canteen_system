<?php
include "../conn.php";
session_start();
if (!isset($_SESSION['accounting_status']) || $_SESSION['accounting_status'] !== 'login') {
    header("Location: accounting_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./acc_css/acc_req.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .side-bar {
            width: 200px;
            background-color: var(--light);
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.4);
            display: grid;
            grid-template-rows: 100px 1fr;

            .header-logo {
                width: 100%;
                display: grid;
                place-items: center;

                h3 {
                    color: var(--dark);
                }
            }

            .menu {
                display: flex;
                flex-direction: column;

                span {
                    font-size: 1.3em;
                    font-weight: bold;
                    text-align: center;
                    color: var(--dark);
                    margin-top: 20px;
                    cursor: context-menu;
                }

                .menu-item {
                    box-shadow: 0px 0px 2px rgb(0, 0, 0, 0.1);
                    display: flex;
                    padding: 10px;
                    text-decoration: none;
                    color: var(--dark);
                    text-align: center;

                    a {
                        text-decoration: none;
                        color: var(--dark);
                        flex-grow: 1;
                        display: flex;
                        justify-content: start;
                    }
                }

                .menu-item:hover {
                    a {
                        color: var(--light);
                    }

                    background-color: var(--bg);
                    box-shadow: var(--box-shadow);
                }

                .active {
                    a {
                        color: var(--light);
                    }

                    background-color: var(--bg);
                    box-shadow: var(--box-shadow);
                }
            }
        }

        .main {
            height: 100vh;
            display: grid;
            grid-template-columns: 200px 1fr;
        }

        .header-logo {
            background-image: url(../final_logo.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        #delete_deposit {
            position: fixed;
            text-align: center;
            width: 300px;
            top: 10px;
            right: 75px;
            animation-name: goleft;
            animation-duration: 3500ms;
        }

        @keyframes goleft {
            0% {
                right: -20%;
            }

            25% {
                right: 75px;
            }

            50% {
                right: 75px;
            }

            75% {
                right: 75px;
            }

            100% {
                right: -30%;
            }
        }
    </style>
</head>

<body>
    <?php
    if (isset($_SESSION['delete_deposit'])) {
        echo "<div class='alert alert-warning' id='delete_deposit'>" . $_SESSION['delete_deposit'] . "</div>";
        unset($_SESSION['delete_deposit']);
    }
    ?>
    <div class="main">
        <div class="side-bar">
            <div class="header-logo">

            </div>
            <div class="menu">
                <span>MAIN MENU</span>
                <div class="menu-item">
                    <a href="accounting_dashboard.php">Dashboard</a>
                </div>
                <div class="menu-item">
                    <a href="students.php">Students</a>
                </div>
                <div class="menu-item">
                    <a href="acc_request_deposit.php">Deposit</a>
                </div>
                <div class="menu-item">
                    <a href="history.php">History</a>
                </div>
            </div>
        </div>
        <div class="main-bar">
            <div class="header">
                <h1>DEPOSIT</h1>
            </div>
            <div class="hero p-2">
                <form action="" method="post">
                    <table class="table table-striped">
                        <thead>
                            <th class="bg-warning">ID</th>
                            <th class="bg-warning">Name</th>
                            <th class="bg-warning">Deposit</th>
                            <th class="bg-warning">Date</th>
                            <th class="bg-warning">Time</th>
                            <th class="bg-warning">Action</th>
                        </thead>
                        <tbody>
                            <?php
                            $find = mysqli_query($conn, "SELECT deposit.amount, deposit_id, deposit.STUD_ID, deposit.status, deposit.date, deposit.time, ID_NUMBER,
                            FIRSTNAME, LASTNAME, MIDDLENAME FROM deposit JOIN stud_info ON deposit.STUD_ID = 
                            stud_info.STUD_ID WHERE deposit.status='pending' AND send_to = 'Accounting' ORDER BY deposit.time DESC");
                            while ($row = mysqli_fetch_assoc($find)) {
                                $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'] . " " . $row['MIDDLENAME'][0] . ".";
                                $date = new DateTime($row['date']);
                                $formattedDate = $date->format("M j, Y");
                            ?>
                                <tr>
                                    <td><?= $row['ID_NUMBER']; ?></td>
                                    <td><?= $fn; ?></td>
                                    <td><?= $row['amount']; ?></td>
                                    <td><?= $formattedDate; ?></td>
                                    <td><?= $row['time']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary"
                                            onclick="confirmApproval('<?= $fn; ?>', '<?= $row['deposit_id']; ?>')">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-danger">
                                            <a onclick="confirmDelete(event)" href="approve_deposit.php?reject_id=<?= $row['deposit_id']; ?>" style="color: white; text-decoration: none;">Reject</a>
                                        </button>
                                    </td>
                                </tr>
                            <?php }; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        function confirmApproval(studentName, depositId) {
            const message = `Do you want to approve the cash-in request of student ${studentName}?`;
            if (confirm(message)) {
                // Redirect to approve_deposit.php with the deposit ID
                window.location.href = `approve_deposit.php?approve_id=${depositId}`;
            }
        }

        setTimeout(() => {
            var delete_deposit = document.querySelector("#delete_deposit");
            if (delete_deposit) {
                delete_deposit.style.display = 'none';
                window.location.href = "acc_request_deposit.php";
            }
        }, 3500);

        function confirmDelete(event) {
            if (!confirm("Are you sure you want to reject?")) {
                // if the cancel clicked, then prevent the form submission
                event.preventDefault();
            }
        }
    </script>
</body>

</html>