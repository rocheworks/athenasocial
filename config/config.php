<?php 
ob_start();  //turn on the output buffering
session_start();
$timezone = date_default_timezone_set('Asia/Dubai');
$con = mysqli_connect("localhost", "root", "", "AthenaSocial");
if (mysqli_connect_errno()) {
    echo "Failed to connect".mysqli_connect_errno();
}
?>
