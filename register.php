<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to athenahealth</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/register.js"></script>
</head>
<body>
    
    <style type="text/css">
        *{font-size: 13px;
          font-family: Helvetica, Arial, sans-serif;
        }
    </style>

    <?php 
        if (isset($_POST['btnRegister'])) {
            echo '
                <script>
                    $(document).ready(function(){
                        $("#first").hide();
                        $("#second").show();
                    });
                </script>
            ';
        }
    ?>
<div class="login-box">
    <div class="login_header">
        <h3 style="color: #00688B; font-style: italic; font-size: large;">Athena Social Networks!</h3>
        
        <h4 style="color: #008000; font-size: 15px;">Login or Signup</h4>
    </div>
    <div id="first">
        <form action="register.php" method="POST">   

            <input type="email" name="loginEmail" placeholder="Email" required="true" value="<?php 
                if(isset($_SESSION['loginEmail'])) echo $_SESSION['loginEmail'];
                ?>">

                <br>

                <input type="password" name="loginPassword" placeholder="Password"> 

                <br>   

                <input class="glass-yellow" type="submit" name="btnLogin" value="login">

                <br>  

                <input type="text" readonly="true" tabindex="-1" name="login_err" class="err-display" value="<?php
                    if(in_array("The email-address or the password was wrong...<br>", $error_array))
                       echo "The email-id and/or the password was wrong. Please try again...";
                    ?>"> 

                <br>

                <a href="#" id="signup" class="signup">Need an account? Sign-up here!</a>  

                <br>    
        </form>
    </div>

    <br>
    <div id="second">
    <form action="register.php" method="POST">            
                   
        <input type="text" name="reg_fName" placeholder="first name" value="<?php
            if(isset($_SESSION['reg_fName']))
                echo $_SESSION['reg_fName'];
            ?>" required="true">

        <br>

        <input type="text" name="reg_lName" placeholder="last name" value="<?php 
            if(isset($_SESSION['reg_lName']))
                echo $_SESSION['reg_lName'];
            ?>" required="true">

        <br>

        <input type="text" name="reg_email" placeholder="email" value="<?php 
            if(isset($_SESSION['reg_email']))
                echo $_SESSION['reg_email'];
            ?>" required="true">

        <br>

        <input type="text" name="reg_gender" placeholder="gender: M / F" value="<?php 
            if(isset($_SESSION['reg_gender']))
                echo $_SESSION['reg_gender'];
            ?>" required="true">

            <br>

            <input type="text" name="reg_password" placeholder="password" required="true">

            <br>

            <input type="text" name="reg_password2" placeholder="confirm password" required="true">

            <br>

            <input tabindex="-1" type="text" name="reg_password_err" readonly="true" style="width: 95%;" class="err-display" value="<?php 
                if (in_array("Your password must be between 5 and 30 characters<br>",$error_array)) 
                    echo "Your password must be between 5 and 30 characters";
                else if(in_array("Your passwords do not match<br>", $error_array))
                    echo "Your passwords do not match";
                else if(in_array("The email format is incorrect. Please try again...<br>", $error_array))
                    echo "The email format is incorrect. Please try again...";
                else if (in_array("Email already in use. Please sign-in<br>", $error_array)) 
                    echo "Email already in use. Please sign-in...";
                else if(in_array("Incorrect gender... Please type 'M' or 'm' for male and 'F' or 'f' for female<br>", $error_array))
                    echo "Incorrect gender. Please type 'M' for male and 'F' for female";
                else if (in_array("First name should be between 2 and 25 characters<br>", $error_array)) 
                    echo "First name should be between 2 and 25 characters";   
                else if (in_array("Last name cannot be more than 25 characters<br>", $error_array)) 
                    echo "Last name cannot be more than 25 characters";                           
                ?>">

                <br>

                <input class="glass" type="submit" name="btnRegister" value="register" >            

                    <br>
                    <input tabindex="-1" type="text" name="reg_success" readonly="true" style="color: #6ab04c; width: 98%; text-align: center; font-weight: bolder;" class="err-display" value="<?php 
                        if(in_array("<span style='color: #14C800'>You are successfully registered to AthenaSocial network platform!</span><br>", $error_array))
                            echo "Your account has been created successfully!";
                        ?>">

                        <br>

                        <a href="#" id="signin" class="signin">Already have an account? Sign-in here!</a> 

                        <br>
                </form>
            </div>
        </div>
</body>
</html>

