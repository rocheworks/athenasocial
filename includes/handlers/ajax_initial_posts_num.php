<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$newUser = new User($con, $_REQUEST['userLoggedIn']);
$newUser->getUpdatedPosts($_REQUEST['userLoggedIn']);

?>
