<?php
   include("./includes/header.php");

   if (isset($_POST['btnPost'])) {
       $post = new Post($con, $loggedInUser);
       $post->submitPosts($_POST['post_text'], $loggedInUser);
   }
?> 
        <div class="user_details column">
            <a href=" <?php 
                echo $loggedInUser;
            ?>">
                <img src="<?php echo $user['profile_pic']; ?>">
            </a>
            <div class="user_details_left_right">
                <a style="color: #fff; background-image: linear-gradient(#333,#777); padding-left: 5px; padding-bottom: 2px; padding-top: 2px; margin:auto; text-align: center; " href=" <?php 
                    echo $loggedInUser;
                ?>">
                    <?php 
                        echo $user['first_name']." ".$user['last_name']." "."<br>";
                    ?>
                </a>
                <div id="#msg">
                   <!-- <?php 
                        //echo "Posts: ". $user['num_posts']."<br>";
                     ?>-->
                </div>
                <div class="numLikes">
                    <?php 
                         echo "Likes: ". $user['num_likes']."<br>";
                    ?>
                </div>
                <p id="msg"></p>
               <!-- <p id="testMsg"></p> -->
            </div>
        </div>

            <div class="main_column column">
                <form class="post_form" action="index.php" method="POST">
                    <textarea name="post_text" id="post_text" placeholder="Think aloud, Make a post, Break the silence!"></textarea>
                    <input type="submit" name="btnPost" id="btnPost" class="btn-post" value="post" onsubmit="updatePosts();">
                    <hr>
                </form>
                
                <!--<?php 
                    //$post = new Post($con, $loggedInUser);
                    //$post->getFriendsPosts();
                ?>-->

                <div class="posts_area"></div>
                <img id="loading" src="assets/images/icons/loading.gif">
                
            </div>

            <script>
                var userLoggedIn='  <?php echo  $loggedInUser; ?>';

                function updatePosts(){
                
                    $('.post_form').submit(function(e){
                           e.preventDefault();                                    
                           $.ajax({
                           url: "includes/handlers/ajax_updated_posts_num.php",                        
                           type: "POST",
                           data: "userLoggedIn=" + userLoggedIn,
                           cache: false,
                           success: function(data){
                                $('#msg').html(data);
                           }
                        });  
                    });                           
                    return false;
                }
                          
                $(document).ready(function() {
            
                    $.ajax({
                        url: "includes/handlers/ajax_initial_posts_num.php",                        
                        type: "POST",
                        data: "userLoggedIn=" + userLoggedIn,
                        cache:false, 
                        success: function(data){
                             $('#msg').html(data);
                        }
                    }); 

                    /*$.ajax({
                        url: "includes/handlers/ajax_updated_posts_body.php",                        
                        type: "POST",
                        data: "userLoggedIn=" + userLoggedIn,
                        cache:false, 
                        success: function(data){
                             $('#testMsg').html(data);
                        }
                    }); */  
                         

                   $('#loading').show();

                    $.ajax({
                        url: "includes/handlers/ajax_load_posts.php",                        
                        type: "POST",
                        data: "page=1&userLoggedIn=" + userLoggedIn,
                        cache:false, 
                        success: function(data){
                            $('#loading').hide();
                            $('.posts_area').html(data);
                        }
                    });

                    $(window).scroll(function(){

                        var height = $('.posts_area').height(); //area containing posts
                        var scroll_top = $(this).scrollTop();
                        var page = $('.posts_area').find('.nextPage').val();
                        var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                        //alert("height: "+height+", scroll_top: "+scroll_top+", page: "+page+", noMorePosts: "+noMorePosts);

                        if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                           // alert("Hi");
                            $('#loading').show();
                            

                            var ajaxReq = $.ajax({
                                url: "includes/handlers/ajax_load_posts.php",
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                                cache: false,
                                success: function(response){
                                    $('.posts_area').find('.nextPage').remove();  //removes current .nextPage
                                    $('.posts_area').find('.noMorePosts').remove(); 

                                    $('#loading').hide();
                                    $('.posts_area').append(response);
                                }
                            });
                        }
                        return false;  //end (window).scroll function
                    });

                });  
            </script> 
    </div>
</body>
</html>

