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
        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
        }

        $getLikes = mysqli_query($con, "SELECT rate, author FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($getLikes);
        $avg_rate = $row['rate'];
        $user_rated = $row['author'];

        if (isset($_POST['btnRate'])) {  
            $post_rate=1;
            $rate_insert = mysqli_query($con, "INSERT INTO post_rates (post_id, author, rate) VALUES ('$post_id','$loggedInUser','$post_rate') ");   
        }
  
        echo '<form action="rate.php?post_id=' . $post_id . '" method="POST">              
                <input type="submit" class="comment_rate" name="btnRate" value="rate">                    
             </form>
        ';        
    ?>
</body>
</html>
