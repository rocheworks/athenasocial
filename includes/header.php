<?php 
require './config/config.php';
include("./includes/classes/User.php");
include("./includes/classes/Post.php");
include("./includes/classes/Message.php");

if (isset($_SESSION['user_name'])) {
    $loggedInUser = $_SESSION['user_name'];
    $user_query = mysqli_query($con, "SELECT * FROM users WHERE user_name = '$loggedInUser'");
    $user = mysqli_fetch_array($user_query);        
}
else
    header("Location: register.php");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Athena Social Networks!</title>

    <!-- all your javascripts, keep below... -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/js/bootbox.min.js"></script>
    <script type="text/javascript" src="assets/js/AthenaSocial.js"></script>   
    <script src="assets/js/jquery.jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script> 

    <!-- keep all your css files below... -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/athena-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />

</head>
<body>

     <div class="top_bar">
        <div class="logo">
            <a href="index.php" style="font-weight: bold; font-size: 13px; color: #0043f0;">Athena Social Networks!</a>          
        </div>
        <nav>     

            <a href="<?php echo $user['user_name']; ?>" style="font-style: italic; font-size: 13px; font-weight: bold; color: #0088cc; top:50px; text-decoration: none;left:-200px; top:10px; position: absolute;" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#0043f0'">
                <?php echo  "Welcome ".$user['first_name'].' '.$user['last_name'].'!'; ?>
            </a>&nbsp;&nbsp;
            
            <a href="index.php" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#ff5b69'">
                <i class="fa fa-home"></i>
            </a>&nbsp;&nbsp;

            <a href="messages.php" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#ff5b69'">
                <i class="fa fa-envelope"></i>
            </a>&nbsp;&nbsp;
            <a href="#" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#ff5b69'">
                <i class="fa fa-bell"></i>
            </a>&nbsp;
            <a href="requests.php" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#ff5b69'">
                <i class="fa fa-users"></i>
            </a>&nbsp;&nbsp;
            <a href="#" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#ff5b69'">
                <i class="fa fa-cog"></i>
            </a>&nbsp;&nbsp;
            <a href="includes/handlers/logout.php" onmouseover="this.style.color = '#009432'"; onmouseout="this.style.color='#ff5b69'">
                <i class="fa fa-sign-out"></i>
            </a>            
            <div class="header_text">
             <a href="index.php" style="font-size: 10px; color: darkgreen;" onmouseover="this.style.color = '#ff5b69'"; onmouseout="this.style.color='#009432'">
                Home</a>
             <a href="messages.php" style="font-size: 10px; color: darkgreen;" onmouseover="this.style.color = '#ff5b69'"; onmouseout="this.style.color='#009432'">
                Messages</a>
             <a href="#" style="font-size: 10px; color: darkgreen;" onmouseover="this.style.color = '#ff5b69'"; onmouseout="this.style.color='#009432'">
                Notify</a>
             <a href="requests.php" style="font-size: 10px; color: darkgreen;" onmouseover="this.style.color = '#ff5b69'"; onmouseout="this.style.color='#009432'">
                Friends</a>
             <a href="#" style="font-size: 10px; color: darkgreen;" onmouseover="this.style.color = '#ff5b69'"; onmouseout="this.style.color='#009432'">
                Settings</a>
             <a href="includes/handlers/logout.php" style="font-size: 10px; color: darkgreen;" onmouseover="this.style.color = '#ff5b69'"; onmouseout="this.style.color='#009432'">
                logout</a>            
            </div>
            <br> 

        </nav>

     </div>

    <div class="wrapper">
