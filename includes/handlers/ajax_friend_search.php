<?php 
include("../../config/config.php");
include("../classes/User.php");
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
$names = explode(" ", $query);

if (strpos($query, "_") !== false) 
    $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE user_name LIKE '$query%' AND user_closed='no' LIMIT 8 ");
else if(count($names) == 2)
    $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%' )AND user_closed='no' LIMIT 8");
else
    $usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%' )AND user_closed='no' LIMIT 8");

if (($query != "") && ($userLoggedIn=="*")) {
    while($row=mysqli_fetch_array($usersReturned)){
        $user = new User($con, $row['user_name']);
        $mutual_friends = $user->getMutualFriends($row['user_name'])." friends in common";
        $friend_user = $user->getUserName();
     
        echo "<div class = 'resultDisplay'>
            <a href='".$row['user_name']."' style = 'color: #111'>
            <div class = 'liveSearchProfilePic'>
            <img src='".$row['profile_pic']."'>
            <div>
               <div class='liveSearchText'>
               ".$row['first_name']." ".$row['last_name']. "
               <!-- <p>".$row['user_name']."</p> -->
               <p id='grey'>".$mutual_friends."</p>
            </div>
            </a>
        </div>";        
    }
}else{
    if ($query != ""){
        while($row=mysqli_fetch_array($usersReturned)){
            $user = new User($con, $userLoggedIn);
            $mutual_friends = $user->getMutualFriends($row['user_name'])." friends in common";
            if ($user->isFriend($row['user_name'])) {
                echo "<div class = 'resultDisplay'>
                    <a href='messages.php?user_name=".$row['user_name']."' style = 'color: #111'>
                        <div class = 'liveSearchProfilePic'>
                            <img src='".$row['profile_pic']."'>
                        <div>
                        <div class='liveSearchText'>
                            ".$row['first_name']." ".$row['last_name']. "
                            <!-- <p>".$row['user_name']."</p> -->
                            <p id='grey'>".$mutual_friends."</p>
                        </div>
                    </a>
                 </div>";
            }
        }
    }
}
//$print_text = "requests.php?user_name='".$friend_user."'";
//echo $print_text;

?>
