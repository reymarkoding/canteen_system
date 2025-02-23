<?php

include "../conn.php";
session_start();

// $student_id = $_SESSION['stud_id'];

// CHECKOUT PRODUCT
if (isset($_POST['checkout'])) {

    $p_id = mysqli_real_escape_string($conn, $_POST['checkout_product_id']);
    $p_name = mysqli_real_escape_string($conn, $_POST['checkout_product_name']);
    $p_price = mysqli_real_escape_string($conn, $_POST['checkout_product_price']);
    $checkout_remaining_quantity = mysqli_real_escape_string($conn, $_POST['checkout_remaining_quantity']);
    $total_quantity = mysqli_real_escape_string($conn, $_POST['checkout_total_quantity']);
    $total = $p_price * $total_quantity;

    if (empty($total_quantity)) {
        $res = [
            'status' => 422,
            'message' => 'Total Quantity is required'
        ];
        echo json_encode($res);
        return false;
    }

    // Check if the product name already exists in the cart
    $check_query = "SELECT * FROM `cart` WHERE `product_id` = '$p_id'";
    $check_query_run = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_query_run) > 0) {
        $res = [
            'status' => 409,
            'message' => 'Product is already in the cart.'
        ];
        echo json_encode($res);
        return false;
    }

    $query = "INSERT INTO `cart`(`product_id`, `product_name`, `total_quantity`, `product_price`,`remaining_quantity`, `total`)
              VALUES ('$p_id','$p_name','$total_quantity','$p_price','$checkout_remaining_quantity','$total')";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Added to cart'
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'Error occurred while adding to cart'
        ];
        echo json_encode($res);
        return false;
    }
}


// TO VIEW INFORMATION FOR MODAL
if (isset($_GET['product_id'])) {
    $prod_id = mysqli_real_escape_string($conn, $_GET['product_id']);

    $query = "SELECT * FROM products WHERE product_id = '$prod_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $product = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Student fetch successfully',
            'data' => $product
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 404,
            'message' => 'Student ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}

// FOR CASH INPUT

// if (isset($_GET['buyall'])) {
//     $sql = "SELECT * FROM `cart` WHERE `STUD_ID` = 0";
//     $result = $conn->query($sql);
//     $row = $result->fetch_array();
//     $product_id = $row['product_id'];
//     $STUD_ID = $row['STUD_ID'];
//     $product_name = $row['product_name'];
//     $total_quantity = $row['total_quantity'];
//     $product_price = $row['product_price'];
//     $total = $row['total'];




//     $insertbuy = "INSERT INTO `buy`( `product_id`, `STUD_ID`, `product_name`, `total_quantity`, `product_price`, `total`, `status`, `buy_date`) VALUES ('$product_id','$STUD_ID','$product_name','$total_quantity','$product_price','$total','Approved',NOW())";
//     $insertbuyquery = $conn->query($insertbuy);
//     if ($result) {
//         $deleteCart = mysqli_query($conn, "DELETE FROM `cart` WHERE STUD_ID = '$STUD_ID'");
//     } else {
//         echo "1";
//     }
// }
