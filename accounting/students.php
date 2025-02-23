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
    <title>Students List</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        .main {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>

<body>

    <!-- MODAL -->
    <!-- ADD BALANCE -->
    <div class="modal fade" id="addBalanceModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBalanceForm" enctype="multipart/form-data" onsubmit="return confirmAddBalance()">
                    <div class="modal-body">
                        <div id="errorAddBal" class="alert alert-warning d-none">
                            <!-- Error message will go here -->
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="hidden" name="old_stud_id" id="stud_id">
                                <label for="add_balance_amount">Add Balance:</label>
                                <input type="number" name="add_balance_amount" id="add_balance_amount" class="form-control" placeholder="Enter amount">
                                <small class="text-muted d-block mt-2">
                                    <strong>Note:</strong> Please ensure the amount entered is correct as this will be added to the student's account balance.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitAddBalance" class="btn btn-primary" disabled>Add Balance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main p-2">
        <button class="btn btn-danger">
            <a href="accounting_dashboard.php" class="text-light" style="text-decoration: none;">Back</a>
        </button>
        <table class="table table-striped" id="bal_Tbl">
            <thead>
                <tr>
                    <th style="width: 5%;">ID NUMBER</th>
                    <th style="width: 10%;">NAME</th>
                    <th style="width: 8%;">BALANCE</th>
                    <th style="width: 8%;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = mysqli_query($conn, "SELECT * FROM stud_info ORDER BY LASTNAME ASC");
                while ($row = mysqli_fetch_assoc($sql)) {
                    // Build the full name with a check for the middle name
                    $fn = $row['LASTNAME'] . ", " . $row['FIRSTNAME'];
                    if (!empty($row['MIDDLENAME'])) {
                        $fn .= " " . $row['MIDDLENAME'][0] . ".";
                    }
                    $s_id = $row['STUD_ID'];
                ?>
                    <tr>
                        <td><?= $row['ID_NUMBER'] ?></td>
                        <td><?= $fn ?></td>
                        <td>₱ <?= number_format($row['BALANCE'], 2) ?></td>
                        <td>
                            <button type="button" value="<?= $s_id; ?>" class="addBalanceBtn btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBalanceModal">
                                <i class="fas fa-plus"></i>
                            </button>
                            <!-- <button type="button" value="<?= $s_id; ?>" class="minusBalanceBtn btn btn-danger" data-bs-toggle="modal" data-bs-target="#minusBalanceModal">
                        <i class="fas fa-minus"></i>
                    </button> -->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>



    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        const balanceInput = document.getElementById('add_balance_amount');
        const submitButton = document.getElementById('submitAddBalance');

        balanceInput.addEventListener('input', function() {
            const value = parseFloat(balanceInput.value);
            if (value >= 100) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        });
    </script>
    <script>
        // red dots
        $(document).ready(function() {
            function checkCashInRequests() {
                $.ajax({
                    url: 'acc_notif.php', // PHP script to check for pending requests
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.pending) {
                            // Show the notification dot if there are pending requests
                            $('#cashInNotifDot').removeClass('d-none');
                        } else {
                            // Hide the notification dot if no pending requests
                            $('#cashInNotifDot').addClass('d-none');
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch cash-in request status.');
                    }
                });
            }

            // Run the check every 5 seconds (5000 ms)
            setInterval(checkCashInRequests, 5000);

            // Initial check on page load
            checkCashInRequests();
        });
    </script>
    <script>
        $(document).on('click', '.addBalanceBtn', function() {
            var stud_id = $(this).val();
            // alert(stud_id)
            $.ajax({
                type: "GET",
                url: "add_balance.php?stud_id=" + stud_id,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 422) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#stud_id').val(res.data.STUD_ID);

                        $('#editProdModal').modal('show');
                    }
                }
            });
        });

        $(document).on('submit', '#addBalanceForm', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('addBalanceForm', true);

            $.ajax({
                type: "POST",
                url: "add_balance.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 200) {
                        // Close the modal
                        $('#addBalanceModal').modal('hide');

                        // Reload the page
                        location.reload();
                    } else if (res.status == 500) {
                        // Display the error message
                        $('#errorAddBal').removeClass('d-none');
                        $('#errorAddBal').text(res.message);
                    }
                }
            });
        });
    </script>
</body>

</html>