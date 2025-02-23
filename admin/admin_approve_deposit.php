<?php
include "../conn.php";
session_start();



if (isset($_GET['approve_id'])) {
    // Set timezone to Manila (Asia/Manila)
    $timezone = new DateTimeZone('Asia/Manila');

    // Create a DateTime object with the current date and time in the specified timezone
    $dateTime = new DateTime('now', $timezone);

    // Format the date and time according to your needs
    $curDay = $dateTime->format("Y-m-d");
    $currentDay = $dateTime->format("M d, Y");
    $current_time = $dateTime->format('h:i A');
    $current_year = $dateTime->format("Y");
    $current_month = $dateTime->format("m");
    $current_day = $dateTime->format("d");
    $currentDate = "$current_year-$current_month-$current_day";


    $approve_id = $_GET['approve_id'];

    $select = mysqli_query($conn, "SELECT amount, STUD_ID FROM `deposit` WHERE deposit_id = '$approve_id'");
    while ($row = mysqli_fetch_array($select)) {
        // extract($row);
        $amount = $row['amount'];
        $STUD_ID = $row['STUD_ID'];
    }
    $details = "Your request is approved!";
    // echo $STUD_ID;
    $query = mysqli_query($conn, "UPDATE `deposit` SET `status`='approved', date = '$currentDate', time = '$current_time' WHERE deposit_id = '$approve_id'");

    $insertReceipt = mysqli_query($conn, "INSERT INTO `receipts`(`STUD_ID`, `deposit_amount`, `status`, `details`) VALUES ('$STUD_ID','$amount','Approved','$details')");

    $insert = mysqli_query($conn, "UPDATE `stud_info` SET `BALANCE`=BALANCE + $amount WHERE STUD_ID = '$STUD_ID'");
    if ($insert) {
        $_SESSION['delete_deposit'] = "Cash In Approved.";
        header("location: student_cash_in.php");
    }
}

if (isset($_GET['reject_id'])) {
    $reject_id = $_GET['reject_id'];
    $sql = mysqli_query($conn, "DELETE FROM `deposit` WHERE deposit_id = '$reject_id'");
    if ($sql) {
        $_SESSION['delete_deposit'] = "Rejected.";
        header("location: student_cash_in.php");
    }
}
