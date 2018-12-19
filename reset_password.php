<?php
include_once('connect_database.php');
include_once('validate_input.php');

//If email is posted
if (isset($_POST['email']) && $_POST['email'] != ''){
    $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);
    $email = testInput($data,$connection);

    if (checkIfAccountExists($email,$connection)){
        $hash = generateHash();
        $url = getPasswordUpdateURL($email,$hash);
        inactivateAccount($email,$hash,$connection);
        sendPasswordResetEmail($email,$url);
        echo 'Please check your email to continue password reset';
    }else{
        echo 'The account could not be verified';
    }
    $connection->close();
}else{
    echo 'Invalid email input';
}

function checkIfAccountExists($email,$conn){
    $db_query = "SELECT username FROM Users WHERE email=?";
    $stmt = $conn->prepare($db_query);
    $stmt->bindParam("s",$email);
    $stmt->execute();

    if ($stmt->num_rows == 1) {
        return true;
    }
    return false;
}

// Inactivate account and assign a new temporary password
function inactivateAccount($email,$hash,$conn){
    $db_query = "UPDATE Users SET password=?, active=?, hash=? WHERE email=?";
    $newPassword = generatePasswordHash();
    $active = 0;
    $stmt = $conn->prepare($db_query);
    $stmt->bind_param("siss",$newPassword,$active,$hash,$email);
    $stmt->execute();
}

function generatePasswordHash(){
    $random = rand(0,10000);
    $newHashedPassword = password_hash($random,PASSWORD_DEFAULT);
    return $newHashedPassword;
}

function generateHash(){
    return md5(rand(0,1000));
}

function sendPasswordResetEmail($email,$url){
    $to = $email;
    $subject = 'Reset Password';
    $mssg = 'This email is to confirm that a password reset has been requested.'.
            ' Follow the link to proceed: '.$url;
    $from = 'From:noreply@yourwebsite.com'.'\r\n';
    mail($to, $subject, $mssg, $from); // Send verification email
}

function getPasswordUpdateURL($email,$hash){
    return 'http://www.domain-name.com/update_password.php?email='.$email.'&hash='.$hash;
}

function removeSessionVariables(){
    session_unset();
    session_destroy();
    session_write_close();
}

?>