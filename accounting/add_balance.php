<?php
include "../conn.php";
session_start();

if (isset($_GET['stud_id'])) {

    $stud_id = mysqli_real_escape_string($conn, $_GET['stud_id']);

    $query = "SELECT * FROM stud_info WHERE stud_id = '$stud_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $products = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Student fetch successfully',
            'data' => $products
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 422,
            'message' => 'Student ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['addBalanceForm'])) {
    // Set timezone to Manila (Asia/Manila)
    $timezone = new DateTimeZone('Asia/Manila');
    $dateTime = new DateTime('now', $timezone);

    // Format the date and time
    $curDay = $dateTime->format("Y-m-d");
    $currentDay = $dateTime->format("M d, Y");
    $current_time = $dateTime->format('h:i A');
    $current_year = $dateTime->format("Y");
    $current_month = $dateTime->format("m");
    $current_day = $dateTime->format("d");
    $currentDate = "$current_year-$current_month-$current_day";

    // Sanitize input values
    $stud_id = mysqli_real_escape_string($conn, $_POST['old_stud_id']);
    $add_balance_amount = floatval($_POST['add_balance_amount']); // Ensure numeric input

    $query = "UPDATE `stud_info` SET `BALANCE`= BALANCE + $add_balance_amount WHERE STUD_ID = '$stud_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $insert = mysqli_query($conn, "INSERT INTO `deposit`(`STUD_ID`, `amount`, `date`, `time`, `status`, `send_to`) VALUES ('$stud_id', '$add_balance_amount', '$currentDate', '$current_time', 'approved', 'Accounting')");

        if ($insert) {
            $res = [
                'status' => 200,
                'message' => 'Balance Added Successfully'
            ];
        } else {
            $res = [
                'status' => 500,
                'message' => 'Failed to insert deposit record'
            ];
        }

        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 500,
            'message' => 'There is a problem with adding balance'
        ];
        echo json_encode($res);
        return false;
    }
}
if (isset($_GET['dep_id'])) {

    $dep_id = mysqli_real_escape_string($conn, $_GET['dep_id']);

    $query = "SELECT * FROM deposit WHERE deposit_id = '$dep_id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) == 1) {
        $deposit = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Student fetch successfully',
            'data' => $deposit
        ];
        echo json_encode($res);
        return false;
    } else {
        $res = [
            'status' => 422,
            'message' => 'Student ID not found'
        ];
        echo json_encode($res);
        return false;
    }
}

if (isset($_POST['updateBalanceForm'])) {
    $old_dep_id = $_POST['old_dep_id'];
    $minus_balance_amount = $_POST['minus_balance_amount'];
    $find = mysqli_query($conn, "SELECT STUD_ID FROM deposit WHERE deposit_id = '$old_dep_id'");
    $row = mysqli_fetch_assoc($find);
    $stud_id = $row['STUD_ID'];

    $query = "UPDATE `deposit` SET `amount`= amount - $minus_balance_amount WHERE deposit_id = '$old_dep_id'";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $updateBalance = mysqli_query($conn, "UPDATE `stud_info` SET `BALANCE`= BALANCE - $minus_balance_amount WHERE STUD_ID = '$stud_id'");
        if ($updateBalance) {

            $res = [
                'status' => 200,
                'message' => 'Quantity Added'
            ];
        }
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
