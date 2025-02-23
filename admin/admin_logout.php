<?php
include "conn.php";
session_start();

$_SESSION['admin_status'] = 'logout';
header("location:adminLogin.php");
