<?php 
include("includes/header.php");

$message_obj = new Message($con, $loggedInUser);

$user_array=array();

if(isset($_GET['profile_user_name'])) {
  $user_name = $_GET['profile_user_name'];
  $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE user_name='$user_name'");
  $user_array = mysqli_fetch_array($user_details_query);  

  $num_friends_query = mysqli_query($con, "SELECT user_name, friend_name FROM m2m_friends WHERE user_name='$user_name'");
  $num_friends = mysqli_num_rows($num_friends_query);
   
  if ($user_array['gender'] == 'f') 
    $user_gender = "her";
  else
    $user_gender = "him";
}



if(isset($_POST['unfriend'])) {
  $user = new User($con, $loggedInUser);
  $user->unFriend($user_name);
}

if(isset($_POST['add_friend'])) {
  $user = new User($con, $loggedInUser);
  $user->sendRequest($user_name);
}
if(isset($_POST['respond_request'])) {
  header("Location: requests.php");
}

if(isset($_POST['post_message'])) {
  if(isset($_POST['message_body'])) {
    $body = mysqli_real_escape_string($con, $_POST['message_body']);
    $date = date("Y-m-d H:i:s");
    $message_obj->sendMessage($username, $body, $date);
  }

  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script> 
          $(function() {
              $('" . $link ."').tab('show');
          });
        </script>";


}

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

    <input type="submit" class="deep_blue" data-toggle="modal" data-target="#post_form" value="tell <?php echo $user_gender."..."; ?>">

    <?php  
    if($loggedInUser != $user_name) {
      echo '<div class="profile_info_bottom"><br>';
        echo $logged_in_user_obj->getMutualFriends($user_name) . " Mutual friends";
      echo '</div>';
    }


    ?>

  </div>


  <div class="profile_main_column column">

    <ul class="nav nav-tabs" role="tablist" id="profileTabs">
      <li role="presentation" class="active"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a></li>
      <li role="presentation"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab">Messages</a></li>
    </ul>

    <div class="tab-content">

      <div role="tabpanel" class="tab-pane fade in active" id="newsfeed_div">
        <div class="posts_area"></div>
        <img id="loading" src="assets/images/icons/loading.gif">
      </div>


      <div role="tabpanel" class="tab-pane fade" id="messages_div">
        <?php  
        

          echo "<h4>You and <a href='" . $user_name ."'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";

          echo "<div class='loaded_messages' id='scroll_messages'>";
            echo $message_obj->getMessages($user_name);
          echo "</div>";
        ?>



        <div class="message_post">
          <form action="" method="POST">
              <textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>
              <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
          </form>

        </div>

        <script>
          var div = document.getElementById("scroll_messages");
          div.scrollTop = div.scrollHeight;
        </script>
      </div>


    </div>


  </div>

<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="postModalLabel">tell <?php echo $profile_user_obj->getFirstName()."..."; ?></h4>
      </div>

      <div class="modal-body">
        <p>This a one-on-one conversation and will appear on your friends' profile page and also their newsfeed!</p>

        <form class="profile_post" action="" method="POST">
          <div class="form-group">
            <br>
            <textarea class="form-control" name="post_body"></textarea>
            <input type="hidden" name="user_from" value="<?php echo $loggedInUser; ?>">
            <input type="hidden" name="user_to" value="<?php echo $user_name; ?>">
          </div>
        </form>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
      </div>
    </div>
  </div>
</div>


<script>
  var userLoggedIn = '<?php echo $loggedInUser; ?>';
  var profileUsername = '<?php echo $user_name; ?>';

  $(document).ready(function() {

    $('#loading').show();

    //Original ajax request for loading first posts 
    $.ajax({
      url: "includes/handlers/ajax_load_profile_posts.php",
      type: "POST",
      data: "page=1&userLoggedIn=" + userLoggedIn + "&profile_user_name=" + profileUsername,
      cache:false,

      success: function(data) {
        $('#loading').hide();
        $('.posts_area').html(data);
      }
    });

    $(window).scroll(function() {
      var height = $('.posts_area').height(); //Div containing posts
      var scroll_top = $(this).scrollTop();
      var page = $('.posts_area').find('.nextPage').val();
      var noMorePosts = $('.posts_area').find('.noMorePosts').val();

      if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
        $('#loading').show();

        var ajaxReq = $.ajax({
          url: "includes/handlers/ajax_load_profile_posts.php",
          type: "POST",
          data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
          cache:false,

          success: function(response) {
            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

            $('#loading').hide();
            $('.posts_area').append(response);
          }
        });

      } //End if 

      return false;

    }); //End (window).scroll(function())


  });

  </script>





  </div>
</body>
</html>
