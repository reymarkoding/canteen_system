<?php

include "../conn.php";
session_start();

// $student_id = $_SESSION['stud_id'];

// CHECKOUT PRODUCT
if (isset($_POST['editCartForm'])) {

    $c_id = mysqli_real_escape_string($conn, $_POST['editCart_cart_id']);
    $p_price = mysqli_real_escape_string($conn, $_POST['editCart_product_price']);
    $new_total_quantity = mysqli_real_escape_string($conn, $_POST['editCart_total_quantity']);
    $total = $p_price * $new_total_quantity;


    if (empty($new_total_quantity)) {
        $res = [
            'status' => 422,
            'message' => 'Total Quantity is required'
        ];
        echo json_encode($res);
        return false;
    }

    $query = "UPDATE `cart` SET `total_quantity`='$new_total_quantity', total = '$total' WHERE cart_id = '$c_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Edit cart'
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'error'
        ];
        echo json_encode($res);
        return false;
    }
}

// TO VIEW INFORMATION FOR MODAL
if (isset($_GET['cart_id'])) {
    $cart_id = mysqli_real_escape_string($conn, $_GET['cart_id']);

    $query = "SELECT * FROM cart WHERE cart_id = '$cart_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $product = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Cart fetch successfully',
            'data' => $product
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 404,
            'message' => 'Cart ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}
