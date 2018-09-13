<?php 
   class User{
       private $user;
       private $con;

       public function __construct($con, $user){
           $this->con = $con;
           $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE user_name='$user'");
           $this->user = mysqli_fetch_array($user_details_query);
       }
       public function getFirstAndLastName(){
        // $userName = this->user['user_name'];
          return $this->user['first_name']." ".$this->user['last_name'];
       }

       public function getProfilePic(){
            return $this->user['profile_pic'];
       }

       public function getUserName(){
           return $this->user['user_name'];
       }

       public function getUpdatedPosts($user){
            $query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE user_name='".trim($user)."'");
            $ret = 0;
            if (mysqli_num_rows($query)==1) {
                $ret = mysqli_fetch_array($query)['num_posts'];                
            }           
            echo "Posts: ".$ret;                   
       }

       public function getNumPosts(){
            $userName = $this->user['user_name'];
            $query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE user_name='$userName'");
            $ret = '0';
            if (mysqli_num_rows($query)==1) {
                $ret = mysqli_fetch_array($query)['num_posts'];                
            }           
            return $ret;
       }

       public function getGender(){
          return $this->user['gender'];
       }

       public function getFirstName(){
          return $this->user['first_name'];
       }

       public function isClosed(){
          if ($this->user['user_closed']=='yes') {
              return true;
          }
          return false;
       }

       public function isFriend($userName){

           $m2mUserName = $this->user['user_name'];
           $friendsList = mysqli_query($this->con, "SELECT * FROM m2m_friends WHERE user_name='$m2mUserName' AND friend_name='$userName'");
           if (mysqli_num_rows($friendsList)>0) 
               return true;
           else
               return false;     
       }
    
       public function isPendingResonseTo($user_from){
          $user_to = $this->user['user_name'];
          $friendRequest = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_from='$user_from' AND user_to='$user_to'");
          if (mysqli_num_rows($friendRequest)>0) 
              return true;
          else
            return false;
          
       }

       public function isPendingResonseFrom($user_to){
          $user_from = $this->user['user_name'];
          $friendRequest = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_from='$user_from' AND user_to='$user_to'");
          if (mysqli_num_rows($friendRequest)>0) 
              return true;
          else
            return false;
          
       }

       public function unFriend($user_to_unfriend){
          $logged_in_user = $this->user['user_name'];
          $unfriend_qry1 = mysqli_query($this->con, "DELETE FROM m2m_friends WHERE user_name='$logged_in_user' AND friend_name='$user_to_unfriend'");
          $unfriend_qry2 = mysqli_query($this->con, "DELETE FROM m2m_friends WHERE user_name='$user_to_unfriend' AND friend_name='$logged_in_user'");
       } 

       public function sendRequest($user_to){
          $user_from = $this->user['user_name'];
          $query = mysqli_query($this->con, "INSERT INTO friend_requests(user_to, user_from) values ('$user_to', '$user_from')");
       }

       public function addFriend($friend_user_name){
          $user_user_name=$this->user['user_name'];
          $insQuery = mysqli_query($this->con, "INSERT INTO m2m_friends(user_name, friend_name) values ('$user_user_name', '$friend_user_name')");
       }

       public function getFriendsArray(){
          $user = $this->user['user_name'];
          $friend_array=array();

          $friendQry = mysqli_query($this->con, "SELECT friend_name FROM m2m_friends WHERE user_name='$user'");
          
          if (mysqli_num_rows($friendQry)>0) {
              while ($row = mysqli_fetch_array($friendQry)) {
                   array_push($friend_array, $row['friend_name']);
              }
          }
          return $friend_array;
       }

       public function getMutualFriends($friend_name){

          $main_user = $this->user['user_name'];
          $mutual_friends_array = array();
          $mutual_friends_count=0;

          $main_user_friends = mysqli_query($this->con, "SELECT friend_name FROM m2m_friends WHERE user_name='$friend_name' AND friend_name!='$friend_name' AND friend_name IN (SELECT friend_name FROM m2m_friends WHERE user_name='$main_user' AND friend_name!='$main_user')");

          if (mysqli_num_rows($main_user_friends) > 0) {
             $mutual_friends_count=mysqli_num_rows($main_user_friends);
             while ($row=mysqli_fetch_array($main_user_friends)) {
               array_push($mutual_friends_array, $row['friend_name']);
             }
          }
          return $mutual_friends_count;
       }
   }
?>
