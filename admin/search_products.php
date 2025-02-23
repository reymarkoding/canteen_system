<?php
include "../conn.php"; // Include your DB connection file

// Check if a search query is provided
$searchQuery = isset($_POST['query']) ? mysqli_real_escape_string($conn, $_POST['query']) : '';

$query = "SELECT category, product_id, product_image, product_name, product_quantity, remaining_quantity, product_price 
          FROM products 
          JOIN categories ON products.category_id = categories.category_id ORDER BY category ASC, remaining_quantity, product_name ASC";

// If a search query is provided, filter results
if ($searchQuery !== '') {
    $query = "SELECT category, product_id, product_image, product_name, product_quantity, remaining_quantity, product_price 
              FROM products 
              JOIN categories ON products.category_id = categories.category_id 
              WHERE product_name LIKE '%$searchQuery%' OR category LIKE '%$searchQuery%' 
              ORDER BY category ASC, product_name ASC";
}

$result = mysqli_query($conn, $query);

$currentCategory = null; // Track the current category

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $category = $row['category'];
        $imageSrc = "uploads/" . $row['product_image'];

        // Add a category header if the category changes
        if ($currentCategory !== $category) {
            $currentCategory = $category;
            echo "<tr class='table-primary' >
                    <td colspan='6'><strong>$currentCategory</strong></td>
                  </tr>";
        }

        // Output product details
        echo "
            <tr>
                <td><center><img src='$imageSrc' class='mt-2' style='width:75px;height:75px;'></center></td>
                <td>{$row['product_name']}</td>
                <td>{$row['remaining_quantity']}</td>
                <td>₱" . number_format($row['product_price'], 2) . "</td>
                <td>
                    <button type='button' value='{$row['product_id']}' class='addQuantity' data-bs-toggle='modal' data-bs-target='#addQuantityModal'>
                        <i class='fas fa-plus'></i> <span>Add Qty</span>
                    </button>
                    <button type='button' value='{$row['product_id']}' class='editBtn' data-bs-toggle='modal' data-bs-target='#editProdModal'>
                        <i class='fas fa-edit'></i> <span>Edit</span>
                    </button>
                    <button type='button' onclick='confirmDelete({$row['product_id']})' class=''>
                        <i class='fas fa-trash'></i> <span>Del</span>
                    </button>
                    <button type='button' value='{$row['product_id']}' class='damageBtn' data-bs-toggle='modal' data-bs-target='#damageModal'>
                        <i class='fas fa-trash'></i> <span>Damage</span>
                    </button>
                </td>
            </tr>
        ";
    }
} else {
    echo "<tr><td colspan='6'><center>No products found.</center></td></tr>";
}
