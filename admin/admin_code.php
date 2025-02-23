<?php
include "../conn.php";
session_start();

if (isset($_GET['product_id'])) {

    $prod_id = mysqli_real_escape_string($conn, $_GET['product_id']);

    $query = "SELECT * FROM products WHERE product_id = '$prod_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $products = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Products fetch successfully',
            'data' => $products
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 422,
            'message' => 'Products ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}

// if (isset($_GET['product_id'])) {
//     $prod_id = mysqli_real_escape_string($conn, $_GET['product_id']);

//     $query = "SELECT * FROM products WHERE product_id = '$prod_id'";
//     $query_run = mysqli_query($conn, $query);

//     if (mysqli_num_rows($query_run) == 1) {
//         $product = mysqli_fetch_array($query_run);

//         $res = [
//             'status' => 200,
//             'message' => 'Products fetch successfully',
//             'data' => $product
//         ];
//         echo json_encode($res);
//         return false;
//     } else {
//         $res = [
//             'status' => 404,
//             'message' => 'Products ID not found'
//         ];
//         echo json_encode($res);
//         return false;
//     }
// }

if (isset($_POST['edit_prod'])) {
    $prod_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $prod_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $p_name = mysqli_real_escape_string($conn, $_POST['product_name']);

    $query = "UPDATE `products` SET `product_name`='$p_name', `product_price`='$prod_price' WHERE product_id = '$prod_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        $res = [
            'status' => 200,
            'message' => 'Product Updated successfully.'
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'Product Not Update.'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['add_quantity'])) {
    $prod_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $p_add_quantity = mysqli_real_escape_string($conn, $_POST['product_add_quantity']);

    $query = "UPDATE `products` SET `product_quantity`= product_quantity + $p_add_quantity, `remaining_quantity`=remaining_quantity + $p_add_quantity WHERE product_id = '$prod_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {

        $res = [
            'status' => 200,
            'message' => 'Quantity Added.'
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'There is a problem with adding quantity'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['damageForm'])) {
    $prod_id = mysqli_real_escape_string($conn, $_POST['damage_product_id']);
    $p_name = mysqli_real_escape_string($conn, $_POST['damage_product_name']);
    $p_add_damage = mysqli_real_escape_string($conn, $_POST['product_add_damage']);

    // First query: Update product quantities
    $query = "UPDATE `products` 
              SET `product_quantity` = `product_quantity` - $p_add_damage, 
                  `remaining_quantity` = `remaining_quantity` - $p_add_damage 
              WHERE `product_id` = '$prod_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        // Second query: Insert into damage_product table
        $insert = "INSERT INTO `damage_product`(`product_id`, `product_name`, `quantity`, `date`) 
                   VALUES ('$prod_id', '$p_name', '$p_add_damage',NOW())";
        $insert_run = mysqli_query($conn, $insert);

        if ($insert_run) {
            $res = [
                'status' => 200,
                'message' => 'Damage Added.'
            ];
        } else {
            $res = [
                'status' => 500,
                'message' => 'Failed to insert into damage_product.'
            ];
        }
    } else {
        $res = [
            'status' => 500,
            'message' => 'Failed to update product quantities.'
        ];
    }

    echo json_encode($res);
    return false;
}

