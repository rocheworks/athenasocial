<?php
    include("./includes/header.php");

    $user_array=array();
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE user_name='$loggedInUser'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends_query = mysqli_query($con, "SELECT user_name, friend_name FROM m2m_friends WHERE user_name='$loggedInUser'");
    $num_friends = mysqli_num_rows($num_friends_query);
?>

  <div class="profile_left" style="left: 0;">
    <img src="<?php echo $user_array['profile_pic']; ?>">

    <div class="profile_info">
      <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
      <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
      <p><?php echo "Friends: " . $num_friends ?></p>
    </div>

    <form action="<?php echo $user_name; ?>" method="POST">
      <?php 
      $profile_user_obj = new User($con, $loggedInUser); 
      if($profile_user_obj->isClosed()) {
        header("Location: user_closed.php");
      }

      $logged_in_user_obj = new User($con, $loggedInUser);  

      ?>
    </form>
  </div>

<div class="friend_admin_boxes" id="main_column">

    <h4 style="font-family: courer new, sans-serif; font-size: 17px; color: #006400; margin: 0px 25px 0px 25px;">Friend Requests</h4>
    <br>
    <div style="margin: 0px 25px 0px 25px;"> 
        <?php 
            $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$loggedInUser'");
            if (mysqli_num_rows($query) == 0) 
                echo "You have no new friend requests !";
            else{
                while ($row=mysqli_fetch_array($query)) {
                    $user_from = $row['user_from'];
                    $user_from_obj = new User($con, $user_from);
                    echo $user_from_obj->getFirstAndLastName()." sent you a friend request !";
                    $user_from_friendsArray = $user_from_obj->getFriendsArray();

                    if (isset($_POST['accept_request'.$user_from])) {
                       $makeFriends1 = mysqli_query($con, "INSERT INTO m2m_friends (user_name, friend_name) values ('$loggedInUser','$user_from')");
                       $makeFriends2 = mysqli_query($con, "INSERT INTO m2m_friends (user_name, friend_name) values('$user_from','$loggedInUser')");

                       $deleteRequest = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$loggedInUser' AND user_from='$user_from'");
                       echo "You are now friends with ".$user_from." !";
                       header("Location: requests.php");
                    }
                    if (isset($_POST['ignore_request'.$user_from])) {
                        $deleteRequest = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$loggedInUser' AND user_from='$user_from'");
                        echo "Friend request send by ".$user_from." has been ignored !";
                        header("Location: requests.php");
                    }
        ?>
        <hr>
        <form action="requests.php" method="POST">
             <div class="request_handlers">
                <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" class="acceptBtn" value="accept request">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" class="ignoreBtn" value="ignore request">
             </div>
        </form>
        <?php
                }
            }

        
        ?>
    </div>
</div>

<div class="friend_admin_boxes" style="height: 300px;" id="search_friends_column">
    <div class="message_post">
        <form action="" method="POST">
             <h4 style="font-family: courer new, sans-serif; font-size: 17px; color: #006400; margin: 0px 25px 0px 25px;">Add Friends</h4>
                <br><br>
                <span>Search for athenahealth members...</span>
                <br><br>
                    To: <input type='text' onkeyup='getAllUsers(this.value)' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>

                    <?php
                            echo "<div class='results'><div>";                                        
                    ?>                 
                </form>
            </div>

            <script>
                var div = document.getElementById("scroll_messages");
                div.scrollTop = div.scrollHeight;
            </script>

        </div>


</div>    
