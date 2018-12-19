<?php
    include_once('connect_database.php');
    include_once('validate_input');

    if(isset($_GET['email']) && !empty($_GET['email']) 
        && isset($_GET['hash']) && !empty($_GET['hash']) 
        && isset($_POST['password']) && !empty($_POST['password'])){
            $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);
            $email = testInput($_GET['email'],$connection);
            $hash = testInput($_GET['email'],$connection);
            $newPassword = testInput($_POST['password'],$connection);
            $active = 0;

            if (checkIfAccountExists($email,$active,$hash,$connection)){
                setNewPassword($email,$active,$hash,$newPassword,$connection);
                echo 'New account password has been set';
                header('redirect_page_url');
            }else{
                echo 'Could not find account';
            }
        $connection->close();
    }

    function checkIfAccountExists($email,$active,$hash,$conn){
        $db_query = "SELECT username FROM Users WHERE email=? AND hash=? AND active=?";
        $stmt = $conn->prepare($db_query);
        $stmt->bindParam("ssi",$email,$hash,'0');
        $stmt->execute();
    
        if ($stmt->num_rows == 1) {
            return true;
        }
        return false;
    }

    //Set new password and re-activate account
    function setNewPassword($email,$active,$hash,$password,$conn){
        $db_query = "UPDATE Users SET password=?,active=? WHERE email=? AND hash=? AND active=?";
        $newPassword = generatePasswordHash($password);
        $stmt = $conn->prepare($db_query);
        $stmt->bindParam("sissi",$newPassword,'1',$email,$hash,$active);
        $stmt->execute();
    }

    function generatePasswordHash($password){
        $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
        return $hashedPassword;
    }

?>