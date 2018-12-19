<?php
    require_once('validate_input.php');
    require_once('connect_database.php');
    require_once('verify_account.php');

    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
        $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);
        $email = testInput($_GET['email'],$connection);
        $hash = testInput($_GET['email'],$connection);
        $active = 0;

        $verify = new VerifyAccount($email,$hash,$active,$connection);

        if ($verify->verify()){
            echo 'Successfully activated profile';
        }else{
            echo 'Could not active profile';
        }
         
    }else{
        echo 'Invalid input';
    }



?>