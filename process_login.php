<?php
    session_start();
    require_once('login.php');
    require_once('validate_input.php');
    require_once('connect_database.php');

    if (getNumOfLoginAttempts() > 5){
        echo 'Exceeded number of attempts.';
        //header('redirect page');
    }

    //check if all fields are not empty
    if (!isEmpty($_POST['username']) && validateUsername($_POST['username'])
        && !isEmpty($_POST['password']) && validatePassword($_POST['password'])){
        //connect to database and clean up posts
        $connection = new mysqli(SERVERNAME,DB_USER,DB_PASSWORD,DB);
        $username = testInput($_POST['username'],$connection);
        $password = testInput($_POST['password'],$connection);

        $login = new Login($username,$password,$connection);
        if ($login->login()){
            echo 'Successfully logged in!';
            setSessionVariables($login);
            resetLoginAttempts();
            //header('redirect page');
        }else{
            echo 'Invalid username and/or password';
            incrementLoginAttempts();
        }
    }else{
        echo 'Invalid inputs';
        incrementLoginAttempts();
    }

    function setSessionVariables($login){
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $login->getUserId();
        $_SESSION['type_id'] = $login->getTypeId();
        $_SESSION['username'] = $login->getUsername();
    }

    function resetLoginAttempts(){
        $_SESSION['login_attempts'] = 0;
    }

    function incrementLoginAttempts(){
        $_SESSION['login_attempts']+= 1;
    }

    function getNumOfLoginAttempts(){
        if ($_SESSION['login_attempts'] == null){
            resetLoginAttempts();
        }
        return $_SESSION['login_attempts'];
    }
?>