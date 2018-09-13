<?php 
include("../../config/config.php");
include("../classes/User.php");
 
$rate_value:$_POST['value'];
$userLoggedIn:$_POST['user']; 
$post_id: ['postId'];

$rate_insert = mysqli_query($con, "INSERT INTO post_rates (post_id, author, rate) VALUES ('$post_id','$userLoggedIn','$rate_value') "); 


?>
