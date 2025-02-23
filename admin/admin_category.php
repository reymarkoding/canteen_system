<?php

include "../conn.php";
session_start();
if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
    header("Location: adminLogin.php");
    exit();
}


if (isset($_POST['submit_category'])) {
    $category_name = $_POST['new_category'];
    $exst_cat_name = false;

    $sql = mysqli_query($conn, "SELECT * FROM categories");
    while ($row = mysqli_fetch_assoc($sql)) {
        $exst_category_name = $row['category'];
        if (strcasecmp($category_name, $exst_category_name) === 0) {
            $exst_cat_name = true;
        }
    }
    if ($exst_cat_name == false) {
        $insert = "INSERT INTO `categories`(`category`) VALUES ('$category_name')";
        $inserted = mysqli_query($conn, $insert);

        if ($inserted) {
            $_SESSION['inserted'] = $category_name . ' is successfully inserted.';
        } else {
            $_SESSION['inserted'] = 'Unable to insert';
        }
    } else {
        $_SESSION['existed'] = $category_name . ' is already existed.';
    }
}

if (isset($_GET['delete'])) {
    $cat_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE category_id = $cat_id");
    $_SESSION['deleted'] = 'Category deleted successfully.';
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
    <link rel="stylesheet" href="../css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./admin_css/admin_category.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <style>
        .box-box {
            height: 30vh;
            width: 50vh;
            background: red;
        }

        .header-logo {
            background-image: url(../final_logo.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .background {
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
    <div class="background">

    </div>

    <!-- Modal for Edit Category -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCategoryForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div id="errorEditCat" class="alert alert-warning d-none">

                        </div>
                        <div class="row">

                            <div class="mb-3">
                                <input type="hidden" name="category_id" id="category_id">
                            </div>


                            <div class="col-8">

                                <div class="mb-3">
                                    <label for="category_name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="category_name" name="new_category_name" placeholder="Enter category name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="main">
        <div class="side-bar">
            <div class="header-logo">
            </div>
            <div class="menu">
                <span>MAIN MENU</span>
                <div class="menu-item">
                    <a href="admin_dashboard.php">Dashboard</a>
                </div>
                <div class="menu-item active">
                    <a href="admin_category.php">Category</a>
                </div>
                <div class="menu-item">
                    <a href="admin_purchase.php">Purchase</a>
                </div>
                <div class="menu-item">
                    <a href="admin_product.php">Product</a>
                </div>
                <div class="menu-item">
                    <a href="admin_sales.php">Sales</a>
                </div>
            </div>
        </div>

        <div class="main-bar">
            <div class="category-box">
                <div class="alert alert-primary d-none" id="success_edit"></div>
                <form action="" method="post">
                    <center>
                        <h3 class="col-12 fw-semibold">Category</h3>
                    </center>
                    <div class="inputs-category">
                        <input required class="form-control" type="text" name="new_category" placeholder="Enter Category">
                        <input class="form-control my-2 btn btn-secondary" type="submit" name="submit_category" value="Submit">
                    </div>
                </form>
                <hr>
                <form action="" method="post">
                    <center>
                        <?php
                        if (isset($_SESSION['inserted'])) {
                            echo "<div id='message_error' class='alert alert-primary alert-dismissible fade show' role='alert'>" . $_SESSION['inserted'] . "</div>";
                            unset($_SESSION['inserted']);
                        }
                        if (isset($_SESSION['existed'])) {
                            echo "<div id='message_error' class='alert alert-danger alert-dismissible fade show' role='alert'>" . $_SESSION['existed'] . "</div>";
                            unset($_SESSION['existed']);
                        }
                        if (isset($_SESSION['deleted'])) {
                            echo "<div id='message_error' class='alert alert-warning alert-dismissible fade show' role='alert'>" . $_SESSION['deleted'] . "</div>";
                            unset($_SESSION['deleted']);
                        }
                        ?>
                    </center>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width: 12%; color: #fff;">Category</th>
                                <th style="width: 5%; color: #fff;">Products in Category</th>
                                <th style="width: 5%; color: #fff;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // SQL query to get all categories and their product usage count
                            $count = mysqli_query($conn, "SELECT categories.category_id, categories.category, COUNT(products.product_name) AS total_uses_category
                                FROM categories
                                LEFT JOIN products ON categories.category_id = products.category_id
                                GROUP BY categories.category
                            ");
                            if (mysqli_num_rows($count) > 0) {
                                while ($row = mysqli_fetch_assoc($count)) {
                                    $category_id = $row['category_id'];
                            ?>
                                    <tr>
                                        <td><?= $row['category']; ?></td>
                                        <td><?= $row['total_uses_category']; ?></td>
                                        <td>
                                            <button type="button" class="editCategory btn btn-primary" value="<?= $category_id ?>" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" onclick="confirmDelete(<?= $category_id; ?>)"
                                                class="btn btn-danger"
                                                value="<?= $category_id ?>"
                                                <?= $row['total_uses_category'] > 0 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='3'>No categories found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="category-count">
                <nav class="navbar">
                    <div class="container-fluid">
                        <form class="d-flex" method="post">
                            <input class="form-control me-2" name="search" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-primary" type="submit">Search</button>
                        </form>
                    </div>
                </nav>
                <div class="search-result">
                    <table class="table table-striped">
                        <tbody>
                            <?php
                            // If search is performed
                            if (isset($_POST['search'])) {
                                $search_term = $_POST['search'];

                                // Query to find products for the searched category
                                $result = mysqli_query($conn, "
                SELECT categories.category, products.product_name
                FROM categories 
                LEFT JOIN products ON categories.category_id = products.category_id
                WHERE categories.category LIKE '%$search_term%'
            ");

                                if (mysqli_num_rows($result) > 0) {
                                    echo "<tr><th>Category</th><th>Products in Category</th></tr>";
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>
                            <td>{$row['category']}</td>
                            <td>{$row['product_name']}</td>
                          </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>No products found for this category.</td></tr>";
                                }
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
            var message_error = document.querySelector("#message_error");
            if (message_error) {
                message_error.style.display = 'none';
                window.location.href = "admin_category.php";
            }
        }, 3000);

        function confirmDelete(delID) {
            var result = confirm('Are you sure you want to delete this category?');

            if (result) {
                window.location.href = 'admin_category.php?delete=' + delID;
            } else {
                window.location.href = 'admin_category.php';
            }
        }

        $(document).on('click', '.editCategory', function() {
            var cat_id = $(this).val();
            // alert(cat_id)
            $.ajax({
                type: "GET",
                url: "category_code.php?category_id=" + cat_id,
                success: function(response) {

                    var res = jQuery.parseJSON(response);
                    if (res.status == 422) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#category_id').val(res.data.category_id);
                        $('#category_name').val(res.data.category);

                        $('#editCategoryModal').modal('show');
                    }
                }
            });
        });

        $(document).on('submit', '#editCategoryForm', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append('edit_cat', true);

            // Send data via AJAX
            $.ajax({
                type: "POST",
                url: "category_code.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var res = jQuery.parseJSON(response);

                    if (res.status == 500) {
                        alert(res.message);
                    } else if (res.status == 200) {
                        $('#success_edit').removeClass('d-none');
                        $('#success_edit').text(res.message);
                        $('#editCategoryModal').modal('hide'); // Close the modal
                        $('#editCategoryForm')[0].reset(); // Reset the form (not the modal)
                        setTimeout(() => {
                            var success_edit = document.querySelector("#success_edit");
                            if (success_edit) {
                                success_edit.style.display = 'none';
                                window.location.href = "admin_category.php";
                            }
                        }, 3000);
                    }
                }
            });
        });
    </script>

</body>


</html>