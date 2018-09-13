<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/athena-styles.css">
    <script type="text/javascript" src="assets/js/AthenaSocial.js"></script>  
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
        else
            $post_id = 0;

        if (isset($_GET['ratings'])) {
            $ratings = $_GET['ratings'];
            $post_id = $_GET['post_id'];
            $get_user_rating = mysqli_query($con, "SELECT COUNT(1) FROM post_rates WHERE post_id='$post_id' AND rated_user='$loggedInUser'");
            if (mysqli_num_rows($get_user_rating)>0) 
                $rate_insert = mysqli_query($con, "UPDATE post_rates SET rate='$ratings' Where post_id='$post_id' AND rated_user='$loggedInUser'"); 
            else
                $rate_insert = mysqli_query($con, "INSERT INTO post_rates (post_id, rated_user, rate) VALUES ('$post_id','$loggedInUser','$ratings') ");            
        }
        else
            $ratings=0;
 
        $getRate = mysqli_query($con, "SELECT rate FROM post_rates WHERE post_id='$post_id'");
        $avg_rate = 0;
                
        if (mysqli_num_rows($getRate)>0) { 

            while($rate_results = mysqli_fetch_assoc($getRate))
                $avg_rate = $rate_results['rate']+$avg_rate;
            
            $avg_rate = $avg_rate/mysqli_num_rows($getRate);
        }

        $getYourRating = mysqli_query($con, "SELECT rate FROM post_rates WHERE rated_user='$loggedInUser' AND post_id='$post_id'");
        if (mysqli_num_rows($getYourRating)>0) 
            $myRating = mysqli_fetch_array($getYourRating)['rate'];
        else
            $myRating = 0;

         $res=10;

        if (isset($_POST['btnSave'])) {

            $get_saved_post = mysqli_query($con, "SELECT * FROM posts_saved WHERE user_name='$loggedInUser' AND post_id='$post_id'");             

            if (mysqli_num_rows($get_saved_post)==0) 
                $post_save = mysqli_query($con, "INSERT INTO posts_saved (user_name, post_id) VALUES ('$loggedInUser', '$post_id')");                        
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

            if ($ratings==0) 
                $ratings=$myRating;
          
        echo '<form name="rate_form" id="rate_form" action="like.php?post_id=' . $post_id . '" method="POST">              
               <input type="submit" class="comment_like" name="btnUnlike" value="Unlike ">  
               <span class="comment_like">Avg-Rating:</span>
               <span class="avg_rate">'.$avg_rate.'</span>
               <span class="comment_like">&nbsp;&nbsp;&nbsp;&nbsp;Your-Rating: </span>
               <select class="comment_your_rate" name = "user_rating" id="user_rate_val" onchange="send_value(this.value,'.$post_id.')">
                    <option selected = "selected">'.$ratings.'</option>
                    <option value = "1">1</option>
                    <option value = "2">2</option>
                    <option value = "3">3</option>
                    <option value = "4">4</option>
                    <option value = "5">5</option>
                </select> 
                <span class="comment_rate">&nbsp;Share</span>  
                <input type="submit" class="comment_like" name="btnSave" id="btnSave" value="Save">      
              </form>
        ';
    }
    else {

        echo '<form name="rate_form" id="rate_form" action="like.php?post_id=' . $post_id . '" method="POST">                
                <input type="submit" class="comment_like" name="btnLike" value="Like"> 
                <span class="comment_like">Avg-Rating:</span>
                <span class="avg_rate">'.$avg_rate.'</span>
                <span class="comment_rate">&nbsp;&nbsp;&nbsp;&nbsp;Your-Rating: </span>
                 <select class="comment_your_rate" name = "user_rating" id="user_rate_val" onchange="send_value(this.value,'.$post_id.')">
                    <option selected = "selected">'.$ratings.'</option>
                    <option value = "1">1</option>
                    <option value = "2">2</option>
                    <option value = "3">3</option>
                    <option value = "4">4</option>
                    <option value = "5">5</option>
                </select>
                <span class="comment_rate" id="btnShare">&nbsp;Share</span>
                <input type="submit" class="comment_like" name="btnSave" id="btnSave" value="Save">                   
              </form>
         ';
    }
        
    ?>
</body>
</html>
