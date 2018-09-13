<?php
    require '../../config/config.php';
    if (isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
        if (isset($_POST['result'])) {
            if (isset($_POST)==true) {
                $del_qry = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
            }
        }
    }
?>
