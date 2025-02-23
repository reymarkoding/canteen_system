<!-- FINAL -->
<?php
include "../conn.php";
session_start();


if (isset($_POST['cashLess'])) {
    $id_number = $_POST['id_number'];
    $balance_from_form = $_POST['balance'];

    if (!$_POST['cart_ids']) {
        $_SESSION['no_purchase'] = "No purchase available.";
        header("location: admin_purchase.php");
        exit();
    }

    // Check if the ID number is empty
    if (empty($id_number)) {
        $_SESSION['empty_id'] = "Student ID number is required.";
        header("location: admin_purchase.php");
        exit();
    }

    // Check if ID_NUMBER exists in the database and get the balance
    $query = mysqli_query($conn, "SELECT STUD_ID, BALANCE, FIRSTNAME FROM stud_info WHERE ID_NUMBER = '$id_number'");
    $row = mysqli_fetch_assoc($query);

    if (!$row) { // If no result is found, show error
        $_SESSION['error'] = "ID Number not found. Please enter a valid ID number.";
        header("location: admin_purchase.php");
        exit();
    } else {
        $STUD_ID = $row['STUD_ID'];
        $f_name = $row['FIRSTNAME'];
        $balance_from_database = $row['BALANCE'];

        // Check if the balance from the form matches the balance in the database
        if ($balance_from_form != $balance_from_database) {
            $_SESSION['error'] = "The balance does not match the database. Please check your account.";
            header("location: admin_purchase.php");
            exit();
        }

        $total_amount = 0; // Variable to accumulate the total amount

        // Calculate the total amount for all items in the cart
        foreach ($_POST['cart_ids'] as $cart_id) {
            $product_query = mysqli_query($conn, "SELECT total_quantity, product_price FROM cart WHERE cart_id = $cart_id");
            $product = mysqli_fetch_assoc($product_query);

            if ($product) {
                $prod_total_quantity = $product['total_quantity'];
                $product_price = $product['product_price'];

                // Add to total amount
                $total_amount += $prod_total_quantity * $product_price;
            }
        }

        // Validate balance against the total amount
        if ($total_amount > $balance_from_database) {
            $_SESSION['insufficient_balance'] = "Insufficient balance. Please top up your account.";
            header("location: admin_purchase.php");
            exit();
        }

        // If the balance is sufficient, proceed with the purchase
        $all_success = true;

        foreach ($_POST['cart_ids'] as $cart_id) {
            // Move each item to the 'buy' database
            $insert_query = "INSERT INTO buy (product_id, STUD_ID, name, product_name, total_quantity, product_price, total, status, buy_date)
                             SELECT product_id, '$STUD_ID', '$f_name', product_name, total_quantity, product_price, total, 'approve', NOW()
                             FROM cart
                             WHERE cart_id = $cart_id";

            if (!mysqli_query($conn, $insert_query)) {
                $all_success = false;
                break;
            }

            // Get product details to update quantity
            $product_query = mysqli_query($conn, "SELECT product_id, total_quantity FROM cart WHERE cart_id = $cart_id");
            $product = mysqli_fetch_assoc($product_query);

            if ($product) {
                $product_id = $product['product_id'];
                $prod_total_quantity = $product['total_quantity'];

                // Update product quantity
                $updateProductQuery = "UPDATE products SET remaining_quantity = remaining_quantity - $prod_total_quantity WHERE product_id = '$product_id'";
                mysqli_query($conn, $updateProductQuery);
            }
        }

        if ($all_success) {
            // Update the student's balance after all items are processed
            $balance_update_query = "UPDATE stud_info SET BALANCE = BALANCE - $total_amount WHERE STUD_ID = '$STUD_ID'";
            mysqli_query($conn, $balance_update_query);

            // Only delete from cart if all insertions were successful
            mysqli_query($conn, "DELETE FROM cart");
            $_SESSION['success_buy'] = "Purchase Successful!";
        } else {
            $_SESSION['error'] = "An error occurred while processing the purchase.";
        }

        header("location: admin_purchase.php");
        exit();
    }
}



if (isset($_POST['cashInput'])) {
    $cash = $_POST['cash'];
    $buyer_name = $_POST['name_buyer'];


    if (!$_POST['cart_ids']) {
        $_SESSION['no_purchase'] = "No purchase available.";
        header("location: admin_purchase.php");
        exit();
    }


    if (!$_POST['name_buyer']) {
        $_SESSION['no_purchase'] = "Put the name of the buyer.";
        header("location: admin_purchase.php");
        exit();
    }

    if (empty($cash)) {
        $_SESSION['no_cashInput'] = "Please Enter a Cash.";
        header("location: admin_purchase.php");
        exit();
    }

    $total_amount = 0; // Variable to accumulate the total amount
    foreach ($_POST['cart_ids'] as $cart_id) {
        // Calculate total amount for all items in the cart
        $product_query = mysqli_query($conn, "SELECT total_quantity, product_price FROM cart WHERE cart_id = $cart_id");
        $product = mysqli_fetch_assoc($product_query);

        if ($product) {
            $prod_total_quantity = $product['total_quantity'];
            $product_price = $product['product_price'];

            // Add to total amount
            $total_amount += $prod_total_quantity * $product_price;
        }
    }

    // Validate cash against total amount
    if ($total_amount > $cash) {
        $_SESSION['insufficient_balance'] = "Insufficient Cash.";
        header("location: admin_purchase.php");
        exit();
    }

    // If cash is sufficient, proceed with the purchase
    $all_success = true;
    foreach ($_POST['cart_ids'] as $cart_id) {
        // Move each item to the 'buy' database
        $insert_query = "INSERT INTO buy (product_id, STUD_ID, name, product_name, total_quantity, product_price, total, status, buy_date)
                         SELECT product_id, 'NULL', '$buyer_name', product_name, total_quantity, product_price, total, 'paid', NOW()
                         FROM cart
                         WHERE cart_id = $cart_id";

        if (!mysqli_query($conn, $insert_query)) {
            $all_success = false;
            break;
        }

        // Get product details to update quantity
        $product_query = mysqli_query($conn, "SELECT product_id, total_quantity FROM cart WHERE cart_id = $cart_id");
        $product = mysqli_fetch_assoc($product_query);

        if ($product) {
            $product_id = $product['product_id'];
            $prod_total_quantity = $product['total_quantity'];

            // Update product quantity
            $updateProductQuery = "UPDATE products SET remaining_quantity = remaining_quantity - $prod_total_quantity WHERE product_id = '$product_id'";
            mysqli_query($conn, $updateProductQuery);
        }
    }

    if ($all_success) {
        $change = $cash - $total_amount;
        $random_id = "SC - " . bin2hex(random_bytes(8));
        $insert_to_cash_tbl = mysqli_query($conn, "INSERT INTO `cash_input`(`random_id`, `cash_amount`, `total_amount`, `change`, `date`)
                                 VALUES ('$random_id','$cash','$total_amount','$change',NOW())");

        mysqli_query($conn, "DELETE FROM cart");
        $_SESSION['success_buy'] = "Purchase Successful!";

        // Store receipt details in session
        $_SESSION['mini_receipt'] = [
            'transaction_id' => $random_id,
            'cash_amount' => $cash,
            'total_amount' => $total_amount,
            'change' => $change,
            'date' => date('Y-m-d H:i:s')
        ];
    } else {
        $_SESSION['error'] = "An error occurred while processing the purchase.";
    }

    header("location: admin_purchase.php");
    exit();
} else {
    $_SESSION['no_cart'] = "No items purchased.";
    header("location: admin_purchase.php");
    exit();
}
?>