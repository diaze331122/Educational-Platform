<?php
    require_once('validate_input.php');
    require_once('connect_database.php');
    require_once('registration.php');

    //check if all fields are not empty
    if (isset($_POST['firstname']) && isset($_POST['lastname']) 
        && isset($_POST['username']) && isset($_POST['email'])
        && isset($_POST['password']) && isset($_POST['type'])){
        //open database connection
        $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);
        //clean up input
        $fname = testInput($_POST['firstname'],$connection);
        $lname = testInput($_POST['lastname'],$connection);
        $username = testInput($_POST['username'],$connection);
        $email = testInput($_POST['email'],$connection);
        $password = testInput($_POST['password'],$connection);
        $type = testInput($_POST['type'],$connection);
        $hash = generateHash(); //generate hash for verification/activation
        //create new registration object        
        $register = new Registration($username,$password,$fname,$lname,$email,$userType,$hash,$conn);
        
        if ($register->registerUser()){
            $url = getVerificationURL($email,$hash);
            sendVerificationEmail($email,$url);
            redirect('page-that-shows-success-mssg');        
        }else{
            echo 'Username of password is already in use';
        }

        function generateHash(){
            return md5(rand(0,1000));
        }

        function sendVerificationEmail($email,$url){
            $to = $email;
            $subject = 'Confirm Account';
            $mssg = 'Thank you for signing up. You can verify your account by clicking this link'.$url;
            $from = 'From:noreply@yourwebsite.com'.'\r\n';
            mail($to, $subject, $mssg, $from); // Send verification email
        }

        function getVerificationURL($email,$hash){
            return 'http://www.domain-name.com/verify.php?email='.$email.'&hash='.$hash;
        }
        
        function redirect($path){
            header($path);
        }
    }
?>