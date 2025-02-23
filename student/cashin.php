<?php

include "../conn.php";
session_start();

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

if (isset($_POST['deposit'])) {
    $STUD_ID = $_POST['STUD_ID'];
    $amount = $_POST['amount'];
    $send_to = $_POST['send_to'];

    if (empty($amount)) {
        $_SESSION['no_amount'] = "Please Input Amount for deposit.";
        header("location: student_cashin.php");
        exit();
    } elseif ($amount < 100) {
        $_SESSION['no_amount'] = "The minimum cash input is ₱100.";
        header("location: student_cashin.php");
        exit();
    }

    // Insert the deposit information into the database
    $insert = mysqli_query($conn, "INSERT INTO `deposit`(`STUD_ID`, `amount`, `date`, `time`, `status`, `send_to`)
                VALUES ('$STUD_ID','$amount','$currentDate','$current_time','pending','$send_to')");
    if ($insert) {
        $_SESSION['success_deposit'] = "Request for deposit has sent.";
        header("location: student_cashin.php");
    }
}
