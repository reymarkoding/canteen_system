<?php
include "../conn.php";
session_start();


if (isset($_GET['category_id'])) {

    $category_id = mysqli_real_escape_string($conn, $_GET['category_id']);

    $query = "SELECT * FROM categories WHERE category_id = '$category_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $category = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Category fetch successfully',
            'data' => $category
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 422,
            'message' => 'Category ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['edit_cat'])) {

    // Sanitize input values
    $cat_id = $_POST['category_id'];
    $new_category_name = mysqli_real_escape_string($conn, $_POST['new_category_name']);

    $query = "UPDATE `categories` SET `category`= '$new_category_name' WHERE category_id = '$cat_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Category edited successfully'
        ];
    } else {
        $res = [
            'status' => 500,
            'message' => 'Error'
        ];
    }

    echo json_encode($res);
    exit; // Ensure no further PHP processing
}
