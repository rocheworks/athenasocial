<?php 
  class Message{

      private $userObj;
      private $con;

      public function __construct($con, $user){
          $this->con = $con;
          $this->userObj = new User($con, $user);
      }

      public function getMostRecentUser(){

         $userLoggedIn = $this->userObj->getUserName();

         $recent_user_qry = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");

         if (mysqli_num_rows($recent_user_qry) == 0) 
           return false;    //no interactions as of now. you will create a new message [as per messages.php]
         
         $row = mysqli_fetch_array($recent_user_qry);
         $user_to = $row['user_to'];
         $user_from = $row['user_from'];

         if ($user_to == $userLoggedIn) 
           return $user_from;
         else
           return $user_to;

      } 

      public function sendMessage($send_to, $body, $date){
        $user_from = $this->userObj->getUserName();
        if ($body != "") {
          $sendMessageQry = mysqli_query($this->con, "INSERT INTO messages(user_to, user_from, body, created_date, opened, viewed, deleted) values ('$send_to', '$user_from', '$body', '$date', 'no', 'no', 'no')");
        }
      }

      public function getMessages($other_user){
        $logged_in_user = $this->userObj->getUserName();
        $data="";
        $UpdateMsg_qry = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$logged_in_user' AND user_from='$other_user'");
        $getMessages_qry = mysqli_query($this->con, "SELECT * FROM messages WHERE ((user_from='$other_user' AND user_to='$logged_in_user') OR user_from='$logged_in_user' AND user_to='$other_user') ");

        while($row = mysqli_fetch_array($getMessages_qry)){
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $body = $row['body'];

            $div_top = ($user_to == $logged_in_user)?"<div class='message' id='green'>" : "<div class='message' id='blue'>";
            $data = $data . $div_top . $body . "</div><br><br>";
        }
        return $data;
      }

      public function getLatestMessage($user_logged_in, $user2){
          $details_array = array();
          $query = mysqli_query($this->con, "SELECT body, user_to, created_date FROM messages WHERE (user_to='$user_logged_in' AND user_from='$user2') OR (user_to='$user2' AND user_from='$user_logged_in') ORDER BY id DESC LIMIT 1");
          $row = mysqli_fetch_array($query);

          $user2Obj = new User($this->con, $user2);

          $send_by = ($row['user_to'] == $user_logged_in) ? ($user2Obj->getFirstName())." said: " : "you said: ";

          $printQry = "SELECT body, user_to, created_date FROM messages WHERE (user_to='$user_logged_in' AND user_from='$user2') OR (user_to='$user2' AND user_from='$user_logged_in') ORDER BY id DESC LIMIT 1";

          $body = $row['body'];

          //calculate post elapsed time 
          $createdDate = $row['created_date'];                 
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

          array_push($details_array, $send_by);
          array_push($details_array, $body);
          array_push($details_array, $time_message);         

          return $details_array;
      }

      public function getConversations(){
        $user_logged_in = $this->userObj->getUserName();
        $return_string = "";
        $conversations = array();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$user_logged_in' OR user_from='$user_logged_in' ORDER BY id DESC ");

        while ($row = mysqli_fetch_array($query)) {
          $user_to_push = ($row['user_to'] != $user_logged_in) ? $row['user_to'] : $row['user_from'];

          if (!in_array($user_to_push, $conversations)) {
            array_push($conversations, $user_to_push);
          }
        }
        foreach ($conversations as $user_name) {
          $user_found_obj = new User($this->con, $user_name);          
          $latest_message_details = $this->getLatestMessage($user_logged_in, $user_name);
          $dots = (strlen($latest_message_details[1]) >= 12)? "..." : "";
          $split = str_split($latest_message_details[1], 12);
          $split = $split[0].$dots;
            
          $return_string.="<a href='messages.php?user_name=$user_name'><div class='user_found_messages'>
             <img src='".$user_found_obj->getProfilePic()."'style='border-radius: 5px; margin-right: 5px; '>".$user_found_obj->getFirstAndLastName()."&nbsp;&nbsp;<span class='timestamp_smaller' id='grey'>".$latest_message_details[2]."</span>
                <p id='grey' style='margin: 0; color: #333;' >".$latest_message_details[0].$split."</p>             
                </div>
                </a>";                
        }
        return $return_string;
      }

    }
?>

 
