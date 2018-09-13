<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/athena-styles.css">
</head>
<body>
    <style type="text/css">
        *{
            font-family: 'courier new', sans-serif;
            font-size: 13px;
            text-shadow: 0px 10px 5px rgba(009,009,009,0.2), #fff 0 0 15px, #fff 0 0 15px;
        }
        body{
            background-color: #fff;
        }
        form{
            position: absolute;
            top: 0;
        }
    </style>
    <?php 
        require './config/config.php';
        include("./includes/classes/User.php");
        include("./includes/classes/Post.php");

        if (isset($_SESSION['user_name'])) {
            $loggedInUser = $_SESSION['user_name'];
            $user_query = mysqli_query($con, "SELECT * FROM users WHERE user_name = '$loggedInUser'");
            $user = mysqli_fetch_array($user_query);        
        }
        else
            header("Location: register.php");

        //get the id of post       
        if (isset($_GET['comment_id'])) {
            $comment_id = $_GET['comment_id'];
        }

        $getLikes = mysqli_query($con, "SELECT likes, author FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($getLikes);
        $total_likes = $row['likes'];
        $user_liked = $row['author'];
        
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE user_name ='$user_liked'");
        $row = mysqli_fetch_array($user_details_query);
        $total_user_likes=$row['num_likes'];

        //like button

        if (isset($_POST['btnLike'])) {       
             
            $total_likes++; 
            $post_id = $post_id;          
            $query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='".trim($post_id)."'");
            $total_user_likes++;
            $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE user_name='".trim($user_liked)."'");
            $update_user_likes = mysqli_query($con, "INSERT INTO likes (user_name, post_id) VALUES ('$loggedInUser', '$post_id')");
        }
        
        //  unlike button
        if (isset($_POST['btnUnlike'])) {
            $total_likes--;
            $query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='".trim($post_id)."'");
            $total_user_likes--;
            $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE user_name='".trim($user_liked)."'");
            $update_user_likes = mysqli_query($con, "DELETE FROM likes WHERE user_name='$loggedInUser' AND post_id='$post_id'");
        }
        // check if the post is currently liked or not?
        $current_like_query = mysqli_query($con, "SELECT * FROM likes WHERE user_name = '$loggedInUser' AND post_id='$post_id'");
        $num_rows = mysqli_num_rows($current_like_query);
        
        if($num_rows > 0) {
        echo '<form action="like.php?post_id=' . $post_id . '" method="POST">              
               <input type="submit" class="comment_like" name="btnUnlike" value="unlike">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="like_value">likes ['. $total_likes .']
                </div>
            </form>
        ';
    }
    else {
        echo '<form action="like.php?post_id=' . $post_id . '" method="POST">                
                <input type="submit" class="comment_like" name="btnLike" value="like">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="like_value">likes ['. $total_likes .']
                </div>
            </form>
        ';
    }
        
    ?>
</body>
</html>
