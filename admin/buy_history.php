<?php
include "../conn.php";
session_start();
if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Lists</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .main {
            height: 100vh;
            display: grid;
            grid-template-rows: 50px 1fr;
        }

        * {
            padding: 0;
            margin: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .navbar .container_fluid {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .search {
            grid-column: 2/3;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
    </style>
</head>

<body>
    <!-- RETURN MODAL -->
    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Return Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="returnForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="errorDamage" class="alert alert-warning d-none"></div>
                        <div class="row">
                            <input type="hidden" class="form-control" name="return_buy_id" id="return_buy_id">
                            <input type="hidden" class="form-control" name="return_product_id" id="return_product_id">
                            <div class="mb-3">
                                <label for="">Reason?</label>
                                <select required name="return_issue" id="" class="form-select">
                                    <option value="">Select Option</option>
                                    <option value="damage">Damage Product</option>
                                    <option value="replace">Replace Product</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Buyer Name:</label>
                                <input readonly type="text" class="form-control" name="return_buyer_name" id="return_buyer_name">
                            </div>
                            <div class="mb-3">
                                <label for="">Product Name:</label>
                                <input readonly type="text" class="form-control" name="return_product_name" id="return_product_name">
                            </div>
                            <div class="mb-3">
                                <label for="">Quantity:</label>
                                <input readonly type="number" class="form-control" name="return_product_quantity" id="return_product_quantity">
                            </div>
                            <div class="mb-3">
                                <label for="">Return Quantity:</label>
                                <input type="number" class="form-control" name="product_add_return" id="return_quantity">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Return</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <div class="main p-5">
        <nav class="navbar navbar-light bg-light">
            <form class="container-fluid" method="GET">
                <div class="buttons">
                    <button class="btn btn-primary me-2" type="button"><a href="admin_dashboard.php" style="text-decoration: none; color: white;">Back</a></button>
                </div>
            </form>
        </nav>

        <div class="tbl_cashin">
            <div id="return_succ" class="alert alert-primary d-none">

            </div>
            <center>
                <h1>BUY HISTORY</h1>
            </center>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Use the date filter to display records for the selected date
                    $sql = mysqli_query($conn, "SELECT * FROM buy WHERE status = 'paid' ORDER BY buy_id DESC");

                    // Loop through the results and display each product
                    while ($row = mysqli_fetch_assoc($sql)) {
                        $buy_id = $row['buy_id'];

                    ?>
                        <tr>
                            <td><?= $row['name']; ?></td>
                            <td><?= $row['product_name']; ?></td>
                            <td><?= $row['product_price']; ?></td>
                            <td><?= $row['total_quantity']; ?></td>
                            <td><?= "₱ " . number_format($row['total'], 2); ?></td>
                            <td><?= $row['buy_date']; ?></td>
                            <td>
                                <button type="button" value="<?= $buy_id ?>" class="returnBtn btn btn-secondary" data-bs-toggle="modal" data-bs-target="#returnModal">
                                    <i class="fas fa-solid fa-arrow-rotate-left"></i> <span>Return</span>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script src="js/qrcode.min.js"></script>
    <script>
        $(document).on('click', '.returnBtn', function() {
            var buy_id = $(this).val();
            // alert(buy_id);
            $.ajax({
                type: "GET",
                url: "admin_code.php?buy_id=" + buy_id,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 422) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#return_buy_id').val(res.data.buy_id);
                        $('#return_product_id').val(res.data.product_id);
                        $('#return_buyer_name').val(res.data.name);
                        $('#return_product_name').val(res.data.product_name);
                        $('#return_product_quantity').val(res.data.total_quantity);

                        $('#returnModal').modal('show');
                    }
                }
            });
        });

        $(document).on('submit', '#returnForm', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('returnForm', true);

            $.ajax({
                type: "POST",
                url: "admin_code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 400) {
                        $('#errorDamage').removeClass('d-none');
                        $('#errorDamage').text(res.message);
                    } else if (res.status == 200) {
                        $('#return_succ').removeClass('d-none');
                        $('#return_succ').text(res.message);
                        // $('#errorDamage').addClass('d-none');
                        $('#returnModal').modal('hide');
                        $('#returnBtn')[0].reset();

                        setTimeout(() => {
                            var success = document.querySelector("#return_succ");
                            if (success) {
                                success.style.display = 'none';
                                window.location.href = "buy_history.php";
                            }
                        }, 3000);
                    }
                }
            });
        });
    </script>
</body>

</html>