// BUY HISTORY
if (isset($_GET['buy_id'])) {

    $buy_id = mysqli_real_escape_string($conn, $_GET['buy_id']);

    $query = "SELECT * FROM buy WHERE buy_id = '$buy_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $buy = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Buy fetch successfully',
            'data' => $buy
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 422,
            'message' => 'Buy ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['returnForm'])) {
    $buy_id = mysqli_real_escape_string($conn, $_POST['return_buy_id']);
    $product_id = mysqli_real_escape_string($conn, $_POST['return_product_id']);
    $product_add_return = mysqli_real_escape_string($conn, $_POST['product_add_return']);
    $return_issue = mysqli_real_escape_string($conn, $_POST['return_issue']);
    $return_product_name = mysqli_real_escape_string($conn, $_POST['return_product_name']);
    $return_product_quantity = mysqli_real_escape_string($conn, $_POST['return_product_quantity']);

    $find = mysqli_query($conn, "SELECT * FROM buy WHERE buy_id = '$buy_id'");
    $row = mysqli_fetch_assoc($find);
    $price = $row['product_price'];

    $total_sum = $product_add_return * $price;

    if ($product_add_return > $return_product_quantity) {
        $res = [
            'status' => 400,
            'message' => 'Insufficient quantity to process the return.'
        ];
        echo json_encode($res);
        return false;
    }

    if ($return_issue === 'replace') {
        $query = "UPDATE `buy` SET `total_quantity`= total_quantity - $product_add_return, `total` = total - $total_sum  WHERE buy_id = '$buy_id'";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            $updateStock = mysqli_query($conn, "UPDATE `products` SET product_quantity = product_quantity + $product_add_return, `remaining_quantity` = `remaining_quantity` + $product_add_return WHERE product_id = '$product_id'");
            if ($updateStock) {

                $res = [
                    'status' => 200,
                    'message' => 'Return successful.'
                ];
            } else {
                $res = [
                    'status' => 500,
                    'message' => 'There is a problem with returning the product.'
                ];
            }
        } else {
            $res = [
                'status' => 500,
                'message' => 'There is a problem with returning the product.'
            ];
        }

        echo json_encode($res);
        return false;
    } else if ($return_issue === 'damage') {

        $query = "UPDATE `buy` SET `total_quantity`= total_quantity - $product_add_return, `total` = total - $total_sum  WHERE buy_id = '$buy_id'";
        $query_run = mysqli_query($conn, $query);

        if ($query_run) {
            $insertDamage = mysqli_query($conn, "INSERT INTO `damage_product`(`product_id`, `product_name`, `quantity`, `date`) VALUES ('$product_id','$return_product_name','$product_add_return',NOW())");
            if ($insertDamage) {

                $res = [
                    'status' => 200,
                    'message' => 'Add to Damage successfully.'
                ];
            } else {
                $res = [
                    'status' => 500,
                    'message' => 'There is a problem with returning the product for damage.'
                ];
            }
        } else {
            $res = [
                'status' => 500,
                'message' => 'There is a problem with returning the product for damage.'
            ];
        }

        echo json_encode($res);
        return false;
    }
}

// if (isset($_POST['returnForm'])) {
//     $buy_id = mysqli_real_escape_string($conn, $_POST['return_buy_id']);
//     $product_id = mysqli_real_escape_string($conn, $_POST['return_product_id']);
//     $product_add_return = mysqli_real_escape_string($conn, $_POST['product_add_return']);
//     $return_issue = mysqli_real_escape_string($conn, $_POST['return_issue']);
//     $return_product_name = mysqli_real_escape_string($conn, $_POST['return_product_name']);

//     $find = mysqli_query($conn, "SELECT * FROM buy WHERE buy_id = '$buy_id'");
//     $row = mysqli_fetch_assoc($find);

//     if (!$row) {
//         echo json_encode([
//             'status' => 404,
//             'message' => 'Purchase record not found.'
//         ]);
//         return false;
//     }

//     $price = $row['product_price'];
//     $total_sum = $product_add_return * $price;

//     // Check if sufficient quantity exists
//     if ($row['total_quantity'] < $product_add_return) {
//         echo json_encode([
//             'status' => 400,
//             'message' => 'Insufficient quantity to process the return.'
//         ]);
//         return false;
//     }

//     // Update the `buy` table
//     $query = "UPDATE `buy` SET `total_quantity` = total_quantity - $product_add_return, `total` = total - $total_sum WHERE buy_id = '$buy_id'";
//     if (mysqli_query($conn, $query)) {
//         if ($return_issue === 'replace') {
//             // Update stock for replacement
//             $updateStock = mysqli_query($conn, "UPDATE `products` SET `product_quantity` = product_quantity + $product_add_return, `remaining_quantity` = `remaining_quantity` + $product_add_return WHERE product_id = '$product_id'");

//             if ($updateStock) {
//                 echo json_encode([
//                     'status' => 200,
//                     'message' => 'Replacement successful.'
//                 ]);
//             } else {
//                 echo json_encode([
//                     'status' => 500,
//                     'message' => 'Failed to update stock for replacement.'
//                 ]);
//             }
//         } else if ($return_issue === 'damage') {
//             // Insert into `damage_product` for damaged items
//             $insertDamage = mysqli_query($conn, "INSERT INTO `damage_product` (`product_id`, `product_name`, `quantity`, `date`) VALUES ('$product_id', '$return_product_name', '$product_add_return', NOW())");

//             if ($insertDamage) {
//                 echo json_encode([
//                     'status' => 200,
//                     'message' => 'Damage return recorded successfully.'
//                 ]);
//             } else {
//                 echo json_encode([
//                     'status' => 500,
//                     'message' => 'Failed to record damaged product.'
//                 ]);
//             }
//         }
//     } else {
//         echo json_encode([
//             'status' => 500,
//             'message' => 'Failed to update purchase record.'
//         ]);
//     }
// }
