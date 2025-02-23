<?php
include "../conn.php";
session_start();

if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] !== 'login') {
  header("Location: adminLogin.php");
  exit();
}
if (isset($_GET['del_purchase_id'])) {
  $cart_id = $_GET['del_purchase_id'];

  $query = mysqli_query($conn, "DELETE FROM `cart` WHERE cart_id = '$cart_id'");
  if ($query) {
    $_SESSION['success_delete'] = "Cancelled";
  }
}

if (isset($_POST['close_receipt'])) {
  unset($_SESSION['mini_receipt']);  // Clear the receipt from the session
  header("Location: admin_purchase.php");  // Redirect back to the admin purchase page
  exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase</title>
  <link rel="stylesheet" href="../css/css/bootstrap.min.css">
  <link rel="stylesheet" href="./admin_css/admin_purchase.css">
  <link rel="stylesheet" href="../css/all.min.css">
  <link rel="stylesheet" href="../css/fontawesome.min.css">
  <script type="text/javascript" src="../js/adapter.min.js"></script>
  <script type="text/javascript" src="../js/vue.min.js"></script>
  <script type="text/javascript" src="../js/instascan.min.js"></script>

  <style>
    .low-quantity {
      background-color: red;
      border-radius: 5px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.7);
    }

    .medium-quantity {
      background-color: yellow;
      border-radius: 5px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.7);
    }

    .high-quantity {
      background-color: green;
      border-radius: 5px;
      box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.7);
    }

    .receipt {
      z-index: 999999;
      background-color: #fff;
      padding: 2%;
      position: fixed;
      top: 25%;
      left: 35%;
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

    .header-logo {
      background-image: url(../final_logo.png);
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
    }
  </style>
</head>

<body>
  <!-- CHECKOUT MODAL -->
  <div class="modal fade" id="checkModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add-To-Cart</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="checkout" enctype="multipart/form-data">
          <div class="modal-body">
            <div id="errorCheckout" class="alert alert-warning d-none">

            </div>
            <div class="row">

              <div class="mb-3">
                <input type="hidden" class="form-control" name="checkout_product_id" id="checkout_product_id">
              </div>

              <div class="col-4 mb-3">
                <img id="checkout_product_image" type="file" name="checkout_product_image" style="width:100px;height:100px" src="" alt="product_image">
              </div>

              <div class="col-8">

                <div class="mb-3">
                  <label for="">Product Name:</label>
                  <input readonly type="text" class="form-control" name="checkout_product_name" id="checkout_product_name">
                </div>
                <div class="mb-3">
                  <input readonly type="hidden" class="form-control" name="checkout_product_quantity" id="checkout_product_quantity">
                </div>
                <div class="mb-3">
                  <label for="">Product Quantity:</label>
                  <input readonly type="number" class="form-control" name="checkout_remaining_quantity" id="checkout_remaining_quantity">
                </div>
                <div class="mb-3">
                  <label for="">Product Price:</label>
                  <input readonly type="number" class="form-control" name="checkout_product_price" id="checkout_product_price">
                </div>
                <div class="mb-3">
                  <label for="">Total Quantity:</label>
                  <input type="number" class="form-control" value="0" name="checkout_total_quantity" id="checkout_total_quantity">
                </div>

                <span id="warning-message" style="color: red; display: none;">
                </span>

              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Buy</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- EDIT CART MODAL -->

  <div class="modal fade" id="editCartModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit Cart</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editCartForm" enctype="multipart/form-data">
          <div class="modal-body">
            <div id="errorCheckout" class="alert alert-warning d-none">

            </div>
            <div class="row">

              <div class="mb-3">
                <input type="hidden" class="form-control" name="editCart_cart_id" id="editCart_cart_id">
              </div>

              <div class="col-8">

                <div class="mb-3">
                  <label for="">Product Name:</label>
                  <input readonly type="text" class="form-control" name="editCart_product_name" id="editCart_product_name">
                </div>
                <div class="mb-3">
                  <label for="">Product Price:</label>
                  <input readonly type="number" class="form-control" name="editCart_product_price" id="editCart_product_price">
                </div>
                <input readonly type="hidden" class="form-control" name="editCart_remaining_quantity" id="editCart_remaining_quantity">

                <div class="mb-3">
                  <label for="">New Quantity:</label>
                  <input type="number" class="form-control" name="editCart_total_quantity" id="editCart_total_quantity">
                </div>

              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="proceedButton" disabled>Proceed</button>

          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="background">

  </div>

  <?php if (isset($_SESSION['mini_receipt'])): ?>
    <div class="receipt border p-3 mt-3">
      <h4>Mini Receipt</h4>
      <p><strong>Transaction ID:</strong> <?php echo $_SESSION['mini_receipt']['transaction_id']; ?></p>
      <p><strong>Cash Amount:</strong> ₱<?php echo number_format($_SESSION['mini_receipt']['cash_amount'], 2); ?></p>
      <p><strong>Total Amount:</strong> ₱<?php echo number_format($_SESSION['mini_receipt']['total_amount'], 2); ?></p>
      <p><strong>Change:</strong> ₱<?php echo number_format($_SESSION['mini_receipt']['change'], 2); ?></p>
      <p><strong>Date:</strong> <?php echo $_SESSION['mini_receipt']['date']; ?></p>
      <form action="admin_purchase.php" method="post">
        <!-- Edit Button (optional functionality can be added) -->
        <!-- <button type="button" class="btn btn-primary me-2" onclick="window.location.href='edit_purchase.php';">
          Edit
        </button> -->
        <!-- Close Button -->
        <button type="submit" class="btn btn-secondary" name="close_receipt">
          Close
        </button>
      </form>
    </div>
  <?php endif; ?>

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
        <div class="menu-item active">
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
      <div class="left-hero">

        <center>
          <h3>Product Lists</h3>
        </center>
        <?php
        if (isset($_SESSION['low_Stock'])) {
          foreach ($_SESSION['low_Stock'] as $product) {
            echo "<div class='alert alert-warning me-2'>" . $product . "</div>";
          }
          unset($_SESSION['low_Stock']); // Clear the session after displaying alerts
        }
        ?>

        <div class="search p-2">
          <input type="text" id="productSearch" class="form-control" placeholder="Search here">
        </div>

        <div id="myProducts" class="product-list">


        </div>
      </div>
      <div class="right-hero">
        <div class="camera p-2">
          <video src="" id="preview" style="height: 300px;width: 100%;object-fit: cover;transform: rotate(90deg);"></video>
          <button id="toggleCamera" class="btn btn-primary mt-2 form-control">Turn Camera On</button>
        </div>

        <?php
        if (isset($_SESSION['success_buy'])) {
          echo "<div class='alert alert-primary m-2' id='success_buy'>" . $_SESSION['success_buy'] . "</div>";
          unset($_SESSION['success_buy']);
        }
        if (isset($_SESSION['success_delete'])) {
          echo "<div class='alert alert-danger m-2' id='success_delete'>" . $_SESSION['success_delete'] . "</div>";
          unset($_SESSION['success_delete']);
        }
        if (isset($_SESSION['empty_id'])) {
          echo "<div class='alert alert-warning m-2' id='empty_id'>" . $_SESSION['empty_id'] . "</div>";
          unset($_SESSION['empty_id']);
        }
        if (isset($_SESSION['no_cart'])) {
          echo "<div class='alert alert-warning m-2' id='empty_id'>" . $_SESSION['no_cart'] . "</div>";
          unset($_SESSION['no_cart']);
        }
        if (isset($_SESSION['error'])) {
          echo "<div class='alert alert-danger m-2' id='empty_id'>" . $_SESSION['error'] . "</div>";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['insufficient_balance'])) {
          echo "<div class='alert alert-danger m-2' id='empty_id'>" . $_SESSION['insufficient_balance'] . "</div>";
          unset($_SESSION['insufficient_balance']);
        }
        if (isset($_SESSION['no_cashInput'])) {
          echo "<div class='alert alert-danger m-2' id='empty_id'>" . $_SESSION['no_cashInput'] . "</div>";
          unset($_SESSION['no_cashInput']);
        }
        if (isset($_SESSION['no_purchase'])) {
          echo "<div class='alert alert-danger m-2' id='empty_id'>" . $_SESSION['no_purchase'] . "</div>";
          unset($_SESSION['no_purchase']);
        }

        ?>

        <div class="cart-table p-2">
          <form action="approve_purchase.php" method="post">
            <table id="checkoutTable" class="checkoutTable table table-stripped">
              <thead>
                <tr>
                  <th>QTY</th>
                  <th>Price</th>
                  <th>Product</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $find = mysqli_query($conn, "SELECT * FROM cart");
                if (mysqli_num_rows($find) > 0) {
                  foreach ($find as $prod) {
                ?>
                    <tr>
                      <td><?= $prod['total_quantity']; ?></td>
                      <td><?= "₱" . number_format($prod['product_price'], 2); ?></td>
                      <td><?= $prod['product_name']; ?></td>
                      <td><?= "₱" . number_format($prod['total'], 2); ?></td>
                      <td>
                        <button type="button" value="<?= $prod['cart_id']; ?>" class="editCartBtn btn btn-success" data-bs-toggle="modal" data-bs-target="#editCartModal">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger">
                          <a class="text-light" style="text-decoration: none;" href="admin_purchase.php?del_purchase_id=<?= $prod['cart_id']; ?>"><i class="fas fa-trash"></i>Cancel</a>
                        </button>
                      </td>
                    </tr>
                    <!-- Hidden input for each cart_id -->
                    <input type="hidden" name="cart_ids[]" value="<?= $prod['cart_id']; ?>">
                <?php
                  }
                }
                ?>
              </tbody>
              <tfoot>
                <?php
                $total_result = mysqli_query($conn, "SELECT SUM(total) as grand_total FROM cart");
                $total_row = mysqli_fetch_assoc($total_result);
                $grand_total = $total_row['grand_total'];
                ?>
                <tr>
                  <td colspan="3">
                    <h3>TOTAL:</h3>
                  </td>
                  <td colspan="2">
                    <h3 id="total_amount">₱<?= number_format($grand_total, 2); ?></h3>
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="text-center bg-light">
                    <label for="" class="text-dark">Enter ID Number:</label>
                    <input readonly type="text" id="id_number" name="id_number" placeholder="Student Id Number" class="form-control">
                    <input readonly type="hidden" id="balance" name="balance" class="form-control">
                  </td>
                  <td colspan="2" class="text-center bg-light">
                    <!-- Approve All Button -->
                    <br>
                    <button
                      type="submit"
                      name="cashLess"
                      class="btn btn-warning"
                      onclick="return confirm('Are you sure you want to buy this item for ₱<?= number_format($grand_total, 2); ?>?');">
                      Buy ₱<?= number_format($grand_total, 2); ?>
                    </button>

                  </td>
                <tr>
                  <td colspan="5"></td>
                </tr>
                <tr>

                  <td colspan="5" class="text-center bg-light">
                    <label for="" class="text-dark">Name of Buyer:</label>
                    <input type="text" id="name_buyer" name="name_buyer" placeholder="Enter Name" class="form-control">
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="text-center bg-light" id="bot-cashInput">
                    <label for="cash" class="text-dark">Enter Cash Amount:</label>
                    <input type="number" id="cash" name="cash" placeholder="00.00" class="form-control">
                  </td>
                  <td colspan="2" class="text-center bg-light" id="bot-cashInput">
                    <br>
                    <button
                      type="submit"
                      name="cashInput"
                      class="btn btn-warning"
                      onclick="return confirm('Are you sure you want to buy this item for ₱<?= number_format($grand_total, 2); ?>?');">
                      Buy ₱<?= number_format($grand_total, 2); ?>
                    </button>

                  </td>
                </tr>
                <tr>
                  <td colspan="5" class="text-center bg-success">
                    <label for="change" class="text-light">Change:</label>
                    <input type="text" id="change" readonly class="form-control">
                  </td>
                </tr>
                </tr>
              </tfoot>
            </table>
            <!-- <div class="tbl-footer">
              <div class="top-hero">
                <?php
                $total_result = mysqli_query($conn, "SELECT SUM(total) as grand_total FROM cart");
                $total_row = mysqli_fetch_assoc($total_result);
                $grand_total = $total_row['grand_total'];
                ?>
                <div class="div">
                  <h3>TOTAL:</h3>
                </div>
                <div class="div">

                  <h3 id="total_amount">₱<?= number_format($grand_total, 2); ?></h3>
                </div>
              </div>
              <div class="bot-hero">
                <div class="top">
                  <label for="" class="text-light">Enter ID Number:</label>
                  <input readonly type="text" id="id_number" name="id_number" placeholder="Student Id Number" class="form-control">
                  <input readonly type="hidden" id="balance" name="balance" class="form-control">
                </div>
                <div class="bottom">
                  <button type="submit" name="cashLess" class="btn btn-warning">
                    Complete Purchase ₱<?= number_format($grand_total, 2); ?>
                  </button>
                </div>
              </div>
            </div> -->
          </form>
        </div>
      </div>
    </div>
  </div>



  <script src="../css/js/bootstrap.bundle.js"></script>
  <script src="../js/jquery.min.js"></script>
  <script>
    $(document).on('input', '#editCart_total_quantity', function() {
      const remainingQuantity = parseInt($('#editCart_remaining_quantity').val(), 10); // Parse remaining quantity
      const newQuantity = parseInt($(this).val(), 10); // Parse new quantity

      // Enable or disable the Proceed button based on the condition
      if (newQuantity > remainingQuantity || isNaN(newQuantity)) {
        $('#proceedButton').attr('disabled', true); // Disable if invalid
      } else {
        $('#proceedButton').attr('disabled', false); // Enable if valid
      }
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const totalQuantityInput = document.getElementById("checkout_total_quantity");
      const remainingQuantityInput = document.getElementById("checkout_remaining_quantity");
      const buyButton = document.querySelector("button[type='submit']");
      const warningMessage = document.getElementById("warning-message");

      totalQuantityInput.addEventListener("input", function() {
        const totalQuantity = parseInt(totalQuantityInput.value, 10) || 0;
        const remainingQuantity = parseInt(remainingQuantityInput.value, 10) || 0;

        if (!totalQuantityInput.value) {
          // Case: No quantity inputted
          buyButton.disabled = true;
          warningMessage.textContent = "Please input a quantity.";
          warningMessage.style.display = "inline";
        } else if (totalQuantity <= 0) {
          // Case: Entered quantity is less than or equal to 0
          buyButton.disabled = true;
          warningMessage.textContent = "Please enter a valid quantity.";
          warningMessage.style.display = "inline";
        } else if (totalQuantity > remainingQuantity) {
          // Case: Entered quantity exceeds the remaining quantity
          buyButton.disabled = true;
          warningMessage.textContent = "The entered quantity exceeds the available product quantity.";
          warningMessage.style.display = "inline";
        } else {
          // Case: Valid quantity entered
          buyButton.disabled = false;
          warningMessage.style.display = "none";
        }
      });
    });


    // document.addEventListener("DOMContentLoaded", function() {
    //   const totalQuantityInput = document.getElementById("checkout_total_quantity");
    //   const remainingQuantityInput = document.getElementById("checkout_remaining_quantity");
    //   const buyButton = document.querySelector("button[type='submit']");

    //   totalQuantityInput.addEventListener("input", function() {
    //     const totalQuantity = parseInt(totalQuantityInput.value, 10) || 0;
    //     const remainingQuantity = parseInt(remainingQuantityInput.value, 10) || 0;

    //     // Disable the "Buy" button if the total quantity exceeds the remaining quantity
    //     if (totalQuantity > remainingQuantity || totalQuantity <= 0) {
    //       buyButton.disabled = true;
    //     } else {
    //       buyButton.disabled = false;
    //     }
    //   });
    // });
  </script>
  <script>
    document.getElementById('cash').addEventListener('input', function() {
      const totalAmount = <?= $grand_total; ?>; // Grand total from PHP
      const cashInput = parseFloat(this.value); // Cash input value

      // Calculate change if the cash input is valid
      if (!isNaN(cashInput) && cashInput >= totalAmount) {
        const change = cashInput - totalAmount;
        document.getElementById('change').value = "₱" + change.toFixed(2); // Display change
      } else {
        document.getElementById('change').value = 'Insufficient cash'; // Clear change if input is less than total
      }
    });

    // EDIT CART
    $(document).on('click', '.editCartBtn', function() {
      var cart_id = $(this).val();
      // alert(cart_id);
      $.ajax({
        type: "GET",
        url: "edit_cart.php?cart_id=" + cart_id,
        success: function(response) {

          var res = jQuery.parseJSON(response);
          if (res.status == 422) {
            alert(res.message);
          } else if (res.status == 200) {
            $('#editCart_cart_id').val(res.data.cart_id);
            $('#editCart_product_name').val(res.data.product_name);
            $('#editCart_total_quantity').val(res.data.total_quantity);
            $('#editCart_product_price').val(res.data.product_price);
            $('#editCart_remaining_quantity').val(res.data.remaining_quantity);
            $('#editCart_total_price').val(res.data.total);

            $('#editCartModal').modal('show');
          }
        }
      });
    });

    $(document).on('submit', '#editCartForm', function(e) {
      e.preventDefault();

      var formData = new FormData(this);
      formData.append('editCartForm', true);

      $.ajax({
        type: "POST",
        url: "edit_cart.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

          var res = jQuery.parseJSON(response);
          if (res.status == 422) {
            $('#errorCheckout').removeClass('d-none');
            $('#errorCheckout').text(res.message);
          } else if (res.status == 200) {
            $('#errorCheckout').addClass('d-none');
            // $('#checkModal').modal('hide');
            // $('#checkout')[0].reset();

            window.location.reload();
            // $('#checkoutTable').load(location.href + " #checkoutTable");
          }
        }
      });
    });
  </script>

  <script>
    // checkout
    $(document).on('click', '.checkoutBtn', function() {
      var product_id = $(this).val();
      $.ajax({
        type: "GET",
        url: "checkout.php?product_id=" + product_id,
        success: function(response) {

          var res = jQuery.parseJSON(response);
          if (res.status == 422) {
            alert(res.message);
          } else if (res.status == 200) {
            $('#checkout_product_id').val(res.data.product_id);
            $('#checkout_product_name').val(res.data.product_name);
            $('#checkout_product_quantity').val(res.data.product_quantity);
            $('#checkout_remaining_quantity').val(res.data.remaining_quantity);
            $('#checkout_product_price').val(res.data.product_price);
            $('#checkout_product_image').attr('src', '../admin/uploads/' + res.data.product_image);

            $('#checkModal').modal('show');
          }
        }
      });
    });

    $(document).on('submit', '#checkout', function(e) {
      e.preventDefault();

      var formData = new FormData(this);
      formData.append('checkout', true);

      $.ajax({
        type: "POST",
        url: "checkout.php",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          var res = jQuery.parseJSON(response);
          if (res.status == 422) {
            $('#errorCheckout').removeClass('d-none');
            $('#errorCheckout').text(res.message);
          } else if (res.status == 409) {
            $('#errorCheckout').removeClass('d-none');
            $('#errorCheckout').text(res.message);
          } else if (res.status == 200) {
            $('#errorCheckout').addClass('d-none');
            $('#checkModal').modal('hide');
            $('#checkout')[0].reset();

            window.location.reload();
          } else {
            $('#errorCheckout').removeClass('d-none');
            $('#errorCheckout').text('An unexpected error occurred');
          }
        }

      });
    });


    setTimeout(() => {
      var success_delete = document.querySelector("#success_delete");
      var success_buy = document.querySelector("#success_buy");
      var empty_id = document.querySelector("#empty_id");
      if (success_buy) {
        success_buy.style.display = 'none';
        window.location.href = "admin_purchase.php";
      }
      if (success_delete) {
        success_delete.style.display = 'none';
        window.location.href = "admin_purchase.php";
      }
      if (empty_id) {
        empty_id.style.display = 'none';
        window.location.href = "admin_purchase.php";
      }
    }, 3000);
  </script>

  <!-- <script>
    // Initialize the scanner
    let scanner;
    let cameraActive = false;

    // Function to start the camera
    function startCamera() {
      scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
      });

      Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
          // Default to the first camera
          let selectedCamera = cameras[0];

          // Try to select the back camera if it exists
          cameras.forEach(camera => {
            if (camera.name.toLowerCase().includes('back')) {
              selectedCamera = camera;
            }
          });

          scanner.start(selectedCamera);
          cameraActive = true;
          document.getElementById('toggleCamera').innerText = 'Turn Off Camera';
          document.getElementById('preview').style.height = '300px'; // Show the camera preview
          document.getElementById('preview').style.width = '100%'; // Adjust the width of the camera preview
        } else {
          console.error('No cameras found');
        }
      }).catch(function(e) {
        console.error(e);
      });

      scanner.addListener('scan', function(content) {
        try {
          let val = JSON.parse(content);
          document.getElementById('balance').value = val.balance;
          document.getElementById('id_number').value = val.id_number;
        } catch (e) {
          alert('Student ID only');
        }
      });
    }

    // Function to stop the camera
    function stopCamera() {
      scanner.stop();
      cameraActive = false;
      document.getElementById('preview').style.height = '0'; // Hide the camera preview
      document.getElementById('preview').style.width = '0'; // Hide the camera preview
      document.getElementById('toggleCamera').innerText = 'Turn On Camera';
    }

    // Event listener for the toggle button
    document.getElementById('toggleCamera').addEventListener('click', function() {
      if (cameraActive) {
        stopCamera();
      } else {
        startCamera();
      }
    });

    // Ensure the camera is off by default
    document.getElementById('preview').style.height = '0'; // Hide the camera preview initially
    document.getElementById('preview').style.width = '0'; // Hide the camera preview initially
  </script> -->
  <script>
    // Initialize the scanner
    let scanner;
    let cameraActive = false;

    // Function to start the camera
    function startCamera() {
      scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
      });

      Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
          // Default to the first camera
          let selectedCamera = cameras[0];

          // Try to select the back camera if it exists
          cameras.forEach(camera => {
            if (camera.name.toLowerCase().includes('back')) {
              selectedCamera = camera;
            }
          });

          scanner.start(selectedCamera);
          cameraActive = true;
          document.getElementById('toggleCamera').innerText = 'Turn Off Camera';
          document.getElementById('preview').style.height = '300px'; // Show the camera preview
          document.getElementById('preview').style.width = '100%'; // Adjust the width of the camera preview
        } else {
          console.error('No cameras found');
        }
      }).catch(function(e) {
        console.error(e);
      });

      scanner.addListener('scan', function(content) {
        try {
          let val = JSON.parse(content);
          document.getElementById('balance').value = val.balance;
          document.getElementById('id_number').value = val.id_number;

          // Automatically stop the camera after scanning
          stopCamera();
        } catch (e) {
          alert('Student ID only');
        }
      });
    }

    // Function to stop the camera
    function stopCamera() {
      if (scanner) {
        scanner.stop();
      }
      cameraActive = false;
      document.getElementById('preview').style.height = '0'; // Hide the camera preview
      document.getElementById('preview').style.width = '0'; // Hide the camera preview
      document.getElementById('toggleCamera').innerText = 'Turn On Camera';
    }

    // Event listener for the toggle button
    document.getElementById('toggleCamera').addEventListener('click', function() {
      if (cameraActive) {
        stopCamera();
      } else {
        startCamera();
      }
    });

    // Ensure the camera is off by default
    document.getElementById('preview').style.height = '0'; // Hide the camera preview initially
    document.getElementById('preview').style.width = '0'; // Hide the camera preview initially
  </script>


  <!-- SEARCH -->
  <script>
    $(document).ready(function() {
      function fetchProducts(searchQuery = '') {
        $.ajax({
          url: 'search_prod_purchase.php', // PHP script to handle product filtering
          method: 'POST',
          data: {
            query: searchQuery
          },
          success: function(response) {
            $('#myProducts').html(response); // Populate the product list dynamically
          },
          error: function() {
            console.error('Failed to fetch products.');
          }
        });
      }

      // Load all products when the page loads
      fetchProducts();

      // Fetch products dynamically as the user types
      $('#productSearch').on('keyup', function() {
        const searchQuery = $(this).val().trim(); // Get the search input value
        fetchProducts(searchQuery); // Update the product list
      });
    });
  </script>
</body>

</html>