<?php

include "../conn.php";
session_start();

$student_id = $_SESSION['stud_id'];

// ADD TO CART
if(isset($_POST['add_cart'])){

    $p_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $p_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $total_quantity = mysqli_real_escape_string($conn, $_POST['total_quantity']);
    $total = $p_price * $total_quantity;

    if(empty($total_quantity)){
        $res = [
            'status' => 422,
            'message' => 'Total Quantity is required'
        ];
        echo json_encode($res);
        return false;
    }

    $query = "INSERT INTO `cart`(`product_id`, `STUD_ID`, `product_name`, `total_quantity`, `product_price`, `total`)
            VALUES ('$p_id','$student_id','$p_name','$total_quantity','$p_price','$total')";
    $query_run = mysqli_query($conn, $query);

    if($query_run){
        $res = [
            'status' => 200,
            'message' => 'Added to cart'
        ];
        echo json_encode($res);
        return false;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'error'
        ];
        echo json_encode($res);
        return false;
    }

}

// BUY PRODUCT
if(isset($_POST['buy_product'])){

    $b_p_id = mysqli_real_escape_string($conn, $_POST['buy_product_id']);
    $b_p_name = mysqli_real_escape_string($conn, $_POST['buy_product_name']);
    $b_p_price = mysqli_real_escape_string($conn, $_POST['buy_product_price']);
    $b_total_quantity = mysqli_real_escape_string($conn, $_POST['buy_total_quantity']);
    $b_total = $b_p_price * $b_total_quantity;

    if(empty($b_total_quantity)){
        $res = [
            'status' => 422,
            'message' => 'Total Quantity is required'
        ];
        echo json_encode($res);
        return false;
    }

    $query = "INSERT INTO `buy`(`product_id`, `STUD_ID`, `product_name`, `total_quantity`, `product_price`, `total`, `status`)
             VALUES ('$b_p_id','$student_id','$b_p_name','$b_total_quantity','$b_p_price','$b_total','pending')";
    $query_run = mysqli_query($conn, $query);

    if($query_run){
        $res = [
            'status' => 200,
            'message' => 'Place ordered'
        ];
        echo json_encode($res);
        return false;
    }
    else
    {
        $res = [
            'status' => 500,
            'message' => 'error'
        ];
        echo json_encode($res);
        return false;
    }

}


// ADD PRODUCT
if(isset($_GET['product_id'])){
    $prod_id = mysqli_real_escape_string($conn, $_GET['product_id']);

    $query = "SELECT * FROM products WHERE product_id = '$prod_id'";
    $query_run = mysqli_query($conn, $query);

    if(mysqli_num_rows($query_run) == 1){
        $product = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Student fetch successfully',
            'data' => $product
        ];
        echo json_encode($res);
        return false;
    }
    else{
        $res = [
            'status' => 404,
            'message' => 'Student ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}


?>