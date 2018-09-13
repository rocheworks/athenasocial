<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

if (isset($_POST['post_body'])) {
    $post = new Post($con, $_POST['user_from']);
    echo "user_from: ".$post->get;
    $post->submitPosts($_POST['post_body'], $_POST['user_to']);
}

?>
