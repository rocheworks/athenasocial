<?php 
   class Post{
       private $userObj;
       private $con;
       private $postCnt;

       public function __construct($con, $user){
           $this->con = $con;
           $this->userObj = new User($con, $user);
       }

       public function submitPosts($body, $send_to){
          $str="";
          $body = strip_tags($body); //clean the body off any HTML tag
          $body = mysqli_real_escape_string($this->con, $body); //escape characters that are illegal for the database such as '

          $check_empty = preg_replace("/\s+/", '', $body); // this check is to check if the body contains valid characters other than space
         
          $author=""; 

          if ($check_empty != "") {

             $createdDate = date('Y-m-d H:i:s');
             $author = $this->userObj->getUserName();

             if ($send_to == $author) 
                $send_to = "none";

            $query = mysqli_query($this->con, "INSERT INTO posts (body, author, send_to, created_date, user_closed, deleted, likes) values('$body', '$author', '$send_to', '$createdDate', 'no', 'no', '0')");

             $returned_id = mysqli_insert_id($this->con);

             $new_posts = $this->userObj->getNumPosts();

             $new_posts++;

             $updateQry = mysqli_query($this->con,"UPDATE users SET num_posts = '$new_posts' where user_name='$author'");   

             $postCnt = $new_posts;

             /*$author_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE user_name = '$author'");
                  $row_author = mysqli_fetch_array($author_details_query);
                  $first_name = $row_author['first_name'];
                  $last_name = $row_author['last_name'];
                  $profile_pic = $row_author['profile_pic'];
                  $time_message="Just now";

              $str = "<div class='status_post'>
                      <div class='post_profile_pic'>
                        <img src='$profile_pic' width='50'>
                      </div>
                      <div class='posted_by' style='color: #ACACAC;'>
                        <a href='$author'>$first_name $last_name</a> $send_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message 
                      </div>
                      <div id='post_body'>
                          $body
                          <br>
                      </div>
                      </div>
                      <hr>";

              echo $str;*/

              return $postCnt;

          }
       }

       public function getPostCnt(){
           echo "Posts: ".$postCnt;
       }   

       public function tests($data){
           $data_query = "";
           $retRes=0;
           $userLoggedIn = $data['userLoggedIn'];
           $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND author='".trim($userLoggedIn)."' OR author IN (SELECT friend_name FROM m2m_friends WHERE user_name='".trim($userLoggedIn)."') ORDER BY id DESC");
           if (mysqli_num_rows($data_query) > 0) {
                 $retRes = mysqli_num_rows($data_query);
           }
           echo $retRes;
       }

       public function getFriendsPosts($data, $limit){

          $page = $data['page'];
          $userLoggedIn = $data['userLoggedIn'];
          //$userLoggedIn = $this->userObj->getUserName();

          if ($page==1) 
             $start = 0;
          else
             $start = ($page-1) * $limit;          

          $str = "";  //string to return in case there is any error

          //$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
         $data_query = mysqli_query($this->con, "SELECT p.* FROM posts p, m2m_friends m WHERE deleted='no' AND p.author = m.friend_name AND m.user_name='".trim($userLoggedIn)."' ORDER BY p.id DESC");



          if (mysqli_num_rows($data_query) > 0) {
          
              $num_iterations = 0; //number of results checked (not necessarily posted)
              $count = 1; //how many results we loaded

              while($row = mysqli_fetch_array($data_query)){
                  $id = $row['id'];
                  $body = $row['body'];
                  $author = $row['author'];
                  $createdDate = $row['created_date'];
                  $send_to="";

                  //handle send_to string. If not send_to none then, then there is a hyperlink to the send_to user
                  
                  if($row['send_to'] == "none")
                      $receiver = "";
                  else{
                      $receiver = new User($this->con, $row['send_to']);
                      $send_to_name = $receiver->getFirstAndLastName();
                      $send_to = "to <a href='".$row['send_to']."'>".$send_to_name."</a>";
                  }
                  
                  //but if the user who posted the post is deactivated, then do not show the post
                  
                  $author_obj = new User($this->con, $author);

                  if ($author_obj->isClosed()) 
                     continue;

                  //$user_logged_obj = new User($this->con, $userLoggedIn);

                 // if ($user_logged_obj->isFriend($author)) {

                      if($num_iterations++ < $start)
                         continue;

                       //once 10 posts have been loaded, break;                   
                       if($count > $limit)
                          break;
                        else
                          $count++;
                  
                      if (("'".trim($userLoggedIn)."'") == ("'".trim($author)."'")) {
                         $delete_button = "<button class='delete_button btn-danger' id='post$id'><p style='margin: -1px;'>x</p></button>";

                       }
                      else{
                        $delete_button="";
                      }

                      $author_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE user_name = '$author'");
                      $row_author = mysqli_fetch_array($author_details_query);
                      $first_name = $row_author['first_name'];
                      $last_name = $row_author['last_name'];
                      $profile_pic = $row_author['profile_pic'];
                      ?>

                      <script>
                          function toggle<?php echo $id; ?>(){
                              var target = $(event.target); //here is where the user clicked
                              if (!target.is("a")) {
                                var element = document.getElementById("toggleComment<?php echo $id; ?>");
                                if (element.style.display=="block") 
                                    element.style.display="none";
                                else
                                    element.style.display="block";  
                              }                                    
                          }
                      </script>

                      <?php

                      $comments_count = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                      $comments_count = mysqli_num_rows($comments_count);

                      //calculate post elapsed time                  
                      $current_time = date('Y-m-d H:i:s');
                      $post_created_date = new DateTime($createdDate);
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

                     // }
                      //here
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

                      $str.= "<div class='status_post' onClick='javascript:toggle$id()'>
                          <div class='post_profile_pic'>
                            <img src='$profile_pic' width='50'>
                          </div>
                          <div class='posted_by' style='color: #ACACAC;'>
                            <a href='$author'>$first_name $last_name</a> $send_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message 
                            $delete_button
                          </div>
                          <div id='post_body'>
                              $body
                              <br>                          
                          </div>     
                              <div class='newsfeedResponses'>
                                  <br>comment [$comments_count] 
                                  <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                              </div>                   
                          </div>    
                          <div class='post_comment' id='toggleComment$id' style='display: none;'>
                             <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                             <input type='submit' class='comment_rate' name='btnRate' value='rate'> 
                          </div>
                          <hr>";
                    //}

                ?>

                <script>
                    $(document).ready(function(){
                       $('#post<?php echo $id; ?>').on('click', function(){
                          bootbox.confirm("Are you sure you want to delete this post?", function(result){
                              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result:result});
                              if (result) 
                                location.reload();
                          });
                       });
                    });
                </script>

                <?php
                  
                }  //for while loop

              if ($count>$limit)
                  $str.="<input type='hidden' class='nextPage' value='".($page + 1)."'><input type='hidden' class='noMorePosts' value='false'>";      
              else
                  $str.="<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center; font-size: 13px; font-family: Roboto, courier new, sans-serif; text-shadow: 0px 10px 5px rgba(009,009,009,0.2), #fff 0 0 15px, #fff 0 0 15px;'>no more posts to show </p>";
       
          }  // if statement before while loop
          echo $str;
      }

      public function testProfilePosts($data, $limit){
          $str="";
          $page = $data['page'];
          $userLoggedIn = $data['userLoggedIn'];
          $profile_user_name = $data['profile_user_name'];
          $str.="SELECT * FROM posts WHERE deleted='no' AND ((author='$profile_user_name' AND send_to='none') OR (send_to='$profile_user_name')) ORDER BY id DESC";
          echo $str;
      }

      public function getTimeLinePosts($data, $limit){

          $page = $data['page'];
          $userLoggedIn = $data['userLoggedIn'];
          $profile_user_name = $data['profile_user_name'];
          //$userLoggedIn = $this->userObj->getUserName();

          if ($page==1) 
             $start = 0;
          else
             $start = ($page-1) * $limit;          

          $str = "";  //string to return in case there is any error

          //$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
         $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND (author='$profile_user_name' AND (send_to='none' OR send_to='$userLoggedIn')) ORDER BY id DESC");

          if (mysqli_num_rows($data_query) > 0) {
          
              $num_iterations = 0; //number of results checked (not necessarily posted)
              $count = 1; //how many results we loaded

              while($row = mysqli_fetch_array($data_query)){
                  $id = $row['id'];
                  $body = $row['body'];
                  $author = $row['author'];
                  $createdDate = $row['created_date'];
                  $send_to="";
          
                      if($num_iterations++ < $start)
                         continue;

                       //once 10 posts have been loaded, break;                   
                       if($count > $limit)
                          break;
                        else
                          $count++;
                  
                      if (("'".trim($userLoggedIn)."'") == ("'".trim($author)."'")) {
                         $delete_button = "<button class='delete_button btn-danger' id='post$id'><p style='margin: -1px;'>x</p></button>";
                       }
                      else{
                        $delete_button="";
                      }

                      $author_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE user_name = '$author'");
                      $row_author = mysqli_fetch_array($author_details_query);
                      $first_name = $row_author['first_name'];
                      $last_name = $row_author['last_name'];
                      $profile_pic = $row_author['profile_pic'];
                      ?>

                      <script>
                          function toggle<?php echo $id; ?>(){
                              var target = $(event.target); //here is where the user clicked
                              if (!target.is("a")) {
                                var element = document.getElementById("toggleComment<?php echo $id; ?>");
                                if (element.style.display=="block") 
                                    element.style.display="none";
                                else
                                    element.style.display="block";  
                              }                                    
                          }
                      </script>

                      <?php

                      $comments_count = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id = '$id'");
                      $comments_count = mysqli_num_rows($comments_count);

                      //calculate post elapsed time                  
                      $current_time = date('Y-m-d H:i:s');
                      $post_created_date = new DateTime($createdDate);
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

                     // }
                      //here
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

                      $str.= "<div class='status_post' onClick='javascript:toggle$id()'>
                          <div class='post_profile_pic'>
                            <img src='$profile_pic' width='50'>
                          </div>
                          <div class='posted_by' style='color: #ACACAC;'>
                            <a href='$author'>$first_name $last_name</a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message 
                            $delete_button
                          </div>
                          <div id='post_body'>
                              $body
                              <br>                          
                          </div>     
                              <div class='newsfeedResponses'>
                                  <br>comment [$comments_count] 
                                  <iframe src='like.php?post_id=$id' scrolling='no'></iframe>
                              </div>                   
                          </div>    
                          <div class='post_comment' id='toggleComment$id' style='display: none;'>
                             <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                          </div>
                          <hr>";
                    //}

                ?>

                <script>
                    $(document).ready(function(){
                       $('#post<?php echo $id; ?>').on('click', function(){
                          bootbox.confirm("Are you sure you want to delete this post?", function(result){
                              $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result:result});
                              if (result) 
                                location.reload();
                          });
                       });
                    });
                </script>

                <?php
                  
                }  //for while loop

              if ($count>$limit)
                  $str.="<input type='hidden' class='nextPage' value='".($page + 1)."'><input type='hidden' class='noMorePosts' value='false'>";      
              else
                  $str.="<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center; font-size: 13px; font-family: Roboto, courier new, sans-serif; text-shadow: 0px 10px 5px rgba(009,009,009,0.2), #fff 0 0 15px, #fff 0 0 15px;'>no more posts to show</p>";

       
          }  // if statement before while loop
          echo $str;         
      }
 }
?>
