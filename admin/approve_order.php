<?php
include "../conn.php";
session_start();

$student_id = $_GET['approve_id'];

if (isset($_POST['approve'])) {
    $id_number = $_POST['id_number'];
    $total_amount = 0;

    // Fetch all pending orders for the student with the correct ID
    $ordersQuery = "SELECT buy.buy_id, SUM(buy.total) as total_payment, buy.product_id, 
                    stud_info.BALANCE, stud_info.QRCODE, stud_info.ID_NUMBER as stud_ID,
                    SUM(buy.total_quantity) as prod_total_quantity 
                    FROM buy 
                    JOIN stud_info ON buy.STUD_ID = stud_info.STUD_ID 
                    WHERE buy.STUD_ID = '$student_id' AND buy.status = 'pending'";
                    
    $ordersResult = mysqli_query($conn, $ordersQuery);

    $quantityQuery = "SELECT SUM(buy.total_quantity) as prod_total_quantity, products.product_quantity
    FROM buy 
    JOIN products ON buy.product_id = products.product_id 
    WHERE buy.STUD_ID = '$student_id' AND buy.status = 'pending'";
    
    $quantityResult = mysqli_query($conn, $quantityQuery);
    $productData = mysqli_fetch_assoc($quantityResult);
    $t_prod_quantity = $productData['prod_total_quantity'];
    $product_quantity = $productData['product_quantity'];

    // Verify the QR code before processing
    $studentData = mysqli_fetch_assoc($ordersResult);
    $stud_ID = $studentData['stud_ID']; // Student's ID from the database
    $stud_Bal = $studentData['BALANCE'];
    $total_payment = $studentData['total_payment'];

    if ($id_number != $stud_ID) {
        echo "<script>alert('You do not own this order!');</script>";
    } elseif($stud_Bal < $total_payment){
        echo "<script>alert('Balance is insufficient for the payment!');</script>";
    } elseif($t_prod_quantity > $product_quantity){
        echo "<script>alert('Low stock level');</script>";
    }
    else {
        // Reset pointer to start looping through each order
        mysqli_data_seek($ordersResult, 0);
        mysqli_data_seek($quantityResult, 0);

        // Process each order individually
        while ($order = mysqli_fetch_assoc($ordersResult)) {
            $order_id = $order['buy_id'];
            $product_id = $order['product_id'];
            $total = $order['total_payment'];
            $prod_total_quantity = $order['prod_total_quantity'];

            // Insert each order as a separate receipt
            $receiptQuery = "INSERT INTO receipts (STUD_ID, order_id, total_amount, transaction_date)
                             VALUES ('$student_id', '$order_id', '$total', NOW())";
            mysqli_query($conn, $receiptQuery);

            // Update each order status to 'paid'
            $updateOrderQuery = "UPDATE buy SET status = 'paid' WHERE buy_id = '$order_id'";
            mysqli_query($conn, $updateOrderQuery);

            $updateProductQuery = "UPDATE products SET product_quantity = product_quantity - $prod_total_quantity WHERE product_id = '$product_id'";
            mysqli_query($conn, $updateProductQuery);
        }

        // Update student balance after calculating the total
        $balance_update_query = "UPDATE stud_info SET BALANCE = BALANCE - $total WHERE STUD_ID = '$student_id'";
        mysqli_query($conn, $balance_update_query);

        echo "<script>alert('Orders approved and receipts created successfully!');
                            window.location.href='admin_orders.php'</script>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./admin_css/approve_order.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <script type="text/javascript" src="../js/adapter.min.js"></script>
    <script type="text/javascript" src="../js/vue.min.js"></script>
    <script type="text/javascript" src="../js/instascan.min.js"></script>
</head>
<body>
    <div class="main">
            <div class="side-bar">
                <div class="header-logo">
                    <h3>Scan</h3>
                </div>
                <div class="menu">
                    <span>MAIN MENU</span>
                    <div class="menu-item">
                        <a href="admin_dashboard.php">Dashboard</a>
                    </div>
                    <div class="menu-item">
                        <a href="admin_category.php">Category</a>
                    </div>
                    <div class="menu-item">
                        <a href="admin_purchase.php">Purchase</a>
                    </div>
                    <div class="menu-item">
                        <a href="admin_product.php">Product</a>
                    </div>
                    <div class="menu-item">
                        <a href="admin_orders.php">Orders</a>
                    </div>
                    <div class="menu-item">
                        <a href="">Users</a>
                    </div>
                    <div class="menu-item">
                        <a href="">Inventory</a>
                    </div>
                    <div class="menu-item">
                        <a href="">Logout</a>
                    </div>
                </div>
            </div>
            <div class="main-bar">
                <div class="scan-box p-2">
                    <form action="" method="post">
                        <div class="header">
                            <h3>Scan here</h3>
                        </div>
                        <div class="camera">
                            <video src="" id="preview" style="height: 300px;width: 100%;object-fit: cover;transform: rotate(90deg);"></video>
                        </div>
                        <div class="submit">
                            <input type="hidden" class="form-control" name="qrcode" id="qrcode" placeholder="Full Name">
                            <input type="hidden" class="form-control" name="id_number" id="id_number" placeholder="Full Name">
                            <input type="text" readonly class="form-control" name="name" id="name" placeholder="Full Name">
                        </div>
                        <div class="submit">
                            <button class="btn btn-success col-12 mt-2" name="approve" type="submit">OK</button>
                        </div>
                    </form>
                </div>
            </div>
    </div>



    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });

        Instascan.Camera.getCameras().then(function (cameras) {
            if (cameras.length > 0) {
                // Default to the first camera
                let selectedCamera = cameras[0];
                
                // Try to select the back camera if it exists
                cameras.forEach(camera => {
                    if (camera.name.toLowerCase().includes('back')) {
                        selectedCamera = camera;
                    }
                });

                scanner.start(selectedCamera);
            } else {
                console.error('No cameras found');
            }
        }).catch(function (e) {
            console.error(e);
        });

        scanner.addListener('scan', function (content) {
            try {
                let val = JSON.parse(content);
                document.getElementById('qrcode').value = content;
                document.getElementById('id_number').value = val.id_number;
                document.getElementById('name').value = val.name;
            } catch (e) {
                alert('Student ID only');
            }
        });
    </script>
</body>
</html>