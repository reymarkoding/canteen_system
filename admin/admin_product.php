<?php
include "../conn.php";
session_start();
if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}

if (isset($_POST['product_submit'])) {

    if (empty($_POST['category_id']) || empty($_POST['product_name']) || empty($_POST['product_quantity']) || empty($_POST['product_price']) || empty($_FILES["product_image"]["name"])) {
        $_SESSION['message_error'] = 'All Fields are Mandatory';
    } else {
        $category_id = $_POST['category_id'];
        $product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
        $product_quantity = $_POST['product_quantity'];
        $product_quantity_limit = $_POST['product_quantity_limit'];
        $product_price = $_POST['product_price'];
        $product_image = $_FILES["product_image"]["name"];
        $ext = pathinfo($product_image, PATHINFO_EXTENSION);
        $allowedTypes = array("jpg", "png", "jpeg", "gif", "JPG");
        $tempName = $_FILES["product_image"]["tmp_name"];
        $target_path = "uploads/" . $product_image;

        // Check if product_quantity_limit is greater than product_quantity
        if ($product_quantity_limit > $product_quantity) {
            $_SESSION['success_added'] = 'Stock limit must not exceed product quantity.';
        } else {
            $find = "SELECT product_name FROM products";
            $finded = mysqli_query($conn, $find);

            $existed = false;

            while ($row = mysqli_fetch_assoc($finded)) {
                $product_name2 = $row['product_name'];

                if (strcasecmp($product_name, $product_name2) === 0) {
                    $existed = true;
                }
            }

            if (in_array($ext, $allowedTypes)) {
                if (move_uploaded_file($tempName, $target_path)) {
                    if ($existed == false) {
                        $sql = "INSERT INTO `products`(`category_id`, `product_name`, `product_quantity`,`remaining_quantity`, `stock_limit`, `product_price`,`product_image`) 
                                VALUES ('$category_id', '$product_name','$product_quantity','$product_quantity','$product_quantity_limit','$product_price','$product_image')";
                        $query = mysqli_query($conn, $sql);

                        if ($query) {
                            $_SESSION['success_added'] = 'Product added successfully.';
                        }
                    } else {
                        $_SESSION['success_added'] = 'Product is already existed!';
                    }
                }
            } else {
                $_SESSION['message_error'] = 'Invalid image format. Please upload a valid image (jpg, png, jpeg, gif).';
            }
        }
    }
}


