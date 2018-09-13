<!DOCTYPE html>
<html>
<head>
    <title>Athena Social Networks!</title>
</head>
<body >
    <style type="text/css">
        *{font-size: 13px;
          font-family: Helvetica, Arial, sans-serif;
          text-shadow: 0px 10px 5px rgba(009,009,009,0.2), #fff 0 0 15px, #fff 0 0 15px;
        }
    </style>
    <?php 
        require './config/config.php';
        include("./includes/classes/User.php");

        if (isset($_SESSION['user_name'])) {
            $loggedInUser = $_SESSION['user_name'];
            $user_query = mysqli_query($con, "SELECT * FROM users WHERE user_name = '$loggedInUser'");
            $user = mysqli_fetch_array($user_query);        
        }
        else
            header("Location: register.php");
    ?>
    <script>

        function toggle(){
            var element = document.getElementById("comment_section");
            if (element.style.display=="block") 
                element.style.display="none";
            else
                element.style.display="block";            
        }

    </script>
    <?php 
        if (isset($_GET['post_id'])) {
            $post_id = $_GET['post_id'];
        }
        $user_query = mysqli_query($con, "SELECT author, send_to FROM posts WHERE id='$post_id'" );
        $row = mysqli_fetch_array($user_query);
        $posted_to = $row['author'];  
                 
        if (isset($_POST['postComment'.$post_id])) {
                $comment_body = $_POST['comment_body'];
                $comment_body = mysqli_escape_string($con, $comment_body);
                $current_time = date("Y-m-d H:i:s");               
                $insert_comment = mysqli_query($con, "INSERT INTO comments (comment_body, commentator, recepient, created_date, is_deleted, post_id) values ('$comment_body', '$loggedInUser', '$posted_to', '$current_time', 'no', '$post_id')");
                echo "<p> Comment Posted! </p>";
            }   
    ?>
    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" method="POST" id="comment_form" name="postComment<?php echo $post_id; ?>">
        <div style="width: 100%;clear: both; position: fixed; background-color: #fff;">
            <textarea style=" display: block; float: left; width: 80%; height: 55px; border-color:#D3D3D3; margin: 3px 3px 3px 5px; " name="comment_body"></textarea>
            <input type="submit" style="display: inline-block;margin-left: 2%;margin-top: 2.5%; padding: 7px; border-radius: 5px; border:none; -webkit-text-shadow: 0px -1px #999; background-image: linear-gradient(#ffff19,#ffff46); box-shadow: 0px 1px 4px -2px #999; -moz-box-shadow: 0px 1px 4px -2px #999; -webkit-box-shadow: 0px 1px 4px -2px #999; -webkit-text-shadow: 0px -1px #999; -moz-text-shadow: 0px -1px #999; text-shadow: 0px 10px 5px rgba(009,009,009,0.3), #fff 0 0 15px, #fff 0 0 15px; color: #111;" name="postComment<?php echo $post_id?>" value="send now !" />             
        </div>
        <br> <br> <br><br><br>
    </form>
    </div>
    <!--load comments> -->

    <?php 
        $get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER BY id ASC");
        $count = mysqli_num_rows($get_comments);
        if ($count > 0) {
            while($comment = mysqli_fetch_array($get_comments)){
                $comment_body = $comment['comment_body'];
                $recepient = $comment['recepient'];
                $commentator = $comment['commentator'];
                $created_date = $comment['created_date'];
                $is_deleted = $comment['is_deleted'];

                //calculate post elapsed time                  
                $current_time = date('Y-m-d H:i:s');
                $post_created_date = new DateTime($created_date);
                $post_display_date = new DateTime($current_time);
                $elapsed_time = $post_created_date->diff($post_display_date);

                if ($elapsed_time->y >= 1) {

                    if($elapsed_time == 1)
                        $time_message = $elapsed_time->y." year ago";
                    else
                        $time_message = $elapsed_time->y." years ago";

                }
                else if($elapsed_time->m >= 1){

                    if($elapsed_time->d == 0)
                        $days = " ago";
                    else if ($elapsed_time->d == 1) 
                      $days = $elapsed_time->d." day ago";
                    else
                      $days = $elapsed_time->d." days ago";
                    if ($elapsed_time->m == 1) 
                       $time_message = $elapsed_time->m." month".$days;
                    else
                      $time_message = $elapsed_time->m." months".$days;
                }
                else if ($elapsed_time->d >=1 ) {
                    if ($elapsed_time->d == 1) 
                      $time_message = "Yesterday";
                    else
                      $time_message = $elapsed_time->d." days ago";        
                }else if ($elapsed_time->h >= 1) {
                    if ($elapsed_time->h == 1) 
                       $time_message = $elapsed_time->h." hour ago";
                    else
                        $time_message = $elapsed_time->h." hours ago";
                }else if ($elapsed_time->i >= 1) {
                    if ($elapsed_time->i == 1) 
                        $time_message = $elapsed_time->i." minute ago";
                    else
                        $time_message = $elapsed_time->i." minutes ago";
                }else{
                    if ($elapsed_time->s < 30) 
                        $time_message = " Just now";
                    else
                        $time_message = $elapsed_time->s." seconds ago";
                }
                $user_obj = new User($con, $commentator);
                ?>
              <form>
                <div class="comment_section" style="width: 97%; margin: 3px 3px 3px 5px;">                 

                    <div style="border: 2px solid #fff; border-radius: 7px;  text-shadow: 0px 10px 5px rgba(009,009,009,0.2); background-color: #D3D3D3; ">     
                        <a target="_parent" style="text-decoration: none;" href="<?php echo $commentator ?>"><img style="border-radius: 3px;  float: left; height: 30px; margin:0 7px;" src="<?php echo $user_obj->getProfilePic(); ?>"  title="<?php echo $commentator ?>"></a>

                        <a target="_parent" style="text-decoration: none; margin:0 7px; color: #b71540;" href="<?php echo $commentator ?>"><?php echo "<br>".$user_obj->getFirstAndLastName(); ?></a>&nbsp;&nbsp;
                        &nbsp;<?php echo "<label style='color: #0652DD; font-size: smaller;'>".$time_message."</label><br><br>&nbsp;".$comment_body."<br><br>";?>
                   
                 </div>      
                </div>
                </form>
                <?php
            }  //end while loop
        }
        else
            echo "<center><br><br>no comments to show !</center>"
    ?>
    
    </div>
</body>
</html>
