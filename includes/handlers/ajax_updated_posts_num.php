<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$newPost = new Post($con, $_REQUEST['userLoggedIn']);
$newPost->getPostCnt();

?>
