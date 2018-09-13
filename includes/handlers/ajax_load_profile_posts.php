<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$limit = 20; //Number of posts to be loaded per call

$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->getTimeLinePosts($_REQUEST, $limit);
?>
