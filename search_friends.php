<?php 
include("includes/header.php");

?>
  <style type="text/css">
    .wrapper {
      margin-left: 0px;
      padding-left: 0px;
    }

  </style>
  
  <div class="profile_left">
    <img src="<?php echo $user_array['profile_pic']; ?>">

    <div class="profile_info">
      <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
      <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
      <p><?php echo "Friends: " . $num_friends ?></p>
    </div>

    <form action="<?php echo $user_name; ?>" method="POST">
      <?php 
      $profile_user_obj = new User($con, $user_name); 
      if($profile_user_obj->isClosed()) {
        header("Location: user_closed.php");
      }

      $logged_in_user_obj = new User($con, $loggedInUser); 

      if($loggedInUser != $user_name) {

        if($logged_in_user_obj->isFriend($user_name)) {
          echo '<input type="submit" name="unfriend" class="danger" value="Unfriend"><br>';
        }
        else if ($logged_in_user_obj->isPendingResonseTo($user_name)) {
          echo '<input type="submit" name="respond_request" class="warning" value="Respond to Request"><br>';
        }
        else if ($logged_in_user_obj->isPendingResonseFrom($user_name)) {
          echo '<input type="submit" name="" class="default" value="Request Sent"><br>';
        }
        else 
          echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';

      }

      ?>
    </form>
    <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="tell me...">

    <?php  
    if($loggedInUser != $user_name) {
      echo '<div class="profile_info_bottom"><br>';
        echo $logged_in_user_obj->getMutualFriends($user_name) . " Mutual friends";
      echo '</div>';
    }


    ?>

  </div>
  </body>
</html>
