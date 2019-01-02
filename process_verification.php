<?php
    require_once('validate_input.php');
    require_once('connect_database.php');
    require_once('verify_account.php');

    if (isset($_GET['i']) && !empty($_GET['i']) && isset($_GET['h']) && !empty($_GET['h'])){
         //open database connection
         $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);

         $user_id = testInput($_GET['i'],$connection);
         $hash = testInput($_GET['h'],$connection); 
         $verify = new VerifyAccount($user_id,$hash,$connection);

         if ($verify->verify()){
             echo 'Successfully activated profile. Account can now be logged in.';
         }else{
             echo 'Could not active profile.';
         }
                   
    }else{
        echo 'Invalid verification';
        //header('redirect error page');
    } 

?>