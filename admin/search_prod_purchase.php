<?php
include "../conn.php"; // Ensure this points to your database connection file
session_start();

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($conn, $_POST['query']);
    $sql = "SELECT category, product_id, product_image, product_name, product_quantity, stock_limit, remaining_quantity, product_price 
            FROM products 
            JOIN categories ON products.category_id = categories.category_id 
            WHERE product_name LIKE '%$query%' OR category LIKE '%$query%' 
            ORDER BY remaining_quantity DESC, product_name ASC";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            $stock_limit = $product['stock_limit'];
            $qty = $product['remaining_quantity'];
            $class = ($qty == $stock_limit) ? 'low-quantity text-light' : 'high-quantity text-light';

            // Determine if the button should be disabled
            $disabled = ($qty == 0) ? 'disabled' : '';

            if ($qty <= $stock_limit) {
                if ($qty == 0) {
                    $lowStockProducts = "Out of stock!";
                } else {
                    $lowStockProducts = "Low Stock!";
                }
            } else {
                $lowStockProducts = ""; // No message if not low stock
            }

            echo '
            <div class="products-box">
                <div class="prod-name">
                    <h5>' . $product['product_name'] . '</h5>
                </div>
                <div class="prod-image">
                    <img style="width:100px;height:100px;" src="../admin/uploads/' . $product['product_image'] . '" alt="Product Image">
                </div>
                <div class="prod-category">
                    <p>' . $product['category'] . '</p>
                </div>
                <div class="prod-quantity">
                    <p class="px-4 py-1 ' . $class . '">Qty: ' . $product['remaining_quantity'] . '</p>
                </div>
                <div class="prod-price">
                    Price: ₱' . number_format($product['product_price'], 2) . '
                </div>
                <div class="add_cart">
                    <button type="button" value="' . $product['product_id'] . '" class="checkoutBtn btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#checkModal" ' . $disabled . '>
                        <h5>Add-To-Cart</h5>
                    </button>
                </div>
                <center><p class="text-warning">' . $lowStockProducts . '</p></center>
            </div>';
        }
    } else {
        echo '<div class="no-products">No products found.</div>';
    }
}
