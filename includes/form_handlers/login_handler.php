<?php

    if (isset($_POST['btnLogin'])) {
        
        $loginEmail = filter_var($_POST['loginEmail'],FILTER_SANITIZE_EMAIL);
        
        $_SESSION['loginEmail'] = $loginEmail;

        $loginPassword = md5($_POST['loginPassword']);
        $check_database_query = mysqli_query($con, "SELECT * FROM users WHERE email = '$loginEmail' AND password = '$loginPassword'");

        if(mysqli_num_rows($check_database_query) == 1){
            $row = mysqli_fetch_array($check_database_query);
            $userName = $row['user_name'];    
            $_SESSION['user_name'] = $userName;
            header("Location: index.php");
            exit();
        }  
        else
            array_push($error_array, "The email-address or the password was wrong...<br>");            
    }
?>
