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
    <title>Product Damage Lists</title>
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
    <div class="main p-5">
        <nav class="navbar navbar-light bg-light">
            <form class="container-fluid" method="GET">
                <div class="buttons">
                    <button class="btn btn-primary me-2" type="button"><a href="admin_product.php" style="text-decoration: none; color: white;">Back</a></button>
                </div>
                <!-- <div class="search">
                    <label for="">
                        <h5>Date</h5>
                    </label>
                    <input type="date" name="date_filter" value="<?= $date_filter ?>" style="width: 100%; margin: 0px 10px;" class="form-control" id="date_filter">
                    <input type="submit" class="btn btn-info" value="Filter">
                </div> -->
            </form>
        </nav>

        <div class="tbl_cashin">
            <center>
                <h1>DAMAGE PRODUCTS</h1>
            </center>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Use the date filter to display records for the selected date
                    $sql = mysqli_query($conn, "SELECT * FROM damage_product");

                    // Loop through the results and display each product
                    while ($row = mysqli_fetch_assoc($sql)) {

                    ?>
                        <tr>
                            <td><?= $row['product_name']; ?></td>
                            <td><?= $row['quantity']; ?></td>
                            <td><?= $row['date']; ?></td>
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
        function Export() {
            const conf = confirm("Please confirm if you wish to proceed exporting the inventory of products?");
            if (conf) {
                window.open("export_product_inventory.php", '_blank');
            }
        }
    </script>
</body>

</html>