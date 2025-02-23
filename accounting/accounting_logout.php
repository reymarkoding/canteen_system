<?php
include "conn.php";
session_start();

$_SESSION['accounting_status'] = 'logout';
header("location:accounting_login.php");
