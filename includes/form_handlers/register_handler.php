<?php 
//declare variables
$fName = "";
$lName = "";
$email = "";
$gender = "";
$password = "";
$password2 = "";
$date = "";
$error_array = array();

if (isset($_POST['btnRegister'])){
    $fName = strip_tags($_POST['reg_fName']);
    $fName = str_replace(' ', '', $fName);
    $fName = ucfirst(strtolower($fName));
    $_SESSION['reg_fName'] = $fName;

    $lName = strip_tags($_POST['reg_lName']);
    $lName = str_replace(' ', '', $lName);
    $lName = ucfirst(strtolower($lName));
    $_SESSION['reg_lName'] = $lName;

    $email = strip_tags($_POST['reg_email']);
    $email = str_replace(' ', '', $email);
    $email = strtolower($email);
    $_SESSION['reg_email'] = $email;

    $gender = strip_tags($_POST['reg_gender']);
    $gender = str_replace(' ', '', $gender);
    $gender = strtolower($gender);
    $_SESSION['reg_gender'] = $gender;

    $password = strip_tags($_POST['reg_password']);
    $password2 = strip_tags($_POST['reg_password2']);

    $date = date("Y-m-d"); //this gets the current date

    if(filter_var($email, FILTER_VALIDATE_EMAIL))
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    else
       array_push($error_array, "The email format is incorrect. Please try again...<br>"); 
 
    $email_check = mysqli_query($con, "SELECT email from users where email = '$email'");
    $email_rows = mysqli_num_rows($email_check);

    if ($email_rows > 0) {
        array_push($error_array,"Email already in use. Please sign-in<br>");
    }

    if((strlen($fName)>25) || (strlen($fName)<2)){
        array_push($error_array, "First name should be between 2 and 25 characters<br>");
    }

    if(strlen($lName)>25){
        array_push($error_array, "Last name cannot be more than 25 characters<br>");
    }
     
    if($password != $password2){
        array_push($error_array, "Your passwords do not match<br>");
    }
 
    if ((strtolower($gender)!='m') && (strtolower($gender)!='f')) {
        array_push($error_array, "Incorrect gender... Please type 'M' or 'm' for male and 'F' or 'f' for female<br>");
    }
    //else{
      //  if(preq_match('/[^A-Za-z0-9]/', $password))
        //    echo "Your password can contain only English characters or numbers";
    //}

    if(strlen($password)>30 || strlen($password)<5){
        array_push($error_array, "Your password must be between 5 and 30 characters<br>");
    }

    if (empty($error_array)) {
        $userName = strtolower(substr($fName,0,1)).strtolower($lName);
       
        $userNameCheck = mysqli_query($con, "SELECT user_name from users where user_name='$userName'");
       
        $i=0;

        while (mysqli_num_rows($userNameCheck) != 0) {
            $i++;
            $userName = $userName.$i;
            $userNameCheck = mysqli_query($con, "SELECT user_name from users where user_name='$userName'");
        }
        
        if ($gender==strtolower('m')) 
            $profile_pic = "./assets/images/profile_pics/default/male.png";
        else
            $profile_pic = "./assets/images/profile_pics/default/female.png";

        $password = md5($password);
     
        $query = mysqli_query($con, "INSERT INTO users (first_name, last_name, user_name, email, password, signup_date, profile_pic, num_posts, num_likes, user_closed, friend_array, gender) values ('$fName', '$lName', '$userName', '$email', '$password', '$date', '$profile_pic', '0', '0', 'no', ',', '$gender')");

        $friend_query = mysqli_query($con, "INSERT INTO m2m_friends (user_name, friend_name) values ('".trim($userName)."','".trim($userName)."')");

        array_push($error_array, "<span style='color: #14C800'>You are successfully registered to AthenaSocial network platform!</span><br>");

        $_SESSION['reg_fName'] = "";
        $_SESSION['reg_lName'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_gender'] = "";
    }
}
?>