if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE product_id = $product_id");
    $_SESSION['success_added'] = 'Product deleted successfully.';
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./admin_css/admin_product.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        #addQuansucc {
            position: fixed;
            top: 2%;
            left: 20%;
            height: 10vh;
            width: 35%;
            animation-name: godown_success_added;
            animation-duration: 3000ms;
        }

        #editQuansucc {
            position: fixed;
            top: 2%;
            left: 20%;
            height: 10vh;
            width: 35%;
            animation-name: godown_success_added;
            animation-duration: 3000ms;
        }


        .top_box {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .header-logo {
            background-image: url(../final_logo.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            min-height: 100vh;
            background: #dcdcdc;
            /* Light Gray */
            z-index: -2;
        }
    </style>
</head>

<body>
    <div class="bg">

    </div>

    <!-- ALERTS -->
    <div class="alert alert-primary d-none" id="addQuansucc">
    </div>
    <div class="alert alert-primary d-none" id="editQuansucc">
    </div>


    <!-- MODALSSSSSS -->

    <!-- DAMAGE -->
    <div class="modal fade" id="damageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="damageForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="errorDamage" class="alert alert-warning d-none"></div>
                        <div class="row">
                            <div class="mb-3">
                                <input type="hidden" class="form-control" name="damage_product_id" id="damage_product_id">
                            </div>
                            <div class="col-4 mb-3">
                                <img id="damage_product_image" type="file" name="damage_product_image" style="width:100px;height:100px" src="" alt="">
                            </div>
                            <div class="col-8">
                                <div class="mb-3">
                                    <label for="">Product Name:</label>
                                    <input readonly type="text" class="form-control" name="damage_product_name" id="damage_product_name">
                                </div>
                                <div class="mb-3">
                                    <label for="">Product Quantity:</label>
                                    <input readonly type="number" class="form-control" name="damage_product_quantity" id="damage_product_quantity">
                                </div>
                                <div class="mb-3">
                                    <label for="">Damage Quantity:</label>
                                    <input type="number" class="form-control" name="product_add_damage" id="damage_quantity">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitDamageBtn" disabled>Add to Damage</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ADD PRODUCT -->
    <div class="add_Prod row justify-content-center" id="add_Prod">
        <div class="add-product col-12 py-3 px-5 text-dark">

            <button id="closePop" class="close-btn">&times;</button>
            <form action="" method="post" enctype="multipart/form-data" onsubmit="return confirmSubmission()">
                <div class="header-add-product">
                    <p>Add Product</p>
                </div>
                <div class="product my-2">
                    <center>
                        <select class="form-select" name="category_id" id="">
                            <option value="">Select Category</option>
                            <?php
                            $category_find = "SELECT * FROM categories";
                            $find_query = mysqli_query($conn, $category_find);
                            while ($row = mysqli_fetch_assoc($find_query)) {
                                $finded_category = $row['category'];
                                $cat_id = $row['category_id'];
                            ?>
                                <option value="<?php echo $cat_id ?>"><?php echo $finded_category ?></option>
                            <?php } ?>
                        </select>
                    </center>
                </div>

                <div class="product my-2">
                    <label for="product_name">Select Image:</label>
                    <input class="form-control" type="file" accept="image/*" name="product_image" value="Upload">
                </div>
                <div class="product my-2">
                    <label for="product_name">Product name:</label>
                    <input id="product_name" class="form-control" type="text" name="product_name" placeholder="Enter product">
                </div>
                <div class="product my-2">
                    <label for="product_quantity">Quantity:</label>
                    <input id="product_quantity" class="form-control" type="number" name="product_quantity" placeholder="Enter quantity">
                </div>
                <div class="product my-2">
                    <label for="product_quantity_limit">Stock Limit:</label>
                    <input id="product_quantity_limit" class="form-control" type="number" name="product_quantity_limit" placeholder="Enter quantity limit">
                </div>
                <div class="product my-2">
                    <label for="product_price">Price:</label>
                    <input id="product_price" class="form-control" type="number" name="product_price" placeholder="Enter price">
                </div>
                <div class="product my-2">
                    <input class="form-control btn btn-light" id="submit" class="w-100 rounded-2" type="submit" name="product_submit" value="Submit">
                </div>
            </form>
        </div>
    </div>

    <!-- ADD QUANTITY -->
    <div class="modal fade" id="addQuantityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addQuantity" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="errorAddQuan" class="alert alert-warning d-none">

                        </div>
                        <div class="row">

                            <div class="mb-3">
                                <input type="hidden" class="form-control" name="product_id" id="product_id2">
                            </div>

                            <div class="col-4 mb-3">
                                <img id="product_image2" type="file" name="product_image" style="width:100px;height:100px" src="" alt="">
                            </div>

                            <div class="col-8">

                                <div class="mb-3">
                                    <label for="">Product Name:</label>
                                    <input readonly type="text" class="form-control" name="product_name" id="product_name2">
                                </div>

                                <div class="mb-3">
                                    <label for="">Add Quantity:</label>
                                    <input type="number" class="form-control" name="product_add_quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="">Product Quantity:</label>
                                    <input readonly type="number" class="form-control" name="product_quantity" id="product_quantity2">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Quantity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="editProdModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editProd" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="errorEdit" class="alert alert-warning d-none">

                        </div>
                        <div class="row">

                            <div class="mb-3">
                                <input type="hidden" class="form-control" name="product_id" id="edit_product_id">
                            </div>

                            <div class="col-4 mb-3">
                                <img id="edit_product_image" type="file" name="product_image" style="width:100px;height:100px" src="" alt="">
                            </div>

                            <div class="col-8">

                                <div class="mb-3">
                                    <label for="">New Product Name:</label>
                                    <input type="text" class="form-control" name="product_name" id="edit_product_name">
                                </div>

                                <div class="mb-3">
                                    <label for="">Product Quantity:</label>
                                    <input readonly type="number" class="form-control" name="product_quantity" id="edit_product_quantity">
                                </div>

                                <div class="mb-3">
                                    <label for="">Update Product Price:</label>
                                    <input type="number" class="form-control" name="product_price" id="edit_product_price">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN HEROOOOO -->
    <div class="main">
        <div class="side-bar">
            <div class="header-logo">
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
                <div class="menu-item active">
                    <a href="admin_product.php">Product</a>
                </div>
                <div class="menu-item">
                    <a href="admin_sales.php">Sales</a>
                </div>
            </div>
        </div>
        <div class="main-bar">
            <?php
            if (isset($_SESSION['success_added'])) {
                echo "<center><div class='success_added alert alert-primary' id='success_added'><h5>" . $_SESSION['success_added'] . "</h5></div></center>";
                unset($_SESSION['success_added']);
            }
            if (isset($_SESSION['message_error'])) {
                echo "<center><div class='success_added alert alert-warning' id='success_added'><h5>" . $_SESSION['message_error'] . "</h5></div></center>";
                unset($_SESSION['message_error']);
            }
            ?>
            <!-- <div id="editsucc" class="alert alert-primary d-none">

            </div> -->
            <div class="tbl-container p-2">

                <div class="header-of-table">
                    <center>
                        <h3 class="product_list fw-semibold text-light">Products</h3>
                    </center>
                </div>
                <div class="middle-table">
                    <div class="search-box mb-1">
                        <input type="text" class="form-control" id="productSearch" placeholder="Search Products Here">
                    </div>
                    <div class="add-buttons">
                        <button class="addProduct py-2 px-3 border-0 rounded mx-2" id="addProductBtn">
                            <i class="fas fa-plus-square mx-2">
                            </i>
                            <span>
                                ADD PRODUCT
                            </span>
                        </button>
                        <button class="btn btn-danger">
                            <a href="damage_products.php" style="text-decoration: none; color: #fff;">Damage Products</a>
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Image</th>
                                <th style="width: 10%;">Name</th>
                                <th style="width: 8%;">Stocks</th>
                                <th style="width: 10%;">Price</th>
                                <th style="width: 25%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <?php
                            $prod_finded = mysqli_query($conn, "SELECT category, product_id, product_image, product_name, product_quantity, remaining_quantity, product_price 
                                            FROM products 
                                            JOIN categories ON products.category_id = categories.category_id ORDER BY category ASC, product_name ASC");

                            $currentCategory = null; // To track the current category
                            if (mysqli_num_rows($prod_finded) > 0) {
                                while ($row = mysqli_fetch_assoc($prod_finded)) {
                                    $category = $row['category'];
                                    $p_id = $row['product_id'];
                                    $image_product = $row['product_image'];
                                    $imageSrc = "uploads/" . $image_product;

                                    // Add a category header when a new category is encountered
                                    if ($currentCategory !== $category) {
                                        $currentCategory = $category;
                                        echo "<tr class='table-primary'>
                            <td colspan='6'><strong>$category</strong></td>
                          </tr>";
                                    }
                            ?>
                                    <tr>
                                        <td>
                                            <center><img src="<?= $imageSrc ?>" class="mt-2" style="width:75px;height:75px;"></center>
                                        </td>
                                        <td><?= $row['product_name'] ?></td>
                                        <td><?= $row['remaining_quantity'] ?></td>
                                        <td>₱<?= number_format($row['product_price'], 2) ?></td>
                                        <td>
                                            <button type="button" value="<?= $p_id ?>" class="addQuantity" data-bs-toggle="modal" data-bs-target="#addQuantityModal">
                                                <i class="fas fa-plus"></i> <span>Add Qty</span>
                                            </button>
                                            <button type="button" value="<?= $p_id ?>" class="editBtn" data-bs-toggle="modal" data-bs-target="#editProdModal">
                                                <i class="fas fa-edit"></i> <span>Edit</span>
                                            </button>
                                            <button type="button" onclick="confirmDelete(<?= $p_id ?>)" class="">
                                                <i class="fas fa-trash"></i> <span>Del</span>
                                            </button>
                                            <button type='button' value="<?= $p_id ?>" class='damageBtn' data-bs-toggle='modal' data-bs-target='#damageModal'>
                                                <i class='fas fa-trash'></i> <span>Damage</span>
                                            </button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr>
                    <td colspan='7'><center>No Products added.</center></td>
                  </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>




    <script src="../css/js/bootstrap.bundle.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        setTimeout(() => {
            var success_added = document.querySelector("#success_added");
            if (success_added) {
                success_added.style.display = 'none';
                window.location.href = "admin_product.php";
            }
        }, 3000);

        $(document).on('click', '.editBtn', function() {
            var product_id = $(this).val();
            $.ajax({
                type: "GET",
                url: "admin_code.php?product_id=" + product_id,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 422) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#edit_product_id').val(res.data.product_id);
                        $('#edit_product_name').val(res.data.product_name);
                        $('#edit_category_id').val(res.data.category_id);
                        $('#edit_product_quantity').val(res.data.product_quantity);
                        $('#edit_product_price').val(res.data.product_price);
                        $('#edit_product_image').attr('src', '../admin/uploads/' + res.data.product_image);

                        $('#editProdModal').modal('show');
                    }
                }
            });
        });


        $(document).on('submit', '#editProd', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('edit_prod', true);

            $.ajax({
                type: "POST",
                url: "admin_code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 500) {
                        $('#errorEdit').removeClass('d-none');
                        $('#errorEdit').text(res.message);
                    } else if (res.status == 200) {
                        $('#editQuansucc').removeClass('d-none');
                        $('#editQuansucc').text(res.message);
                        // $('#errorEdit').addClass('d-none');
                        $('#editProdModal').modal('hide');
                        $('#editProd')[0].reset();

                        $('#prodTable').load(location.href + " #prodTable");
                        setTimeout(() => {
                            var editQuansucc = document.querySelector("#editQuansucc");
                            if (editQuansucc) {
                                editQuansucc.style.display = 'none';
                                window.location.href = "admin_product.php";
                            }
                        }, 3000);
                    }
                }
            });
        });

        $(document).on('click', '.addQuantity', function() {
            var product_id = $(this).val();
            $.ajax({
                type: "GET",
                url: "admin_code.php?product_id=" + product_id,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 422) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#product_id2').val(res.data.product_id);
                        $('#product_name2').val(res.data.product_name);
                        $('#product_quantity2').val(res.data.remaining_quantity);
                        $('#product_image2').attr('src', '../admin/uploads/' + res.data.product_image);

                        $('#addQuantityModal').modal('show');
                    }
                }
            });

        });

        $(document).on('submit', '#addQuantity', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('add_quantity', true);

            $.ajax({
                type: "POST",
                url: "admin_code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 500) {
                        $('#errorAddQuan').removeClass('d-none');
                        $('#errorAddQuan').text(res.message);
                    } else if (res.status == 200) {
                        $('#addQuansucc').removeClass('d-none');
                        $('#addQuansucc').text(res.message);
                        // $('#errorAddQuan').addClass('d-none');
                        $('#addQuantityModal').modal('hide');
                        $('#addQuantity')[0].reset();

                        $('#prodTable').load(location.href + " #prodTable");
                        setTimeout(() => {
                            var addQuansucc = document.querySelector("#addQuansucc");
                            if (addQuansucc) {
                                addQuansucc.style.display = 'none';
                                window.location.href = "admin_product.php";
                            }
                        }, 3000);
                    }
                }
            });
        });

        // DAMAGE
        $(document).on('click', '.damageBtn', function() {
            var product_id = $(this).val();
            $.ajax({
                type: "GET",
                url: "admin_code.php?product_id=" + product_id,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 422) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#damage_product_id').val(res.data.product_id);
                        $('#damage_product_name').val(res.data.product_name);
                        $('#damage_product_quantity').val(res.data.remaining_quantity);
                        $('#damage_product_image').attr('src', '../admin/uploads/' + res.data.product_image);

                        $('#damageModal').modal('show');
                    }
                }
            });

        });
        $(document).on('submit', '#damageForm', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('damageForm', true);

            $.ajax({
                type: "POST",
                url: "admin_code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 500) {
                        $('#errorDamage').removeClass('d-none');
                        $('#errorDamage').text(res.message);
                    } else if (res.status == 200) {
                        $('#addQuansucc').removeClass('d-none');
                        $('#addQuansucc').text(res.message);
                        // $('#errorAddQuan').addClass('d-none');
                        $('#damageModal').modal('hide');

                        $('#prodTable').load(location.href + " #prodTable");
                        setTimeout(() => {
                            var addQuansucc = document.querySelector("#addQuansucc");
                            if (addQuansucc) {
                                addQuansucc.style.display = 'none';
                                window.location.href = "admin_product.php";
                            }
                        }, 3000);
                    }
                }
            });
        });

        const damageQuantityInput = document.getElementById('damage_quantity');
        const productQuantityInput = document.getElementById('damage_product_quantity');
        const submitButton = document.getElementById('submitDamageBtn');

        damageQuantityInput.addEventListener('input', () => {
            const damageQuantity = parseInt(damageQuantityInput.value);
            const productQuantity = parseInt(productQuantityInput.value);

            if (!isNaN(damageQuantity) && !isNaN(productQuantity) && damageQuantity > 0) {
                // Enable submit button only if damage quantity is valid
                submitButton.disabled = damageQuantity > productQuantity;
            } else {
                // Disable button for invalid input
                submitButton.disabled = true;
            }
        });

        // SEARCH
        $(document).ready(function() {
            // Function to fetch and display products
            function fetchProducts(searchQuery = '') {
                $.ajax({
                    url: 'search_products.php', // Path to the PHP file
                    method: 'POST',
                    data: {
                        query: searchQuery
                    },
                    success: function(response) {
                        $('#productTableBody').html(response);
                    }
                });
            }

            // Load all products by default when the page loads
            fetchProducts();

            // Search products as user types
            $('#productSearch').on('keyup', function() {
                const searchQuery = $(this).val();
                fetchProducts(searchQuery);
            });
        });


        // CONFIRMATION ADD PRODUCT
        function confirmSubmission() {
            // Get the values of quantity and price
            const quantity = document.getElementById("product_quantity").value;
            const price = document.getElementById("product_price").value;
            const p_name = document.getElementById("product_name").value;

            // Create the confirmation message
            const message = `Are you sure you want to add ${p_name} with a quantity of ${quantity} and a price of ${price}?`;

            // Show the confirmation dialog
            return confirm(message);
        }
        document.getElementById("closePop").addEventListener("click", function() {
            document.getElementById("add_Prod").style.display = "none";
        });


        // ADD PRODUCT MODAL
        var addBtn = document.querySelector("#addProductBtn");
        var addProdModal = document.querySelector("#add_Prod");
        var closeBtn = document.querySelector("#closePop");

        addBtn.onclick = function() {
            addProdModal.style.display = 'block';
        }
        closeBtn.onclick = function() {
            loginPopup.style.display = 'none';
        }

        // ADD CATEGORY MODAL
        var addCategoryBtn = document.querySelector("#addCategoryBtn");
        var add_Category = document.querySelector("#add_Category");
        var closePopCategory = document.querySelector("#closePopCategory");

        addCategoryBtn.onclick = function() {
            add_Category.style.display = 'block';
        }
        closePopCategory.onclick = function() {
            loginPopup.style.display = 'none';
        }

        // SET TIME OUT MESSAGE ERROR OF EMPTY FIELDS SA ADD PRODUCT
        setTimeout(() => {
            var message_error = document.querySelector("#message_error");
            if (message_error) {
                message_error.style.display = 'none';
                window.location.href = "admin_product.php";
            }
        }, 3000);

        // SET TIME OUT OF ALERT OF SUCCESS ADDED


        // CONFIRM DELETE PRODUCT
        function confirmDelete(delID) {
            var result = confirm('Are you sure you want to delete this product?');

            if (result) {
                window.location.href = 'admin_product.php?delete=' + delID;
            } else {
                window.location.href = 'admin_product.php';
            }
        }


        // Update the label of Select Category
        function updateLabel() {

            var categories = document.querySelector("#categories");
            var labelToChange = document.querySelector("#labelToChange");

            if (categories.value) {
                labelToChange.textContent = 'Type: ' + categories.value;
            } else {
                labelToChange.textContent = 'Select a category';
            }
        }
    </script>
</body>

</html>